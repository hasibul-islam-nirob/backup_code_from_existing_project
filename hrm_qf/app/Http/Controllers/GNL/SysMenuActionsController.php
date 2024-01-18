<?php

namespace App\Http\Controllers\GNL;

use Illuminate\Http\Request;
use App\Model\GNL\SysMenuAction;
use App\Model\GNL\UserPermission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class SysMenuActionsController extends Controller
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

        $mid = null;
        $menuActions = UserPermission::where(['is_delete' => 0, 'menu_id' => $mid])->get();

        if ($request->ajax()) {
            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');

            $module_id   = (empty($request->input('module_id'))) ? null : $request->input('module_id');
            $menu_id   = (empty($request->input('menu_id'))) ? null : $request->input('menu_id');
            $isActive   = (empty($request->input('isActive'))) ? null : $request->input('isActive');
            $permission_id   = (empty($request->input('permission_id'))) ? null : $request->input('permission_id');

            $search    = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = SysMenuAction::where('is_delete', 0)
                ->where(function ($query) use ($search, $isActive) {
                    if (!empty($search)) {
                        $query->where('name', 'LIKE', "%{$search}%")
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
                ->where(function ($masterQuery) use ($module_id, $menu_id, $permission_id) {
                    if (!empty($module_id)) {
                        $masterQuery->where('module_id', '=', $module_id);
                    }
                    if (!empty($menu_id)) {
                        // $masterQuery->orWhere('menu_id', '=', $menu_id);
                        $masterQuery->where('menu_id', '=', $menu_id);
                    }
                    if (!empty($permission_id)) {
                        $masterQuery->where('set_status', '=', $permission_id);
                    }

                })
                ->orderBy('id', 'ASC');

            $totalFiltered = $masterQuery->count();
            $masterQuery   = $masterQuery->offset($start)->limit($limit)->get();
            // dd($masterQuery);
            $totalData = SysMenuAction::where([['is_delete', 0]])->count();

            $DataSet = array();
            $i       = $start;


            $actionList = DB::table('gnl_dynamic_form_value')
                    ->where([['is_active', 1], ['is_delete', 0],
                        ['type_id', 2], ['form_id', "GCONF.5"]])
                    ->orderBy('order_by', 'ASC')
                    ->pluck('name', 'value_field')
                    ->toArray();
            // dd($actionList);

            foreach ($masterQuery as $key => $row) {
                // dd($masterQuery);
                $parentMenu = "";

                if ($row->parent_menu_id == 0) {
                    $parentMenu = 'Root';
                } else {
                    foreach ($masterQuery as $menuName) {
                        if ($row->parent_menu_id == $menuName->id) {
                            $parentMenu .= $menuName->menu_name . " <br>(<small style='color:blue;'>" . $menuName->route_link . "</small>)";
                        }
                    }
                }

                $DataSet[] = [
                    'id'          => ++$i,
                    'module'   => !empty($row->SysModule->module_name) ? $row->SysModule->module_name : '',
                    'menu_name'   => !empty($row->SysMenu->menu_name) ? $row->SysMenu->menu_name : '-',
                    'name'   => $row->name,
                    'action_type' => (isset($actionList[$row->set_status])) ? '['.$row->set_status.']  '.$actionList[$row->set_status] : '-' ,
                    // 'parent_menu' => $parentMenu,
                    'route_link'  => $row->route_link,
                    'menu_icon'   => $row->menu_icon,
                    'order_by'    => $row->order_by,
                    'module_name' => !empty($row->SysMenu->menu_name) ? $row->SysMenu->menu_name : '-',
                    'action'      => Role::roleWiseArray($this->GlobalRole, $row->id, ['view'], $row->is_active),
                ];
            }
            echo json_encode([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $DataSet,
            ]);
        }
        else {
            return view('GNL.SysMenuActions.index', compact('menuActions', 'mid'));
        }
    }

    public function add(Request $request)
    {
        $this->middleware('permission');


        if ($request->isMethod('post')) {

            $request->validate([
                'name' => 'required',
                //                'controller' => 'required',
                //                'action' => 'required',
            ]);

            $data = $request->all();

            if (isset($data['menu_actions'])) {
                $data['menu_actions'] = isset($data['menu_actions']) ? $data['menu_actions'] : array();
            }
            $isCreate = SysMenuAction::create($data);

            if ($isCreate) {

                // $lastIns = $isCreate->id;

                $lastInsertQuery = SysMenuAction::latest()->first();
                $lastIns         = $lastInsertQuery->id;

                $notification = array(
                    'message'    => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/sys_menu_action')->with($notification);
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
            return view('GNL.SysMenuActions.add', compact('module', 'parent_menu'));
        }
    }

    public function edit(Request $request, $id = null)
    {
        $this->middleware('permission');

        $mid = null;
        $sumenus = SysMenuAction::where('id', $id)->first();
        $upermission = $sumenus;

        if ($request->isMethod('post')) {

            $request->validate([
                'name' => 'required',
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
                return redirect('gnl/sys_menu_action')->with($notification);
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
            return view('GNL.SysMenuActions.edit', compact('module', 'sumenus', 'parent_menu', 'upermission', 'mid'));
        }
    }

    public function delete(Request $request)
    {

        $this->middleware('permission');

        $sumenus            = SysMenuAction::where('id', $request->RowID)->first();
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

        // $module = SysMenuActions::where('id', $id)->delete();
        $module = SysMenuAction::where('id', $id)->get()->each->delete();

        if ($module) {
            $notification = array(
                'message'    => 'Successfully Destory',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isActive($id = null)
    {

        $this->middleware('permission');

        $sumenus = SysMenuAction::where('id', $id)->first();

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

}
