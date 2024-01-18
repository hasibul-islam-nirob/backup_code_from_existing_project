<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\HOIG;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class HOIGController extends Controller
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

            $masterQuery = HOIG::where(['is_active' => 1])
            ->where(function ($masterQuery) use ($search) {
                if (!empty($search)) {
                    $masterQuery->where('table_name', 'LIKE', "%{$search}%");
                }
            })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = HOIG::count();

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
            return view('GNL.HOIG.index');
        }
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'table_name' => 'required',
            ]);

            $RequestData = $request->all();
            $isInsert = HOIG::create($RequestData);
            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Head Office DB table',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/ho_db_ignore')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Head Office DB table',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HOIG.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        $HOtable = HOIG::where('id', $id)->first();
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
                return Redirect::to('gnl/ho_db_ignore')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Head Office Db Table Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.HOIG.edit', compact('HOtable'));
        }
    }

    public function delete(Request $request)
    {
        $delete = HOIG::where('id', $request->RowID)->get()->each->delete();

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
        $HOIGData = HOIG::where('id', $id)->first();

        if ($HOIGData->is_active == 1) {
            $HOIGData->is_active = 0;
        } else {
            $HOIGData->is_active = 1;
        }

        $Status = $HOIGData->save();

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
