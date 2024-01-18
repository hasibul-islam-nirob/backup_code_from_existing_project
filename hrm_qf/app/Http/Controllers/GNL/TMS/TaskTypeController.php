<?php
namespace App\Http\Controllers\GNL\TMS;

use App\Model\GNL\TMS\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

class TaskTypeController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'type_name' => 'required',
                'task_type_code' => 'required',
            );

            $attributes = array(
                'type_name'     => 'Type Name',
                'task_type_code'     => 'Type Name',
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
        }

        // if ($errorMsg == null && $operationType == 'store') {

        //     $typeData = TaskType::find($requestData['id']);
        //     $type_name = $requestData->type_name;

        //     if ($Data->bill_no != $requestData->bill_no) {
        //         $errorMsg = "Sorry Purchase Bill number did not matched.";
        //     }
        // }

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

            $masterQuery = TaskType::where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('type_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'ASC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = TaskType::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'type_name' => $Row->type_name,
                    'task_type_code' => $Row->task_type_code,
                    'action' => Role::roleWiseArrayPopup($this->GlobalRole, encrypt($Row->id))
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

                if(TaskType::where([['is_delete', 0], ['is_active', 1]])->where('task_type_code', '=', $request['task_type_code'])->exists()){
                    return response()->json([
                        'message'    => "Duplicate code, Code Exists!!",
                        'status' => false,
                        'statusCode' => 400,
                    ], 400);
                } else {

                    $typeData                  = new TaskType();
                    $typeData->type_name       = $request['type_name'];
                    $typeData->task_type_code  = $request['task_type_code'];
                    $typeData->is_active       = ($status == 'save') ? 1 : 0;
    
                    if ($status == 'save') {
                        $typeData->is_active = 1;
                        $typeData->save();
    
                        DB::commit();
    
                        return response()->json([
                            'message'    => "Type Save Successfully!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }

                    DB::commit();
                }

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

    public function update(Request $request, $status)
    {

        // $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";
        $passport = $this->getPassport($request, 'update');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                if(
                    TaskType::where([['is_delete', 0], ['is_active', 1]])
                    ->where('task_type_code', '=', $request['task_type_code'])
                    ->where('id', '<>', decrypt($request['id']))
                    ->exists()
                ){
                    return response()->json([
                        'message'    => "Duplicate code, Code Exists!!",
                        'status' => false,
                        'statusCode' => 400,
                    ], 400);
                } else {

                    $typeData                  = TaskType::find(decrypt($request['id']));
                    $typeData->type_name       = $request['type_name'];
                    $typeData->task_type_code  = $request['task_type_code'];
                    $typeData->is_active       = ($status == 'save') ? 1 : 0;
    
                    if ($status == 'save') {
                        $typeData->is_active = 1;
                        $typeData->save();
    
                        DB::commit();
    
                        return response()->json([
                            'message'    => "Type Save Successfully!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }

                    DB::commit();

                }

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

        $TaskTypeData = TaskType::where('id', decrypt($id))->first();

        if ($TaskTypeData) {
            $responseData = [
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => $TaskTypeData
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

        $targetRow            = TaskType::where('id', decrypt($id))->first();
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
}
