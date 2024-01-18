<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;

class GroupController extends Controller
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

            $masterQuery = Group::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('group_name', 'LIKE', "%{$search}%")
                            ->orWhere('group_email', 'LIKE', "%{$search}%")
                            ->orWhere('group_phone', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = Group::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'group_name' => $Row->group_name,
                    'group_email' => $Row->group_email,
                    'group_phone' => $Row->group_phone,
                    'group_logo' => (!empty($Row->group_logo) && file_exists($Row->group_logo)) ? '<img src="'.asset($Row->group_logo).'" style="height: 32PX; width: 32PX;">' : '<img src="'.asset('assets/images/dummy.png').'" style="height: 32PX; width: 32PX;">',
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
            return view('GNL.Group.index');
        }
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'group_name' => 'required',
                'group_phone' => 'required',
                // 'group_logo' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:500',
            ]);

            ## ## Check File validation
            $fileInfo = Common::upload_validation($_FILES['group_logo'], 1, 'image');

            $RequestData = $request->all();
            $RequestData['group_logo'] = null;
            $isInsert = Group::create($RequestData);
            $SuccessFlag = false;

            if ($isInsert) {
                $SuccessFlag = true;
                $lastInsertQuery = Group::latest()->first();

                $tableName = $lastInsertQuery->getTable();
                $pid = $lastInsertQuery->id;

                if (!empty($request->file('group_logo'))) {

                    $uploadFile = $request->file('group_logo');
                    $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                    $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                    ## ## File Upload Function
                    $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                    $lastInsertQuery->group_logo = $upload;
                    $isSuccess = $lastInsertQuery->update();

                    if ($isSuccess) {
                        $SuccessFlag = true;
                    } else {
                        $SuccessFlag = false;
                    }
                }
            }

            if ($SuccessFlag) {
                $notification = array(
                    'message' => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/group')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Group',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }

        } else {
            return view('GNL.Group.add');
        }

    }

    public function edit(Request $request, $id = null)
    {

        $GroupData = Group::where(['id' => $id, 'is_delete' => 0])->first();
        $tableName = $GroupData->getTable();
        $pid = $id;

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'group_name' => 'required',
                'group_phone' => 'required',
                // 'group_logo' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:500',
            ]);

            ## ## Check File validation
            $fileInfo = Common::upload_validation($_FILES['group_logo'], 1, 'image');

            $Data = $request->all();

            if (!empty($request->file('group_logo'))) {

                $uploadFile = $request->file('group_logo');
                $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                ## ## File Upload Function
                $upload = Common::fileUpload($uploadFile, $tableName, $pid);
                $Data['group_logo'] = $upload;
            }

            $isUpdate = $GroupData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Group Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/group')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Group',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        }

        return view('GNL.Group.edit', compact('GroupData'));
    }

    public function view($id = null)
    {
        $GroupData = Group::where('id', $id)->first();
        return view('GNL.Group.view', compact('GroupData'));
    }

    public function delete($id = null)
    {

        $GroupData = Group::where('id', $id)->first();

        $GroupData->is_delete = 1;
        $delete = $GroupData->save();
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
        $GroupData = Group::where('id', $id)->first();
        if ($GroupData->is_active == 1) {
            $GroupData->is_active = 0;
        } else {
            $GroupData->is_active = 1;
        }
        $Status = $GroupData->save();

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
