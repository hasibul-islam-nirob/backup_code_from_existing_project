<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\HODB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
class HODBController extends Controller
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

            $masterQuery = HODB::where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('table_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = HODB::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'table_name' => $Row->table_name,
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
            return view('GNL.HODB.index');
        }
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([

                'table_name' => 'required',

            ]);

            $RequestData = $request->all();

            $isInsert = HODB::create($RequestData);


                    if ($isInsert) {
                        $notification = array(
                            'message' => 'Successfully Inserted New Head Office DB table',
                            'alert-type' => 'success',
                        );
                        return Redirect::to('gnl/ho_db')->with($notification);
                    } else {
                        $notification = array(
                            'message' => 'Unsuccessful to insert data in Head Office DB table',
                            'alert-type' => 'error',
                        );
                        return Redirect()->back()->with($notification);
                    }



        } else {


            return view('GNL.HODB.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        // $Branchtable = HODB::where(['is_active' => 1])->orderBy('id', 'ASC')->get();
        $HOtable = HODB::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([

                'table_name' => 'required',
            ]);

            $Data = $request->all();

            $isUpdate = $HOtable->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Head Office Db Table Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/ho_db')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Head Office Db Table Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {

            // $HODBData = HODB::where('id', $id)->first();
            // $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.HODB.edit', compact('HOtable'));
        }
    }

    public function view($id = null)
    {
        $HOtable = HODB::where('id', $id)->first();

        return view('GNL.HODB.view', compact('HOtable'));
    }

    public function delete(Request $request)
    {
        $delete = HODB::where('id', $request->RowID)->get()->each->delete();

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
        $HODBData = HODB::where('id', $id)->first();

        if ($HODBData->is_active == 1) {

            $HODBData->is_active = 0;
            # code...
        } else {

            $HODBData->is_active = 1;
        }

        $Status = $HODBData->save();

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
