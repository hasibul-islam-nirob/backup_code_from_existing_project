<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
class OfflinePos
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

        $flag = true;


        ## offline branch user permitted menu
        $permitedMenus = [
            'pos/customer',
            'pos/day_end',
            'pos/month_end',
            'pos/sales',
            'pos/issue',
            'pos/s_return',
            'pos/issue_return',
            'pos/transfer',
            'pos/waiver_product',
            'pos/report',
            // 'gnl/sys_user',
            // 'gnl/employee',
            'pos/branch_status'
        ];

        ## online branch user Not permissted menus
        $NotOnlinepermitedMenus = [
            'pos/customer',
            'pos/day_end',
            'pos/month_end',
            'pos/sales',
            'pos/issue',
            'pos/s_return',
            'pos/issue_return',
            'pos/transfer',
            'pos/waiver_product',
        ];


        ## offline branch user not perrmitted menus
        $NotPermitedMenus = [
            'pos/report/stock_ho_s',
            'pos/report/stock_inv_ho_s',
            'pos/report/prod_stock_branch_s',
            'pos/report/purchase_s',
            'pos/report/purchase_return_s',
        ];

        $current_route_name = Route::getCurrentRoute()->uri();
        $check_route_name = $current_route_name;
        $explode_route_name = explode('/', $current_route_name);

        $requested_module = null;
        $requested_menu = null;
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



        if (Common::isSuperUser() != true && Common::isDeveloperUser() != true) {
            if (Common::getDBConnection() == "sqlite") {
                ## conditions start
                ## Check Module
                if (!empty($requested_module)) {
                    $flag = ($requested_module == 'pos') || ($requested_module == 'gnl') ? true : false;
                }

                ## Check permission
                if($flag == true){
                    if(Common::getBranchId() == 1){

                        ### ho user allowed all route except pos report
                        if (!empty($requested_menu) && ($requested_menu != "pos/report")) {

                            if($requested_menu != "pos/branch_status"){
                                $flag = false;
                            }


                        }else {

                            if(!empty($requested_action)){
                                $isPermited = array_search($requested_action, $NotPermitedMenus);
                                if ($isPermited !== false) {
                                    $flag = false;
                                }

                            }
                        }

                        ### ho user not permitted in gnl
                        if($requested_module == 'gnl'){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    if (!empty($requested_menu)) {
                        // dd($requested_action);
                        $isPermited = array_search($requested_menu, $permitedMenus);

                        if ($isPermited === false) {
                            $flag = false;
                        }
                    }
                }
                ## conditions end
            }else{

                ########
                ## If Online logged branch user , and offline taken .. then prevent branch user from online any branch transaction entry
                ##
                ########


                ## formId 9 = Offline Pos
                $OfflinePOS =  (!empty(DB::table('gnl_company_config')->where([['company_id',Common::getCompanyId()],['form_id',9]])->first()))? 1 : 0;

                // dd($OfflinePOS);

                if($OfflinePOS){ #if offline pos taken then forbid branch enrty online (bcz both online offline entry conflicts)
                    ## Check Module
                    if (!empty($requested_module)) {
                        $flag = ($requested_module == 'pos') || ($requested_module == 'gnl') ? true : false;
                    }
                    // dd($requested_menu);

                    if($flag == true){

                        if(Common::getBranchId() != 1 && !empty($requested_menu)){

                            $isPermited = array_search($requested_menu, $NotOnlinepermitedMenus);

                            if ($isPermited !== false) {
                                $flag = false;
                            }
                        }
                    }

                }
            }

            ########
            ## In  offline Logged from different branch then must prevent Him
            ########
            if (Common::getDBConnection() == "sqlite") {
                $BranchFixed = DB::connection()->table('fixed_branch_data')->where('is_active',1)->first();
                ## check only fixed branched users to access
                if($BranchFixed->branch_id != Common::getBranchId()){
                    $flag =false;
                }
            }

        }

        if ($flag == false) {
            $notification = array(
                'message' => 'You are not authorised this location. Please go to online/Offline URL',
                'alert-type' => 'error',
            );
            return Redirect::to('/')->with($notification);
        } else {
            return $next($request);
        }

    }
}
