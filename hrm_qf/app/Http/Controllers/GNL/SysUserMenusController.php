<?php

namespace App\Http\Controllers\GNL;

use Illuminate\Http\Request;
use App\Model\GNL\SysUsrMenus;
use App\Model\GNL\UserPermission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class SysUserMenusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('permission');
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->middleware('permission');

        $action = Route::currentRouteAction();

        if ($request->ajax()) {
            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');

            // dd($limit,$start);

            $module_id = (empty($request->input('module_id'))) ? null : $request->input('module_id');
            $menu_id   = (empty($request->input('menu_id'))) ? null : $request->input('menu_id');
            $isActive   = (empty($request->input('isActive'))) ? null : $request->input('isActive');

            $search    = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            // ,['is_active', 1]
            $masterQuery = SysUsrMenus::where([['is_delete', 0]])
                ->where(function ($query) use ($search, $isActive) {
                    if (!empty($search)) {
                        $query->where('menu_name', 'LIKE', "%{$search}%")
                            // ->orWhere('menu_name', 'LIKE', "%{$search}%")
                            ->orWhere('route_link', 'LIKE', "%{$search}%")
                            ->orWhere('order_by', 'LIKE', "%{$search}%");
                    }

                    if (!empty($isActive)) {
                        if ($isActive == 1) {
                            $query->where('is_active', 1);
                        } else {
                            $query->where('is_active', '<>', 1);
                        }
                    }
                })
                ->where(function ($masterQuery) use ($module_id) {
                    if (!empty($module_id)) {
                        $masterQuery->where('module_id', '=', $module_id);
                    }
                })
                ->where(function ($masterQuery) use ($menu_id) {
                    if (!empty($menu_id)) {
                        $masterQuery->where('parent_menu_id', '=', $menu_id);
                    }
                })
                ->orderBy('module_id', 'ASC')
                ->orderBy('order_by', 'ASC');

            $totalFiltered = $masterQuery->count();
            $masterQuery   = $masterQuery->offset($start)->limit($limit)->get();
            // dd($masterQuery);
            $totalData = SysUsrMenus::where([['is_delete', 0]])->count();

            $DataSet = array();
            $i       = $start;
            foreach ($masterQuery as $key => $Row) {
                $parentMenu = "";

                if ($Row->parent_menu_id == 0) {
                    $parentMenu = 'Root';
                } else {
                    foreach ($masterQuery as $menuName) {
                        if ($Row->parent_menu_id == $menuName->id) {
                            $parentMenu .= $menuName->menu_name . " <br>(<small style='color:blue;'>" . $menuName->route_link . "</small>)";
                        }
                    }
                }

                $DataSet[] = [
                    'id'          => ++$i,
                    'menu_name'   => $Row->menu_name,
                    'parent_menu' => $parentMenu,
                    'route_link'  => $Row->route_link,
                    'menu_icon'   => $Row->menu_icon,
                    'order_by'    => $Row->order_by,
                    'module_name' => $Row->SysModule->module_name,
                    'action'      => Role::roleWiseArray($this->GlobalRole, $Row->id, [], $Row->is_active),
                ];
            }
            echo json_encode([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $DataSet,
            ]);
        } else {
            return view('GNL.SysUsrMenus.index');
        }
    }

    public function add(Request $request)
    {
        $this->middleware('permission');

        if ($request->isMethod('post')) {

            $request->validate([
                'menu_name' => 'required',
                //                'controller' => 'required',
                //                'action' => 'required',
            ]);

            $data = $request->all();
            $data['menu_actions'] = isset($data['menu_actions']) ? $data['menu_actions'] : array();

            $isCreate = SysUsrMenus::create($data);

            if ($isCreate) {

                // $lastIns = $isCreate->id;

                $lastInsertQuery = SysUsrMenus::latest()->first();
                $lastIns         = $lastInsertQuery->id;

                if(count($data['menu_actions']) > 0){
                    $this->autoAddPermission($lastIns, $isCreate->route_link, $data['menu_actions'], $data['module_id']);
                }

                $notification = array(
                    'message'    => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/sys_menu')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Insert',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            $module = DB::table('gnl_sys_modules')
                ->where(['is_delete' => 0, 'is_active' => 1])
                ->get();
            $parent_menu = DB::table('gnl_sys_menus')
                ->where(['is_delete' => 0, 'is_active' => 1])
                ->get();
            return view('GNL.SysUsrMenus.add', compact('module', 'parent_menu'));
        }
    }

    public function edit(Request $request, $id = null)
    {
        $this->middleware('permission');

        $sumenus = SysUsrMenus::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $request->validate([
                'menu_name' => 'required',
                //'controller' => 'required',
                //'action' => 'required',
            ]);

            $data = $request->all();
            // $data['menu_link'] = $data['controller'] . "/" . $data['action'];
            $isUpdate = $sumenus->update($data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('gnl/sys_menu')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $module = DB::table('gnl_sys_modules')
                ->where(['is_delete' => 0, 'is_active' => 1])
                ->get();
            $parent_menu = DB::table('gnl_sys_menus')
                ->where(['is_delete' => 0, 'is_active' => 1])
                ->get();
            return view('GNL.SysUsrMenus.edit', compact('module', 'sumenus', 'parent_menu'));
        }
    }

    public function delete(Request $request)
    {
        $this->middleware('permission');

        $sumenus            = SysUsrMenus::where('id', $request->RowID)->first();
        $sumenus->is_delete = 1;
        $delete             = $sumenus->save();

        if ($delete) {
            return [
                'message' => 'Successfully Deleted',
                'status'  => 'success',
            ];
        } else {
            return [
                'message' => 'Unsuccessful to Delete',
                'status'  => 'error',
            ];
        }
    }

    public function destroy($id = null)
    {
        $this->middleware('permission');

        // $module = SysUsrMenus::where('id', $id)->delete();
        $module = SysUsrMenus::where('id', $id)->get()->each->delete();

        if ($module) {
            $notification = array(
                'message'    => 'Successfully Destory',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    }

    // public function view($id = null) {
    //     $sumenus = SysUsrMenus::findOrFail($id = null);
    //     return view('GNL.SysUser.view', compact('sumenus'));
    // }

    public function isActive($id = null)
    {
        $this->middleware('permission');

        $sumenus = SysUsrMenus::where('id', $id)->first();

        if ($sumenus->is_active == 1) {
            $sumenus->is_active = 0;
        } else {
            $sumenus->is_active = 1;
        }

        $sumenus->update();
        $notification = array(
            'message'    => 'user activation is changed',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    }

    public function autoAddPermission($mid, $menu_link, $actionForMenu = [], $module_id)
    {
        if(count($actionForMenu) < 1){
            return true;
        }

        /**
         * Set Status
         *
         * 1 = Add
         * 2 = Edit
         * 3 = View
         * 4 = Publish(is_active)
         * 5 = Unpublish(is_active)
         * 6 = Delete
         * 7 = Approve
         * 8 = All Data
         * 9 = Change Password
         * 10 = Permission
         * 11 = Print
         * 12 = print pdf
         * 13 = Force Delete
         * 14 = Permission Folder
         */

        foreach ($actionForMenu as $actionStatus) {

            $actionData = array();

            if($actionStatus == 1){ ## Add
                $actionData = [
                    'name'        => 'New Entry',
                    'route_link'  => $menu_link . '/add',
                    'method_name' => 'add',
                    'page_title'  => 'Entry',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 2){ ## Update
                $actionData = [
                    'name'        => 'Update',
                    'route_link'  => $menu_link . '/edit',
                    'method_name' => 'edit',
                    'page_title'  => 'Update',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 3){ ## View
                $actionData = [
                    'name'        => 'View',
                    'route_link'  => $menu_link . '/view',
                    'method_name' => 'view',
                    'page_title'  => 'Details',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 4){ ## Publish
                $actionData = [
                    'name'        => 'Publish',
                    'route_link'  => $menu_link . '/publish',
                    'method_name' => 'isActive',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 5){ ## Unpublish
                $actionData = [
                    'name'        => 'Unpublish',
                    'route_link'  => $menu_link . '/publish',
                    'method_name' => 'isActive',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 6){ ## Delete
                $actionData = [
                    'name'        => 'Delete',
                    'route_link'  => $menu_link . '/delete',
                    'method_name' => 'delete',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 7){ ## Approve
                $actionData = [
                    'name'        => 'Approve',
                    'route_link'  => $menu_link . '/approve',
                    'method_name' => 'isApproved',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            ## 9 == Change Password
            ## 10 == Permission
            ## 11 == Print
            ## 12 == Print pdf
            ## 14 == Permission Folder

            if($actionStatus == 13){ ## Force Delete
                $actionData = [
                    'name'        => 'Force Delete',
                    'route_link'  => $menu_link . '/destroy',
                    'method_name' => 'destroy',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 15){ ## Execute
                $actionData = [
                    'name'        => 'Execute',
                    'route_link'  => $menu_link . '/execute',
                    'method_name' => 'execute',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 16){ ## Send
                $actionData = [
                    'name'        => 'Send',
                    'route_link'  => $menu_link . '/send',
                    'method_name' => 'send',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 101){ ## All Data (Permitted Branches)
                $actionData = [
                    'name'        => 'All Data (Permitted Branches)',
                    'route_link'  => $menu_link,
                    'method_name' => 'access_data',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 102){ ## All Data (Without HO)
                $actionData = [
                    'name'        => 'All Data (Without HO)',
                    'route_link'  => $menu_link,
                    'method_name' => 'access_data',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 103){ ## All Data (Only HO)
                $actionData = [
                    'name'        => 'All Data (Only HO)',
                    'route_link'  => $menu_link,
                    'method_name' => 'access_data',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 104){ ## All Data (Only own Department)
                $actionData = [
                    'name'        => 'All Data (Only own Department)',
                    'route_link'  => $menu_link,
                    'method_name' => 'access_data',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if($actionStatus == 105){ ## All Data (Only permitted Samity)
                $actionData = [
                    'name'        => 'All Data (Only permitted Samity)',
                    'route_link'  => $menu_link,
                    'method_name' => 'access_data',
                    'menu_id'     => $mid,
                    'order_by'    => $actionStatus,
                    'set_status'  => $actionStatus,
                    'module_id' => $module_id
                ];
            }

            if(count($actionData) > 0){
                $isCreate = UserPermission::create($actionData);
            }
        }
    }

    //permission method begins------------------------------>
    public function indexPermission($mid = null)
    {
        $menuActions = UserPermission::where(['is_delete' => 0, 'menu_id' => $mid])->get();
        return view('GNL.SysUsrMenus.index_permission', compact('menuActions', 'mid'));
    }

    public function addPermission(Request $request, $mid = null)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'set_status' => 'required',
                'name'       => 'required',
            ]);

            $data            = $request->all();
            $data['menu_id'] = $mid;

            $isCreate = UserPermission::create($data);
            if ($isCreate) {
                $notification = array(
                    'message'    => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/sys_permission/' . $mid)->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Insert',
                    'alert-type' => 'error',
                );
                return Redirect()->back()->with($notification);
            }
        } else {
            // $module = DB::table('gnl_sys_modules')->get();
            // $parent_menu = DB::table('gnl_sys_menus')->get();
            return view('GNL.SysUsrMenus.add_permission', compact('mid'));
        }
    }

    public function editPermission(Request $request, $mid = null, $id = null)
    {
        $upermission = UserPermission::where(['id' => $id, 'menu_id' => $mid])->first();

        if ($request->isMethod('post')) {

            $request->validate([
                'set_status' => 'required',
                'name'       => 'required',
//                'method_name' => 'required',
            ]);

            $data     = $request->all();
            $isUpdate = $upermission->update($data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('gnl/sys_permission/' . $mid)->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            // $module = DB::table('gnl_sys_modules')->get();
            // $parent_menu = DB::table('gnl_sys_menus')->get();
            return view('GNL.SysUsrMenus.edit_permission', compact('upermission', 'mid'));
        }
    }

    public function deletePermission($mid = null, $id = null)
    {
        $permission = UserPermission::where(['menu_id' => $mid, 'id' => $id])->first();
        if ($permission->is_delete == 0) {

            $permission->is_delete = 1;
            $isSuccess             = $permission->update();

            if ($isSuccess) {
                $notification = array(
                    'message'    => 'Successfully Deleted',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }
        }
    }

    public function destroyPermission($mid = null, $id = null)
    {
        // $permission = UserPermission::where(['id' => $id, 'menu_id' => $mid])->delete();
        $permission = UserPermission::where(['id' => $id, 'menu_id' => $mid])->get()->each->delete();

        if ($permission) {
            $notification = array(
                'message'    => 'Successfully Destory',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isActivePermission($mid = null, $id = null)
    {
        $permission = UserPermission::where(['id' => $id, 'menu_id' => $mid])->first();

        if ($permission->is_active == 1) {
            $permission->is_active = 0;
        } else {
            $permission->is_active = 1;
        }

        $permission->update();
        $notification = array(
            'message'    => 'user activation is changed',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    }

}
