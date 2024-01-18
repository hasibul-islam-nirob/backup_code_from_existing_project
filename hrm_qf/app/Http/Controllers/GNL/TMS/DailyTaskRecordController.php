<?php

namespace App\Http\Controllers\GNL\TMS;

use DateTime;
use App\Model\GNL\TMS\DailyTaskRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\TmsService as TMS;
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class DailyTaskRecordController extends controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'task_id'    => 'required',
                // 'emp_id' => 'required',
                'task_title'   => 'required',
                'task_date'    => 'required',
                'description' => 'required',
            );

            $attributes = array(
                // 'task_id'    => 'Task',
                // 'emp_id' => 'Employee',
                'task_title'   => 'Task Title',
                'task_date'    => 'Task Date',
                'description' => 'Description',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();

            // if ($requestData->status == 1 || $requestData->status == 2) {
            //     $IgnoreArray = ['delete', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            // } elseif($requestData->status == 5){
            //     $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            // }

            $errorMsg = $IgnoreArray;
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

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            // Searching Variables
            $module_id          = (empty($request->module_id) ? null : $request->module_id);
            $task_type_id       = (empty($request->task_type_id) ? null : $request->task_type_id);
            $emp_id             = (empty($request->emp_id) ? null : $request->emp_id);
            $filter_assigned_by = (empty($request->filter_assigned_by) ? null : $request->filter_assigned_by);
            $start_date         = (empty($request->start_date) ? null : (new DateTime($request->start_date))->format('Y-m-d H:i:s'));
            $to_date            = (empty($request->to_date) ? null : (new DateTime($request->to_date))->format('Y-m-d H:i:s'));

            $masterQuery = DB::table('tms_emp_daily_task as tdt')
                ->where([['tdt.is_delete', 0]])
                ->leftJoin('tms_task_types', 'tdt.task_type_id', '=', 'tms_task_types.id')
                ->where(function ($masterQuery) use ($search, $module_id, $task_type_id, $emp_id, $filter_assigned_by, $start_date, $to_date) {
                    if (!empty($search)) {
                        $masterQuery->where('tdt.task_title', 'LIKE', "%{$search}%")
                            // ->orWhere('tms_task_types.type_name', 'LIKE', "%{$search}%")
                            // ->orWhere('tms_emp_daily_task.task_code', 'LIKE', "%{$search}%")
                            ->orWhere('tdt.module_id', 'LIKE', "%{$search}%");
                    }

                    if (!empty($module_id)) {
                        $masterQuery->where('tdt.module_id', $module_id);
                    }

                    if (!empty($task_type_id)) {
                        $masterQuery->where('tdt.task_type_id', $task_type_id);
                    }

                    if (!empty($emp_id)) {
                        $masterQuery->where('tdt.emp_id', $emp_id);
                    }

                    if (!empty($filter_assigned_by)) {
                        $masterQuery->where('tdt.assigned_by', $filter_assigned_by);
                    }

                    if (!empty($start_date)) {
                        $masterQuery->where('tdt.task_date', '>=', $start_date);
                    }

                    if (!empty($to_date)) {
                        $masterQuery->where('tdt.task_date', '<=', $to_date);
                    }

                    if (!empty($start_date) && !empty($to_date)) {
                        $masterQuery->whereBetween('tdt.task_date', [$start_date, $to_date]);
                    }
                })
                ->select(
                    'tdt.id',
                    'tdt.task_title',
                    'tdt.module_id',
                    'tdt.task_date',
                    'tdt.is_active',
                    'tdt.is_delete',
                    'tdt.description',
                    'tms_task_types.type_name',
                    'tms_task_types.task_type_code',
                    'tdt.assigned_by',
                    'tdt.emp_id'
                )
                ->orderBy('tdt.task_date', 'DESC');

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = DB::table('tms_emp_daily_task')->where([['is_delete', '=', 0]])->count();
            $totalFiltered = $totalData;

            if (!empty($search) || !empty($module_id) || !empty($task_type_id) || !empty($emp_id) || !empty($filter_assigned_by) || !empty($start_date)
            || !empty($to_date)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $EmployeeID = array();
            $i = $start;

            $moduleIdArr = (!empty($masterQuery)) ? $masterQuery->pluck('module_id')->unique()->toArray() : array();

            $moduleData = DB::table('gnl_sys_modules')
                // ->where([['is_delete', 0], ['is_active', 1]])
                ->where([['is_delete', 0]])
                ->whereIn('id', $moduleIdArr)
                ->pluck('module_name', 'id')
                ->unique()
                ->toArray();

            $assignedByArr = (!empty($masterQuery)) ? $masterQuery->pluck('assigned_by')->unique()->toArray() : array();
            $assignedToArr = (!empty($masterQuery)) ? $masterQuery->pluck('emp_id')->unique()->toArray() : array();
            $employeeIdArr = array_merge($assignedByArr, $assignedToArr);

            $employeeQuery = DB::table('hr_employees')
                ->whereIn('id', $employeeIdArr)
                ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
                ->orderBy('emp_code', 'ASC')
                ->pluck('emp_name', 'id')
                ->unique()
                ->toArray();

            foreach ($masterQuery as $key => $Row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($Row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                array_push($EmployeeID, $Row->assigned_by, $Row->emp_id);

                $DataSet[] = [
                    'sl' => ++$i,
                    // 'task_code' => $Row->task_code,
                    'task_title' => $Row->task_title,
                    'task_type' => $Row->type_name,
                    'module_id' => (isset($moduleData[$Row->module_id])) ? $moduleData[$Row->module_id]: "",
                    'task_date' => Common::viewDateFormat($Row->task_date),
                    'assigned_by' => isset($employeeQuery[$Row->assigned_by]) ? $employeeQuery[$Row->assigned_by] : "-",
                    'emp_info' => isset($employeeQuery[$Row->emp_id]) ? $employeeQuery[$Row->emp_id] : "-",
                    'action' => Role::roleWiseArrayPopup($this->GlobalRole, encrypt($Row->id), $IgnoreArray),
                ];
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $DataSet,
            );

            return response()->json($json_data);
        }
    }

    public function insert(Request $request, $status)
    {
        // $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $taskData                     = new DailyTaskRecord();
                $taskData->module_id          = $request['module_id'];
                $taskData->task_type_id       = $request['task_type_id'];
                $taskData->task_title         = $request['task_title'];
                $taskData->task_date          = (new DateTime($request['task_date']))->format('Y-m-d');
                $taskData->assigned_by        = $request['assigned_by'];
                $taskData->emp_id             = $request['emp_id'];
                $taskData->description        = $request['description'];
                $taskData->attachment         = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'), 'tms_emp_daily_task', '') : null;

                // $taskData->is_active  = ($status == 'save') ? 1 : 0;
                $taskData->company_id = Common::getCompanyId();

                if ($status == 'save') {

                    $taskData->is_active = 1;
                    $taskData->save();

                    DB::commit();

                    return response()->json([
                        'message'    => "Application Sent and Approved!!",
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ], 200);
                }

                $taskData->save();

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
        // $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $taskData = DailyTaskRecord::find(decrypt($request['id']));

                $taskData->module_id        = $request['module_id'];
                $taskData->task_type_id       = $request['task_type_id'];
                // $taskData->task_code          = TMS::generateTaskTypeCode($taskData->module_id, $taskData->task_type_id);
                $taskData->task_title         = $request['task_title'];
                $taskData->task_date          = (new DateTime($request['task_date']))->format('Y-m-d');
                $taskData->assigned_by        = $request['assigned_by'];
                $taskData->emp_id             = $request['emp_id'];
                $taskData->description        = $request['description'];


                // $taskData->is_active  = ($status == 'save') ? 1 : 0;
                $taskData->company_id = Common::getCompanyId();

                if (!empty($request->file('attachment'))) {

                    $uploadFile = $request->file('attachment');
                    $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                    $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                    ## ## File Upload Function
                    $upload = Common::fileUpload($uploadFile, 'tms_emp_daily_task', $taskData->id);
                    $taskData->attachment = $upload;
                }

                if ($status == 'save') {

                    // $taskData->is_active = 0;
                    $taskData->save();

                    DB::commit();

                    return response()->json([
                        'message'    => "Task Sent and Approved!!",
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ], 200);
                }

                $taskData->save();

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

    public function get($id)
    {

        $TaskData = DailyTaskRecord::where('id', decrypt($id))->first();

        if ($TaskData) {

            $moudleId = array($TaskData->module_id);
            $moduleData = DB::table('gnl_sys_modules')
                // ->where([['is_delete', 0], ['is_active', 1]])
                ->where([['is_delete', 0]])
                ->whereIn('id', $moudleId)
                ->pluck('module_name', 'id')
                ->unique()
                ->toArray();

            $employeeId = array();
            $assigned_by = $TaskData->assigned_by;
            $emp_id = $TaskData->emp_id;
            array_push($employeeId, $assigned_by, $emp_id);

            $employeeQuery = DB::table('hr_employees')
                ->whereIn('id', $employeeId)
                ->orderBy('emp_code', 'ASC')
                ->pluck('emp_name', 'id')
                ->toArray();

            $task_type = array($TaskData->task_type_id);
            $taskTypeQuery = DB::table('tms_task_types')
                ->whereIn('id', $task_type)
                ->pluck('type_name', 'id')
                ->toArray();


            ## attachment
            $attachment = null;

            if (!empty($TaskData->attachment)) {
                if (file_exists($TaskData->attachment)) {
                    $attachment = asset($TaskData->attachment);
                }
            }


            $TaskData->attachment = $attachment;

            $responseData = [
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => $TaskData,
                'employeeInfo' => $employeeQuery,
                'taskTypeInfo' => $taskTypeQuery,
                'moduleName' => $moduleData,
            ];

            return response()->json($responseData, 200);
        } else {
            $responseData = [
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => ''
            ];

            return response()->json($responseData, 500);
        }
    }

    public function delete($id)
    {

        $targetRow            = DailyTaskRecord::where('id', decrypt($id))->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();

        if ($delete) {
            return response()->json([
                'message'    => 'Successfully deleted',
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 500,
                'status' => 'error',
                'message'    => 'Failed to delete',
                'result_data' => ''
            ], 500);
        }
    }

    // public function isApprove($task_code = null)
    // {
    //     $taskData = DailyTaskRecord::where('task_code', $task_code)->first();

    //     if ($taskData->status == 0) {
    //         $taskData->status = 1;
    //         $isSuccess = $taskData->update();

    //         if ($isSuccess) {

    //             $notification = array(

    //                 'message' => 'Successfully Approved',
    //                 'alert-type' => 'success',
    //             );
    //             return redirect()->back()->with($notification);
    //         }

    //     } else if ($taskData->status == 1) {
    //         $taskData->status = 2;
    //         $isSuccess = $taskData->update();

    //         if ($isSuccess) {

    //             $notification = array(

    //                 'message' => 'Working Done',
    //                 'alert-type' => 'success',
    //             );
    //             return redirect()->back()->with($notification);
    //         }

    //     } else if ($taskData->status == 2) {
    //         $taskData->status = 5;
    //         $isSuccess = $taskData->update();

    //         if ($isSuccess) {

    //             $notification = array(

    //                 'message' => 'Task Successfully Completed',
    //                 'alert-type' => 'success',
    //             );
    //             return redirect()->back()->with($notification);
    //         }

    //     } else if ($taskData->is_active == 0) {
    //         $taskData->is_active = 1;
    //         $isSuccess = $taskData->update();

    //         if ($isSuccess) {

    //             $notification = array(

    //                 'message' => 'Task Accepted',
    //                 'alert-type' => 'success',
    //             );
    //             return redirect()->back()->with($notification);
    //         }

    //     } else {
    //         $taskData->status = 0;
    //         $isSuccess = $taskData->update();

    //         if ($isSuccess) {

    //             $notification = array(

    //                 'message' => 'Successfully Save As Draft',
    //                 'alert-type' => 'warning',
    //             );
    //             return redirect()->back()->with($notification);
    //         }
    //     }


    // }

    public function getData(Request $request)
    {
        $data = array();

        // if ($request->context == 'genaretTaskCodeForTask') {

        //     ## Task Code generate Start
        //     $ModuleName = $request->module_name;
        //     $TaskType = $request->task_type;

        //     $TaskCode = TMS::generateTaskTypeCode($ModuleName, $TaskType);

        //     $data = array(
        //         'taskCode' => !empty($TaskCode) ? $TaskCode : null,
        //     );

        //     ## Task Code generate End
        // }

        return response()->json($data);
    }
}
