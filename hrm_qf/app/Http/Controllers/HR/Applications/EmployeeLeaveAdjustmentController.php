<?php

namespace App\Http\Controllers\HR\Applications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\HR\Employee;
use App\Model\HR\FiscalYear;
use App\Model\HR\EmployeeLeaveAdjustment;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\HR\Others\CommonController;
use App\Http\Controllers\HR\Process\ApplicationProcessController;
use Illuminate\Support\Facades\Date;
use Symfony\Component\VarDumper\Cloner\Data;

class EmployeeLeaveAdjustmentController extends Controller
{
    
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'employee_id' => 'required',
                'fiscal_year_id' => 'required',
                'adjustment_for' => 'required',
                'adjustment_month' => 'required',
                'adjustment_value' => 'required',
                'application_date' => 'required',

            );

            $attributes = array(
                'employee_id' => 'Employee Id',
                'fiscal_year_id' => 'Fiscal year id',
                'adjustment_for' => 'Adjustment for',
                'adjustment_month' => 'Adjustment month',
                'adjustment_value' => 'Adjustment value',
                'application_date' => 'Application date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();

            if ($requestData->is_active == 1) { // only view
                $IgnoreArray = ['view','delete',  'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view','delete',  'send', 'btnHide' => true];
            }

            // 'view',

            $errorMsg = $IgnoreArray;

            //dd($errorMsg);
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request)
    {

        // ss($request->all());
        if ($request->isMethod('post')) {

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");


            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            $totalRecords = EmployeeLeaveAdjustment::where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            
            $start_date            = (empty($request->start_date)) ? null : date("Y-m-d", strtotime($request->start_date));
            $end_date            = (empty($request->end_date)) ? null :  date("Y-m-d", strtotime($request->end_date));
            $employee_id            = (empty($request->employee_id)) ? null : $request->employee_id;

            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchId          = (empty($request->branch_id)) ? null : $request->branch_id;

            $selBranchArr = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData  = EmployeeLeaveAdjustment::from('hr_app_leaves_adjustment AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $selBranchArr){
                    ## Calling Permission Query Function
                    HRS::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $selBranchArr);
                })
                /* Old Permission Code
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId){
                    if(Common::isSuperUser() == true || Common::isDeveloperUser() == true){
                        ## nothing to do
                    }
                    else {

                        $tmpBranch = HRS::getUserAccesableBranchIds();
                        if(in_array(101, $statusArray)){
                            ## All Data for Permitted Branches
                            ## nothing to do
                            // $query->whereIn('apl.branch_id', $selBranchArr)
                        }
                        elseif(in_array(102, $statusArray)){
                            ## All Branch Data Without HO
                            $perQuery->where('apl.branch_id' , '<>' ,1);
                        }
                        elseif(in_array(103, $statusArray)){
                            ## All Data Only HO
                            $perQuery->where('apl.branch_id', 1);
                        }
                        else{
                            $perQuery->where('apl.created_by', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('apl.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })
                */
                ->where(function($query) use ($start_date, $end_date, $employee_id, $branchId){

                    if (!empty($start_date) && empty($end_date)) {
                        $query->where('effective_date','>=',$start_date);
                    }
                    if (empty($start_date) && !empty($end_date)) {
                        $query->where('effective_date','=<',$end_date);
                    }
                    if (!empty($start_date) && !empty($end_date)) {
                        $query->where('effective_date','>=',$start_date);
                        $query->where('effective_date','<=',$end_date);
                    }

                    if (!empty($employee_id)) {
                        $query->where('emp_id', $employee_id);
                    }
                    if (!empty($branchId)) {
                        $query->where('branch_id', $branchId);
                    }

                });

            // $totalRecords = ;
            $totalRecordswithFilter = $allData->count();

            $allData = $allData->skip($start)->take($rowperpage)->select('apl.*')->get();

            $data      = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');
                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                if (!empty($row->employee['emp_name'])) {
                    $empName = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                }else{
                    $empName = 'All Employee';
                }


                $data[$key]['id']                 = $sno;
                $data[$key]['employee_name']      = $empName;

                $data[$key]['fiscal_year']      = $row->fiscalYear['fy_name'];
                $data[$key]['adjustment_for']      = ($row->adjustment_for == 1) ? "Leave Adjustment" : "Salary Deduction";;
                $data[$key]['adjustment_month']      = $row->month['name'];
                $data[$key]['adjustment_value']      = $row->adjustment_value;
                $data[$key]['effective_date']      = date('d/m/Y', strtotime($row->effective_date));

                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                


                $statusFlag = "<span>Draft</span>";
                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Active</span>';
                }
                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>Rejected</span>';
                }
                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0"><i class="fas fa-hourglass-end mr-2"></i>Processing</span>';
                }
                $data[$key]['status'] = $statusFlag;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;
            }

            // dd($data);

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);
        }
    }

    public function insert(Request $request, $status)
    {


        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        // ss($request->all());
        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;

        $fiscal_year_id = !empty($request['fiscal_year_id']) ? $request['fiscal_year_id'] : null;
        $adjustment_for = !empty($request['adjustment_for']) ? $request['adjustment_for'] : null;
        $adjustment_month = !empty($request['adjustment_month']) ? $request['adjustment_month'] : null;
        $adjustment_value = !empty($request['adjustment_value']) ? $request['adjustment_value'] : null;
        $application_date = !empty($request['application_date']) ? $request['application_date'] : null;
        $description = !empty($request['description']) ? $request['description'] : null;
        $note = !empty($request['note']) ? $request['note'] : null;


        $checkDuplicate = DB::table('hr_app_leaves_adjustment')
                        ->where([['emp_id', $employeeId],['fiscal_year_id', $fiscal_year_id], ['adjustment_month', $adjustment_month],['adjustment_for', $adjustment_for]])
                        ->count();
        if ($checkDuplicate > 0) {
            return response()->json([
                'message'    => "Data Already Exist",
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => '',
            ], 400);
        }


        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $appl                   = new EmployeeLeaveAdjustment();

                $appl->emp_id             = $employeeId;
                $appl->fiscal_year_id     = $fiscal_year_id;
                $appl->adjustment_for     = $adjustment_for;
                $appl->adjustment_month   = $adjustment_month;
                $appl->adjustment_value   = $adjustment_value;
                $appl->branch_id          = $branchId;
                $appl->description        = $description;
                $appl->application_date   = (new DateTime($application_date))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($application_date))->format('Y-m-d');
                $appl->note               = $note;
                $appl->created_at     = (new DateTime())->format('Y-m-d H:i:s');
                $appl->created_by     = Auth::user()->id;

                if ($status != 'send') {
                    $appl->is_active = 0;
                }

                $appl->save();

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function update(Request $request, $status)
    {


        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        // ss($request->all());
        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;

        $fiscal_year_id = !empty($request['fiscal_year_id']) ? $request['fiscal_year_id'] : null;
        $adjustment_for = !empty($request['adjustment_for']) ? $request['adjustment_for'] : null;
        $adjustment_month = !empty($request['adjustment_month']) ? $request['adjustment_month'] : null;
        $adjustment_value = !empty($request['adjustment_value']) ? $request['adjustment_value'] : null;
        $application_date = !empty($request['application_date']) ? $request['application_date'] : null;
        $description = !empty($request['description']) ? $request['description'] : null;
        $note = !empty($request['note']) ? $request['note'] : null;


        // $checkDuplicate = DB::table('hr_app_leaves_adjustment')
        //                 ->where([['emp_id', $employeeId],['fiscal_year_id', $fiscal_year_id], ['adjustment_month', $adjustment_month],['adjustment_value', $adjustment_value]])
        //                 ->count();
        // if ($checkDuplicate > 0) {
        //     return response()->json([
        //         'message'    => "Data Already Exist",
        //         'status' => 'error',
        //         'statusCode' => 400,
        //         'result_data' => '',
        //     ], 400);
        // }


        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $appl                   = EmployeeLeaveAdjustment::find($request['adjustment_id']);;

                $appl->emp_id             = $employeeId;
                $appl->fiscal_year_id     = $fiscal_year_id;
                $appl->adjustment_for     = $adjustment_for;
                $appl->adjustment_month   = $adjustment_month;
                $appl->adjustment_value   = $adjustment_value;
                $appl->branch_id          = $branchId;
                $appl->description        = $description;
                $appl->application_date   = (new DateTime($application_date))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($application_date))->format('Y-m-d');
                $appl->note               = $note;
                $appl->updated_at     = (new DateTime())->format('Y-m-d H:i:s');
                $appl->updated_by     = Auth::user()->id;

                if ($status != 'send') {
                    $appl->is_active = 0;
                }else{
                    $appl->is_active = 1;
                }

                $appl->save();

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }


    public function get(Request $request, $id){

        return CommonController::get_application('\\App\\Model\\HR\\EmployeeLeaveAdjustment', $id, ['branch', 'employee', 'fiscalYear', 'month']);
    }


    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\EmployeeLeaveAdjustment', $id);
    }

    public function getEligibleLeaev($emp, $lv_cat, $lv_cat_config, $fy)
    {

        $lv_total = $lv_cat_config->allocated_leave;
        $lv_acquired = 0;

        /* Count elligible leaves (fiscal year wise)*/
        if ($lv_cat->leave_type_uid == 1) { //Pay
            if ($lv_cat_config->consume_policy == 'eligible') {

                $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                if ((new DateTime(date('Y-m-d')))->format('d') >= 22) { //After 22th day of the month, an employee can acquire a leave
                    $month_elapsed++;
                }

                if (date('Y-m-d') > $fy->fy_end_date) {
                    $month_elapsed = 12;
                }

                $lv_acquired = (int) floor(($lv_total / 12) * $month_elapsed);
            } elseif ($lv_cat_config->consume_policy == 'yearly_allocated') {
                $lv_acquired = $lv_total;
            }
        } elseif ($lv_cat->leave_type_uid == 3) { //Earn

            if (empty($emp->join_date)) {
                throw new \Exception('Joining date is not assigned for this employee!!');
            }

            if (empty($emp->permanent_date)) {
                throw new \Exception('Permanent date is not assigned for this employee!!');
            }

            $lv_count_from = ($lv_cat_config->eligibility_counting_from == 'joining_date') ? $emp->join_date : $emp->permanent_date;

            if ((int)floor(abs(strtotime($lv_count_from) - strtotime(date('Y-m-d'))) / (365 * 60 * 60 * 24)) < $lv_cat_config->consume_after) {
                return 0;
            }

            if ($lv_cat_config->consume_policy == 'eligible') {

                $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                if ((new DateTime(date('Y-m-d')))->format('d') >= 22) { //After 22th day of the month, an employee can acquire a leave
                    $month_elapsed++;
                }

                if (date('Y-m-d') > $fy->fy_end_date) {
                    $month_elapsed = 12;
                }

                $lv_acquired = (int) floor(($lv_total / 12) * $month_elapsed);
            }

            return $lv_acquired;
        } elseif ($lv_cat->leave_type_uid == 4) { //Parental

            /* if(count($appl_s) >= $lv_cat_config->times_of_leave){
                throw new \Exception('You can not apply for parental leave more than ' . $lv_cat_config->times_of_leave . ' times!!');
            } */

            if ($lv_cat_config->consume_policy == 'yearly_allocated') {
                $lv_acquired = $lv_total;
            }
        }
        /* Count elligible leaves (fiscal year wise)*/
        return $lv_acquired;
    }

    public function getConsumedLeave($emp_id, $lv_cat_id, $fy)
    {
        $startDate = (new DateTime($fy->fy_start_date))->format('Y-m-d');
        $endDate = (new DateTime($fy->fy_end_date))->format('Y-m-d');

            ## Main <Old> Code
        /*
            $appl_s = DB::table('hr_app_leaves')
                ->where(['is_delete', 0])
                ->where('leave_cat_id', $lv_cat_id)
                ->whereIn('emp_id', [$emp_id, 0])
                ->where(function ($q) {
                    $q->where('is_active', 1);
                    $q->orWhere('is_active', 3);
                })
                ->whereDate('date_to', '>=', (new DateTime($fy->fy_start_date))->format('Y-m-d'))
                ->whereDate('date_from', '<=', (new DateTime($fy->fy_end_date))->format('Y-m-d'))
                ->select(DB::raw('SUM(DATEDIFF(date_to, date_from) +1 ) AS consumed'))
                ->first();
        */
        $empInfo = DB::table('hr_employees')->where('id', $emp_id)->first();
        $empJoinDate = optional($empInfo)->join_date;

        $appl_s = DB::table('hr_app_leaves')
        ->where('is_delete', 0)
        ->where('leave_cat_id', $lv_cat_id)
        ->whereIn('emp_id', [$emp_id, 0])
        ->where(function ($q) {
            $q->where('is_active', 1);
            $q->orWhere('is_active', 3);
        })
        ->where('leave_date','>',$empJoinDate)
        ->whereDate('date_to', '>=', (new DateTime($fy->fy_start_date))->format('Y-m-d'))
        ->whereDate('date_from', '<=', (new DateTime($fy->fy_end_date))->format('Y-m-d'))
        ->get();

        // ss($appl_s, $fy);

        $leaveCounter = 0;
        if( count($appl_s) > 0){
            foreach($appl_s as $rowLeave){
                $startDate = $rowLeave->date_from;
                $endDate = $rowLeave->date_to;
                $tempDate = $startDate;
    
                while ($tempDate <= $endDate) {
                    $leaveCounter += 1;
                    $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                }
            }
        }
        // dd($leaveCounter);

        return $leaveCounter;

        // return ((isset($appl_s->consumed)) ? $appl_s->consumed : 0);
    }

    public function getLeaveInfo(Request $request, $emp_id, $app_date)
    {

        try {
            $app_date = (new DateTime($app_date))->format('Y-m-d');

            $emp = Employee::with('organizationData')->where('id', $emp_id)->first();
            $lv_cat = DB::table('hr_leave_category')->where('is_active', 1)->where('is_delete', 0)->get();

            if (empty($emp->organizationData->rec_type_id)) {
                throw new \Exception('Recruitment type is not assigned.');
            }

            $emp_rec_type_id = $emp->organizationData->rec_type_id;

            $fy = DB::table('gnl_fiscal_year')
            ->where('fy_start_date', '<=', $app_date)
            ->where('fy_end_date', '>=', $app_date)
            ->whereIn('fy_for', ['LFY', 'BOTH'])
            ->first();

            $lv_info = [];

            // dd($fy);

            foreach ($lv_cat as $lvc) {

                // dd($lv_cat, $lvc->leave_type_uid);

                $lv_cat_config = DB::table('hr_leave_category_details')
                    ->where([['leave_cat_id', $lvc->id], ['rec_type_id', $emp_rec_type_id]])
                    ->where('effective_date_from', '<=', $app_date)
                    ->orderBy('effective_date_from', 'desc')
                    ->first();

                if (!empty($lv_cat_config) && $lvc->leave_type_uid != 2) {
                    $lv_info['consumed'][$lvc->name] = $this->getConsumedLeave($emp->id, $lvc->id, $fy);
                    $lv_info['eligible'][$lvc->name] = $this->getEligibleLeaev($emp, $lvc, $lv_cat_config, $fy);
                    $lv_info['allocated'][$lvc->name] = $lv_cat_config->allocated_leave;
                } else if ($lvc->leave_type_uid != 2) {
                    $lv_info['consumed'][$lvc->name] = 0;
                    $lv_info['eligible'][$lvc->name] = 0;
                    $lv_info['allocated'][$lvc->name] = 0;
                }
            }

            // dd($lv_info);

            return response()->json([
                'lv_info' => $lv_info,
                'emp' => $emp,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'    => $e->getMessage(),
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => '',
            ], 400);
        }
    }
}
