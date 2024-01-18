<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\HR\ConfigRelation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

class ConfigRelationController extends Controller
{
    
    public function getPassport($request, $operationType, $data = null){
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'name'                          => 'required'
                
            );

            $attributes = array(
                'name'                          => 'Relation Name'
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }


        if ($errorMsg == null &&  ($operationType == 'store' || $operationType == 'update')) {
            
            $duplicateQuery = ConfigRelation::where([['name', $request->name], ['is_active', 1], ['is_delete', 0]])->count();
            if ($duplicateQuery > 0) {
                $errorMsg = "Relation name already exist.";
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

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = DB::table('hr_relationships')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_relationships')->where([['is_active', 1], ['is_delete', 0]])->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            $sl = (int)$request->start + 1;
            $data      = array();

            foreach ($masterQuery as $key => $row) {

                $IgnoreArray = ['view'];

                $passport = $this->getPassport(null, $operationType = 'index', $row);
                if ($passport['isValid'] == false) {
                    $IgnoreArray = ['delete'];
                }

                $data[$key]['id']                          = $sl++;
                $data[$key]['name']                        = $row->name;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);
            }

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                'data'              => $data,
            );

            return response()->json($json_data);
        }
    }

    public function insert(Request $request)
    {
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

                $requestData = $request->all();
                $isInsert = ConfigRelation::create($requestData);

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


    public function update(Request $request)
    {
        if ($request->isMethod('post')) {

            $updateData = ConfigRelation::where('id', decrypt($request->edit_id))->first();
            $passport = $this->getPassport($request, 'update', $updateData);

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['message'],
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400
                );
                return response()->json($notification, $notification['statusCode']);
            }

            try {

                $requestData = $request->all();
                $isUpdate = $updateData->update($requestData);

                if ($isUpdate) {
                    $notification = array(
                        'message' => 'Successfully updated Data',
                        'alert-type' => 'success',
                        'status' => 'success',
                        'statusCode' => 200,
                    );
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to updated data',
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


    public function get(Request $request)
    {
        $relationData = DB::table('hr_relationships')->where('id', decrypt($request->id))->where('is_delete', 0)->first();

        return response()->json($relationData);
    }

    public function delete($id){
        
        $delete = DB::table('hr_relationships')->where('id', decrypt($id))->update(['is_delete' => 1]);

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
