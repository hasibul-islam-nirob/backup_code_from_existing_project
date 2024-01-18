<?php

namespace App\Http\Controllers\GNL\TMS;

use DateTime;
use App\Model\GNL\TMS\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\TmsService as TMS;
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class NewTaskController extends controller {

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'module_id'    => 'required',
                'task_type_id' => 'required',
                'task_title'   => 'required',
                'task_date'    => 'required',
            );

            $attributes = array(
                'module_id'    => 'Module Name',
                'task_type_id' => 'Task Type',
                'task_title'   => 'Task Title',
                'task_date'    => 'Task Date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();

            if ($requestData->status == 1 || $requestData->status == 2) {
                $IgnoreArray = ['delete', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif($requestData->status == 5){
                $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            }

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
            $module_id = (empty($request->module_id) ? null : $request->module_id);
            $task_type_id = (empty($request->task_type_id) ? null : $request->task_type_id);
            $status_id = (empty($request->status_id) ? null : $request->status_id);
            $userInfo = Auth::user();
            $roleId   = $userInfo->sys_user_role_id;
            $sysUser  = $userInfo->emp_id;

            // dd($roleId , $sysUser);
            
            $masterQuery = DB::table('tms_tasks')
                ->where([['tms_tasks.is_delete', 0]])
                ->join('tms_task_types', 'tms_tasks.task_type_id', '=', 'tms_task_types.id')
                ->where(function ($masterQuery) use($roleId, $sysUser)
                {
                    if($roleId != 4 && $roleId != 1)
                    {
                        $masterQuery->where('tms_tasks.assigned_by', $sysUser)
                            ->orWhere('tms_tasks.assigned_to', $sysUser);
                    }
                })
                ->where(function ($masterQuery) use ($search, $module_id, $task_type_id, $status_id) {
                    if (!empty($search)) {
                        $masterQuery->where('tms_task_types.type_name', 'LIKE', "%{$search}%")
                            ->orWhere('tms_tasks.task_title', 'LIKE', "%{$search}%")
                            ->orWhere('tms_tasks.task_code', 'LIKE', "%{$search}%")
                            ->orWhere('tms_tasks.module_id', 'LIKE', "%{$search}%");
                    }

                    if ($module_id) {
                        $masterQuery->where('tms_tasks.module_id', $module_id);
                    }

                    if ($task_type_id) {
                        $masterQuery->where('tms_tasks.task_type_id', $task_type_id);
                    }

                    if (!empty($status_id)) {
                        if($status_id == "-1"){
                            $masterQuery->where('tms_tasks.status', 0);
                        } else {
                            $masterQuery->where('tms_tasks.status', $status_id);
                        }
                    }
                })
                ->select('tms_tasks.id', 'tms_tasks.task_code', 'tms_tasks.task_title', 'tms_tasks.module_id', 'tms_tasks.task_date', 'tms_tasks.is_active', 'tms_tasks.is_delete',
                        'tms_tasks.description', 'tms_tasks.instruction', 'tms_task_types.type_name', 'tms_task_types.task_type_code', 'tms_tasks.assigned_by', 'tms_tasks.assigned_to', 'tms_tasks.status')
                ->orderBy('tms_tasks.id', 'DESC');

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            // $totalData = DB::table('tms_tasks')->where([['is_delete', '=', 0]])->count();
            $totalData = $tempQueryData->count();
            $totalFiltered = $totalData;

            if (!empty($search) || !empty($module_id) || !empty($task_type_id) || !empty($status_id)) {
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
            $assignedToArr = (!empty($masterQuery)) ? $masterQuery->pluck('assigned_to')->unique()->toArray() : array();
            $employeeIdArr = array_merge($assignedByArr, $assignedToArr);

            $employeeQuery = DB::table('hr_employees')
                ->whereIn('id', $employeeIdArr)
                ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
                ->orderBy('emp_code', 'ASC')
                ->pluck('emp_name', 'id')
                ->unique()
                ->toArray();

            foreach ($masterQuery as $key => $Row){

                $IgnoreArray = array();

                $passport = $this->getPassport($Row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                array_push($EmployeeID, $Row->assigned_by, $Row->assigned_to);

                $DataSet[] = [

                    'sl' => ++$i,
                    'id' => $Row->id,
                    'task_code' => $Row->task_code,
                    'task_title' => $Row->task_title,
                    'task_type' => $Row->type_name,
                    'module_id' => $moduleData[$Row->module_id],
                    'task_date' => Common::viewDateFormat($Row->task_date),
                    'assigned_by' => isset($employeeQuery[$Row->assigned_by]) ? $employeeQuery[$Row->assigned_by] : "-",
                    'assigned_to' => isset($employeeQuery[$Row->assigned_to]) ? $employeeQuery[$Row->assigned_to] : "-",
                    'description' => $Row->description,
                    'instruction' => $Row->instruction,
                    'status' => $Row->status,
                    'is_active' => $Row->is_active,
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
                $taskData                     = new Task();
                $taskData->module_id          = $request['module_id'];
                $taskData->task_type_id       = $request['task_type_id'];
                $taskData->task_code          = TMS::generateTaskTypeCode($taskData->module_id, $taskData->task_type_id);
                $taskData->task_title         = $request['task_title'];
                $taskData->task_date          = (new DateTime($request['task_date']))->format('Y-m-d');
                $taskData->assigned_by        = $request['assigned_by'];
                $taskData->assigned_to        = $request['assigned_to'];
                $taskData->task_code          = $request['task_code'];
                $taskData->description        = $request['description'];
                $taskData->instruction        = $request['instruction'];
                $taskData->status             = $request['status'];
                $taskData->attachment         = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'), 'tms_tasks', '') : null;

                $taskData->is_active  = ($status == 'save') ? 1 : 0;
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
                $taskData = Task::find(decrypt($request['id']));

                if(($taskData->module_id != $request['module_id']) || ($taskData->task_type_id != $request['task_type_id'])){
                    $taskData->module_id        = $request['module_id'];
                    $taskData->task_type_id       = $request['task_type_id'];
                    $taskData->task_code          = TMS::generateTaskTypeCode($taskData->module_id, $taskData->task_type_id);
                }

                $taskData->task_title         = $request['task_title'];
                $taskData->task_date          = (new DateTime($request['task_date']))->format('Y-m-d');
                $taskData->assigned_by        = $request['assigned_by'];
                $taskData->assigned_to        = $request['assigned_to'];
                // $taskData->task_code          = $request['task_code'];
                $taskData->description        = $request['description'];
                $taskData->instruction        = $request['instruction'];
                // $taskData->attachment         = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'), 'tms_tasks', '') : null;

                $taskData->is_active  = ($status == 'save') ? 1 : 0;
                $taskData->company_id = Common::getCompanyId();

                if (!empty($request->file('attachment'))) {

                    $uploadFile = $request->file('attachment');
                    $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                    $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                    ## ## File Upload Function
                    $upload = Common::fileUpload($uploadFile, 'tms_tasks', $taskData->id);
                    $taskData->attachment = $upload;
                }

                if ($status == 'save') {

                    $taskData->is_active = 0;
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

        $TaskData = Task::where('id', decrypt($id))->first();

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
            $assigned_to = $TaskData->assigned_to;
            array_push($employeeId, $assigned_by, $assigned_to);

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

            if(!empty($TaskData->attachment)){
                if(file_exists($TaskData->attachment)){
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

        $targetRow            = Task::where('id', decrypt($id))->first();
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

    public function isApproveBackup($task_code = null)
    {
        $taskData = Task::where('task_code', $task_code)->first();

        if ($taskData->status == 0) {
            $taskData->status = 1;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Successfully Approved',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }

        } else if ($taskData->status == 1) {
            $taskData->status = 2;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Working Done',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }

        } else if ($taskData->status == 2) {
            $taskData->status = 5;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Task Successfully Completed',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }

        } else if ($taskData->is_active == 0) {
            $taskData->is_active = 1;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Task Accepted',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }

        } else {
            $taskData->status = 0;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Successfully Save As Draft',
                    'alert-type' => 'warning',
                );
                return redirect()->back()->with($notification);
            }
        }


    }

    public function isApprove(Request $req)
    {
        $id = $req->id;
        // dd(        $task_code,'jshbghjsvajajygdya');
        $taskData = Task::where('id', $id)->first();

        if ($taskData->status == 0) {
            $taskData->status = 1;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Successfully Approved',
                    'alert-type' => 'success',
                );
                return response()->json($notification);
            }

        } else if ($taskData->status == 1) {
            $taskData->status = 2;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Working Done',
                    'alert-type' => 'success',
                );
                return response()->json($notification);
            }

        } else if ($taskData->status == 2) {
            $taskData->status = 5;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Task Successfully Completed',
                    'alert-type' => 'success',
                );
                return response()->json($notification);
            }

        } else if ($taskData->is_active == 0) {
            $taskData->is_active = 1;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Task Accepted',
                    'alert-type' => 'success',
                );
                return response()->json($notification);
            }

        } else {
            $taskData->status = 0;
            $isSuccess = $taskData->update();

            if ($isSuccess) {

                $notification = array(

                    'message' => 'Successfully Save As Draft',
                    'alert-type' => 'warning',
                );
                return response()->json($notification);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = array();

        if ($request->context == 'genaretTaskCodeForTask') {

            ## Task Code generate Start
            $ModuleName = $request->module_name;
            $TaskType = $request->task_type;

            $TaskCode = TMS::generateTaskTypeCode($ModuleName, $TaskType);

            $data = array(
                'taskCode' => !empty($TaskCode) ? $TaskCode : null,
            );

            ## Task Code generate End
        }

        return response()->json($data);
    }

}
