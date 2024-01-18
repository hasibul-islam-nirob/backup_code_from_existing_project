<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\HR\Room;
use Illuminate\Http\Request;
use App\Model\GNL\HR\EmpDepartment;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;

class RoomController extends Controller
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

            $masterQuery = Room::where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('room_name', 'LIKE', "%{$search}%")
                            ->orWhere('room_code', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = Room::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'room_name' => $Row->room_name,
                    'room_code' => $Row->room_code,
                    'dept_name' => $Row->department['dept_name'],
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
            return view('GNL.Room.index');
        }
    }

    public function add(Request $request)
    {
        $departmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'dept_id' => 'required',
                'room_name' => 'required'
            ]);

            $RequestData = $request->all();

            $isInsert = Room::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/room')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data !',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.Room.add', compact('departmentData'));
        }
    }

    public function edit(Request $request, $id = null)
    {
        $roomData = Room::where('id', $id)->first();
        $departmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'dept_id' => 'required',
                'room_name' => 'required',
            ]);

            $Data = $request->all();

            $isUpdate = $roomData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Data !',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/room')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.Room.edit', compact('roomData','departmentData'));
        }
    }

    public function view($id = null)
    {
        $roomData = Room::where('id', $id)->first();
        $departmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();
        return view('GNL.Room.view', compact('roomData','departmentData'));
    }

    public function delete(Request $request)
    {
        $roomData = Room::where('id', $request->RowID)->first();
        $roomData->is_delete = 1;
        $delete = $roomData->save();

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
        $roomData = Room::where('id', $id)->first();

        if ($roomData->is_active == 1) {
            $roomData->is_active = 0;
        } else {
            $roomData->is_active = 1;
        }

        $Status = $roomData->save();

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
