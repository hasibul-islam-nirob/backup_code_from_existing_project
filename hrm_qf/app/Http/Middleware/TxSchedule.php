<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Services\CommonService as Common;
class TxSchedule
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

        $transactionMenus = array();

        $transactionMenuIndex = [
            'mfn/samityFieldOfficerChange',
            'mfn/samityDayChange',
            'mfn/memberSamityTransfer',
            'mfn/memberPrimaryProductTransfer',

            'mfn/auto_pro',
            'mfn/trnsc_auth',
            'mfn/generate_interest_provision',
            'mfn/generate_interest_provision',

            'mfn/regularloan',
            'mfn/oneTimeLoan',
            'mfn/regularloanTransaction',
            'mfn/oneTimeLoanTransaction',

            'mfn/savings/account',
            'mfn/savings/deposit',
            'mfn/savings/withdraw',
            'mfn/savings/status',
            'mfn/savings/closing',
            'mfn/savings/interest_payment',

            'pos/purchase',
            'pos/purchase_return',
            'pos/product_order',
            'pos/issue',
            'pos/issue_return',
            'pos/sales_cash',
            'pos/sales_installment',
            'pos/sales_return',
            'pos/collection',
            'pos/transfer',
            'pos/supplier_payment',
            'pos/requisition',
        ];

        foreach($transactionMenuIndex as $menu){
            array_push($transactionMenus, $menu . '/add');
            array_push($transactionMenus, $menu . '/edit');
            array_push($transactionMenus, $menu . '/delete');
        }

        $current_route_name = Route::getCurrentRoute()->uri();
        $check_route_name = $current_route_name;
        $explode_route_name = explode('/', $current_route_name);

        // dd($current_route_name, $explode_route_name);

        $requested_module = null;
        $requested_menu = null;
        $requested_action = null;
        $temp_arr = ["add", "edit", "delete", "execute", "approve", "send"];

        switch (count($explode_route_name)) {

            ## amra count 4 ta porjonto accept kori baki gulo parameter hisebe count hobe.
            case 0:
                break;
            case 1:
                $requested_module = $explode_route_name[0];
                break;
            case 2:
                $requested_module = $explode_route_name[0];
                $requested_menu = $requested_module . '/' . $explode_route_name[1];
                break;
            case 3:
                $requested_module = $explode_route_name[0];
                $requested_menu = $requested_module . '/' . $explode_route_name[1];
                $requested_action = $requested_menu . '/' . $explode_route_name[2];
                break;
            case 4:
                $requested_module = $explode_route_name[0];

                if(array_search($explode_route_name[3], $temp_arr) !== false) {
                    $requested_menu = $requested_module . '/' . $explode_route_name[1] . '/' . $explode_route_name[2];
                    $requested_action = $requested_menu . '/' . $explode_route_name[3];
                }
                else {
                    $requested_menu = $requested_module . '/' . $explode_route_name[1];
                    $requested_action = $requested_menu . '/' . $explode_route_name[2];
                }
                break;
            default: ## get url a parameter pathale seta ei array te astase.
                $requested_module = $explode_route_name[0];

                if(array_search($explode_route_name[4], $temp_arr) !== false) {
                    
                    $requested_menu = $requested_module 
                                    . '/' . $explode_route_name[1] 
                                    . '/' . $explode_route_name[2]
                                    . '/' . $explode_route_name[3];
                    $requested_action = $requested_menu . '/' . $explode_route_name[4];
                }
                elseif(array_search($explode_route_name[3], $temp_arr) !== false) {

                    $requested_menu = $requested_module 
                                    . '/' . $explode_route_name[1] 
                                    . '/' . $explode_route_name[2];
                    $requested_action = $requested_menu . '/' . $explode_route_name[3];
                }
                else {
                    $requested_menu = $requested_module . '/' . $explode_route_name[1];
                    $requested_action = $requested_menu . '/' . $explode_route_name[2];
                }
                break;
        }

        if (!empty($requested_module)) {
            $flag = ($requested_module == 'pos') || ($requested_module == 'mfn') ? true : false;
        }

        $txFlag = false;
        if($flag == true){
            
            $getSchedule = DB::table('gnl_companies')->where('id',Common::getCompanyId())
                    ->first(['tx_start_time','tx_end_time','applicable_for']);

            $txStartTime = $getSchedule->tx_start_time;
            $txEndTime = $getSchedule->tx_end_time;

            $applicableFor = $getSchedule->applicable_for;

            $isPermited = array_search($requested_action, $transactionMenus);
            
            if(!empty($txStartTime) && !empty($txEndTime) && $isPermited !== false){
                $txStartTime = (new DateTime($txStartTime))->format('H:i:s');
                $txEndTime = (new DateTime($txEndTime))->format('H:i:s');

                $currentTime = (new DateTime())->format('H:i:s');

                ## All Branch Without Ho || All with HO
                if(($applicableFor == 1 && Common::getBranchId() != 1) || $applicableFor == 2){
                    if($currentTime >= $txStartTime && $currentTime <= $txEndTime){
                        $txFlag = true;
                    }
                }else{
                    $txFlag = true;
                } 
            }else{
                $txFlag = true;
            }

            if(!empty($txStartTime) && !empty($txEndTime)){
                $txStartTime = (new DateTime($txStartTime))->format('h:i a');
                $txEndTime = (new DateTime($txEndTime))->format('h:i a');
            }
            
        }

        if ($txFlag == false) {

            $notification = array(
                'message' => 'Transaction is allowed in between ' . $txStartTime . ' and ' .$txEndTime ,
                'alert-type' => 'error',
                'display_type' => 'swal'
            );

            if($request->ajax()){
                return response()->json($notification);
            }
            else {
                return redirect()->back()->with($notification);
            }
            
        } else {
            return $next($request);
        }

    }
}
