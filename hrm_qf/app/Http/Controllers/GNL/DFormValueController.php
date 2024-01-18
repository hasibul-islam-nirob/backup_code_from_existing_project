<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Model\GNL\DFormValue;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;

use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class DFormValueController extends Controller
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
                'form_id' => 'Form',
                'order_by' => 'Odering',
            );

            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'type_id' => 'required',
                'form_id' => 'required',
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
                0 => 'gdfv.id',
                1 => 'gdfv.uid',
                2 => 'gdfv.name',
                4 => 'gdfv.order_by'
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $typeId   = (empty($request->input('typeId'))) ? null : $request->input('typeId');
            $formUid = (empty($request->input('formUid'))) ? null : $request->input('formUid');
            $isActive   = (empty($request->input('isActive'))) ? null : $request->input('isActive');

            $search    = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $DFormValues = DB::table('gnl_dynamic_form_value as gdfv')
                ->where('gdfv.is_delete', 0)
                ->join('gnl_dynamic_form_type as gdft', 'gdfv.type_id', '=', 'gdft.id')
                ->join('gnl_dynamic_form as gdf', function ($query) {
                    $query->on([['gdf.type_id', 'gdfv.type_id'], ['gdf.uid', 'gdfv.form_id']]);
                })
                ->select('gdfv.*', 'gdft.name as typeName', 'gdf.name as formName')
                ->where(function ($query) use ($typeId, $formUid, $isActive, $search) {

                    if (!empty($typeId)) {
                        $query->where('gdfv.type_id', $typeId);
                    }

                    if (!empty($formUid)) {
                        $query->where('gdfv.form_id', $formUid);
                    }

                    if (!empty($isActive)) {
                        if ($isActive == 1) {
                            $query->where('gdfv.is_active', 1);
                        } else {
                            $query->where('gdfv.is_active', '<>', 1);
                        }
                    }

                    if (!empty($search)) {
                        $query->where('gdfv.name', 'LIKE', "%{$search}%");
                        $query->orWhere('gdfv.value_field', 'LIKE', "%{$search}%");
                        $query->orWhere('gdf.name', 'LIKE', "%{$search}%");
                        $query->orWhere('gdft.name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy($order, $dir);

            // dd($DFormValues->toArray());

            $tempQueryData = clone $DFormValues;
            $DFormValues = $DFormValues->offset($start)->limit($limit)->get();
            $totalData = DFormValue::where('is_delete', 0)->count();
            $totalFiltered = $totalData;

            if (!empty($search) || !empty($typeId) || !empty($formUid) || !empty($isActive)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;

            foreach ($DFormValues as $key => $row) {
                $IgnoreArray = array();

                $TempSet = array();
                $TempSet = [
                    'id' => ++$i,
                    'uid' => $row->uid,
                    'name' => $row->name,
                    'value_field' => $row->value_field,
                    'form_type' => $row->typeName,
                    'form_name' => $row->formName,
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
        return view('GNL.DFormValue.index');
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

            $RequestData = $req->all();
            $RequestData['uid'] = $this->generateUid($RequestData['type_id'], $RequestData['form_id']);
            $isInsert = DFormValue::create($RequestData);

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

        $typeData = DB::table('gnl_dynamic_form_type')->where([['is_delete', 0], ['is_active', 1]])->get();
        $formData = DB::table('gnl_dynamic_form')->where([['is_delete', 0], ['is_active', 1]])->get();

        return view('GNL.DFormValue.add', compact('typeData', 'formData'));
    }

    public function edit($id = null, Request $req)
    {

        $queryData = DFormValue::where('id', $id)->first();;

        if ($req->isMethod('post')) {
            $passport = $this->getPassport($req, $operationType = 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message' => $passport['errorMsg'],
                    'alert-type' => 'error',
                );
                return response()->json($notification);
            }

            $RequestData = $req->all();

            if (($queryData->form_id != $RequestData['form_id']) || ($queryData->type_id != $RequestData['type_id'])) {
                $RequestData['uid'] = $this->generateUid($RequestData['type_id'], $RequestData['form_id'], $id);
            }

            $isUpdate = $queryData->update($RequestData);

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
        $formData = DB::table('gnl_dynamic_form')->where([['is_delete', 0], ['is_active', 1]])->get();

        return view('GNL.DFormValue.edit', compact('typeData', 'formData', 'queryData'));
    }

    public function delete($id = null)
    {

        $queryData = DFormValue::where('id', $id)->first();

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
        $queryData = DFormValue::where('id', $id)->first();

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
    //     $queryData = DFormValue::where('id', $id)->get()->each->delete();

    //     if ($$queryData) {
    //         $notification = array(
    //             'message' => 'Successfully Destory',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }

    public function generateUid($type_id, $form_id, $rowId = null)
    {

        $maxUid = DB::table('gnl_dynamic_form_value')
            ->where('type_id', $type_id)
            ->where('form_id', $form_id)
            ->when(!empty($rowId), function ($query) use ($rowId) {
                $query->where('id', '<>', $rowId);
            })
            ->max('uid');

        if (empty($maxUid)) {
            $maxUid = 0;
        }

        return $maxUid + 1;
    }

    // public function generateUid($module_id, $type_id, $rowId = null)
    // {

    //     $moduleData = DB::table('gnl_sys_modules')
    //         ->where([['is_delete', 0], ['is_active', 1], ['id', $module_id]])
    //         ->select('module_short_name')
    //         ->first();

    //     $moduleName = $moduleData->module_short_name;

    //     $prefix = $moduleName . ".";
    //     $prefix = strtoupper($prefix);

    //     $record = DB::table('gnl_dynamic_form')
    //         ->select(['id', 'uid'])
    //         ->where('type_id', $type_id)
    //         ->where('module_id', $module_id)
    //         ->where('uid', 'LIKE', "{$prefix}%")
    //         ->when(!empty($rowId), function ($query) use ($rowId) {
    //             $query->where('id', '<>', $rowId);
    //         })
    //         ->orderBy('uid', 'DESC')
    //         ->first();

    //     if ($record) {
    //         $OldBillNoA = explode($prefix, $record->uid);
    //         // $newCode     = $prefix . sprintf("%05d", ($OldBillNoA[1] + 1));
    //         $newCode     = $prefix . ($OldBillNoA[1] + 1);
    //     } else {
    //         $newCode = $prefix . "1";
    //     }

    //     return $newCode;
    // }

}
