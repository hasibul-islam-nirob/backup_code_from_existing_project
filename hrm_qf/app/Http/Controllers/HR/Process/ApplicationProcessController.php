<?php

namespace App\Http\Controllers\HR\Process;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Applications\EmployeeTerminateController;
use App\Http\Controllers\HR\Applications\EmployeeTransferController;
use App\Http\Controllers\HR\Applications\EmployeeActiveResponsibilityController;
use App\Http\Controllers\HR\Applications\EmployeeContractConcludeController;
use App\Http\Controllers\HR\Applications\EmployeeDemotionController;
use App\Http\Controllers\HR\Applications\EmployeeDismissController;
use App\Http\Controllers\HR\Applications\EmployeeMovementController;
use App\Http\Controllers\HR\Applications\EmployeeLeaveController;
use App\Http\Controllers\HR\Applications\EmployeePromotionController;
use App\Http\Controllers\HR\Applications\EmployeeResignController;
use App\Http\Controllers\HR\Applications\EmployeeRetirementController;
use App\Http\Controllers\HR\Others\CommonController;

use App\Model\HR\Employee;
use App\Model\HR\EmployeeResign;
use App\Model\HR\EmployeePromotion;
use App\Model\HR\EmployeeDemotion;
use App\Model\HR\EmployeeTerminate;
use App\Model\HR\EmployeeDismiss;
use App\Model\HR\AllApproval;
use App\Services\HrService as HRS;
use App\Services\CommonService as Common;
use App\Services\HrService;
use App\Services\RoleService as Role;
use Illuminate\Http\Request;
use DateTime;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationProcessController extends Controller
{
    public function getPassport($requestData, $operationType, $dmp, $status)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'comment' => 'required',
                // 'effective_date' => ((($status == 'Approved') && ($dmp == 1)) ? 'required' : ''),
            );

            $attributes = array(
                // 'comment'     => 'Comment',
                // 'effective_date'        => 'Effective date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request, $applStatus)
    {

        if ($request->isMethod('post')) {

            // dd($request->all());

            $allAppl = [];
            $source = '';

            if ($applStatus == "approved_application") {
                # code...
                $allAppl = $this->get_all_approved_application($request);
                $source = 'aa';
            } elseif ($applStatus == "pending_application") {
                # code...
                $allAppl = $this->get_all_pending_application($request);
                $source = 'pa';
            } elseif ($applStatus == "rejected_application") {
                # code...
                $allAppl = $this->get_all_rejected_application($request);
                $source = 'ra';
            }

            $data = [];
            $totalData = count($allAppl);
            $totalData = count($data);
            $totalFiltered = $totalData;
            $index = 0;

            foreach ($allAppl as $key => $applCat) {

                foreach ($applCat as $key1 => $row) {

                    $totalData = count($applCat);
                    $totalFiltered = $totalData;

                    if($row->current_stage == null){
                        $currentStage = '-';
                    }else{

                        $currentStageArr = explode("-", $row->current_stage);
                        $designationID = $currentStageArr[0];
                        $departmentID =  $currentStageArr[1];
                        ## Designation Query
                        $designationData = HRS::fnForDesignationData(array($designationID), 'name');
                        ## Department Query
                        $departmentData = HRS::fnForDepartmentData(array($departmentID));
                        $currentStage = $currentStageArr[2] .' - '. implode($departmentData) .' - '. implode($designationData);

                    }

                    
                    if($this->get_application_type($row)[0] === "Leave Application"){

                        $data[$index]['application_code'] = $row->leave_code;
                        $data[$index]['applying_date'] = (new DateTime($row->leave_date))->format('d-m-Y');
                        $data[$index]['start_end_date'] = (new DateTime($row->date_from))->format('d-m-Y').' - '.(new DateTime($row->date_to))->format('d-m-Y');
                        $data[$index]['effective_date'] = (new DateTime($row->effective_date))->format('d-m-Y');

                    }elseif($this->get_application_type($row)[0] === "Employee Movement"){
                        
                        $data[$index]['application_code'] = $row->movement_code;
                        $data[$index]['applying_date'] = (new DateTime($row->appl_date))->format('d-m-Y');
                        $data[$index]['start_end_date'] = (new DateTime($row->movement_date))->format('d-m-Y').' - '.(new DateTime($row->movement_date))->format('d-m-Y');
                        $data[$index]['effective_date'] = (new DateTime($row->effective_date))->format('d-m-Y');
                    }

                    $data[$index]['id'] = $row->id;
                    $data[$index]['sl'] = $index + 1;
                    $data[$index]['source'] = $source;
                    $data[$index]['application_type'] = $this->get_application_type($row)[0];
                    $data[$index]['application_cat'] = $this->get_application_type($row)[1];
                    $data[$index]['applicant_name'] = $row->employee['emp_name'] . " (" . $row->employee['emp_code'] . ")";
                    
                    // $data[$index]['applying_date'] = (new DateTime($row->leave_date))->format('d-m-Y');
                    // $data[$index]['start_end_date'] = $dateString;
                    // $data[$index]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                    $data[$index]['is_active'] = $row->is_active;
                    $data[$index]['display_for'] = $row->display_for;
                    $data[$index]['status'] = ($row->is_active == 3) ? '<p style="color: #0c10f0"><b><i class="fas fa-hourglass-end mr-2"></i>Processing</b></p>' : (($row->is_active == 1) ? '<p style="color: #0cf041"><b><i class="fas fa-check mr-2"></i>Approved</b></p>' : (($row->is_active == 0) ? '<p><b>Draft</b></p>' : '<p style="color: #d40f0f"><b><i class="fas fa-times mr-2"></i>Rejected</b></p>'));
                    $data[$index]['current_stage'] = $currentStage;
                    $data[$index]['action'] = Role::roleWiseArray($this->GlobalRole, encrypt($row->id));

                    $index++;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );
            // dd($json_data);
            return response()->json($json_data);
        } else {

            return view('HR.Process.ApplicationProcess.index');
        }
    }

    public function get_all_pending_application($request)
    {
        $userInfo = Auth::user();
        $empInfo = Employee::find($userInfo->emp_id);
        $currentStage = null;

        $zoneId         = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId       = (empty($request->region_id)) ? null : $request->region_id;
        $areaId         = (empty($request->area_id)) ? null : $request->area_id;
        $branchId       = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId  = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId   = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId     = (empty($request->employee_id)) ? null : $request->employee_id;
        // $applicationType = (empty($request->application_type)) ? null : $request->application_type;
        $startDate       = (empty($request->start_date)) ? null : $request->start_date;
        $endDate         = (empty($request->end_date)) ? null : $request->end_date;
        $applStatus      = (empty($request->appl_status)) ? null : $request->appl_status;
        $applicationCode = (empty($request->application_code)) ? null : $request->application_code;

        // ss($request->all());

        if(Common::isSuperUser() == false && Common::isDeveloperUser() == false){

            if (empty($empInfo)) {
                return array();
            }

            $currentStage = $empInfo->designation_id . '-' . $empInfo->department_id . '-' . (($userInfo->branch_id != 1) ? 'bo' : 'ho');
        }

        $allApplModel = [
            0 => 'EmployeeResign',
            1 => 'EmployeePromotion',
            2 => 'EmployeeDemotion',
            3 => 'EmployeeDismiss',
            4 => 'EmployeeTerminate',
            5 => 'EmployeeTransfer',
            6 => 'EmployeeActiveResponsibility',
            7 => 'EmployeeContractConclude',
            8 => 'EmployeeRetirement',
            9 => 'EmployeeLeave',
            10 => 'EmployeeMovement',
            11 => 'AppAdvanceSalary',
            12 => 'AppSecurityMoney',
            13 => 'HrApplicationLoan',
        ];

        ## Search Filter for => Application Type Start
        if (!empty($request->application_type)) {
            if (array_key_exists($request->application_type, $allApplModel)) {
                $allApplModel = [$request->application_type => $allApplModel[$request->application_type]];
            }
        }
        ## Search Filter for => Application Type End

        $appl_pend = [];
        foreach ($allApplModel as $model) {
            $model = '\\App\\Model\\HR\\' . $model;
            $ap = $model::where('is_active', 3)
                ->where(function ($data2) use ($currentStage){

                    if( $currentStage != null ){
                        $data2->where('current_stage', 'LIKE', $currentStage . "%");
                    }
                })
                ->where(function ($data) use ($userInfo, $employeeId, $branchId, $designationId, $departmentId, $startDate, $endDate, $applStatus, $applicationCode, $model) {
                    if ($userInfo->branch_id != 1) {
                        $data->whereIn('branch_id', Common::getBranchIdsForAllSection(['branchId'=> $userInfo->branch_id]));
                    }

                    if(!empty($employeeId)){
                        $data->where('emp_id', $employeeId);
                    }

                    // if(!empty($applicationCode)){

                    //     $modelInstance = new $model();
                    //     if ($modelInstance->getConnection()->getSchemaBuilder()->hasColumn($modelInstance->getTable(), 'leave_code')) {
                    //         $data->orWhere('leave_code', 'LIKE', $applicationCode . "%");
                    //     }
                    // }
                })
                ->get();


            $appl_pend[] = $ap;
        }

        return $appl_pend;
    }

    public function get_all_approved_application($request)
    {
        $appl_appr = [];
        $evId_by_appl = array();


        if(Common::isSuperUser() == true){

            $evId_by_appl = DB::table('hr_approval_all')
            ->where('inspection_status', 'Approved')
            ->get()
            ->groupBy('event_id');

        }else{
            $evId_by_appl = DB::table('hr_approval_all')
            ->where([['inspect_by', Auth::id()], ['inspection_status', 'Approved']])
            ->get()
            ->groupBy('event_id');
        }

        // $evId_by_appl = DB::table('hr_approval_all')
        //     ->where([['inspect_by', Auth::id()], ['inspection_status', 'Approved']])
        //     ->get()
        //     ->groupBy('event_id');

        foreach ($evId_by_appl as $eventId => $appl) {
            $applType = $this->get_application_type(null, $eventId);
            $applIds = $appl->pluck('master_id');
            $model = '\\App\\Model\\HR\\' . $applType[1];
            $appl_s = $model::whereIn('id', $applIds)->where('is_active', 1)->get();
            $appl_appr[] = $appl_s;
        }

        // dd($appl_appr);
        return $appl_appr;
    }

    public function get_all_rejected_application($request)
    {

        $appl_rej = [];
        $evId_by_appl = array();

        if(Common::isSuperUser() == true){

            $evId_by_appl = DB::table('hr_approval_all')
            ->where('inspection_status', 'Rejected')
            ->get()
            ->groupBy('event_id');

        }else{
            $evId_by_appl = DB::table('hr_approval_all')
            ->where([['inspect_by', Auth::id()], ['inspection_status', 'Rejected']])
            ->get()
            ->groupBy('event_id');
        }

        // $evId_by_appl = DB::table('hr_approval_all')
        //     ->where([['inspect_by', Auth::id()], ['inspection_status', 'Rejected']])
        //     ->get()
        //     ->groupBy('event_id');

        foreach ($evId_by_appl as $eventId => $appl) {
            $applType = $this->get_application_type(null, $eventId);
            $applIds = $appl->pluck('master_id');
            $model = '\\App\\Model\\HR\\' . $applType[1];
            $appl_s = $model::whereIn('id', $applIds)->where('is_active', 2)->get();
            $appl_rej[] = $appl_s;
        }
        return $appl_rej;
    }

    public function get_application_details_with_notes($applId, $applCat)
    {
        try {
            $model = '\\App\\Model\\HR\\' . $applCat;

            $appl =  $model::with('employee', 'branch', 'reasons')->find($applId);
            $applType = $this->get_application_type($appl);
            $prevApprovalNotes = AllApproval::where([['master_id', $applId], ['event_id', $applType[4]]])->orderBy('step_no')->with('employee')->get();

            if (!empty($appl)) {
                return response()->json([
                    'message'    => "Data fetched successfully!!",
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => [
                        'application' => $appl,
                        'notes' => $prevApprovalNotes,
                    ],
                    'applType' => $applType,
                ], 200);
            } else {
                return response()->json([
                    'message'    => "No data found!!",
                    'status' => 'error',
                    'statusCode' => 400,
                    'result_data' => ''
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'    => "Internal Server Error. Try Again!!",
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function proceed_application(Request $request, $applCat, $status, $dmp)
    {
        $passport = $this->getPassport($request, 'store', $dmp, $status);
        if ($passport['isValid']) {

            try {
                $max_step = AllApproval::where([['master_id', $request['application_id']], ['event_id', $request['event_id']]])->max('step_no');
                if ($max_step == null) {
                    $max_step = 0;
                }

                DB::beginTransaction();

                $approval = new AllApproval();
                $approval->master_id = $request['application_id'];
                $approval->event_id = $request['event_id'];
                $approval->step_no = $max_step + 1;
                $approval->inspect_by = Auth::id();
                $approval->inspection_status = $status;
                $approval->related_data = ($request['effective_date'] != '') ? (new DateTime($request['effective_date']))->format('Y-m-d') : null;
                $approval->comment = $request['comment'];
                $approval->comment_date = date("Y-m-d");
                $approval->save();

                $model = '\\App\\Model\\HR\\' . $applCat;
                $appl =  $model::find($request['application_id']);

                if ($status == 'Approved') {

                    $applicant = Employee::find($appl->emp_id);
                    $permission_for = ($appl->branch_id == 1) ? "ho" : "bo";
                    $next_approval = DB::table('hr_reporting_boss_config')
                        ->where([['event_id', $request['event_id']], ['permission_for', $permission_for]])
                        ->where([['department_for_id', $applicant->department_id], ['designation_for_id', $applicant->designation_id]])
                        ->where('level', $max_step + 2)
                        ->where([['is_delete', 0],['is_active', 1]])
                        ->first();

                    if (empty($next_approval)) {
                        //All steps are complete
                        //dd(empty($next_approval));
                        $this->approve($appl, $request['event_id']);
                    } else {
                        //There are some pending steps
                        //Calculate next stage and update the application next stage
                        $appl->current_stage = CommonController::get_stage($next_approval);
                     
                    }
                } else {
                    //Rejected application
                    $appl->is_active = 2;
                    $appl->current_stage = null;
                  
                }
                $appl->save();

                DB::commit();

                return response()->json([
                    'message'    => "Data saved successfully!!",
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

    public function get_application_type($object = null, $eventId = null)
    {
        if ($eventId == 1 || (is_object($object) && get_class($object) == 'App\Model\HR\SalaryIncrement')) {
            return [
                0 => 'Salary Increment',
                1 => 'SalaryIncrement', //Model
                2 => 'SalaryIncrement',
                3 => 'salary_increment',
                4 => 1, //Event id
            ];
        } else if ($eventId == 2 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeTransfer')) {
            return [
                0 => 'Transfer Application',
                1 => 'EmployeeTransfer',
                2 => 'Transfer',
                3 => 'transfer',
                4 => 2, //Event id
            ];
        } else if ($eventId == 3 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeTerminate')) {
            return [
                0 => 'Terminate Application',
                1 => 'EmployeeTerminate',
                2 => 'Terminate',
                3 => 'terminate',
                4 => 3, //Event id
            ];
        } else if ($eventId == 4 || (is_object($object) && get_class($object) == 'App\Model\HR\Employee Entry')) {
            return [
                0 => 'Employee Entry',
                1 => 'EmployeeEntry',
                2 => 'Employee Entry',
                3 => 'employee_entry',
                4 => 4, //Event id
            ];
        } else if ($eventId == 5 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeLeave')) {
            return [
                0 => 'Leave Application',
                1 => 'EmployeeLeave',
                2 => 'Leave',
                3 => 'leave',
                4 => 5, //Event id
            ];
        } else if ($eventId == 6 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeLoan')) {
            return [
                0 => 'Loan Application',
                1 => 'EmployeeLoan',
                2 => 'Loan',
                3 => 'loan',
                4 => 6, //Event id
            ];
        } else if ($eventId == 7 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeResign')) {
            return [
                0 => 'Resign Application',
                1 => 'EmployeeResign',
                2 => 'Resign',
                3 => 'resign',
                4 => 7, //Event id
            ];
        } else if ($eventId == 8 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeePromotion')) {
            return [
                0 => 'Promotion Application',
                1 => 'EmployeePromotion',
                2 => 'Promotion',
                3 => 'promotion',
                4 => 8, //Event id
            ];
        } else if ($eventId == 9 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeDemotion')) {
            return [
                0 => 'Demotion Application',
                1 => 'EmployeeDemotion',
                2 => 'Demotion',
                3 => 'demotion',
                4 => 9, //Event id
            ];
        } else if ($eventId == 10 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeDismiss')) {
            return [
                0 => 'Dismiss Application',
                1 => 'EmployeeDismiss',
                2 => 'Dismiss',
                3 => 'dismiss',
                4 => 10, //Event id
            ];
        } else if ($eventId == 11 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeActiveResponsibility')) {
            return [
                0 => 'Employee Active Responsibility',
                1 => 'EmployeeActiveResponsibility',
                2 => 'Active Responsibility',
                3 => 'active_responsibility',
                4 => 11, //Event id
            ];
        } else if ($eventId == 12 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeContractConclude')) {
            return [
                0 => 'Employee Contract Conclude',
                1 => 'EmployeeContractConclude',
                2 => 'Contract Conclude',
                3 => 'contract_conclude',
                4 => 12, //Event id
            ];
        } else if ($eventId == 13 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeRetirement')) {
            return [
                0 => 'Employee Retirement',
                1 => 'EmployeeRetirement',
                2 => 'Retirement',
                3 => 'retirement',
                4 => 13, //Event id
            ];
        }
        else if ($eventId == 14 || (is_object($object) && get_class($object) == 'App\Model\HR\EmployeeMovement')) {
            return [
                0 => 'Employee Movement',
                1 => 'EmployeeMovement',
                2 => 'Movement',
                3 => 'movement',
                4 => 14, //Event id
            ];
        }
    }

    public function get_effective_date($eventId, $masterId)
    {
        $temp =  AllApproval::where([['event_id', $eventId], ['master_id', $masterId]])
            ->where('related_data', '!=', null)
            ->orderBy('step_no', 'DESC')
            ->get()
            ->first();
        if (!empty($temp)) {
            return $temp->related_data;
        } else {
            return null;
        }
    }

    public function approve($appl, $eventId)
    {

        $appl->is_active = 1;
        $appl->current_stage = null;
        $effective_date = $this->get_effective_date($eventId, $appl->id);
        if ($effective_date != null) {
            $appl->effective_date = $effective_date;
        }
        $appl->save();

        if ($eventId == 7) { //Resign Application
            (new EmployeeResignController)->finish($appl, $effective_date);
        } elseif ($eventId == 8) { //Promotion Application
            (new EmployeePromotionController)->finish($appl, $effective_date);
        } elseif ($eventId == 9) { //Demotion Application
            (new EmployeeDemotionController)->finish($appl, $effective_date);
        } elseif ($eventId == 10) { //Dismiss Application
            (new EmployeeDismissController)->finish($appl, $effective_date);
        } elseif ($eventId == 11) { //Active Responsibility Application
            (new EmployeeActiveResponsibilityController)->finish();
        } elseif ($eventId == 12) { //Contract Conclude Application
            (new EmployeeContractConcludeController)->finish();
        } elseif ($eventId == 13) { //Retirement Application
            (new EmployeeRetirementController)->finish($appl, $effective_date);
        } elseif ($eventId == 2) { //Transfer Application
            (new EmployeeTransferController)->finish($appl, $effective_date);
        } elseif ($eventId == 3) { //Terminate Application
            (new EmployeeTerminateController)->finish($appl, $effective_date);
        }elseif ($eventId == 14) { //Movement Application
            (new EmployeeMovementController)->finish($appl, $effective_date);
        }elseif ($eventId == 5) { //Movement Application
            (new EmployeeLeaveController)->finish($appl, $effective_date);
        }
    }

    public function getEmpInfoFromSysEmpID($create = null, $approved = null){
        // dd($create, $approved);
        $employeeData = DB::table('hr_employees')
            ->where([['is_delete', 0]])
            ->whereIn('id', [$create, $approved])
            ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
            ->pluck('emp_name', 'id')
            ->toArray();
        // dd($employeeData);
        return response()->json($employeeData);
    }
}
