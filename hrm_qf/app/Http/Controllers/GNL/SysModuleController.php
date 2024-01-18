<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\SysModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class SysModuleController extends Controller
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

            $masterQuery = SysModule::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('module_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = SysModule::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'module_name' => $Row->module_name,
                    'module_short_name' => $Row->module_short_name,
                    'route_link' => $Row->route_link,
                    'module_icon' => ($Row->module_icon != null) ? '<i class="fa '.$Row->module_icon.'" style="font-size:25px;" aria-hidden="true"></i>' : "",
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
            return view('GNL.SysModule.index');
        }
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'module_name' => 'required',
                'module_short_name' => 'required',
                // 'module_icon' => 'required | mimes:jpeg,jpg,png,JPEG,JPG,PNG| max:500',
            ]);

            $data = $request->all();
            $data['module_icon'] = null;
            $isCreate = SysModule::create($data);

            if ($isCreate) {

                // // $mid = $isCreate->id;
                // $lastInsertQuery = SysModule::latest()->first();
                // $mid = $lastInsertQuery->id;

                // $micon = $request->file('module_icon');

                // if ($micon != null) {

                //     $icon_name = hexdec(uniqid());
                //     $ext_icn = strtolower($micon->getClientOriginalExtension());
                //     $icon_full_name = 'icon_' . $icon_name . '.' . $ext_icn;
                //     $upload_icon_path = 'assets/images/module-icon/';
                //     $icon_url = $upload_icon_path . $icon_full_name;

                //     $data['module_icon'] = $icon_url;
                //     $isUpIcon = $isCreate->update($data);

                //     if ($isUpIcon) {

                //         $success_icn = $micon->move($upload_icon_path, $icon_full_name);

                //         if ($success_icn) {

                //             $notification = array(
                //                 'message' => 'Successfully Inserted',
                //                 'alert-type' => 'success',
                //             );
                //             return Redirect::to('gnl/sys_module')->with($notification);
                //         } else {
                //             $isCreate->delete();
                //             $notification = array(
                //                 'message' => 'Unsuccessful to Insert',
                //                 'alert-type' => 'error',
                //             );
                //             return Redirect()->back()->with($notification);
                //         }
                //     } else {
                //         $notification = array(
                //             'message' => 'Unsuccessful to Insert',
                //             'alert-type' => 'error',
                //         );
                //         return redirect()->back()->with($notification);
                //     }
                // } else {
                //     $notification = array(
                //         'message' => 'Unsuccessful to Insert',
                //         'alert-type' => 'error',
                //     );
                //     return redirect()->back()->with($notification);
                // }

                $notification = array(
                    'message' => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/sys_module')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Insert',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            return view('GNL.SysModule.add');
        }
    }

    public function edit(Request $request, $id = null)
    {
        $module = SysModule::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $request->validate([
                'module_name' => 'required',
                'module_short_name' => 'required',
            ]);

            $data = $request->all();
            $micon = $request->module_icon;

            $isUpdate = $module->update($data);
            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('gnl/sys_module')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }

            // if ($micon != null) {
            //     $icon_name = hexdec(uniqid());
            //     $ext_icn = strtolower($micon->getClientOriginalExtension());
            //     $icon_full_name = 'icon_' . $icon_name . '.' . $ext_icn;
            //     $upload_icon_path = 'uploads/sys_module/' . $id . '/';
            //     $icon_url = $upload_icon_path . $icon_full_name;

            //     $data['module_icon'] = $icon_url;
            //     $isUpIcon = $module->update($data);

            //     if ($isUpIcon) {

            //         $success_icn = $micon->move($upload_icon_path, $icon_full_name);

            //         if ($success_icn) {

            //             $notification = array(
            //                 'message' => 'Successfully Inserted',
            //                 'alert-type' => 'success',
            //             );
            //             return Redirect::to('gnl/sys_module')->with($notification);
            //         } else {
            //             $isCreate->delete();
            //             $notification = array(
            //                 'message' => 'Unsuccessful to Insert',
            //                 'alert-type' => 'error',
            //             );
            //             return Redirect()->back()->with($notification);
            //         }
            //     } else {
            //         $notification = array(
            //             'message' => 'Unsuccessful to Insert',
            //             'alert-type' => 'error',
            //         );
            //         return redirect()->back()->with($notification);
            //     }
            // } else {

            //     $isUpdate = $module->update($data);
            //     if ($isUpdate) {
            //         $notification = array(
            //             'message' => 'Successfully Updated',
            //             'alert-type' => 'success',
            //         );
            //         return redirect('gnl/sys_module')->with($notification);
            //     } else {
            //         $notification = array(
            //             'message' => 'Unsuccessful to Update',
            //             'alert-type' => 'error',
            //         );
            //         return redirect()->back()->with($notification);
            //     }
            // }
            // $isUpdate = $module->update($data);
        } else {
            return view('GNL.SysModule.edit', compact('module'));
        }
    }

    public function delete($id = null)
    {
        $module = SysModule::where('id', $id)->first();
        if ($module->is_delete == 0) {

            $module->is_delete = 1;
            $isSuccess = $module->update();

            if ($isSuccess) {
                $notification = array(
                    'message' => 'Successfully Deleted',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }
        }
    }

    public function destroy($id = null)
    {
        $module = SysModule::where('id', $id)->get()->each->delete();

        if ($module) {
            $notification = array(
                'message' => 'Successfully Destory',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isActive($id = null)
    {
        $module = SysModule::where('id', $id)->first();

        if ($module->is_active == 1) {
            $module->is_active = 0;
        } else {
            $module->is_active = 1;
        }

        $module->update();

        $notification = array(
            'message' => 'user activation is changed',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    }

}
