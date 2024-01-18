<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use Redirect;
use Response;
use App\Model\GNL\DForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;

use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class DFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }

    public function getPassport($req, $operationType, $wareaData = null)
    {
        $errorMsg = null;

        if ($operationType != 'delete') {

            $attributes = array(
                'name' => 'Title',
                'type_id' => 'Form Type',
                'input_type' => 'Input Type',
                'order_by' => 'Odering',
            );

            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'type_id' => 'required',
                'input_type' => 'required',
                'order_by' => 'required'
            ], [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' <br /> ', $validator->errors()->all());
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $wareaValid = array(
            'isValid' => $isValid,
            'errorMsg' => $errorMsg
        );

        return $wareaValid;
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $columns = array(
                0 => 'gdf.id',
                1 => 'gdf.uid',
                2 => 'gdf.name',
                3 => 'gdf.order_by'
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $moduleId = (empty($request->input('moduleId'))) ? null : $request->input('moduleId');
            $typeId   = (empty($request->input('typeId'))) ? null : $request->input('typeId');
            $isActive   = (empty($request->input('isActive'))) ? null : $request->input('isActive');

            $search    = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $DForms = DB::table('gnl_dynamic_form as gdf')
                ->where('gdf.is_delete', 0)
                ->join('gnl_dynamic_form_type as gdft', 'gdf.type_id', '=', 'gdft.id')
                ->join('gnl_sys_modules as gsm', 'gdf.module_id', '=', 'gsm.id')
                ->select('gdf.*', 'gdft.name as typeName', 'gsm.module_name')

                ->where(function ($query) use ($moduleId, $typeId, $isActive, $search) {
                    if (!empty($moduleId)) {
                        $query->where('gdf.module_id', $moduleId);
                    }

                    if (!empty($typeId)) {
                        $query->where('gdf.type_id', $typeId);
                    }

                    if (!empty($isActive)) {
                        if ($isActive == 1) {
                            $query->where('gdf.is_active', 1);
                        } else {
                            $query->where('gdf.is_active', '<>', 1);
                        }
                    }

                    if (!empty($search)) {
                        $query->where('gdf.name', 'LIKE', "%{$search}%");
                        $query->orWhere('gdft.name', 'LIKE', "%{$search}%");
                        $query->orWhere('gsm.module_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy($order, $dir);

            $tempQueryData = clone $DForms;
            $DForms = $DForms->offset($start)->limit($limit)->get();
            $totalData = DForm::where([['is_delete', 0]])->count();
            $totalFiltered = $totalData;

            if (!empty($search) || !empty($moduleId) || !empty($typeId) || !empty($isActive)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;

            foreach ($DForms as $key => $row) {
                $IgnoreArray = array();

                $TempSet = array();
                $TempSet = [
                    'id' => ++$i,
                    // 'row' => $row->id,
                    'uid' => $row->uid,
                    'name' => $row->name,
                    'form_type' => $row->typeName,
                    'module' => $row->module_name,
                    'input_type' => $row->input_type,
                    'order_by' => $row->order_by,
                    'note' => $row->note,
                    'status' => ($row->is_active == 1) ? "active" : "in-active",
                    'action' => Role::roleWiseArray($this->GlobalRole, $row->id, $IgnoreArray, $row->is_active)
                ];

                $DataSet[] = $TempSet;
            }

            $data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => intval($totalFiltered),
                'data' => $DataSet,
            );

            return response()->json($data);
        }
        return view('GNL.DForm.index');
    }

    public function add(Request $req)
    {

        if ($req->isMethod('post')) {
            $passport = $this->getPassport($req, $operationType = 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message' => $passport['errorMsg'],
                    'alert-type' => 'error',
                );
                return response()->json($notification);
            }

            $requestData = $req->all();
            $requestData['uid'] = $this->generateUid($requestData['module_id'], $requestData['type_id']);

            $isInsert = DForm::create($requestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to record insert.',
                    'alert-type' => 'error',
                );
            }

            return response()->json($notification);
        }
        $module = DB::table('gnl_sys_modules')->where([['is_active', 1], ['is_delete', 0]])->get();
        $typeData = DB::table('gnl_dynamic_form_type')->where([['is_delete', 0], ['is_active', 1]])->get();

        return view('GNL.DForm.add', compact('typeData', 'module'));
    }

    public function edit($id = null, Request $req)
    {

        $queryData = DForm::where('id', $id)->first();

        if ($req->isMethod('post')) {
            $passport = $this->getPassport($req, $operationType = 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message' => $passport['errorMsg'],
                    'alert-type' => 'error',
                );
                return response()->json($notification);
            }

            $requestData = $req->all();

            if ($queryData->module_id != $requestData['module_id']) {
                $requestData['uid'] = $this->generateUid($requestData['module_id'], $requestData['type_id'], $id);
            }

            $isUpdate = $queryData->update($requestData);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated',
                    'alert-type' => 'success',
                );
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to record update.',
                    'alert-type' => 'error',
                );
            }

            return response()->json($notification);
        }

        $typeData = DB::table('gnl_dynamic_form_type')->where([['is_delete', 0], ['is_active', 1]])->get();
        $module = DB::table('gnl_sys_modules')->where([['is_active', 1], ['is_delete', 0]])->get();
        return view('GNL.DForm.edit', compact('typeData', 'queryData', 'module'));
    }

    public function delete($id = null)
    {

        $queryData = DForm::where('id', $id)->first();

        $queryData->is_delete = 1;
        $delete = $queryData->save();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            // return response()->json($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            // return response()->json($notification);
        }

        return redirect()->back()->with($notification);
    }

    public function isActive($id = null)
    {
        $queryData = DForm::where('id', $id)->first();

        if ($queryData->is_active == 1) {
            $queryData->is_active = 0;
        } else {
            $queryData->is_active = 1;
        }

        $Status = $queryData->save();

        if ($Status) {
            $notification = array(
                'message' => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // public function destroy($id = null)
    // {
    //     $queryData = DForm::where('id', $id)->get()->each->delete();

    //     if ($$queryData) {
    //         $notification = array(
    //             'message' => 'Successfully Destory',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }

    public function generateUid($module_id, $type_id, $rowId = null)
    {
        $moduleData = DB::table('gnl_sys_modules')
            ->where([['is_delete', 0], ['is_active', 1], ['id', $module_id]])
            ->select('module_short_name')
            ->first();

        $moduleName = $moduleData->module_short_name;
        $prefix = $moduleName . ".";
        $prefix = strtoupper($prefix);

        $record = DB::table('gnl_dynamic_form')
            ->select(['id', 'uid'])
            ->where('type_id', $type_id)
            ->where('module_id', $module_id)
            ->where('uid', 'LIKE', "{$prefix}%")
            ->when(!empty($rowId), function ($query) use ($rowId) {
                $query->where('id', '<>', $rowId);
            })
            ->orderBy('uid', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($prefix, $record->uid);
            $newCode     = $prefix . ($OldBillNoA[1] + 1);
        } else {
            $newCode = $prefix . "1";
        }

        return $newCode;
    }
}
