<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\Group;
use App\Model\GNL\Project;
use Illuminate\Http\Request;
use App\Model\GNL\ProjectType;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class ProjectTypeController extends Controller
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

            $masterQuery = ProjectType::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('project_type_name', 'LIKE', "%{$search}%")
                            ->orWhere('project_type_code', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('project_type_code', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = ProjectType::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'project_type_name' => $Row->project_type_name,
                    'project_type_code' => $Row->project_type_code,
                    'project_name' => $Row->project->project_name,
                    'comp_name' => $Row->company->comp_name,
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
            return view('GNL.ProjectType.index');
        }
        /*$ProjectTypeData = ProjectType::where('is_delete', 0)->orderBy('project_type_code', 'ASC')->get();
        return view('GNL.ProjectType.index', compact('ProjectTypeData'));*/
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'project_type_name' => 'required',
            ]);
            $RequestData = $request->all();
            $isInsert = ProjectType::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Project Type Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/project_type')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Project Type',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.ProjectType.add', compact('GroupData'));
        }
    }

    public function edit(Request $request, $id = null)
    {
        $ProjectTypeData = ProjectType::where('id', $id)->first();
        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'project_type_name' => 'required',
            ]);

            $Data = $request->all();
            $isUpdate = $ProjectTypeData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Project Type Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/project_type')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Project Type',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/project_type')->with($notification);
            }
        } else {

            $ProjectTypeData = ProjectType::where('id', $id)->first();
            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.ProjectType.edit', compact('ProjectTypeData', 'GroupData'));
        }
    }

    public function view($id = null)
    {
        $ProjectTypeData = ProjectType::where('id', $id)->first();
        return view('GNL.ProjectType.view', compact('ProjectTypeData'));
    }

    public function delete($id = null)
    {
        $ProjectTypeData = ProjectType::where('id', $id)->first();
        $ProjectTypeData->is_delete = 1;
        $delete = $ProjectTypeData->save();

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

    public function isActive($id = null)
    {
        $ProjectTypeData = ProjectType::where('id', $id)->first();
        if ($ProjectTypeData->is_active == 1) {
            $ProjectTypeData->is_active = 0;
        } else {
            $ProjectTypeData->is_active = 1;
        }
        $Status = $ProjectTypeData->save();

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
