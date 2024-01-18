<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\BranchDB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class BranchDBController extends Controller
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

            $masterQuery = BranchDB::where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('table_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = BranchDB::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'table_name' => $Row->table_name,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id, [], $Row->is_active),
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
            return view('GNL.BranchDB.index');
        }
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([

                'table_name' => 'required',

            ]);

            $RequestData = $request->all();

            $isInsert = BranchDB::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Branch DB table',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/br_db')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Branch DB table',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }

        } else {

            return view('GNL.BranchDB.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        // $Branchtable = BranchDB::where(['is_active' => 1])->orderBy('id', 'ASC')->get();
        $Branchtable = BranchDB::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([

                'table_name' => 'required',
            ]);

            $Data = $request->all();

            $isUpdate = $Branchtable->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Branch Db Table Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/br_db')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Branch Db Table Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {

            // $BranchDBData = BranchDB::where('id', $id)->first();
            // $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.BranchDB.edit', compact('Branchtable'));
        }
    }

    public function view($id = null)
    {
        $Branchtable = BranchDB::where('id', $id)->first();

        return view('GNL.BranchDB.view', compact('Branchtable'));
    }

    public function delete(Request $request)
    {
        $delete = BranchDB::where('id', $request->RowID)->get()->each->delete();

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

    public function isActive($id = null)
    {
        $BranchDBData = BranchDB::where('id', $id)->first();

        if ($BranchDBData->is_active == 1) {

            $BranchDBData->is_active = 0;
            # code...
        } else {

            $BranchDBData->is_active = 1;
        }

        $Status = $BranchDBData->save();

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

    // public function delete($id = null)
    // {
    //     $BranchDBData = BranchDB::where('id', $id)->first();

    //     $BranchDBData->is_delete = 1;

    //     $delete = $BranchDBData->save();

    //     if ($delete) {
    //         $notification = array(
    //             'message' => 'Successfully Deleted',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     } else {
    //         $notification = array(
    //             'message' => 'Unsuccessful to Delete',
    //             'alert-type' => 'error',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }

}
