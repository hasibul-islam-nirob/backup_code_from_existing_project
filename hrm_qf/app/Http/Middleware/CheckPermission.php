<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use App\Services\CommonService as Common;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if ($request->ajax()){
        //     return $next($request);
        // }

        // $temp_current_route_name = Route::getCurrentRoute()->action['prefix'];
        // // dd($temp_current_route_name);
        // if ($temp_current_route_name == "mfn/savings/interest_payment" 
        //     || $temp_current_route_name == "mfn/generate_interest_provision") {
        //     return redirect('/under_construction');
        // }

        if (Common::isSuperUser() === true) {
            return $next($request);
        }

        $current_route_name = Route::getCurrentRoute()->uri();
        $check_route_name   = $current_route_name;
        $explode_route_name = explode('/', $current_route_name);
        
        $controller = class_basename(Route::current()->controller);

        $method = null;
        if(!empty($controller)){
            $method = explode("@",Route::getCurrentRoute()->action['controller'])[1];
        }

        //allow all request if it is comming from mfn
        if (strpos($check_route_name, 'mfn') !== false) {
            return $next($request);
        }
        //end

        $exceptionalController = [ // '/' a index function use hoy ni
            'WaiverReportController',
            'WriteOffReportController',
            'RebateReportController',
            'SavingsProvisionReportController',
            'AutoVoucherConfigurationController',
        ];

        // 10 => Permission => /sys_permission (atar bisoye discuss need)
        // 14 => Permission Folder => (discuss needed)
        $actionList = [
            "/add",
            "/edit",
            "/view",
            "/publish",
            "/delete",
            // "/approve",
            "/change_pass",
            "/destroy",
            "/execute",
            "/print",
            "/download_pdf",
            "/download_excel",
            "/reports",
        ];

        //if route contains any of actionList => must go through check. else next
        $canPasswithOutCheck = true;

        foreach ($actionList as $action) {
            if (strpos($check_route_name, $action) !== false) {
                $canPasswithOutCheck = false;
                break;
            }
        }

        if (strpos($check_route_name, 'checkDayEndData') > 0) {
            return $next($request);
        }

        //make sure it is not going to index methode
        if ($canPasswithOutCheck && $method != 'index' && array_search($controller, $exceptionalController) == false) {
            return $next($request);
        }

        $ModulePermissions = (!empty(session()->get('LoginBy.user_role.role_module'))) ? session()->get('LoginBy.user_role.role_module') : array();
        // $MenuPermissions = (!empty(session()->get('LoginBy.user_role.role_menu'))) ? session()->get('LoginBy.user_role.role_menu') : array();
        $ActionPermissions = (!empty(session()->get('LoginBy.user_role.role_permission'))) ? session()->get('LoginBy.user_role.role_permission') : array();

        $MenuPermissions = array_keys($ActionPermissions);

        $requested_module = null;
        $requested_menu   = null;
        $requested_action = null;

        if (isset($explode_route_name[0])) {
            $requested_module = $explode_route_name[0];
        }

        if (isset($explode_route_name[1])) {
            $requested_menu = $explode_route_name[0] . '/' . $explode_route_name[1];
        }

        if (isset($explode_route_name[2])) {
            $requested_action = $explode_route_name[0] . '/' . $explode_route_name[1] . '/' . $explode_route_name[2];
        }

        $flag = true;

        // // // Check Module
        if (!empty($requested_module)) {
            $isModule = array_search($requested_module, array_column($ModulePermissions, 'module_link'));
            if ($isModule === false) {
                $flag = false;
            }
        }

        // // // Check Menu
        if (!empty($requested_menu)) {
            $isMenu = array_search($requested_menu, $MenuPermissions);

            if ($isMenu === false) {
                // // For Report
                if (!empty($requested_action)) {
                    $requested_menu = $requested_action;
                }

                // mfn/reports/collection_sheet
                $isMenu = array_search($requested_menu, $MenuPermissions);
                if ($isMenu === false) {
                    // mfn/reports/pksf/pomis1
                    if (isset($explode_route_name[3])) {
                        $requested_menu   = $requested_menu . '/' . $explode_route_name[3];
                        $requested_action = $requested_menu;
                        $isMenu           = array_search($requested_menu, $MenuPermissions);
                        // dd($requested_menu, $MenuPermissions);
                        if ($isMenu === false) {
                            // mfn/reports/pksf
                            if (isset($explode_route_name[4])) {
                                $requested_menu   = $requested_menu . '/' . $explode_route_name[4];
                                $requested_action = $requested_menu;
                                $isMenu           = array_search($requested_menu, $MenuPermissions);

                                if ($isMenu === false) {
                                    $flag = false;
                                }
                            } else {
                                $flag = false;
                            }
                        }

                    } else {
                        $flag = false;
                    }
                }
            }
        }

        // dd($ActionPermissions[$requested_menu]);
        // dd($requested_action, array_column($ActionPermissions[$requested_menu], 'route_link'));
        // Check Action
        if (!empty($requested_action)) {
            if (isset($ActionPermissions[$requested_menu])) {
                $isAction = array_search($requested_action, array_column($ActionPermissions[$requested_menu], 'route_link'));
                if ($isAction === false) {

                    // if(!isset($ActionPermissions[$requested_action])){
                    //     $flag = false;
                    // }
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        }

        if ($flag === false) {
            return redirect('/access_denied');
        }

        return $next($request);
    }
}
