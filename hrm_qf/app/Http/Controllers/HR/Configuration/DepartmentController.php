<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\HR\EmpDepartment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function getPassport($request, $operationType, $data = null){
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'dept_name'                          => 'required',
            );

            $attributes = array(
                'dept_name'                          => 'Name',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($errorMsg == null &&  ($operationType == 'store' || $operationType == 'update')) {

            $duplicateQuery = DB::table('hr_departments')
                ->where([['dept_name', $request->dept_name], ['is_delete', 0]])
                ->where(function ($query) use ($operationType, $data) {
                    if ($operationType == 'update') {
                        $query->where('id', '<>', $data->id);
                    }
                })
                ->count();
            if ($duplicateQuery > 0) {
                $errorMsg = "Department name already exist.";
            }
        }


        /*

         ## condition check for bank
        if ($errorMsg == null && ($operationType == 'delete' || $operationType == 'index')) {
            $childData = DB::table('hr_bank_branches')->where([['bank_id', $data->id], ['is_delete', 0]])->count();

            if ($childData > 0) {
                $errorMsg = "Branchs of Bank Data Exist! Please delete child data first.";
            }
        }
        
        */

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }



    public function index(Request $request){

        if ($request->isMethod('post')) {
            
            $columns = array(
                0 => 'hr_departments.dept_name',
                1 => 'hr_departments.short_name',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = DB::table('hr_departments')
                ->where('is_delete', 0)
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('dept_name', 'LIKE', "%{$search}%");
                        $query->orWhere('short_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_departments')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }
            

            $sl = (int)$request->start + 1;
            $data      = array();
            foreach ($masterQuery as $key => $row) {

                $IgnoreArray = ['view'];
                $countExistDeptData = DB::table('hr_employees')->where([['is_active', 1],['is_delete', 0],['department_id', $row->id]])->count();
                if ($countExistDeptData > 0) {
                    $IgnoreArray = ['delete', 'view'];
                }

                $data[$key]['id']             = $sl++;
                $data[$key]['dept_name']      = $row->dept_name;
                $data[$key]['short_name']     = $row->short_name;
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



    public function insert(Request $request){

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
                    $isInsert = EmpDepartment::create($RequestData);

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


    public function get(Request $request)
    {
        //$getData = DB::table('hr_departments')->where('id', decrypt($request->id))->where('is_delete', 0)->first();
        $getData = EmpDepartment::where('id', decrypt($request->id))->where('is_delete', 0)->first();

        return response()->json($getData);
    }


    public function update(Request $request)
    {
        if ($request->isMethod('post')) {

            $updateData = EmpDepartment::where('id', decrypt($request->edit_id))->first();
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


    public function delete($id)
    {
        $deletedData = EmpDepartment::where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = EmpDepartment::where('id', decrypt($id))->update(['is_delete' => 1]);

        if ($delete) {
            $notification = array(
                'message'    => "Successfully deleted",
                'alert-type' => 'success',
                'status' => 'success',
                'statusCode' => 200
            );
        } else {
            $notification = array(
                'message'    => "Failed to delete",
                'alert-type' => 'error',
                'status' => 'error',
                'statusCode' => 400
            );
        }

        return response()->json($notification, $notification['statusCode']);
    }


    /*
    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index()
    {
        $DepartmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        return view('HR.Configuration.Department.index', compact('DepartmentData'));
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'dept_name' => 'required',
            ]);

            $RequestData = $request->all();
           
            $isInsert = EmpDepartment::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/department')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data !',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('HR.Configuration.Department.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        $DepartmentData = EmpDepartment::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'dept_name' => 'required',
            ]);

            $Data = $request->all();
           
            $isUpdate = $DepartmentData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/department')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('HR.Configuration.Department.edit', compact('DepartmentData'));
        }
    }

    public function view($id = null)
    {
        $DepartmentData = EmpDepartment::where('id', $id)->first();
        return view('HR.Configuration.Department.view', compact('DepartmentData'));
    }

    public function delete($id = null)
    {
        $DepartmentData = EmpDepartment::where('id', $id)->first();
        $DepartmentData->is_delete = 1;
        $delete = $DepartmentData->save();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isactive($id = null)
    {
        $DepartmentData = EmpDepartment::where('id', $id)->first();

        if ($DepartmentData->is_active == 1) {
            $DepartmentData->is_active = 0;
        } else {
            $DepartmentData->is_active = 1;
        }

        $Status = $DepartmentData->save();

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
    */

}
