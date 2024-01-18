<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Model\HR\Room;
use Illuminate\Http\Request;
use App\Model\HR\EmpDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;

class RoomController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index()
    {
        $roomData = Room::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();

        return view('HR.Configuration.Room.index', compact('roomData'));
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
                return Redirect::to('hr/room')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data !',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('HR.Configuration.Room.add', compact('departmentData'));
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
                return Redirect::to('hr/room')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update Data',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('HR.Configuration.Room.edit', compact('roomData','departmentData'));
        }
    }

    public function view($id = null)
    {
        $roomData = Room::where('id', $id)->first();
        $departmentData = EmpDepartment::where([['is_active', 1], ['is_delete', 0]])
            ->orderBy('id', 'ASC')
            ->get();
        return view('HR.Configuration.Room.view', compact('roomData','departmentData'));
    }

    public function delete($id = null)
    {
        $roomData = Room::where('id', $id)->first();
        $roomData->is_delete = 1;
        $delete = $roomData->save();

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
