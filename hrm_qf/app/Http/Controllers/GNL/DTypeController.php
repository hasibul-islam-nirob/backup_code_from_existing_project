<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use Redirect;
use Response;
use App\Model\GNL\DType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;

use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class DTypeController extends Controller
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
            );

            $validator = Validator::make($req->all(), [
                'name' => 'required',
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

    public function index(Request $request) {

        if ($request->ajax()) {

            $columns = array(
                0 => 'id',
                1 => 'name'
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // $dTypes = DType::where('is_delete', 0)->orderBy($order, $dir);
            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');
            $dTypes = DB::table('gnl_dynamic_form_type')->where('is_delete', 0)
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    }
                })
                // ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);


            $tempQueryData = clone $dTypes;
            $dTypes = $dTypes->offset($start)->limit($limit)->get();

            $totalData = DType::where([['is_delete', 0]])->count();
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i =$start;

            foreach ($dTypes as $key => $row) {
                $IgnoreArray = array();

                $TempSet = array();
                $TempSet = [
                    'id' => ++$i,
                    'row' => $row->id,
                    'name' => $row->name,
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
        return view('GNL.DType.index');
    }


    public function add(Request $req) {

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
            $isInsert = DType::create($RequestData);

            if($isInsert){
                $notification = array(
                    'message' => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
            }
            else{
                $notification = array(
                    'message' => 'Unsuccessful to record insert.',
                    'alert-type' => 'error',
                );
            }

            return response()->json($notification);

        }
        return view('GNL.DType.add');
    }

    public function edit($id = null, Request $req) {

        $queryData = DType::where('id', $id)->first();;

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

            $isUpdate = $queryData->update($RequestData);

            if($isUpdate){
                $notification = array(
                    'message' => 'Successfully Updated',
                    'alert-type' => 'success',
                );
            }
            else{
                $notification = array(
                    'message' => 'Unsuccessful to record update.',
                    'alert-type' => 'error',
                );
            }

            return response()->json($notification);

        }
        return view('GNL.DType.edit',compact('queryData'));
    }

    public function delete($id = null) {

        $queryData = DType::where('id', $id)->first();

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
        $queryData = DType::where('id', $id)->first();

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
    //     $queryData = DType::where('id', $id)->get()->each->delete();

    //     if ($$queryData) {
    //         $notification = array(
    //             'message' => 'Successfully Destory',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }
}
