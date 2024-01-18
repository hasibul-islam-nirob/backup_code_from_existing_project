<?php

namespace App\Http\Controllers\GNL\HR;

use Illuminate\Http\Request;
use App\Model\GNL\HR\EmpDepartment;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;

class DepartmentController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $request)
    {

        if ($request->ajax()){

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('dept_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = EmpDepartment::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'dept_name' => $Row->dept_name,
                    'short_name' => $Row->short_name,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id),
                ];
            }
            echo json_encode([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            ]);
        }
        else{
            return view('GNL.HR.Department.index');
        }

        /*$DepartmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        return view('HR.Department.index', compact('DepartmentData'));*/
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
                return Redirect::to('gnl/department')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data !',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HR.Department.add');
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
                return Redirect::to('gnl/department')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HR.Department.edit', compact('DepartmentData'));
        }
    }

    public function view($id = null)
    {
        $DepartmentData = EmpDepartment::where('id', $id)->first();
        return view('GNL.HR.Department.view', compact('DepartmentData'));
    }

    public function delete(Request $request)
    {
        $DepartmentData = EmpDepartment::where('id', $request->RowID)->first();
        $DepartmentData->is_delete = 1;
        $delete = $DepartmentData->save();

        if ($delete) {
            return [
                'message' => 'Successfully Deleted',
                'status' => 'success',
            ];
        } else {
            return [
                'message' => 'Unsuccessful to Delete',
                'status' => 'error',
            ];
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

}
