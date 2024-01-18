<?php

namespace App\Http\Controllers\GNL\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Model\GNL\HR\EmployeeDesignation;
use App\Services\CommonService as Common;

class DesignationController extends Controller
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

            $masterQuery = EmployeeDesignation::where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = EmployeeDesignation::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'name' => $Row->name,
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
            return view('GNL.HR.Designation.index');
        }
        /*$DesignationData = EmployeeDesignation::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        return view('HR.Designation.index', compact('DesignationData'));*/
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'name' => 'required',
            ]);

            $RequestData = $request->all();

            $isInsert = EmployeeDesignation::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/designation')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data !',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HR.Designation.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        $DesignationData = EmployeeDesignation::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'name' => 'required',
            ]);

            $Data = $request->all();

            $isUpdate = $DesignationData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/designation')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HR.Designation.edit', compact('DesignationData'));
        }
    }

    public function view($id = null)
    {
        $DesignationData = EmployeeDesignation::where('id', $id)->first();
        return view('GNL.HR.Designation.view', compact('DesignationData'));
    }

    public function delete(Request $request)
    {
        $DesignationData = EmployeeDesignation::where('id', $request->RowID)->first();
        $DesignationData->is_delete = 1;
        $delete = $DesignationData->save();

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
        $DesignationData = EmployeeDesignation::where('id', $id)->first();

        if ($DesignationData->is_active == 1) {
            $DesignationData->is_active = 0;
        } else {
            $DesignationData->is_active = 1;
        }

        $Status = $DesignationData->save();

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
