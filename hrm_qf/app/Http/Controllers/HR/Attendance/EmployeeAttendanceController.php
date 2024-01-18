<?php

namespace App\Http\Controllers\HR\Attendance;

use DateTime;
use App\Model\HR\Employee;
use Illuminate\Http\Request;
// use Maatwebsite\Excel\Excel;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\HR\EmployeeAttendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\RoleService as Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Imports\EmployeeAttendanceImport;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class EmployeeAttendanceController extends Controller
{

    public function getPassport($requestData, $operationType)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'emp_id'   => 'required',
                'time_and_date' => 'required',
            );

            $attributes = array(
                'emp_id'   => 'Employee',
                'time_and_date' => 'Time and date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        } elseif ($operationType == 'storeByFile') {
            if ($requestData->hasFile('attendance_file')) {
                $extension = $requestData->file('attendance_file')->getClientOriginalExtension();
                if ($extension == "xlsx" || $extension == 'xls' || $extension == 'csv' || $extension == 'xml' || $extension == 'txt') {
                    $passport = array(
                        'isValid' => true,
                        'message' => "",
                    );

                    return $passport;
                } else {
                    $passport = array(
                        'isValid' => false,
                        'message' => "Sorry!! Only xlsx, xls, csv, xml and text format is supportet.",
                    );

                    return $passport;
                }
            } else {
                $passport = array(
                    'isValid' => false,
                    'message' => "Please select a file",
                );

                return $passport;
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length"); // Rows display per page

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            $searchValue = $search_arr['value']; // Search value

            // dd($columnIndex_arr);
            ## =========Search Start==================

            $zoneId                 = (empty($request->input('zone_id'))) ? null : $request->input('zone_id');
            $regionId                 = (empty($request->input('region_id'))) ? null : $request->input('region_id');
            $areaId                 = (empty($request->input('area_id'))) ? null : $request->input('area_id');
            $branchId               = (empty($request->input('branch_id'))) ? null : $request->input('branch_id');

            $departmentId     = (empty($request->department_id)) ? null : $request->department_id;
            $designationId    = (empty($request->designation_id)) ? null : $request->designation_id;

            $attStartDate     = (empty($request->start_date)) ? null : $request->start_date;
            $attEndDate       = (empty($request->end_date)) ? null : $request->end_date;

            $employeeId       = (empty($request->employee_id)) ? null : $request->employee_id;


            $accessAbleBranchIds = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            // dd($userInfo, $loginUserDeptId, $statusArray, $this->GlobalRole);

            $masterQuery = DB::table('hr_attendance as ha')
                ->where([['ha.is_delete', 0], ['ha.is_active', 1]])
                ->join('hr_employees as he', 'he.id', 'ha.emp_id')
                ->join('gnl_branchs as gb', 'gb.id', 'he.branch_id')
                ->join('hr_departments as dept', 'dept.id', 'he.department_id')
                ->join('hr_designations as des', 'des.id', 'he.designation_id')
                ->whereIn('he.branch_id', $accessAbleBranchIds)

                /* ======== Old Code For Permission =========
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId){
                    if(Common::isSuperUser() == true || Common::isDeveloperUser() == true){
                        ## nothing to do
                    }
                    else {

                        if(in_array(101, $statusArray)){
                            ## All Data for Permitted Branches
                            ## nothing to do
                            // $query->whereIn('ha.branch_id', $selBranchArr)
                        }
                        elseif(in_array(102, $statusArray)){
                            ## All Branch Data Without HO
                            $perQuery->where('ha.branch_id' , '<>' ,1);
                        }
                        elseif(in_array(103, $statusArray)){
                            ## All Data Only HO
                            $perQuery->where('ha.branch_id', 1);
                        }
                        elseif(in_array(104, $statusArray)){
                            ## All data for own department of permitted branches
                            // $perQuery->whereIn('ha.branch_id', $tmpBranch);
                            $perQuery->where('ha.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(105, $statusArray)){
                            ## All data for own department of all branches without HO
                            $perQuery->where('ha.branch_id', '<>' , 1);
                            $perQuery->where('ha.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(106, $statusArray)){
                            ## All data for own department only HO
                            $perQuery->where([['ha.branch_id', 1],['ha.department_id', $loginUserDeptId]]);
                        }
                        else{
                            $perQuery->where('ha.created_by', $userInfo['id']);
                            // $perQuery->orWhere('ha.id', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('ha.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })
                */
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $accessAbleBranchIds){

                    if(Common::isSuperUser() == true || Common::isDeveloperUser() == true ){
                        ## nothing to do
                        // dd(Common::isSuperUser() == true);
                    }
                    else {
            
                        // dd($statusArray);
                        if(in_array(100, $statusArray)){
                            ## All Data for Permitted Branches
                            $perQuery->whereIn('branch_id', $accessAbleBranchIds);
            
                        }elseif(in_array(100.1, $statusArray)){
                            ## 	Own Data
                            $perQuery->where('emp_id', $userInfo['emp_id']);
                        
                        }elseif(in_array(104.1, $statusArray)){
                            ## Own department for All branch with HO
                            $perQuery->where('department_id', $loginUserDeptId);
                        
                        }elseif(in_array(104.2, $statusArray)){
                            ## 	All department for All branch without HO
                            $perQuery->where('branch_id', '<>' , 1);
                        
                        }elseif(in_array(104.3, $statusArray)){
                            ## 	Own department for All branch without HO
                            $perQuery->where('branch_id', '<>' , 1);
                            $perQuery->where('department_id', $loginUserDeptId);
                        
                        }elseif(in_array(104.4, $statusArray)){
                            ## 	All department Only HO
                            $perQuery->where('branch_id', 1);
                        
                        }elseif(in_array(104.5, $statusArray)){
                            ## 	Own department Only HO
                            $perQuery->where('branch_id', 1);
                            $perQuery->where('department_id', $loginUserDeptId);
                        
                        }elseif(in_array(104.6, $statusArray)){
                            ## 	All department for permitted branch
                            $perQuery->whereIn('branch_id', $accessAbleBranchIds);
                        
                        }elseif(in_array(104.7, $statusArray)){
                            ## 	Own department for permitted branch
                            $perQuery->whereIn('branch_id', $accessAbleBranchIds);
                            $perQuery->where('department_id', $loginUserDeptId);
                        
                        }
                    }

                    ## Calling Permission Query Function
                    // HRS::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $accessAbleBranchIds);
                })

                ->where(function ($query) use ($searchValue) {
                    if (!empty($searchValue)) {
                        // $query->where('ha.emp_id', 'like', "%{$searchValue}%");
                        $query->where('ha.time_and_date', 'like', "%{$searchValue}%");
                        $query->orWhere('he.emp_name', 'like', "%{$searchValue}%");
                        $query->orWhere('he.emp_code', 'like', "%{$searchValue}%");
                        $query->orWhere('gb.branch_name', 'like', "%{$searchValue}%");
                        $query->orWhere('gb.branch_code', 'like', "%{$searchValue}%");
                        $query->orWhere('dept.dept_name', 'like', "%{$searchValue}%");
                        $query->orWhere('des.name', 'like', "%{$searchValue}%");
                    }
                })
                ->where(function ($query) use ($attStartDate, $attEndDate,$employeeId, $departmentId, $designationId) {

                    if (!empty($employeeId) ) {
                        $query->where('ha.emp_id', $employeeId);
                    }

                    if (!empty($departmentId) ) {
                        $query->where('he.department_id', $departmentId);
                    }

                    if (!empty($designationId) ) {
                        $query->where('he.designation_id', $designationId);
                    }

                    if (!empty($attStartDate) && !empty($attEndDate)) {
                        $query->where('ha.time_and_date', '>=', date('Y-m-d 00:00:00', strtotime($attStartDate)));
                        $query->where('ha.time_and_date', '<=', date('Y-m-d 23:59:59', strtotime($attEndDate)));
                    } elseif (!empty($attStartDate) && empty($attEndDate)) {
                        $query->where('ha.time_and_date', '>=', date('Y-m-d 00:00:00', strtotime($attStartDate)));
                    } elseif (!empty($attEndDate) && empty($attStartDate)) {
                        $query->where('ha.time_and_date', '<=', date('Y-m-d 23:59:59', strtotime($attEndDate)));
                    }
                })
                ->select(
                    'he.emp_name AS name',
                    'he.emp_code AS em_code',
                    'gb.branch_name AS branchName',
                    'gb.branch_code AS branchCode',
                    'dept.dept_name AS departmentName',
                    'des.name AS designationsName',
                    'ha.*'
                )
                ->orderBy('ha.time_and_date', 'DESC')
                ->orderBy($columnName, $columnSortOrder);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->skip($start)->take($rowperpage)->get();

            // $totalRecords = DB::table('hr_attendance')->where('is_delete', 0)->count();
            $totalRecords = $tempQueryData->count();
            // $totalRecordswithFilter = count($masterQuery);
            $totalRecordswithFilter = $totalRecords;

            if (
                !empty($searchValue)
                || !empty($request->branch_id)
                || !empty($request->department_id)
                || !empty($request->designation_id)
                || !empty($request->start_date)
                || !empty($request->end_date)
                || !empty($request->employee_id)
            ) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            $data      = array();
            $current_date = date('Y-m-d', strtotime(Common::systemCurrentDate()));
            $sno = $start + 1;
            foreach ($masterQuery as $key => $row) {

                ## Find Created By Person
                $createByInfoData = DB::table('gnl_sys_users')->where('id', $row->created_by)->first();
                $createByInfo = '';
                $emp_id = optional($createByInfoData)->emp_id;
                if ($emp_id == null) {
                    $createByInfo = optional($createByInfoData)->full_name;
                }else{
                    $createdByEmpInfo = DB::table('hr_employees')->where('id', $emp_id)->first();
                    // dd($createdByEmpInfo);
                    $empName = optional($createdByEmpInfo)->emp_name;
                    $empCode = optional($createdByEmpInfo)->emp_code;

                    $createByInfo = $empName.' ['.$empCode.']';
                }
                ## Find Created By Person



                $IgnoreArray = [];

                if (!empty($row->time_and_date < $current_date) || $row->isFileUpload == 1) {
                    $IgnoreArray = [];
                }

                $data[$key]['id']            = $sno;
                $data[$key]['employee'] = $row->name . ' [' . $row->em_code . ']';
                $data[$key]['branch_id'] = $row->branchName . ' [' . $row->branchCode . ']';
                $data[$key]['department_id'] = $row->departmentName;
                $data[$key]['designation_id'] = $row->designationsName;

                $markingColor = '';
                if(date("d-m-Y H:i", strtotime($row->time_and_date)) != date("d-m-Y H:i", strtotime($row->created_at))){
                    $data[$key]['time_and_date'] = '<span style="color:red;">'.date("d/m/Y H:i a", strtotime($row->time_and_date)).'</span>';
                    $data[$key]['created_at'] = '<span style="color:red;">'.date("d/m/Y H:i a", strtotime($row->created_at)).'</span>';
                }else{
                    $data[$key]['time_and_date'] = '<span>'.date("d/m/Y H:i a", strtotime($row->time_and_date)).'</span>';
                    $data[$key]['created_at'] = '<span>'.date("d/m/Y H:i a", strtotime($row->created_at)).'</span>';
                }

                // $data[$key]['time_and_date'] = '<span style="color:red;">'.date("d-m-Y H:i a", strtotime($row->time_and_date)).'</span>';
                // $data[$key]['created_at'] = '<span style="color:red;">'.date("d-m-Y H:i a", strtotime($row->created_at)).'</span>';

                $data[$key]['created_by'] = $createByInfo;


                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;
            }

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
                "all" => $masterQuery
            );
            return response()->json($json_data);
        }
    }

    public function insert(Request $request)
    {

        $haveAttRules = DB::table('hr_attendance_rules')->where([['is_delete', 0], ['is_active', 1]])->count();
        if ($haveAttRules < 1) {
            return response()->json(400);
        }

        if ($request->isMethod('post')) {

            $passport = $this->getPassport($request, $operationType = 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['message'],
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400,
                );
                return response()->json($notification, $notification['statusCode']);
            }

            try {

                $RequestData = $request->all();

                if (!empty($RequestData['time_and_date'])) {
                    $eDate = new DateTime($RequestData['time_and_date']);
                    $eDate = $eDate->format('Y-m-d');
                    $RequestData['time_and_date'] = $eDate . ' ' . $RequestData['ext_start_time'];
                } else {
                    $RequestData['time_and_date'] = null;
                }

                $checkDateTime = $RequestData['time_and_date'];
                $ifExist = EmployeeAttendance::where([['is_delete', 0], ['is_active', 1], ['emp_id', $RequestData['emp_id']], ['time_and_date', $checkDateTime]])->count();

                if ($ifExist > 0) {
                    return response()->json([
                        'message'     => 'Data Already Exist',
                        'status'      => 'worning',
                        'statusCode'  => 400,
                        'result_data' => '',
                    ], 400);
                }

                // ss($ifExist, $checkDateTime, $RequestData['time_and_date'], $RequestData);

                $isInsert = EmployeeAttendance::create($RequestData);

                if ($isInsert) {
                    $notification = array(
                        'message' => 'Successfully Inserted Data',
                        'alert-type' => 'success',
                        'status' => 'success',
                        'statusCode' => 200,
                    );
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to insert data',
                        'alert-type' => 'error',
                        'status' => 'error',
                        'statusCode' => 400,
                    );
                }
            } catch (\Exception $e) {

                $notification = array(
                    'message' => 'Internal Server Error. Try Again!!',
                    'alert-type' => 'error',
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                    'statusCode' => 500,
                );
            }

            return response()->json($notification, $notification['statusCode']);
        }
    }

    public function insert_by_file(Request $request)
    {

        $haveAttRules = DB::table('hr_attendance_rules')->where([['is_delete', 0], ['is_active', 1]])->count();
        if ($haveAttRules < 1) {
            return response()->json(400);
        }

        $passport = $this->getPassport($request, 'storeByFile');

        if ($passport['isValid']) {
            //DB::beginTransaction();
            try {

                $extension = $request->file('attendance_file')->getClientOriginalExtension();

                $branchId       = !empty($request->branch_id) ? $request->branch_id : null;
                $departmentId   = !empty($request->department_id) ? $request->department_id : null;
                $designationId  = !empty($request->designation_id) ? $request->designation_id : null;

                $employeeData = DB::table('hr_employees')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 1]])
                    ->where(function ($query) use ($branchId, $departmentId, $designationId) {

                        if (!empty($branchId)) {
                            $query->where('branch_id', $branchId);
                        }

                        if (!empty($departmentId)) {
                            $query->where('department_id', $departmentId);
                        }

                        if (!empty($designationId)) {
                            $query->where('designation_id', $designationId);
                        }
                    })
                    ->get(['id', 'emp_code', 'user_id', 'branch_id', 'department_id', 'designation_id'])
                    ->keyBy('emp_code')
                    ->toArray();

                // ============================================================

                if ($extension == 'csv' || $extension == 'xlsx' || $extension == 'xls') {

                    // https://sweetcode.io/import-and-export-excel-files-data-using-in-laravel/

                    try {

                        // ## old code.
                        // Excel::import(new EmployeeAttendanceImport, request()->file('attendance_file'));

                        $importFileData = Excel::toArray(new EmployeeAttendanceImport, request()->file('attendance_file'));
                        $importFileData = isset($importFileData[0]) ? $importFileData[0] : array();


                        $companyId = Common::getCompanyId();
                        foreach ($importFileData as $fileData) {

                            // ss( isset(($fileData['date'])) , isset(($fileData['time'])) , '', !empty($fileData['datetime']) , isset(($fileData['datetime'])) );
                            $requestData = array();

                            $requestData['company_id']      = $companyId;
                            $requestData['isFileUpload']      = 1;


                            if (!isset($fileData['id_number']) && !isset($fileData['employee_code'])) {
                                continue;
                            }

                            if (!isset($fileData['datetime']) && !isset($fileData['date']) && !isset($fileData['time'])){
                                continue;
                            }

                            if (!isset($employeeData[$fileData['id_number']]) && !isset($employeeData[$fileData['employee_code']])) {
                                continue;
                            }

                            $empployee_id = "";

                            if (isset($fileData['id_number']) && !empty($fileData['id_number'])) {
                                $empployee_id = $employeeData[$fileData['id_number']]->id;
                            }
                            elseif(isset($fileData['employee_code']) && !empty($fileData['employee_code'])){
                                $empployee_id = $employeeData[$fileData['employee_code']]->id;
                            }
                            else {
                                continue;
                            }

                            $entryTime = "";
                            if (isset($fileData['datetime']) && !empty($fileData['datetime'])) {

                                if (gettype($fileData['datetime']) == 'string') {
                                    $entryTime = (new DateTime($fileData['datetime']))->format('Y-m-d H:i:s');
                                } else {
                                    $entryTime = HRS::AttendanceDateCreate($fileData['datetime']);
                                }
                            } elseif (isset(($fileData['date'])) && isset(($fileData['time']))) {

                                if (!empty($fileData['date']) && !empty($fileData['time'])) {
                                    $tmpDate = (new DateTime(HRS::AttendanceDateCreate($fileData['date'])))->format('Y-m-d');
                                    $tmpTime = (new DateTime(HRS::AttendanceDateCreate($fileData['time'])))->format('H:i:s');

                                    $entryTime = $tmpDate . ' ' . $tmpTime;
                                } else {
                                    continue;
                                }
                            } else {
                                continue;
                            }

                            ## date & time entry
                            $requestData['emp_id'] = $empployee_id;
                            $requestData['time_and_date'] = $entryTime;

                            $countExist = EmployeeAttendance::where('emp_id', $requestData['emp_id'])
                                ->where([['is_delete', 0], ['is_active', 1]])
                                ->where('time_and_date', $requestData['time_and_date'])
                                ->count();

                            if ($countExist > 0) {
                                continue;
                            }

                            $isCreate = EmployeeAttendance::create($requestData);
                        }


                        // ## ====== Delete Duplicate Data Start ==========
                        // $rows = EmployeeAttendance::groupBy('emp_id','time_and_date')
                        //     ->selectRaw('id, emp_id, time_and_date, count(*) as count')
                        //     ->havingRaw('COUNT(*) > 1')
                        //     ->get()
                        //     ->each->delete();

                        // while(count($rows->toArray()) > 0){
                        //     $rows = EmployeeAttendance::groupBy('emp_id','time_and_date')
                        //         ->selectRaw('id, emp_id, time_and_date, count(*) as count')
                        //         ->havingRaw('COUNT(*) > 1')
                        //         ->get()
                        //         ->each->delete();
                        // }

                        // // ## Table ID Rearrange Start
                        // DB::statement("SET @count = 0;");
                        // DB::statement("UPDATE hr_attendance SET hr_attendance.id = @count:= @count + 1;");
                        // DB::statement("ALTER TABLE hr_attendance AUTO_INCREMENT = 1;");
                        // // ## Table ID Rearrange End

                        // ## ====== Delete Duplicate Data End ==========

                    } catch (\Exception $e) {
                        // ss($e);
                        return response()->json([
                            'message'     => $e->getMessage(),
                            'status'      => 'error',
                            'statusCode'  => 400,
                            'result_data' => '',
                        ], 400);
                    }
                } elseif ($extension == 'xml') {
                    $xmlString = file_get_contents(request()->file('attendance_file'));
                    $xmlObject = simplexml_load_string($xmlString);

                    $json     = json_encode($xmlObject);
                    $phpArray = json_decode($json, true);

                    // ss($phpArray);

                    foreach ($phpArray['records']['record'] as $row) {
                        EmployeeAttendance::create(
                            [
                                'emp_code'   => (empty($row['AC-No_'])) ? "" : $row['AC-No_'],
                                'name'       => (empty($row['Name'])) ? "" : $row['Name'],
                                'schedule'   => (empty($row['Schedule'])) ? "" : $row['Schedule'],
                                'date'       => (empty($row['Date'])) ? "" : (new DateTime($row['Date']))->format('Y-m-d'),
                                'timetable'  => (empty($row['Timetable'])) ? "" : $row['Timetable'],
                                'on_duty'    => (empty($row['On_duty'])) ? "" : $row['On_duty'],
                                'off_duty'   => (empty($row['Off_duty'])) ? "" : $row['Off_duty'],
                                'clock_in'   => (empty($row['Clock_In'])) ? "" : $row['Clock_In'],
                                'clock_out'  => (empty($row['Clock_Out'])) ? "" : $row['Clock_Out'],
                                'late'       => (empty($row['Late'])) ? "" : $row['Late'],
                                'early'      => (empty($row['Early'])) ? "" : $row['Early'],
                                'absent'     => (empty($row['Absent'])) ? "" : $row['Absent'],
                                'ot_time'    => (empty($row['OT_Time'])) ? "" : $row['OT_Time'],
                                'work_time'  => (empty($row['Work_Time'])) ? "" : $row['Work_Time'],
                                'department' => (empty($row['Department'])) ? "" : $row['Department'],
                            ]
                        );
                    }
                } elseif ($extension == 'txt') {
                    $file = fopen(request()->file('attendance_file'), "r");

                    while (!feof($file)) {
                        dd(fgets($file));
                    }

                    fclose($file);
                }

                //DB::commit();
                return response()->json([
                    'message'     => $passport['message'],
                    'status'      => 'success',
                    'statusCode'  => 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'     => "Internal Server Error. Try Again!!",
                    'status'      => 'error',
                    'statusCode'  => 500,
                    'result_data' => '',
                    'error'       => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'     => $passport['message'],
                'status'      => 'error',
                'statusCode'  => 400,
                'result_data' => '',
            ], 400);
        }
    }

    public function backupp_code(Request $request)
    {
        $tempFile = $_FILES['attendance_file']['tmp_name'];
        $xlData   = file($tempFile);

        $xlData = array_map(function ($raw) {
            return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw);
        }, $xlData);

        $xlData = array_values(array_filter($xlData));

        // dd($xlData);

        $end    = count($xlData) - 1;
        $first  = 8;
        $number = range($first, $end);

        $compactData = array();
        // $compactData = array_map(function($n) use ($first, $xlData) {

        //     $lineData = explode('@', $xlData[$n]);
        //     if($n == $first){
        //         unset($lineData[0]);
        //     }

        //     return $lineData;
        // }, $number);

        // dd($compactData);

        foreach ($number as $n) {
            $lineData = explode('@', $xlData[$n]);
            if ($n == $first) {
                unset($lineData[0]);
            }
            $compactData = array_merge($compactData, $lineData);
        }
        $compactDataNew = array_chunk($compactData, 16);

        dd($compactDataNew);
        // ----------------------------------------------------
    }

    public function update(Request $request)
    {
        $haveAttRules = DB::table('hr_attendance_rules')->where([['is_delete', 0], ['is_active', 1]])->count();
        if ($haveAttRules < 1) {
            return response()->json(400);
        }

        if ($request->isMethod('post')) {

            // $updateData = EmployeeAttendance::find(decrypt($request->edit_id));
            $updateData = EmployeeAttendance::where('id', decrypt($request->edit_id))->first();

            if ($updateData->isFileUpload == 1) {
                $notification = array(
                    'message'    => "Can not update this data. Its automated.",
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400,
                );
                return response()->json($notification, $notification['statusCode']);
            }

            $passport = $this->getPassport($request, 'update', $updateData);

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['message'],
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400,
                );
                return response()->json($notification, $notification['statusCode']);
            }

            try {

                $RequestData = $request->all();

                if (!empty($RequestData['time_and_date'])) {
                    $eDate = new DateTime($RequestData['time_and_date']);
                    $eDate = $eDate->format('Y-m-d');
                    $RequestData['time_and_date'] = $eDate . ' ' . $RequestData['ext_start_time'];
                } else {
                    $RequestData['time_and_date'] = null;
                }

                $checkDateTime = $RequestData['time_and_date'] . ':00';
                $ifExist = EmployeeAttendance::where([['is_delete', 0], ['is_active', 1], ['emp_id', $RequestData['emp_id']], ['time_and_date', $checkDateTime], ['id', '<>', $updateData->id]])->count();

                if ($ifExist > 0) {
                    return response()->json([
                        'message'     => 'Data Already Exist',
                        'status'      => 'worning',
                        'statusCode'  => 400,
                        'result_data' => '',
                    ], 400);
                }

                $isInsert = $updateData->update($RequestData);

                if ($isInsert) {
                    $notification = array(
                        'message' => 'Successfully Inserted Data',
                        'alert-type' => 'success',
                        'status' => 'success',
                        'statusCode' => 200,
                    );
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to insert data',
                        'alert-type' => 'error',
                        'status' => 'error',
                        'statusCode' => 400,
                    );
                }
            } catch (\Exception $e) {

                $notification = array(
                    'message' => 'Internal Server Error. Try Again!!',
                    'alert-type' => 'error',
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                    'statusCode' => 500,
                );
            }

            return response()->json($notification, $notification['statusCode']);
        }
    }

    public function get($id)
    {
        $id = decrypt($id);

        $queryData = EmployeeAttendance::with('employee')->find($id);

        if ($queryData) {
            $responseData = [
                'status'      => 'success',
                'statusCode'  => 200,
                'result_data' => $queryData,
            ];
            return response()->json($responseData, 200);
        } else {
            $responseData = [
                'status'      => 'error',
                'statusCode'  => 500,
                'result_data' => '',
            ];
            return response()->json($responseData, 500);
        }
    }

    public function delete($id)
    {
        $targetRow            = EmployeeAttendance::where('id', decrypt($id))->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();

        if ($delete) {
            return response()->json([
                'message'     => 'Successfully deleted',
                'status'      => 'success',
                'statusCode'  => 200,
                'result_data' => '',
            ], 200);
        } else {
            return response()->json([
                'statusCode'  => 500,
                'status'      => 'error',
                'message'     => 'Failed to delete',
                'result_data' => '',
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = array();

        if ($request->context == 'employeeData') {

            $requestData = $request->all();

            $data = HRS::getOptionsForEmployee($requestData);
        }

        return response()->json($data);
    }

    public function getEmployeeInfo(Request $request)
    {

        $queryData = DB::table('hr_employees')->where('is_delete', 0)->where('id', $request->id)->first();
        // $queryData = EmployeeAttendance::with('employee')->find($request->id);
        return response()->json($queryData);
    }

    public function exampleFileDownload()
    {
        return response()->download(public_path('example_files/example_attendance.xlsx'));
    }

    public function getDesignationData(Request $request)
    {
        $data = array();

        if ($request->context == 'getDesignationData') {

            $branchId = $request->branchId;

            $allData = DB::table('hr_employees')
                ->where([['is_active', 1], ['is_delete', 0], ['branch_id', $branchId]])
                ->selectRaw('designation_id')->get()->toArray();

            $allDesigId = array_column($allData, 'designation_id');

            $designationData = DB::table('hr_designations')
                ->where([['is_active', 1], ['is_delete', 0]])->whereIn('id', $allDesigId)->select('id', 'name')->get();
        }

        return response()->json($designationData);
    }
}
