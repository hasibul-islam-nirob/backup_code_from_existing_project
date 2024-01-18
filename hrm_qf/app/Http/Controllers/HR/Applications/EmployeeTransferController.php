<?php

namespace App\Http\Controllers\HR\Applications;

use DateTime;
use App\Model\HR\Employee;
use App\Services\HrService;
use Illuminate\Http\Request;
use App\Model\HR\EmployeeTransfer;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\AccService as ACCS;
use App\Services\FamService as FAMS;
use App\Services\GnlService as GNLS;
use App\Services\InvService as INVS;
use App\Services\MfnService as MFNS;

use App\Services\PosService as POSS;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use App\Services\BillService as BILLS;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\HR\Others\CommonController;
use App\Http\Controllers\HR\Process\ApplicationProcessController;


class EmployeeTransferController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'add_branch_from_id' => 'required',
                'add_branch_to_id' => 'required',
                'employee_id' => 'required',
                'transfer_date'        => 'required',
                'exp_effective_date'     => 'required',

            );

            $attributes = array(
                'exp_effective_date'     => 'Expected effective date',
                'transfer_date'        => 'Transfer Date',
                // 'add_branch_from_id' => 'Branch From',
                'add_branch_to_id' => 'Branch To',
                'employee_id' => 'Employee',

            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();

            if ($requestData->is_active == 1) { // only view
                $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit', 'send', 'btnHide' => true];
            }

            $errorMsg = $IgnoreArray;

            //dd($errorMsg);
        }

        if ($operationType == 'send') {

            $checkTx = '';

            $checkTx = ACCS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            if(empty($checkTx)){
                $checkTx = BILLS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            }
            if(empty($checkTx)){
                $checkTx = FAMS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            }
            if(empty($checkTx)){
                $checkTx = INVS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            }
            if(empty($checkTx)){
                $checkTx = MFNS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            }
            if(empty($checkTx)){
                $checkTx = POSS::checkTransactionForEmployee($requestData->employee_id,'transferring');
            }

            if (!empty($checkTx)) {
                $errorMsg = $checkTx;
            }
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


            $totalRecords = EmployeeTransfer::select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = EmployeeTransfer::select('count(*) as allcount')->where('is_delete', 0)->where('transfer_code', 'like', '%' .$searchValue . '%')->count();

            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchTo          = (empty($request->branch_to)) ? null : $request->branch_to;

            $selBranchArr = Common::getBranchIdsForAllSection([
                // 'companyId'     => $companyId,
                // 'projectId'     => $projectId,
                // 'projectTypeId' => $projectTypeId,
                'branchId'      => $branchTo,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData  = EmployeeTransfer::from('hr_app_transfers AS apl')
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
                        elseif(in_array(104, $statusArray)){
                            ## All data for own department of permitted branches
                            // $perQuery->whereIn('apl.branch_id', $tmpBranch);
                            $perQuery->where('apl.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(105, $statusArray)){
                            ## All data for own department of all branches without HO
                            $perQuery->where('apl.branch_id', '<>' , 1);
                            $perQuery->where('apl.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(106, $statusArray)){
                            ## All data for own department only HO
                            $perQuery->where([['apl.branch_id', 1],['apl.department_id', $loginUserDeptId]]);
                        }
                        else{
                            $perQuery->where('apl.created_by', $userInfo['id']);
                            // $perQuery->orWhere('apl.emp_id', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('apl.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })*/
                ->when(true, function($query) use ($columnName, $columnSortOrder, $request){ //Ordering
                    if($columnName == "transfer_code"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "employee_name"){
                        if(empty($request->department_id) && empty($request->designation_id)){
                            $query->join('hr_employees as e', function ($join){
                                $join->on('apl.emp_id', '=', 'e.id');
                            });
                        }
                        $query->orderBy('e.emp_name', $columnSortOrder);
                    }
                    elseif($columnName == "branch_from"){
                        $query->join('gnl_branchs as b', function ($join){
                            $join->on('apl.branch_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);
                    }
                    elseif($columnName == "branch_to"){
                        $query->join('gnl_branchs as b', function ($join){
                            $join->on('apl.branch_to_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);
                    }
                    elseif($columnName == "transfer_date"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "effective_date"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "status"){
                        $query->orderBy('apl.is_active', $columnSortOrder);
                    }
                    elseif($columnName == "reason"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "id"){
                        $query->orderBy('apl.id', 'desc');
                    }
                })
                ->when(true, function ($query) use ($request, $userInfo, $searchValue, $selBranchArr) {

                    

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }

                    elseif(!empty($request->branch_from)){
                        $query->where('apl.branch_id', $request->branch_from);
                    }

                    elseif(!empty($request->designation_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }

                    elseif(!empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }

                    if (!empty($request->appl_code)) {
                        $query->where('apl.transfer_code', 'LIKE', "%{$request->appl_code}%");
                    }

                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {
                        $query->whereBetween('apl.transfer_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    }

                    elseif(!empty($request->start_date)){
                        $query->where('apl.transfer_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    }

                    elseif(!empty($request->end_date)){
                        $query->where('apl.transfer_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }

                    // if (Common::isSuperUser() == false && Common::isDeveloperUser() == false) {

                    //     ## for all data action
                    //     if(Common::isActionPermitedForThisUser($actionAllData = 21) == false){

                    //         // $query->where('apl.emp_id', $userInfo['emp_id']);
                    //         // $query->orWhere('apl.created_by', $userInfo['id']);
                    //         if(!empty($userInfo['emp_id'])){
                    //             $query->where('apl.emp_id', $userInfo['emp_id']);
                    //         }
                    //         else if(!empty($userInfo['id'])){
                    //             $query->where('apl.created_by', $userInfo['id']);
                    //         }

                    //         ## for all without ho == 22
                    //         if(Common::isActionPermitedForThisUser($actionAllData = 22) == true && Common::isHeadOffice() == true){
                    //             $query->orWhere('apl.branch_id', '<>', $this->hoId);
                    //         }
                    //         ## for only ho == 23
                    //         elseif(Common::isActionPermitedForThisUser($actionAllData = 23) == true && Common::isHeadOffice() == true){
                    //             $query->orWhere('apl.branch_id', $this->hoId);
                    //         }
                    //     }

                    // }

                })
                ->where(function($query2) use ($searchValue){
                    if (!empty($searchValue)) {
                        $query2->where('transfer_code', 'like', '%' . $searchValue . '%');
                    }
                })
                ->skip($start)->take($rowperpage)->select('apl.*')->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['branch_from']        = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['branch_to']          = $row->branch_to['branch_name'] . " [" . $row->branch_to['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['transfer_date']      = (new DateTime($row->transfer_date))->format('d-m-Y');
                $data[$key]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['transfer_code']        = $row->transfer_code;

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Approved</span>';
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

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);

        }
    }

    public function insert(Request $request, $status)
    {
        $permission_for = ($request['add_branch_from_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                     = new EmployeeTransfer();
                $appl->branch_id          = $request['add_branch_from_id'];
                $appl->branch_to_id          = $request['add_branch_to_id'];
                $appl->transfer_code        = HrService::generateTransferCode($request['add_branch_from_id']);
                $appl->emp_id             = $request['employee_id'];
                $appl->description        = $request['description'];
                $appl->transfer_date        = (new DateTime($request['transfer_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['exp_effective_date']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = Common::getCompanyId();

                if ($status == 'send') {

                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode'=> 400,
                            'result_data' => ''
                        ], 400);
                    }

                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(2, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 2);

                        if(isset($request->attachment) && count($request->attachment) > 0){
                            $this->uploadFiles($request->attachment);
                        }

                        DB::commit();

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }
                    $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    $appl->current_stage = null;
                }

                $appl->save();

                if(isset($request->attachment) && count($request->attachment) > 0){
                    $this->uploadFiles($request->attachment);
                }

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function uploadFiles($attachments, $fk = null){

        foreach($attachments as $key => $file){

            DB::table('hr_attachments')->insert([
                'path' => Common::fileUpload($file, 'employee_transfer', ''),
                'foreign_key' => (!empty($fk)) ? $fk : EmployeeTransfer::latest()->first()->id,
                'ref_table_name' => 'hr_app_transfers',
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);

        }
    }

    public function update(Request $request, $status)
    {

        $permission_for = ($request['add_branch_from_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl = EmployeeTransfer::find(decrypt($request['transfer_id']));

                $appl->branch_id          = $request['add_branch_from_id'];
                $appl->branch_to_id          = $request['add_branch_to_id'];
                $appl->emp_id             = $request['employee_id'];
                $appl->description        = $request['description'];
                $appl->transfer_date        = (new DateTime($request['transfer_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->is_active          = ($status == 'send') ? 3 : 0;

                if(isset($request->fileIds) && count($request->fileIds) > 0){
                    DB::table('hr_attachments')->where('foreign_key', decrypt($request['transfer_id']))->where('ref_table_name', 'hr_app_transfers')
                        ->whereNotIn('id', $request->fileIds)->delete();
                }

                if ($status == 'send') {

                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode'=> 400,
                            'result_data' => ''
                        ], 400);
                    }


                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(2, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 2);

                        if(isset($request->attachment) && count($request->attachment) > 0){
                            $this->uploadFiles($request->attachment, $appl->id);
                        }

                        DB::commit();

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }
                    $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    $appl->current_stage = null;
                }

                $appl->save();

                if(isset($request->attachment) && count($request->attachment) > 0){
                    $this->uploadFiles($request->attachment, $appl->id);
                }

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => true,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => true,
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => false,
            ], 400);
        }
    }

    public function get($id)
    {
        return CommonController::get_application('\\App\\Model\\HR\\EmployeeTransfer', $id, ['branch', 'employee', 'branch_to', 'attachments']);
    }

    public function send($id)
    {
        $apl = EmployeeTransfer::find(decrypt($id));

        $passport = $this->getPassport((object)['employee_id' => $apl->emp_id], 'send');

        if (!$passport['isValid']) {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }

        return CommonController::send_application('\\App\\Model\\HR\\EmployeeTransfer', $id, 2);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\EmployeeTransfer', $id);
    }

    public function finish($appl, $exe_date){
        DB::beginTransaction();
        try{
            //1) Employee Table [branch] Change.
            DB::table('hr_employees')->where('id', $appl->emp_id)->update(['branch_id' => $appl->branch_to_id]);
            //2) User Table [branch] Change.
            DB::table('gnl_sys_users')->where('emp_id', $appl->emp_id)->update(['branch_id' => $appl->branch_to_id]);
        }
        catch(\Exception $e){
            DB::rollback();
        }
        /* DB::table('hr_approval_queries')->insert([
            [
                'query' => "update `hr_employees` set `branch_id` = ". $appl->branch_to_id ." where `id` = ". $appl->emp_id,
                'appl_name' => 'transfer',
                'execution_date' => $exe_date,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s')
            ],
            [
                'query' => "update `gnl_sys_users` set `branch_id` = ". $appl->branch_to_id ." where `emp_id` = ". $appl->emp_id,
                'appl_name' => 'transfer',
                'execution_date' => ($exe_date == null) ? $appl->exp_effective_date : $exe_date,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s')
            ],
        ]); */

        DB::commit();
    }

}
