<?php

namespace App\Http\Controllers;

use Picqer;
use Datetime;
use App\Model\GNL\Area;
use App\Model\POS\Customer;
use App\Model\POS\Supplier;
use App\Model\POS\Collection;
use App\Model\POS\SalesMaster;
use App\Model\POS\ShopSalesMaster;
use App\Model\POS\PProcessingFee;
use App\Services\HrService as HRS;
use App\Services\HtmlService as HTML;
use Illuminate\Support\Facades\DB;
use App\Services\AccService as ACCS;
use App\Services\FamService as FAMS;
use App\Services\InvService as INVS;
use App\Services\PosService as POSS;
use App\Services\TmsService as TMS;
use Illuminate\Http\Request;
use App\Model\FAM\Product as FamProduct;
use App\Model\INV\Product as InvProduct;
use App\Model\POS\Product as PosProduct;
use Facade\Ignition\QueryRecorder\Query;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Route;
use App\Services\HmsService as HMS;

class AjaxController extends Controller
{

    /**
     * ajaxSelectBoxLoad method used for Select Box loading data, This method is called via ajax
     * @param TableName - @type - String - Database table name
     * @param WhereColumn - Database column name,
     *            this variable is used in where condition of selecting uery
     * @param FeildVal - This variable is used in where condition for column value
     * @param SelectColumn - Database column name, this variable is used in select option for selecting uery
     * @param SelectedVal - This variable is used in edit page section,
     *          when SelectedVal find, its match Query data and if match its return selected text into selecting option
     */

    // public function ajaxSelectBoxLoad(Request $request)
    // {

    //     if ($request->ajax()) {

    //         $FeildVal = $request->FeildVal;
    //         $TableName = base64_decode($request->TableName);
    //         $WhereColumn = base64_decode($request->WhereColumn);
    //         $SelectColumn = base64_decode($request->SelectColumn);

    //         $fixedRowName = $request->fixedRowName;
    //         // $fixedRowValue = base64_decode($request->fixedRowValue);
    //         $fixedRowValueArray = explode(',', base64_decode($request->fixedRowValue));

    //         // dd($fixedRowName, $fixedRowValueArray);

    //         $SelectArr = explode(',', $SelectColumn);
    //         $PrimaryKey = $SelectArr[0];
    //         $DisplayKey1 = $SelectArr[1];

    //         $DisplayKey2 = '';

    //         if (isset($SelectArr[2])) {
    //             $DisplayKey2 = $SelectArr[2];
    //         }

    //         $SelectedVal = $request->SelectedVal;

    //         // Query
    //         if (!empty($DisplayKey2)) {
    //             $QueryData = DB::table($TableName)
    //                 ->where([$WhereColumn => $FeildVal, 'is_delete' => 0, 'is_active' => 1])
    //                 ->select([$PrimaryKey, $DisplayKey1, $DisplayKey2])
    //                 ->orWhere(function ($QueryData) use ($fixedRowName, $fixedRowValueArray) {
    //                     if (!empty($fixedRowName) && !empty($fixedRowValueArray)) {
    //                         // dd($fixedRowValueArray);
    //                         $QueryData->whereIn($fixedRowName,  $fixedRowValueArray);
    //                     }
    //                 })
    //             // ->orderBy([$SelectArr[1] => 'ASC'])
    //                 ->get();
    //         } else {
    //             $QueryData = DB::table($TableName)
    //                 ->where([$WhereColumn => $FeildVal, 'is_delete' => 0, 'is_active' => 1])
    //                 ->select([$PrimaryKey, $DisplayKey1])
    //                 ->orWhere(function ($QueryData) use ($fixedRowName, $fixedRowValueArray) {
    //                     if (!empty($fixedRowName) && !empty($fixedRowValueArray)) {
    //                         // dd($fixedRowValueArray);
    //                         $QueryData->whereIn($fixedRowName,  $fixedRowValueArray);
    //                     }
    //                 })
    //                 ->get();
    //         }
    //         // dd($QueryData);
    //         $output = '<option value="">Select One</option>';
    //         foreach ($QueryData as $Row) {

    //             $SelectText = '';

    //             if ($SelectedVal != null) {
    //                 if ($SelectedVal == $Row->$PrimaryKey) {
    //                     $SelectText = 'selected="selected"';
    //                 }
    //             }
    //             if ($DisplayKey2 != null) {
    //                 $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey1 . ' - ' . $Row->$DisplayKey2 . '</option>';
    //             } else {
    //                 $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey1 . '</option>';
    //             }

    //         }

    //         echo $output;
    //     }
    // }

    public function ajaxSelectBoxLoad(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $extraCondition = base64_decode($request->extraCondition);
            $fixedRowName = $request->fixedRowName;
            $fixedRowValueArray = explode(',', base64_decode($request->fixedRowValue));
            // $fixedRowValue = base64_decode($request->fixedRowValue);

            $SelectArr   = explode(',', $SelectColumn);
            $PrimaryKey  = $SelectArr[0];
            $DisplayKey1 = $SelectArr[1];
            $DisplayKey2 = '';
            $isActive = $request->isActive;

            if (isset($SelectArr[2])) {
                $DisplayKey2 = $SelectArr[2];
            }

            $SelectedVal = $request->SelectedVal;
            $option      = isset($request['option']) ? $request['option'] : null;

            // Query
            if (!empty($DisplayKey2)) {
                $QueryData = DB::table($TableName)
                    // ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                    ->where('is_delete', 0)
                    ->where(function ($QueryData) use ($isActive, $WhereColumn, $FeildVal, $extraCondition) {
                        if ($isActive != 'isActiveOff') {
                            $QueryData->where('is_active', 1);
                        }

                        if (!empty($WhereColumn) && !empty($FeildVal)) {
                            $QueryData->where($WhereColumn, $FeildVal);
                        }

                        if (!empty($extraCondition)) {
                            // $QueryData->where($extraCondition);
                            $QueryData->whereRaw($extraCondition);
                        }
                    })
                    ->select([$PrimaryKey, $DisplayKey1, $DisplayKey2])
                    ->orWhere(function ($QueryData) use ($fixedRowName, $fixedRowValueArray) {
                        if (!empty($fixedRowName) && !empty($fixedRowValueArray)) {
                            // dd($fixedRowValueArray);
                            $QueryData->whereIn($fixedRowName, $fixedRowValueArray);
                        }
                    })
                    // ->orderBy([$SelectArr[1] => 'ASC'])
                    ->get();
            } else {
                $QueryData = DB::table($TableName)
                    ->where('is_delete', 0)
                    ->where(function ($QueryData) use ($isActive, $WhereColumn, $FeildVal, $extraCondition) {
                        if ($isActive != 'isActiveOff') {
                            $QueryData->where('is_active', 1);
                        }

                        if (!empty($WhereColumn) && !empty($FeildVal)) {
                            $QueryData->where($WhereColumn, $FeildVal);
                        }

                        if (!empty($extraCondition)) {
                            // $QueryData->where($extraCondition);
                            $QueryData->whereRaw($extraCondition);
                        }
                    })
                    ->select([$PrimaryKey, $DisplayKey1])
                    ->orWhere(function ($QueryData) use ($fixedRowName, $fixedRowValueArray) {
                        if (!empty($fixedRowName) && !empty($fixedRowValueArray)) {
                            // dd($fixedRowName, $fixedRowValueArray);
                            $QueryData->whereIn($fixedRowName, $fixedRowValueArray);
                        }
                    })
                    ->get();
            }
            $optionVal = ($option == 'all') ? 'All' : 'Select Option';
            $output    = '<option value="">' . $optionVal . '</option>';
            foreach ($QueryData as $Row) {

                $SelectText = '';

                if ($SelectedVal != null) {
                    if ($SelectedVal == $Row->$PrimaryKey) {
                        $SelectText = 'selected="selected"';
                    }
                }
                if ($DisplayKey2 != null) {
                    $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey1 . ' [' . $Row->$DisplayKey2 . ']</option>';
                } else {
                    $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey1 . '</option>';
                }
            }

            echo $output;
        }
    }

    public function ajaxSelectBoxLoadObj(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $SelectArr    = explode(',', $SelectColumn);
            // dd($SelectArr);

            $PrimaryKey = $SelectArr[0];
            $DisplayKey = $SelectArr[1];

            // $SelectedVal = $request->SelectedVal;

            // Query
            $QueryData = DB::table($TableName)
                ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                ->select([$PrimaryKey, $DisplayKey])
                // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            // dd($QueryData);

            // if(count($QueryData->toarray()) > 0){
            //     return  $QueryData;
            // }
            // else{
            //     return false;
            // }

            return $QueryData;
        }
    }

    // public function ajaxSelectBoxLoadForTargetBranch(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $branchId = $request->branchId;
    //         $projectId = $request->projectId;
    //         $projectTypeId = $request->projectTypeId;
    //         $selectedVal = $request->selectedVal;

    //         $queryData = ACCS::getLedgerData([
    //                 'branchId' => $branchId,
    //                 'projectId' => $projectId,
    //                 'projectTypeId' => $projectTypeId,
    //                 'accType' => [4, 5],
    //                 'groupHead' => 0
    //                 ]);

    //         $data = [
    //             'status' => 'success',
    //             'message' => '',
    //             'result_data' => $queryData,
    //         ];

    //         return response()->json($data);
    //     }
    // }

    public function ajaxSelectBoxCodeLoad(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $SelectArr    = explode(',', $SelectColumn);
            $PrimaryKey   = $SelectArr[0];
            $CodeKey      = $SelectArr[1];
            $DisplayKey   = $SelectArr[2];

            $SelectedVal = $request->SelectedVal;

            // Query
            $QueryData = DB::table($TableName)
                ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                ->select([$PrimaryKey, $CodeKey, $DisplayKey])
                // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $SelectText = '';

                if ($SelectedVal != null) {
                    if ($SelectedVal == $Row->$PrimaryKey) {
                        $SelectText = 'selected="selected"';
                    }
                }
                $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey . " [" . $Row->$CodeKey . ']</option>';
            }

            echo $output;
        }
    }

    public function ajaxSelectBoxCodeLoadLast(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $SelectArr    = explode(',', $SelectColumn);
            $PrimaryKey   = $SelectArr[0];
            $CodeKey      = $SelectArr[1];
            $DisplayKey   = $SelectArr[2];

            $SelectedVal = $request->SelectedVal;

            // Query
            $QueryData = DB::table($TableName)
                ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                ->select([$PrimaryKey, $CodeKey, $DisplayKey])
                // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            // dd( $QueryData, $SelectedVal);

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $SelectText = '';

                if ($SelectedVal != null) {
                    if ($SelectedVal == $Row->$PrimaryKey) {
                        $SelectText = 'selected="selected"';
                    }
                }
                $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey . ' [' . $Row->$CodeKey . '] </option>';
            }

            echo $output;
        }
    }

    public function ajaxSelectBoxLoadforLedger(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $SelectArr    = explode(',', $SelectColumn);
            $PrimaryKey   = $SelectArr[0];
            $DisplayKey   = $SelectArr[1];

            $SelectedVal = $request->SelectedVal;

            // Query
            $QueryData = DB::table($TableName)
                ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                ->select(['id', $PrimaryKey, $DisplayKey])
                ->orderBy($PrimaryKey, 'ASC')
                // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            if ($SelectedVal != null) {
                $checkselecteddata = DB::table($TableName)
                    ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                    ->where('id', $SelectedVal)
                    ->select([$PrimaryKey, $DisplayKey])
                    ->orderBy($PrimaryKey, 'ASC')
                    ->first();
                $last = $checkselecteddata->order_by - 1;
                //  dd($checkselecteddata);

            } else {
                $last = count($QueryData->toArray());
            }

            //   dd($QueryData, $SelectedVal,$PrimaryKey);
            $output = '<option value="0">At First</option>';

            if (!empty($QueryData)) {
                foreach ($QueryData as $Row) {

                    $SelectText = '';

                    if ($last != null) {
                        if ($last == $Row->$PrimaryKey) {
                            $SelectText = 'selected="selected"';
                        }
                        // if ($SelectedVal == $Row->id) {
                        //     $SelectText = 'selected="selected"';
                        // }
                    }
                    if ($request->SelectedVal != $Row->id) {
                        $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '> After ' . $Row->$DisplayKey . '</option>';
                    }
                }
            }

            echo $output;
        }
    }

    public function ajaxGetZone(Request $request)
    {
        if ($request->ajax()) {

            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';
            $returnFor = (isset($request->returnFor)) ? $request->returnFor : 'input'; ## "search"

            $zoneQuery = DB::table('gnl_zones')
                // ->where([['is_delete', 0], ['is_active', 1]])
                ->where([['is_delete', 0]])
                ->where(function ($query) use ($returnFor) {
                    if ($returnFor != 'search') {
                        $query->where('is_active', 1);
                    }
                })
                ->select('id', 'zone_name', 'zone_code')
                ->orderBy('zone_code', 'ASC')
                ->get();


            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $zoneQuery,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($zoneQuery as $Row) {
                    $output .= '<option value="' . $Row->id . '">' . $Row->zone_name . ' [' . $Row->zone_code . ']</option>';
                }
                echo $output;
            }
        }
    }

    public function ajaxGetRegion(Request $request)
    {
        if ($request->ajax()) {

            $zoneId     = (isset($request->zoneId)) ? $request->zoneId : null;
            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';
            $returnFor = (isset($request->returnFor)) ? $request->returnFor : 'input'; ## "search"

            $accessArr = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_approve', 1]])
                ->where(function ($query) use ($zoneId, $returnFor) {
                    if ($returnFor != "search") {
                        $query->where('is_active', 1);
                    }

                    if (!empty($zoneId)) {
                        $query->where('zone_id', $zoneId);
                    }
                })
                ->pluck('region_id')
                ->unique()
                ->toArray();

            $queryData = DB::table('gnl_regions')
                ->where([['is_delete', 0]])
                ->whereIn('id', $accessArr)
                ->where(function ($query) use ($returnFor) {
                    if ($returnFor != "search") {
                        $query->where('is_active', 1);
                    }
                })
                ->select('id', 'region_name', 'region_code')
                ->orderBy('region_code', 'ASC')
                ->get();

            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $queryData,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($queryData as $Row) {
                    $output .= '<option value="' . $Row->id . '">' . $Row->region_name . ' [' . $Row->region_code . ']</option>';
                }
                echo $output;
            }
        }
    }

    public function ajaxGetArea(Request $request)
    {
        if ($request->ajax()) {

            $zoneId     = (isset($request->zoneId)) ? $request->zoneId : null;
            $regionId     = (isset($request->regionId)) ? $request->regionId : null;
            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';
            $returnFor = (isset($request->returnFor)) ? $request->returnFor : 'input'; ## "search"

            $accessArr = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_approve', 1]])
                ->where(function ($query) use ($zoneId, $regionId, $returnFor) {
                    if ($returnFor != "search") {
                        $query->where('is_active', 1);
                    }

                    if (!empty($zoneId)) {
                        $query->where('zone_id', $zoneId);
                    }

                    if (!empty($regionId)) {
                        $query->where('region_id', $regionId);
                    }
                })
                ->pluck('area_id')
                ->unique()
                ->toArray();

            $queryData = DB::table('gnl_areas')
                ->where([['is_delete', 0]])
                ->whereIn('id', $accessArr)
                ->where(function ($query) use ($returnFor) {
                    if ($returnFor != "search") {
                        $query->where('is_active', 1);
                    }
                })
                ->select('id', 'area_name', 'area_code')
                ->orderBy('area_code', 'ASC')
                ->get();

            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $queryData,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($queryData as $Row) {
                    // $output .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->area_code) . ' - ' . $Row->area_name . '</option>';
                    $output .= '<option value="' . $Row->id . '">' . $Row->area_name . ' [' . $Row->area_code . ']</option>';
                }
                echo $output;
            }
        }
    }

    public function getBranch(Request $request)
    {

        if ($request->ajax()) {

            $zoneId     = (isset($request->zoneId)) ? $request->zoneId : null;
            $regionId     = (isset($request->regionId)) ? $request->regionId : null;
            $areaId     = (isset($request->areaId)) ? $request->areaId : null;

            $branchId     = (isset($request->branchId)) ? $request->branchId : "-1"; ##  all with ho

            $projectId     = (isset($request->projectId)) ? $request->projectId : null;
            $projectTypeId     = (isset($request->projectTypeId)) ? $request->projectTypeId : null;
            $companyId     = (isset($request->companyId)) ? $request->companyId : null;

            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';
            $returnFor = (isset($request->returnFor)) ? $request->returnFor : 'input'; ## "search"

            $ignorHO    = (isset($request->ignorHO)) ? $request->ignorHO : null;

            if(!empty($ignorHO) && $ignorHO == "1" && $branchId < 1){
                $branchId = "-2"; ## all without HO
            }

            $queryData = Common::getBranchIdsForAllSection([
                'branchId' => $branchId,
                'zoneId' => $zoneId,
                'regionId' => $regionId,
                'areaId' => $areaId,
                'projectId' => $projectId,
                'projectTypeId' => $projectTypeId,
                'companyId' => $companyId,
                'returnFor' => $returnFor,
                'fnReturn' => 'dataObject'
            ]);

            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $queryData,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($queryData as $Row) {
                    $SelectText = '';
                    // $output .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->branch_code) . ' - ' . $Row->branch_name . '</option>';
                    $output .= '<option value="' . $Row->id . '">' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
                }

                echo $output;
            }
        }
    }

    public function ajaxMenuList(Request $request)
    {

        if ($request->ajax()) {

            $module_id   = $request->module_id;
            $SelectedVal = $request->SelectedVal;
            // Query
            $QueryData = DB::table('gnl_sys_menus')
                ->where([['is_delete', 0], ['module_id', $module_id]])
                ->orderBy('parent_menu_id', 'ASC')
                ->orderBy('menu_name', 'ASC')
                ->get();

            // dd($QueryData);

            $output = '<option value="0">Root</option>';
            foreach ($QueryData as $Row) {

                $SelectText = '';

                if ($SelectedVal != null) {
                    if ($SelectedVal == $Row->id) {
                        $SelectText = 'selected="selected"';
                    }
                }
                $output .= '<option value="' . $Row->id . '" ' . $SelectText . '>' . $Row->menu_name . ' [' . $Row->route_link . ']</option>';
            }

            echo $output;
        }
    }

    public function ajaxCheckOBStockBranch(Request $request)
    {

        if ($request->ajax()) {

            // Query
            $BranchData = DB::table('gnl_branchs')
                ->select(
                    DB::raw('id, branch_name, branch_code, (SELECT COUNT(ob.id) '
                        . 'FROM `pos_ob_stock_m` as ob '
                        . 'WHERE ob.branch_id = gnl_branchs.id AND ob.is_delete = 0 ) as BranchCount')
                )
                ->where(['is_delete' => 0, 'is_approve' => 1])
                ->having('BranchCount', '=', 0)
                ->get();

            $output = '<option value="">Select Branch</option>';
            foreach ($BranchData as $Row) {
                $output .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
            }

            echo $output;
        }
    }
    // ajaxCheckOBStockBranch

    ## For deleting ledger
    public function ajaxDeleteLedger(Request $request)
    {

        if ($request->ajax()) {

            $key = $request->RowID;
            $any = DB::table('acc_account_ledger')->where(['is_delete' => 0, 'parent_id' => $key])->count();
            // $branch_id = $DayEndData->branch_id;
            // $branch_date = $DayEndData->branch_date;

            // $checkdata = $Model::where('branch_id', '=', $branch_id)
            //     ->where('is_active', 0)
            //     ->where('branch_date', '>', $DayEndData->branch_date)
            //     ->count();

            if ($any > 0) {
                $response = array(
                    'text' => 'child'
                );
                return json_encode($response);
            } else {
                $voucherIdArr = DB::table('acc_voucher_details')
                    ->where(function ($query) use ($key) {
                        $query->where('debit_acc', $key)
                            ->orWhere('credit_acc', $key);
                    })
                    ->pluck('voucher_id')
                    ->toArray();

                $voucherDataM = DB::table('acc_voucher')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $voucherIdArr)
                    ->select('id')
                    ->count();

                // dd($voucherDebitData, $voucherCreditData);
                // $Ledger = $Model::where('id', $key)->first();
                // $Ledger->is_delete = 1;
                // $delete = $Ledger->update();
                // $DayEndData->is_active = 1;
                // $isSuccess = $DayEndData->update();

                // if ($isSuccess) {

                //     $deletedata = $Model::where('branch_id', '=', $branch_id)
                //         ->where('branch_date', '>', $DayEndData->branch_date)
                //         ->delete();

                //     return 'deleted';

                // } else {
                //     return 'db_error';
                // }

                if ($voucherDataM > 0) {
                    $response = array(
                        'text' => 'has_transaction',
                        'message' => Common::AccessDeniedReason('transaction')
                    );

                    return json_encode($response);
                } else {
                    $response = array(
                        'text' => 'ok'
                    );
                    return json_encode($response);
                }
            }
        }
    }

    /**
     * ajaxdDeleteCheck method used for checking data when delete function occur, This method is called via ajax
     * @param TableName - Database table name
     */
    public function ajaxdDeleteCheck(Request $request)
    {

        if ($request->ajax()) {
            $key        = $request->key;
            $columnname = base64_decode($request->columnname);
            $condition2 = base64_decode($request->condition2);
            $where1     = null;
            $where2     = null;

            if ($condition2 != null) {
                $conditionarr = explode(',', $condition2);
                $where1       = $conditionarr[0];
                $where2       = $conditionarr[1];
            }
            $table1      = base64_decode($request->table1);
            $table2      = base64_decode($request->table2);
            $table3      = base64_decode($request->table3);
            $QueryCheck  = 0;
            $QueryCheck1 = 0;
            $QueryCheck2 = 0;

            if ($table1 != null) {
                if ($condition2 != null) {
                    $QueryCheck = DB::table($table1)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck = DB::table($table1)->where($columnname, $key)->count();
                }
            }
            if ($table2 != null) {
                if ($condition2 != null) {
                    $QueryCheck1 = DB::table($table2)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck1 = DB::table($table2)->where($columnname, $key)->count();
                }
            }
            if ($table3 != null) {
                if ($condition2 != null) {
                    $QueryCheck2 = DB::table($table3)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck2 = DB::table($table3)->where($columnname, $key)->count();
                }
            }


            return $QueryCheck + $QueryCheck1 + $QueryCheck2;
        }
    }

    public function ajaxDeleteCheckPopUp(Request $request)
    {
        if ($request->ajax()) {
            $key        = decrypt($request->key);
            $columnname = base64_decode($request->columnname);
            $condition2 = base64_decode($request->condition2);
            $where1     = null;
            $where2     = null;

            if ($condition2 != null) {
                $conditionarr = explode(',', $condition2);
                $where1       = $conditionarr[0];
                $where2       = $conditionarr[1];
            }
            $table1      = base64_decode($request->table1);
            $table2      = base64_decode($request->table2);
            $table3      = base64_decode($request->table3);
            $QueryCheck  = 0;
            $QueryCheck1 = 0;
            $QueryCheck2 = 0;

            if ($table1 != null) {
                if ($condition2 != null) {
                    $QueryCheck = DB::table($table1)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck = DB::table($table1)->where($columnname, $key)->count();
                }
            }
            if ($table2 != null) {
                if ($condition2 != null) {
                    $QueryCheck1 = DB::table($table2)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck1 = DB::table($table2)->where($columnname, $key)->count();
                }
            }
            if ($table3 != null) {
                if ($condition2 != null) {
                    $QueryCheck2 = DB::table($table3)->where([$columnname => $key, $where1 => $where2])->count();
                } else {
                    $QueryCheck2 = DB::table($table3)->where($columnname, $key)->count();
                }
            }


            return $QueryCheck + $QueryCheck1 + $QueryCheck2;
        }
    }

    /**
     * ajaxCheckDuplicate method used for checking unique data, This method is called via ajax
     * @param TableName - Database table name
     */
    // public function ajaxCheckDuplicate(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $Model = 'App\\Model\\POS\\' . base64_decode($request->model);
    //         // dd($Model);
    //         $query = $request->get('query');
    //         $cond_where = $request->get('forWhich');

    //         $data = $Model::where($cond_where, $query)->first();
    //         // dd($data);

    //         if ($data) {
    //             return response()->json(array("exists" => true,"rowID" => $data->id));
    //         } else {
    //             return response()->json(array("exists" => false));
    //         }
    //     }
    // }

    /**
     * ajaxCheckDuplicate method used for checking unique data, This method is called via ajax
     * @param directory - Directory of Model, model - Model Name, forwhich - Field Name, query- value from input field
     */

    public function ajaxCheckDuplicate(Request $request)
    {
        if ($request->ajax()) {
            $table       = base64_decode($request->get('tableName'));
            $columnName  = $request->get('columnName');
            $columnValue = $request->get('columnValue');
            $editableID  = $request->get('editableID');

            $columnNameArr  = (!empty($columnName)) ? explode('&&', $columnName) : array();
            $columnValueArr = (!empty($columnValue)) ? explode('&&', $columnValue) : array();

            $whereCond = [];

            foreach ($columnNameArr as $k => $column) {
                if (isset($columnValueArr[$k])) {
                    array_push($whereCond, [$column, $columnValueArr[$k]]);
                }
            }

            if (count($whereCond) > 0) {
                $queryData = DB::table($table)
                    ->where($whereCond)
                    ->first();

                if ($queryData) {
                    if ($editableID == $queryData->id) {
                        return response()->json(array("exists" => 0));
                    } else {
                        return response()->json(array("exists" => 1));
                    }
                } else {
                    return response()->json(array("exists" => 0));
                }
                // // // 0 = Unique
                // // // 1 = Duplicate
            } else {
                // // // -1 = Condition Mis match
                return response()->json(array("exists" => -1));
            }
        }
    }

    public function ajaxSupplierInfo(Request $request)
    {
        if ($request->ajax()) {
            //    dd(1);
            $Data = DB::table('pos_suppliers')->where('id', $request->ID)->select('supplier_type', 'comission_percent')->first();

            // dd($Data, $request->ID);
            echo json_encode($Data);
        }
    }

    /**
     * ajaxBarcodeGenerate method used for generating barcode , This method is called via ajax
     * @param test - @Type - String -  test details
     */
    public function ajaxBarcodeGenerate(Request $request)
    {
        if ($request->ajax()) {

            $GroupID = $request->GroupID;
            $CatID   = $request->CatID;
            $BrandID = $request->BrandID;

            $GroupID = sprintf("%02d", $GroupID);
            $CatID   = sprintf("%03d", $CatID);
            $BrandID = sprintf("%03d", $BrandID);
            $newID   = sprintf("%05d", "1");

            $barcode_pre = $GroupID . $CatID . $BrandID;

            $data = PosProduct::query()
                ->select(['id', 'sys_barcode'])
                // ->where(['prod_group_id' => $request->GroupID, 'prod_cat_id' => $request->CatID, 'prod_brand_id' => $request->BrandID])
                ->where('sys_barcode', 'LIKE', "{$barcode_pre}%")
                ->orderBy('sys_barcode', 'DESC')->first();

            // dd($barcode_pre);

            if ($data) {
                $barcode = $data->sys_barcode + 0;
                $barcode += 1;
                $barcode = (string) $barcode;

                // dd($barcode, sprintf("%013s", $barcode));

                $barcode = sprintf("%013s", $barcode);
            } else {
                $barcode = $barcode_pre . $newID;
                $barcode = sprintf("%013s", $barcode);
            }

            $barcode_generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            $image             = '<img width="80%" src="data:image/png;base64,' . base64_encode($barcode_generator->getBarcode($barcode, $barcode_generator::TYPE_CODE_128)) . '">';

            $Data = [
                'barcode'   => $barcode,
                'bar_image' => $image,
            ];
            echo json_encode($Data);
        }
    }

    /**
     * ajaxBarcodeGenerateForShop method used for generating barcode for shop , This method is called via ajax
     * @param test - @Type - String -  test details
     */

    public function ajaxBarcodeGenerateForShop(Request $request)
    {
        if ($request->ajax()) {

            $GroupID = $request->GroupID;
            $CatID   = $request->CatID;
            $BrandID = $request->BrandID;

            $BAR_INITIAL = '';

            $com_prefix =  DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 10]])->first();

            if (!empty($com_prefix->form_value)) {
                $BAR_INITIAL = $com_prefix->form_value;
            } else {
                $BAR_INITIAL = 'TT';
            }

            $GroupID = sprintf("%02d", $GroupID);
            $CatID   = sprintf("%03d", $CatID);
            $BrandID = sprintf("%03d", $BrandID);
            $newID   = sprintf("%05d", "1");

            $barcode_pre = $BAR_INITIAL . $GroupID . $CatID . $BrandID;

            $data = PosProduct::query()
                ->select(['id', 'sys_barcode'])
                // ->where(['prod_group_id' => $request->GroupID, 'prod_cat_id' => $request->CatID, 'prod_brand_id' => $request->BrandID])
                ->where('sys_barcode', 'LIKE', "{$barcode_pre}%")
                ->orderBy('sys_barcode', 'DESC')->first();

            if ($data) {
                // $barcode = $data->sys_barcode;
                // $barcode += 1;
                // $barcode = (string) $barcode;

                $oldBarNum = explode($barcode_pre, $data->sys_barcode);
                $barcode   = $barcode_pre . sprintf("%05d", ($oldBarNum[1] + 1));

                // dd($barcode, sprintf("%013s", $barcode));

                $barcode = sprintf("%014s", $barcode);
            } else {
                $barcode = $barcode_pre . $newID;
                $barcode = sprintf("%014s", $barcode);
            }

            $barcode_generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            $image             = '<img width="80%" src="data:image/png;base64,' . base64_encode($barcode_generator->getBarcode($barcode, $barcode_generator::TYPE_CODE_128)) . '">';

            $Data = [
                'barcode'   => $barcode,
                'bar_image' => $image,
            ];
            echo json_encode($Data);
        }
    }

    public function ajaxBranchDate(Request $request)
    {
        $BranchId = $request->BranchId;
        $Module   = $request->Module;

        $BranchDate = Common::systemCurrentDate($BranchId, $Module);

        return $BranchDate;
    }

    /*     * ********************************** Bill No Generate Start */

    public function ajaxGenerateBillPurchase(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillPurchase($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillPurchaseReturn(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = POSS::generateBillPurchaseReturn($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillAudit(Request $request)
    {
        $BranchID = $request->BranchID;

        $BillNo = POSS::generateBillAudit($BranchID);

        return $BillNo;
    }

    public function ajaxGenerateBillPurchaseReturnInv(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = INVS::generateBillPurchaseReturn($BranchId);

        return $BillNo;
    }

    //////////// Fixed Asset Management //////////
    public function ajaxGenerateBillPurchaseReturnFam(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = FAMS::generateBillPurchaseReturn($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillIssue(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = POSS::generateBillIssue($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillIssueInv(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = INVS::generateBillIssue($BranchId);

        return $BillNo;
    }
    ///////////// Fixed Asset Management //////
    public function ajaxGenerateBillIssueFam(Request $request)
    {
        $BranchId = $request->BranchId;
        $BillNo   = FAMS::generateBillIssue($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillIssueReturn(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillIssueReturn($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillIssueReturnInv(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = INVS::generateBillIssueReturn($BranchId);

        return $BillNo;
    }

    //////////// Fixed Asset Management //////////

    public function ajaxGenerateBillIssueReturnFam(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = FAMS::generateBillIssueReturn($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillTransfer(Request $request)
    {

        $BranchId = $request->BranchID;

        $BillNo = POSS::generateBillTransfer($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillTransferInv(Request $request)
    {

        $BranchId = $request->BranchID;

        $BillNo = INVS::generateBillTransfer($BranchId);

        return $BillNo;
    }

    /////////////   Fixed Asset Management ////////

    public function ajaxGenerateBillTransferFam(Request $request)
    {

        $BranchId = $request->BranchID;

        $BillNo = FAMS::generateBillTransfer($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillVoucher(Request $request)
    {
        $voucherType    = $request->vouchertype;
        $projectID      = $request->projectID;
        $project_typeID = $request->project_typeID;
        $BranchID       = $request->BranchID;

        $BillNo = ACCS::generateBillVoucher($BranchID, $voucherType, $projectID, $project_typeID);

        return $BillNo;
    }

    public function ajaxProductLoadUses(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            //$CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('inv_products')
                ->select(['id', 'product_name', 'cost_price', 'product_code'])
                ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select Product</option>';
            foreach ($QueryData as $Row) {

                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->product_code . '"
                                    pbarcode="' . $Row->product_code . '"
                                    pcprice="' . $Row->cost_price . '">';

                if (!empty($Row->product_code)) {
                    $output .= $Row->product_name . ' [' . $Row->product_code . ']';
                } else {
                    $output .= $Row->product_name;
                }

                $output .= '</option>';
            }

            echo $output;
        }
    }

    // public function ajaxGenerateBillSales(Request $request)
    // {
    //     // // $companyID = $request->compID;
    //     // $branchID = $request->branchID;
    //     // $branchCode = Branch::where(['is_delete' => 0, 'is_approve' => 1, 'id' => $branchID])
    //     //     ->select('branch_code')
    //     //     ->first();
    //     // $ldate = date('Ym');
    //     // $PreBillNo = "SL" . $branchCode->branch_code . $ldate;
    //     // $record = SalesMaster::where('branch_id', $branchID)
    //     //     ->select(['id', 'sales_bill_no'])
    //     //     ->where('sales_bill_no', 'LIKE', "{$PreBillNo}%")
    //     //     ->orderBy('sales_bill_no', 'DESC')
    //     //     ->first();
    //     // // dd($record);
    //     // if ($record) {
    //     //     $OldBillNoA = explode($PreBillNo, $record->sales_bill_no);
    //     //     $BillNo = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
    //     // } else {
    //     //     $BillNo = $PreBillNo . sprintf("%05d", 1);
    //     // }
    //     // return $BillNo;
    //     $BranchId = $request->BranchID;
    //     $BillNo = POSS::generateBillSales($BranchId);
    //     return $BillNo;
    // }

    public function ajaxGenerateBillSalesReturn(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillSalesReturn($BranchId);

        return $BillNo;
    }
    public function ajaxGenerateBillShopSalesReturn(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillShopSalesReturn($BranchId);

        return $BillNo;
    }

    public function ajaxGenerateBillUsessReturn(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = INVS::generateBillUsesReturn($BranchId);

        return $BillNo;
    }

    /*     * ****************************** Bill No Generate End */

    /*     * *************************** Product Load of Transaction Portion Start */

    // ajaxProductLoad

    public function ajaxProductLoadPurchaseBackupTobedeletded(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID       = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $groupWiseProduct = (isset($request->groupWiseProduct)) ? $request->groupWiseProduct : false;
            $groupWiseCostShow = (isset($request->groupWiseCostShow) && $request->groupWiseCostShow == "true") ? true : false;
            $groupBy = (isset($request->groupBy) && $request->groupBy == "false") ? false : true;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            ## price update list
            $updatedPriceList = POSS::fnUpdatedSalesPrice_Multiple(Common::systemCurrentDate());
            $output           = '<option value="">Select One</option>';



            if ($groupWiseProduct == true) {
                // Query
                $queryData = PosProduct::where([['is_delete', '=', 0], ['is_active', '=', 1]])
                    ->where(function ($queryData) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $queryData->where('supplier_id', $SupplierID);
                        }

                        if (!empty($GroupID)) {
                            $queryData->where('prod_group_id', $GroupID);
                        }

                        if (!empty($CategoryID)) {
                            $queryData->where('prod_cat_id', $CategoryID);
                        }

                        if (!empty($SubCatID)) {
                            $queryData->where('prod_sub_cat_id', $SubCatID);
                        }

                        if (!empty($ModelID)) {
                            $queryData->where('prod_model_id', $ModelID);
                        }
                    });
                if ($groupBy == true) {
                    $queryData = $queryData->groupBy(
                        'prod_group_id',
                        'prod_cat_id',
                        'prod_sub_cat_id',
                        'prod_brand_id',
                        'prod_model_id',
                        'prod_size_id',
                        'prod_color_id',
                        'product_name'
                    );
                }

                $queryData = $queryData->orderBy('product_name', 'ASC')
                    ->get();

                foreach ($queryData as $row) {

                    $updatedPrice = (isset($updatedPriceList[$row->id])) ? $updatedPriceList[$row->id] : $row->sale_price;

                    $output .= '<option value="' . $row->id . '"
                                    pname= "' . $row->product_name . '"
                                    pbarcode="' . $row->prod_barcode . '"
                                    pcprice="' . $row->cost_price . '"
                                    data-info="<b style=\'color:#804739;\'>' . $row->product_name . '</b>';
                    if ($groupBy != true) {
                        $output .=  ' [' . $row->prod_barcode . ']';
                    }
                    $output .= '<br>
                                    <small>
                                        <b>Group: </b>' . $row->pgroup->group_name . ' ||
                                        <b>Category: </b>' . $row->category->cat_name . ' ||
                                        <b>Sub-Category: </b>' . $row->subcategory->sub_cat_name . '<br>
                                        <b>Brand: </b>' . $row->brand->brand_name . ' ||
                                        <b>Model: </b>' . $row->model->model_name . ' ||
                                        <b>Size: </b>' . $row->size->size_name . ' ||
                                        <b>Color: </b>' . $row->color->color_name;
                    if ($groupWiseCostShow == true) {
                        $output .=  ' || <span style=\'color:#804739;\'><b>CP:</b> ' . $row->cost_price . '</span>';
                    }
                    $output .= ' </small>">';

                    $output .= $row->product_name . ' [' . $row->prod_barcode . ']';
                    $output .= '</option>';
                }
            } else {
                // Query
                $QueryData = DB::table('pos_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();

                foreach ($QueryData as $Row) {

                    $updatedPrice = (isset($updatedPriceList[$Row->id])) ? $updatedPriceList[$Row->id] : $Row->sale_price;

                    $output .= '<option value="' . $Row->id . '"
                                        pname= "' . $Row->product_name . '"
                                        sbarcode="' . $Row->sys_barcode . '"
                                        pbarcode="' . $Row->prod_barcode . '"
                                        pcprice="' . $Row->cost_price . '"
                                        psprice="' . $updatedPrice . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }

    ## ajaxProductLoad json data

    public function ajaxProductLoadPurchase(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID       = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $groupWiseProduct = (isset($request->groupWiseProduct)) ? $request->groupWiseProduct : false;
            $groupWiseCostShow = (isset($request->groupWiseCostShow) && $request->groupWiseCostShow == "true") ? true : false;
            $groupBy = (isset($request->groupBy) && $request->groupBy == "false") ? false : true;


            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            ## price update list
            $updatedPriceList = POSS::fnUpdatedSalesPrice_Multiple(Common::systemCurrentDate());


            if ($groupWiseProduct == true) {
                // Query
                $queryData = PosProduct::where([['is_delete', '=', 0], ['is_active', '=', 1]])
                    ->where(function ($queryData) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $queryData->where('supplier_id', $SupplierID);
                        }

                        if (!empty($GroupID)) {
                            $queryData->where('prod_group_id', $GroupID);
                        }

                        if (!empty($CategoryID)) {
                            $queryData->where('prod_cat_id', $CategoryID);
                        }

                        if (!empty($SubCatID)) {
                            $queryData->where('prod_sub_cat_id', $SubCatID);
                        }

                        if (!empty($ModelID)) {
                            $queryData->where('prod_model_id', $ModelID);
                        }
                    });
                if ($groupBy == true) {
                    $queryData = $queryData->groupBy(
                        'prod_group_id',
                        'prod_cat_id',
                        'prod_sub_cat_id',
                        'prod_brand_id',
                        'prod_model_id',
                        'prod_size_id',
                        'prod_color_id',
                        'product_name'
                    );
                }

                $queryData = $queryData->orderBy('product_name', 'ASC')
                    ->get()->load('pgroup', 'category', 'subcategory', 'brand', 'model', 'size', 'color');
            } else {
                // Query
                $queryData = DB::table('pos_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();
            }


            $data = array(
                'queryData' => $queryData,
                'groupWiseCostShow' => $groupWiseCostShow,
                'groupWiseProduct' => ($groupWiseProduct == true) ? true : false,
                'updatedPriceList' => $updatedPriceList,
                'groupBy' => $groupBy,
            );

            return response()->json($data);
        }
    }


    public function ajaxProductLoadPurchaseInv(Request $request)
    {
        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            $groupWiseProduct = (isset($request->groupWiseProduct)) ? $request->groupWiseProduct : false;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            if ($groupWiseProduct == true) {
                // Query
                $queryData = DB::table('inv_products')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $queryData->where('supplier_id', $SupplierID);
                        }

                        if (!empty($GroupID)) {
                            $queryData->where('prod_group_id', $GroupID);
                        }

                        if (!empty($CategoryID)) {
                            $queryData->where('prod_cat_id', $CategoryID);
                        }

                        if (!empty($SubCatID)) {
                            $queryData->where('prod_sub_cat_id', $SubCatID);
                        }

                        if (!empty($ModelID)) {
                            $queryData->where('prod_model_id', $ModelID);
                        }
                    })
                    ->groupBy(
                        'prod_group_id',
                        'prod_cat_id',
                        'prod_sub_cat_id',
                        'prod_brand_id',
                        'prod_model_id',
                        'prod_size_id',
                        'prod_color_id',
                        'product_name'
                    )
                    ->orderBy('product_name', 'ASC')
                    ->get();
                $output = '<option value="">Select One</option>';
                foreach ($queryData as $row) {

                    $output .= '<option value="' . $row->id . '"
                                    pname= "' . $row->product_name . '"
                                    pbarcode="' . $row->product_code . '"
                                    data-info="<b style=\'color:#804739;\'>' . $row->product_name . '</b> <br>
                                    <small>
                                        <b>Group: </b>' . $row->pgroup->group_name . ' ||
                                        <b>Category: </b>' . $row->category->cat_name . ' ||
                                        <b>Sub-Category: </b>' . $row->subcategory->sub_cat_name . '<br>
                                        <b>Brand: </b>' . $row->brand->brand_name . ' ||
                                        <b>Model: </b>' . $row->model->model_name . ' ||
                                        <b>Size: </b>' . $row->size->size_name . ' ||
                                        <b>Color: </b>' . $row->color->color_name . '
                                    </small>"
                                >';
                    $output .= $row->product_name . ($row->product_code ? ' (' . $row->product_code . ')' : '');
                    $output .= '</option>';
                }
            } else {
                // Query
                $QueryData = DB::table('inv_products')
                    ->select(['id', 'product_name', 'cost_price', 'product_code'])
                    ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();

                // dd( $QueryData);

                $output = '<option value="">Select One</option>';
                foreach ($QueryData as $Row) {

                    $output .= '<option value="' . $Row->id . '"
                                        pname= "' . $Row->product_name . '"
                                        sbarcode="' . $Row->product_code . '"
                                        pcprice="' . $Row->cost_price . '">';
                    $output .= $Row->product_name . ($Row->product_code ? ' [' . $Row->product_code . ']' : '');
                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }
    ////////////  Fixed Asset Management
    public function ajaxProductLoadPurchaseFam(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('fam_products')
                ->select(['id', 'product_name', 'cost_price', 'product_code'])
                ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            // dd( $QueryData);

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->product_code . '"
                                    pcprice="' . $Row->cost_price . '">';
                $output .= $Row->product_name . ($Row->product_code ? ' [' . $Row->product_code . ']' : '');
                $output .= '</option>';
            }

            echo $output;
        }
    }
    ////////////  Fixed Asset Management
    public function ajaxProductLoadFam(Request $request)
    {

        if ($request->ajax()) {

            $NameID     = (isset($request->NameID)) ? $request->NameID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            $BranchID   = (isset($request->BranchID)) ? $request->BranchID : null;
            $TypeID     = (isset($request->TypeID)) ? $request->TypeID : null;

            $Requisition = (isset($request->Requisition)) ? $request->Requisition : null;

            $groupWiseProduct = (isset($request->groupWiseProduct)) ? $request->groupWiseProduct : false;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $TypeArr     = (!empty($TypeID)) ? ['prod_type_id', '=', $TypeID] : ['prod_type_id', '<>', 3];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $BranchArr   = (!empty($BranchID)) ? ['branch_id', '=', $BranchID] : ['branch_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $NameArr     = (!empty($NameID)) ? ['prod_name_id', '=', $NameID] : ['prod_name_id', '<>', ''];

            if ($groupWiseProduct == true) {
                // Query
                $queryData = DB::table('fam_products')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($GroupID, $CategoryID, $SubCatID) {
                        // $SupplierID,
                        // if (!empty($SupplierID)) {
                        //     $queryData->where('supplier_id', $SupplierID);
                        // }

                        if (!empty($GroupID)) {
                            $queryData->where('prod_group_id', $GroupID);
                        }

                        if (!empty($CategoryID)) {
                            $queryData->where('prod_cat_id', $CategoryID);
                        }

                        if (!empty($SubCatID)) {
                            $queryData->where('prod_sub_cat_id', $SubCatID);
                        }

                        // $ModelID
                        // if (!empty($ModelID)) {
                        //     $queryData->where('prod_model_id', $ModelID);
                        // }
                    })
                    ->groupBy(
                        'prod_group_id',
                        'prod_cat_id',
                        'prod_sub_cat_id',
                        'prod_brand_id',
                        'prod_model_id',
                        'prod_size_id',
                        'prod_color_id',
                        'prod_type_id',
                        'prod_name_id'
                    )
                    ->orderBy('prod_name_id', 'ASC')
                    ->get();

                $output = "";
                foreach ($queryData as $row) {

                    $output .= '<option value="' . $row->id . '"
                                    pname= "' . $row->product_name . '"
                                    pbarcode="' . $row->prod_barcode . '"
                                    data-info="<b style=\'color:#804739;\'>' . $row->product_name . '</b> <br>
                                    <small>
                                        <b>Group: </b>' . $row->pgroup->group_name . ' ||
                                        <b>Category: </b>' . $row->category->cat_name . ' ||
                                        <b>Sub-Category: </b>' . $row->subcategory->sub_cat_name . '<br>
                                        <b>Brand: </b>' . $row->brand->brand_name . ' ||
                                        <b>Model: </b>' . $row->model->model_name . ' ||
                                        <b>Size: </b>' . $row->size->size_name . ' ||
                                        <b>Color: </b>' . $row->color->color_name . '
                                    </small>"
                                >';
                    $output .= $row->product_name . ' (' . $row->prod_barcode . ')';
                    $output .= '</option>';
                }
            } else {
                // Query
                $QueryData = DB::table('fam_products')
                    ->select(['id', 'product_name', 'cost_price', 'product_code'])
                    ->where([['is_delete', '=', 0], $GroupArr, $CategoryArr, $SubCatArr])
                    // $SupplierArr, $ModelArr
                    ->orderBy('product_name', 'ASC')
                    ->get();

                // dd( $QueryData);

                $output = '<option value="">Select One</option>';
                foreach ($QueryData as $Row) {

                    $output .= '<option value="' . $Row->id . '"
                                        pname= "' . $Row->product_name . '"
                                        sbarcode="' . $Row->product_code . '"
                                        pcprice="' . $Row->cost_price . '">';
                    $output .= $Row->product_name . ($Row->product_code ? ' (' . $Row->product_code . ')' : '');
                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }

    /**
     * ajaxBarcodeGenerate method used for generating barcode , This method is called via ajax
     * @param test - @Type - String -  test details
     */
    public function ajaxProductCodeFam(Request $request)
    {
        $BranchId     = $request->branch_id;
        $project_id   = $request->project_id;
        $product_id   = $request->product_id;
        $prod_type_id = $request->prod_type_id;

        ##incomplete function
        $BillNo = FAMS::generateProductCode($BranchId, $product_id, $project_id, $prod_type_id);

        return $BillNo;
    }
    /////////////////////////////////////

    public function ajaxProductLoadPurchaseReturn(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->sys_barcode . '"
                                    pbarcode="' . $Row->prod_barcode . '"
                                    pcprice="' . $Row->cost_price . '"
                                    psprice="' . $Row->sale_price . '" >';
                $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                $output .= '</option>';
            }

            echo $output;
        }
    }

    public function ajaxProductLoadIssue(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->sys_barcode . '"
                                    pbarcode="' . $Row->prod_barcode . '"
                                    pcprice="' . $Row->cost_price . '"
                                    psprice="' . $Row->sale_price . '" >';
                $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                $output .= '</option>';
            }

            echo $output;
        }
    }

    public function ajaxProductLoadIssueReturn(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $branchID = (isset($request->branch_from)) ? $request->branch_from : null;

            //$CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            $TypeID     = (isset($request->prod_type_id)) ? $request->prod_type_id : null;
            $TypeArr     = (!empty($TypeID)) ? ['prod_type_id', '=', $TypeID] : ['prod_type_id', '<>', 3];

            // ## Branch wise product load from pos_issue_d
                // if (!empty($branchID)) {
                //     $previousProdIssueAdded = DB::table('pos_issues_m as pim')
                //         ->where([['pim.is_delete', '=', 0], ['pim.is_active', '=', 1]])
                //         ->where('pid.branch_to', $branchID)
                //         ->join('pos_issues_d as pid', function ($SaleD) {
                //             $SaleD->on('pid.issue_bill_no', '=', 'pim.bill_no');
                //         })
                //         ->select('pid.product_id', 'pid.product_quantity')
                //         ->groupBy('pid.product_id')
                //         ->pluck('pid.product_id')
                //         ->toArray();
                // }

                // // Query
                // $QueryData = DB::table('pos_products')
                //     ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                //     ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr])
                //     ->whereIn('id', $previousProdIssueAdded)
                //     ->orderBy('product_name', 'ASC')
                //     ->get();
            // ## Branch wise product load from pos_issue_d

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {
                $cp_set = !empty($Row->cost_price) && $Row->cost_price != '0.00' && $Row->cost_price != '0' ? '(CP: ' . Common::getDecimalValue($Row->cost_price) . ')' : '';

                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->sys_barcode . '"
                                    pbarcode="' . $Row->prod_barcode . '"
                                    pcprice="' . $Row->cost_price . '"
                                    psprice="' . $Row->sale_price . '" >';

                $output .= $Row->product_name . ' [' . $Row->prod_barcode . '] ' . $cp_set;
                $output .= '</option>';
            }

            echo $output;
        }
    }

    /* Get Product Barcode by selecting product model for Transfer */

    public function ajaxProductLoadTransfer(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            $TypeID     = (isset($request->prod_type_id)) ? $request->prod_type_id : null;
            $TypeArr     = (!empty($TypeID)) ? ['prod_type_id', '=', $TypeID] : ['prod_type_id', '<>', 3];

            //$CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            $transfer_bill = (isset($request->transfer_bill)) ? $request->transfer_bill : null;

            if (!empty($transfer_bill)) {
                $TransferDetailsData = DB::table('pos_transfers_d')->where('transfer_bill_no', $transfer_bill)
                    ->select('product_id', 'product_quantity')
                    ->pluck('product_quantity', 'product_id')
                    ->toArray();
            }

            // dd($TransferDetailsData);

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            ##stock check code variable
            ## formId 15 = stockwise product load
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

            ##stock check code variable
            $BranchID  = (isset($request->branch_from)) ? $request->branch_from : Common::getBranchId();

            $sDate     = null;
            $eDate     = Common::systemCurrentDate($BranchID, 'pos');
            // $checkstock = true;
            $FindStock = array();
            ##stock check code variable end

            ###stock check code
            if ($checkstock) {
                $FindStock = POSS::stockQuantity_Multiple($BranchID, $QueryData->pluck('id')->toArray(), $sDate, $eDate);
            }
            ###stock check code end

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {
                ###stock check code
                #default stock 1
                $stock = 1;
                if ($checkstock) {
                    $stock = $FindStock[$Row->id]['Stock'];
                    ##edit transfer details data if any then plus is with stocck
                    if (isset($TransferDetailsData[$Row->id])) {
                        $stock +=  $TransferDetailsData[$Row->id];
                    }
                }
                ###stock check end
                $mrp_set = !empty($Row->sale_price) && $Row->sale_price != '0.00' && $Row->sale_price != '0' ? '(MRP: ' . Common::getDecimalValue($Row->sale_price) . ')' : '';
                #stock check condition
                if ($stock >= 1) {
                    $output .= '<option value="' . $Row->id . '"
                    pname= "' . $Row->product_name . '"
                    sbarcode="' . $Row->sys_barcode . '"
                    pbarcode="' . $Row->prod_barcode . '"
                    stock="' . $stock . '"
                    pcprice="' . $Row->cost_price . '"
                    psprice="' . $Row->sale_price . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']' . $mrp_set;
                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }

    public function ajaxProductLoadPurReturn(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            $TypeID     = (isset($request->prod_type_id)) ? $request->prod_type_id : null;
            $TypeArr     = (!empty($TypeID)) ? ['prod_type_id', '=', $TypeID] : ['prod_type_id', '<>', 3];

            //$CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];
            ##stock check code variable
            $BranchID  = (isset($request->branch_id)) ? $request->branch_id : Common::getBranchId();

            $bill_no = (isset($request->bill_no)) ? $request->bill_no : null;

            if (!empty($bill_no)) {
                $previousBillAddedQnt = DB::table('pos_purchases_r_d')->where('pr_bill_no', $bill_no)
                    ->select('product_id', 'product_quantity')
                    ->pluck('product_quantity', 'product_id')
                    ->toArray();
            }
            // dd( $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr);

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            ##stock check code variable
            ## formId 15 = stockwise product load
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

            $sDate     = null;
            $eDate     = Common::systemCurrentDate($BranchID, 'pos');
            // $checkstock = true;
            $FindStock = array();
            ##stock check code variable end

            ###stock check code
            if ($checkstock) {
                $FindStock = POSS::stockQuantity_Multiple($BranchID, $QueryData->pluck('id')->toArray(), $sDate, $eDate);
            }
            ###stock check code end
            // dd($FindStock);

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {
                ###stock check code
                #default stock 1
                $stock = 1;
                if ($checkstock) {
                    $stock = $FindStock[$Row->id]['Stock'];
                    ##edit transfer details data if any then plus is with stocck
                    if (isset($previousBillAddedQnt[$Row->id])) {
                        $stock +=  $previousBillAddedQnt[$Row->id];
                    }
                }
                ###stock check end
                $cp_set = !empty($Row->cost_price) && $Row->cost_price != '0.00' && $Row->cost_price != '0' ? '(CP: ' . Common::getDecimalValue($Row->cost_price) . ')' : '';

                #stock check condition
                if ($stock >= 1) {
                    $output .= '<option value="' . $Row->id . '"
                    pname= "' . $Row->product_name . '"
                    sbarcode="' . $Row->sys_barcode . '"
                    pbarcode="' . $Row->prod_barcode . '"
                    stock="' . $stock . '"
                    pcprice="' . $Row->cost_price . '"
                    psprice="' . $Row->sale_price . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . '] ' . $cp_set;
                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }

    public function ajaxProductLoadTransferInv(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            //$CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('inv_products')
                ->select(['id', 'product_name', 'product_code'])
                ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $row) {

                $output .= '<option value="' . $row->id . '"
                                    pname= "' . $row->product_name . '"
                                    sbarcode="' . $row->product_code . '"';
                $output .= ($row->product_code != '' ? $row->product_name . ' - ' . $row->product_code : $row->product_name);
                $output .= '</option>';
            }

            echo $output;
        }
    }

    ///////// Fixed Asset Management //////////
    public function ajaxProductLoadTransferFamss(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            //$CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('fam_products')
                ->select(['id', 'product_name', 'product_code'])
                ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $row) {

                $output .= '<option value="' . $row->id . '"
                                    pname= "' . $row->product_name . '"
                                    sbarcode="' . $row->product_code . '"';
                $output .= ($row->product_code != '' ? $row->product_name . ' - ' . $row->product_code : $row->product_name);
                $output .= '</option>';
            }

            echo $output;
        }
    }

    public function ajaxProductLoadSales(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            //$CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $TypeID     = (isset($request->prod_type_id)) ? $request->prod_type_id : null;

            $TypeArr     = (!empty($TypeID)) ? ['prod_type_id', '=', $TypeID] : ['prod_type_id', '<>', 3];

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            $sales_bill = (isset($request->bill_no)) ? $request->bill_no : null;

            if (!empty($sales_bill)) {
                $SalesDetailsData = DB::table('pos_sales_d')->where('sales_bill_no', $sales_bill)
                    ->select('product_id', 'product_quantity')
                    ->pluck('product_quantity', 'product_id')
                    ->toArray();
            }

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $TypeArr])
                ->orderBy('product_name', 'ASC')
                ->orderBy('prod_barcode', 'ASC')
                ->get();

            ##stock check code variable
            ## formId 15 = stockwise product load
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

            ##stock check code variable
            if (isset($request->branchId)) {
                $BranchID = $request->branchId;
            } elseif (isset($request->branch_from)) {
                $BranchID = $request->branch_from;
            } else {
                $BranchID = Common::getBranchId();
            }

            $sDate     = null;
            $current_date     = Common::systemCurrentDate($BranchID, 'pos');
            // $checkstock = true;
            $FindStock = array();
            ##stock check code variable end

            ###stock check code
            if ($checkstock) {
                $FindStock = POSS::stockQuantity_Multiple($BranchID, $QueryData->pluck('id')->toArray(), $sDate, $current_date);
            }
            ###stock check code end

            ## price update list
            $updatedPriceList = POSS::fnUpdatedSalesPrice_Multiple($current_date);

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {
                ###stock check code
                #default stock 1
                $stock = 1;
                if ($checkstock) {
                    $stock = $FindStock[$Row->id]['Stock'];

                    if (isset($SalesDetailsData[$Row->id])) {
                        $stock +=  $SalesDetailsData[$Row->id];
                    }
                }
                ###stock check end
                #stock check condition

                if ($stock >= 1) {
                    $updatedPrice = (isset($updatedPriceList[$Row->id])) ? $updatedPriceList[$Row->id] : $Row->sale_price;
                    $mrp_set = !empty($updatedPrice) && $updatedPrice != '0.00' && $updatedPrice != '0' ? '(MRP: ' . Common::getDecimalValue($updatedPrice) . ')' : '';

                    $output .= '<option value="' . $Row->id . '"
                                        pname= "' . $Row->product_name . '"
                                        sbarcode="' . $Row->sys_barcode . '"
                                        pbarcode="' . $Row->prod_barcode . '"
                                        pcprice="' . $Row->cost_price . '"
                                        psprice="' . $updatedPrice . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';

                    if ($TypeID != 3) {
                        $output .= $mrp_set;
                    }

                    $output .= '</option>';
                }
            }

            echo $output;
        }
    }

    public function ajaxProductLoadSalesReturn(Request $request)
    {

        if ($request->ajax()) {

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            //$CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            // $CompanyArr = (!empty($CompanyID)) ? ['company_id', '=', $CompanyID] : ['company_id', '<>', ''];
            $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
            $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
            $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
            $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
            $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

            // Query
            $QueryData = DB::table('pos_products')
                ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                ->orderBy('product_name', 'ASC')
                ->get();
            ## price update list
            $updatedPriceList = POSS::fnUpdatedSalesPrice_Multiple(Common::systemCurrentDate());

            $output = '<option value="">Select One</option>';
            foreach ($QueryData as $Row) {

                $updatedPrice = (isset($updatedPriceList[$Row->id])) ? $updatedPriceList[$Row->id] : $Row->sale_price;
                $output .= '<option value="' . $Row->id . '"
                                    pname= "' . $Row->product_name . '"
                                    sbarcode="' . $Row->sys_barcode . '"
                                    pbarcode="' . $Row->prod_barcode . '"
                                    pcprice="' . $Row->cost_price . '"
                                    psprice="' . $updatedPrice . '" >';
                $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                $output .= '</option>';
            }

            echo $output;
        }
    }

    /* End */

    public function ajaxSalebillDetails(Request $request)
    {

        if ($request->ajax()) {

            ## Get the sales bill number and return bill number from the request
            $bill         = $request->SalesBillNo;
            $returnBillNo = (isset($request->returnBillNo)) ? $request->returnBillNo : null;

            ## Fetch data from SalesMaster and ShopSalesMaster tables
            ## In futuer fetching data from single table, may be table name will be salesM
            $cashSaleM = SalesMaster::where([['sales_bill_no', $bill], ['is_delete', 0], ['is_active', 1]])->get();
            $shopSaleM = ShopSalesMaster::where([['sales_bill_no', $bill], ['is_delete', 0], ['is_active', 1]])->get();

            ## Merge the data from both tables
            $SaleM = $cashSaleM->merge($shopSaleM);

            ## Filter the merged data to find the specific record with the given sales bill number
            $SaleM = collect($SaleM)->where('sales_bill_no', $bill)->first();

            ## Get the branch date using Common::systemCurrentDate() method
            $branch_date = Common::systemCurrentDate($SaleM->branch_id, 'pos');

            ## Fetch branch data from the gnl_branchs table
            $BranchData = DB::table("gnl_branchs")->where('id', $SaleM->branch_id)->first();

            ## Fetch data from pos_sales_d and pos_shop_sales_d tables with pos_products joins
            ## In futuer fetching data from single table, may be table name will be salesD
            $cashSaleD = DB::table('pos_sales_d as psd')
                ->where('sales_bill_no', $bill)
                ->select('psd.*', 'prod.product_name')
                ->leftjoin('pos_products as prod', function ($SaleD) {
                    $SaleD->on('prod.id', '=', 'psd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->get();

            $shopSaleD = DB::table('pos_shop_sales_d as pssd')
                ->where('sales_bill_no', $bill)
                ->select('pssd.*', 'prod.product_name')
                ->leftjoin('pos_products as prod', function ($SaleD) {
                    $SaleD->on('prod.id', '=', 'pssd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->get();
            ## Merge data from both queries
            $SaleD = $cashSaleD->merge($shopSaleD);

            $getDiscount = POSS::fnGetDiscount($SaleM);

            if($getDiscount['Discount'] == false || $getDiscount['dis_type'] == "Regular"){

                $totalAmt = $SaleM->total_amount;
                $dis_rate = $SaleM->discount_rate;
                $dis_amount = round(($totalAmt * $dis_rate) / 100);

                $totalAmt = $totalAmt - round(($totalAmt * $dis_rate) / 100);

            } else {

                $totalAmt = $SaleM->total_amount - $getDiscount['Amount'];
            }

            $sl           = 1;
            $output       = '';
            $option       = '<option value="">Select Product</option>';

            $totalQnt  = 0;
            $totalRetQnt  = 0;
            $totalAmount  = 0;
            $totalDisAmount  = 0;
            $totalAmountWithDis  = 0;
            $updatedPrice = 0;

            ## Loop through the merged SaleD data
            if (count($SaleD->toArray()) > 0) {
                foreach ($SaleD as $Row) {

                    $productID = $Row->product_id;

                    $saleReturnData = DB::table('pos_sales_return_m as sm')
                        ->where('sm.sales_bill_no', $Row->sales_bill_no)
                        ->where([['sm.is_delete', 0], ['sm.is_active', 1]])
                        ->where(function ($query) use ($returnBillNo) {

                            if (!empty($returnBillNo)) {
                                $query->where('sm.return_bill_no', '<>', $returnBillNo);
                            }
                        })
                        ->join('pos_sales_return_d as sd', function ($query) use ($productID) {
                            $query->on('sm.return_bill_no', '=', 'sd.return_bill_no')
                                ->where('sd.product_id', $productID);
                        })
                        ->get();

                    $countdata = $saleReturnData->where('return_bill_no', '<>', $returnBillNo)->sum('product_quantity');

                    $updatedPrice           = POSS::fnForUpdatedSalesPrice($productID, $SaleM->sales_date);
                    $unitPrice              = $updatedPrice ? $updatedPrice : $Row->product_unit_price;
                    $eachProdTSalesPrice    = $Row->product_quantity * $unitPrice;

                    $discountAmount         = round(($eachProdTSalesPrice * $dis_rate) / 100);
                    $eachProdTAmount        = $eachProdTSalesPrice - $discountAmount;
                    $mrp_set = !empty($unitPrice) && $unitPrice != '0.00' && $unitPrice != '0' ? ' [MRP: ' . Common::getDecimalValue($unitPrice) . ']' : '';

                    $output .= '<tr>';
                        $output .= '<td class="text-center">' . $sl++ . '</td>';
                        $output .= '<td class="text-left">' . $Row->product_name . ' (' . $Row->product_barcode . ')' . $mrp_set . '</td>';
                        $output .= '<td class="text-center">' . $Row->product_quantity . '</td>';
                        $output .= '<td class="text-center">' . $countdata . '</td>';
                        $output .= '<td class="text-right">' . Common::getDecimalValue($unitPrice) . '</td>';
                        $output .= '<td class="text-right">' . Common::getDecimalValue($eachProdTSalesPrice) . '</td>';
                        $output .= '<td class="text-right">' . Common::getDecimalValue($discountAmount) . '</td>';
                        $output .= '<td class="text-right">' . Common::getDecimalValue($eachProdTAmount) . '</td>';
                    $output .= '</tr>';

                    if ($countdata < $Row->product_quantity) {

                        // $stockQnt = $Row->product_quantity - $countdata;
                        $stockQnt = $Row->product_quantity;

                        $option .= '<option
                            value="' . $Row->product_id .
                            '" pcprice="' . $Row->product_cost_price .
                            '" psprice="' . $Row->product_unit_price .
                            '" pname="' . $Row->product_name .
                            '" sbarcode="' . $Row->product_system_barcode .
                            '" pbarcode="' . $Row->product_barcode .
                            '" pquantity="' . $stockQnt .
                            '" retquantity="' . $countdata .
                            '">' . $Row->product_name . ' [' . $Row->product_barcode . '] ' . $mrp_set;
                        $option .= '</option>';
                    }

                    $totalQnt += $Row->product_quantity;
                    $totalRetQnt += $countdata;

                    $totalAmount += $eachProdTSalesPrice;
                    $totalDisAmount += $discountAmount;
                    $totalAmountWithDis += $eachProdTAmount;
                }

                $output .= '<tr>';
                    $output .= '<td class="text-right text-uppercase" colspan="2"><strong>Total</strong></td>';
                    $output .= '<td class="text-center"><strong>' . $totalQnt . '</strong></td>';
                    $output .= '<td class="text-center"><strong>' . $totalRetQnt . '</strong></td>';
                    // $output .= '<td></td>';
                    $output .= '<td></td>';
                    $output .= '<td class="text-right"><strong>' . Common::getDecimalValue($totalAmount) . '</strong></td>';
                    $output .= '<td class="text-right"><strong>' . Common::getDecimalValue($totalDisAmount) . '</strong></td>';
                    $output .= '<td class="text-right"><strong>' . Common::getDecimalValue($totalAmountWithDis) . '</strong></td>';
                $output .= '</tr>';
            }

            ## Prepare the response array
            $response = [
                'master'        => $SaleM,
                'option'        => $option,
                'tbody'         => $output,
                'branch_date'   => $branch_date,
                'branch_name'   => $BranchData->branch_name . " [" . $BranchData->branch_code . "]",
            ];

            ## Encode and output the response as JSON
            echo json_encode($response);
        }
    }

    public function ajaxSalebillDetailsBackUp(Request $request)
    {

        if ($request->ajax()) {

            $bill         = $request->SalesBillNo;
            $returnBillNo = (isset($request->returnBillNo)) ? $request->returnBillNo : null;

            // Query
            $SaleM = SalesMaster::where('sales_bill_no', $bill)->first();

            $branch_date = Common::systemCurrentDate($SaleM->branch_id, 'pos');

            $BranchData = DB::table("gnl_branchs")->where('id', $SaleM->branch_id)->first();

            $SaleD = DB::table('pos_sales_d as psd')
                ->where('sales_bill_no', $bill)
                ->select('psd.*', 'prod.product_name')
                ->leftjoin('pos_products as prod', function ($SaleD) {
                    $SaleD->on('prod.id', '=', 'psd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->get();

            $output       = '';
            $option       = '<option value="">Select Product</option>';
            $totalRetQnt  = 0;
            $updatedPrice = '';

            if (count($SaleD->toArray()) > 0) {
                foreach ($SaleD as $Row) {

                    $productID = $Row->product_id;
                    $countdata = DB::table('pos_sales_return_m as sm')
                        ->where('sm.sales_bill_no', $Row->sales_bill_no)
                        ->where([['sm.is_delete', 0], ['sm.is_active', 1]])
                        ->where(function ($countdata) use ($returnBillNo) {

                            if (!empty($returnBillNo)) {
                                $countdata->where('sm.return_bill_no', '<>', $returnBillNo);
                            }
                        })
                        ->join('pos_sales_return_d as sd', function ($countdata) use ($productID) {
                            $countdata->on('sm.return_bill_no', '=', 'sd.return_bill_no')
                                ->where('sd.product_id', $productID);
                        })
                        ->sum('sd.product_quantity');

                    $updatedPrice = POSS::fnForUpdatedSalesPrice($productID, $SaleM->sales_date);

                    $unitPrice = $updatedPrice ? $updatedPrice : $Row->product_unit_price;

                    $totalSalesPrice = $Row->product_quantity * $unitPrice;
                    $mrp_set = !empty($unitPrice) && $unitPrice != '0.00' && $unitPrice != '0' ? '(MRP: ' . Common::getDecimalValue($unitPrice) . ')' : '';

                    $output .= '<tr>
                        <td>' . $Row->product_name . ' [' . $Row->product_system_barcode . ']' . $mrp_set . '</td>
                        <td>' . $Row->product_quantity . '</td>
                        <td>' . $countdata . '</td>
                        <td>' . $unitPrice . '</td>
                        <td>' . $totalSalesPrice . '</td>
                    </tr>';

                    if ($countdata < $Row->product_quantity) {

                        // $stockQnt = $Row->product_quantity - $countdata;
                        $stockQnt = $Row->product_quantity;

                        $option .= '<option
                        value="' . $Row->product_id .
                            '" pcprice="' . $Row->product_cost_price .
                            '" psprice="' . $Row->product_unit_price .
                            '" pname="' . $Row->product_name .
                            '" sbarcode="' . $Row->product_system_barcode .
                            '" pbarcode="' . $Row->product_barcode .
                            '" pquantity="' . $stockQnt .
                            '" retquantity="' . $countdata .
                            '">' . $Row->product_name . ' [' . $Row->product_system_barcode . '] ' . $mrp_set . '</option>';
                    }

                    $totalRetQnt += $countdata;
                }

                $output .= '<tr><td><strong>Total</strong> </td><td><strong>' . $SaleM->total_quantity . '</strong></td><td><strong>' . $totalRetQnt . '</strong></td><td></td><td><strong>' . $SaleM->total_amount . '</strong></td></tr>';
            }
            $response = [
                'master' => $SaleM,
                'option' => $option,
                'tbody'  => $output,
                'branch_date' => $branch_date,
                'branch_name' => $BranchData->branch_name . " [" . $BranchData->branch_code . "]",

            ];

            echo json_encode($response);
        }
    }

    public function ajaxUsebillDetails(Request $request)
    {
        if ($request->ajax()) {

            $bill         = $request->usesBillNo;
            $returnBillNo = (isset($request->returnBillNo)) ? $request->returnBillNo : null;

            // Query
            $useM = DB::table('inv_use_m')->where('uses_bill_no', $bill)->first();

            $useD = DB::table('inv_use_d as psd')
                ->where('uses_bill_no', $bill)
                ->select('psd.*', 'prod.product_name', 'prod.cost_price')
                ->leftjoin('inv_products as prod', function ($useD) {
                    $useD->on('prod.id', '=', 'psd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->get();

            $output      = '';
            $option      = '<option value="">Select Product</option>';
            $totalRetQnt = 0;

            if (count($useD->toArray()) > 0) {
                foreach ($useD as $Row) {

                    $productID = $Row->product_id;
                    $countdata = DB::table('inv_use_return_m as sm')
                        ->where('sm.uses_bill_no', $Row->uses_bill_no)
                        ->where([['sm.is_delete', 0], ['sm.is_active', 1]])
                        ->where(function ($countdata) use ($returnBillNo) {

                            if (!empty($returnBillNo)) {
                                $countdata->where('sm.return_bill_no', '<>', $returnBillNo);
                            }
                        })
                        ->join('inv_use_return_d as sd', function ($countdata) use ($productID) {
                            $countdata->on('sm.return_bill_no', '=', 'sd.return_bill_no')
                                ->where('sd.product_id', $productID);
                        })
                        ->sum('sd.product_quantity');

                    $output .= '<tr><td>' . $Row->product_name . '</td><td>' . $Row->product_quantity . '</td><td>' . $countdata . '</td></tr>';

                    if ($countdata < $Row->product_quantity) {

                        // $stockQnt = $Row->product_quantity - $countdata;
                        $stockQnt = $Row->product_quantity;

                        $option .= '<option
                        value="' . $Row->product_id .
                            '" pname="' . $Row->product_name .
                            '" pcprice="' . $Row->cost_price .
                            '" pquantity="' . $stockQnt .
                            '" retquantity="' . $countdata .
                            '">' . $Row->product_name . '</option>';
                    }
                    $totalRetQnt += $countdata;
                }

                $output .= '<tr><td><strong>Total</strong> </td><td><strong>' . $useM->total_quantity . '</strong></td><td><strong>' . $totalRetQnt . '</strong></td></tr>';
            }
            $response = [
                'master' => $useM,
                'option' => $option,
                'tbody'  => $output,
            ];

            echo json_encode($response);
        }
    }

    ////////////Fixed Asset Management///////////

    public function ajaxUsebillDetailsFam(Request $request)
    {
        if ($request->ajax()) {

            $bill         = $request->usesBillNo;
            $returnBillNo = (isset($request->returnBillNo)) ? $request->returnBillNo : null;

            // Query
            $useM = DB::table('fam_use_m')->where('uses_bill_no', $bill)->first();

            $useD = DB::table('fam_use_d as psd')
                ->where('uses_bill_no', $bill)
                ->select('psd.*', 'prod.prod_code', 'prod.unit_cost_price')
                ->leftjoin('fam_products as prod', function ($useD) {
                    $useD->on('prod.id', '=', 'psd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->get();

            $output      = '';
            $option      = '<option value="">Select Product</option>';
            $totalRetQnt = 0;

            if (count($useD->toArray()) > 0) {
                foreach ($useD as $Row) {

                    $productID = $Row->product_id;
                    $countdata = DB::table('fam_use_return_m as sm')
                        ->where('sm.uses_bill_no', $Row->uses_bill_no)
                        ->where([['sm.is_delete', 0], ['sm.is_active', 1]])
                        ->where(function ($countdata) use ($returnBillNo) {

                            if (!empty($returnBillNo)) {
                                $countdata->where('sm.return_bill_no', '<>', $returnBillNo);
                            }
                        })
                        ->join('fam_use_return_d as sd', function ($countdata) use ($productID) {
                            $countdata->on('sm.return_bill_no', '=', 'sd.return_bill_no')
                                ->where('sd.product_id', $productID);
                        })
                        ->sum('sd.product_quantity');

                    $output .= '<tr><td>' . $Row->prod_code . '</td><td>' . $Row->product_quantity . '</td><td>' . $countdata . '</td></tr>';

                    if ($countdata < $Row->product_quantity) {

                        // $stockQnt = $Row->product_quantity - $countdata;
                        $stockQnt = $Row->product_quantity;

                        $option .= '<option
                        value="' . $Row->product_id .
                            '" pname="' . $Row->prod_code .
                            '" pcprice="' . $Row->unit_cost_price .
                            '" pquantity="' . $stockQnt .
                            '" retquantity="' . $countdata .
                            '">' . $Row->prod_code . '</option>';
                    }
                    $totalRetQnt += $countdata;
                }

                $output .= '<tr><td><strong>Total</strong> </td><td><strong>' . $useM->total_quantity . '</strong></td><td><strong>' . $totalRetQnt . '</strong></td></tr>';
            }
            $response = [
                'master' => $useM,
                'option' => $option,
                'tbody'  => $output,
            ];

            echo json_encode($response);
        }
    }
    /*     * *********************************** Product Load of Transaction Portion End */

    //supplier name load function
    public function ajaxSupplierNameLoad(Request $request)
    {

        if ($request->ajax()) {

            if ($request->SupplierID != null) {
                $data = Supplier::where(['id' => $request->SupplierID])->select('sup_name')->first();

                $orders = DB::table('pos_orders_m')->where([['is_approve', 1], ['order_to', $request->SupplierID], ['is_delivered', 0], ['is_completed', 0], ['is_active', 1], ['is_delete', 0]])->get();

                $data = array(
                    'suplierName' => $data->sup_name,
                    'orderList'   => $orders,
                );
            } else {
                $data = array();
            }

            return response()->json($data);
        }
    }

    public function ajaxOrderListLoad(Request $request)
    {

        if ($request->ajax()) {
            $SupplierID = $request->SupplierID;
            $orders = DB::table('pos_orders_m')->where([['is_approve', 1], ['is_delivered', 0], ['is_active', 1], ['is_delete', 0]])
                ->where(function ($orders) use ($SupplierID) {
                    if (!empty($SupplierID)) {
                        $orders->where('order_to', $SupplierID);
                    }
                })
                ->orderBy('id', 'DESC')->get();

            $data = array(
                'orderList'   => $orders,
            );

            return response()->json($data);
        }
    }

    public function ajaxOrderWiseAreaLoad(Request $request)
    {

        if ($request->ajax()) {

            $order_id =  $request->order_id;

            $masterQuery = DB::table('pos_orders_m as pom')
                ->where([['pom.is_active', 1], ['pom.is_delete', 0]])
                ->leftjoin('pos_orders_d as pod', function ($masterQuery) {
                    $masterQuery->on('pod.order_no', '=', 'pom.order_no');
                })
                ->where('pom.order_no', $order_id)
                ->get();

            $area_array = array();
            $HeadOfficeArea = false;

            foreach ($masterQuery as $i => $value) {
                if (empty($value->delivery_place)) {
                    $val = json_decode($value->requisition_branch_to);
                    foreach ($val as $key => $q_val) {
                        if ($key == 1) {
                            $HeadOfficeArea = true;
                        }
                        array_push($area_array, Common::getAreaId($key));
                    }
                    # code...
                } else {
                    if ($value->delivery_place == 1) {
                        $HeadOfficeArea = true;
                    }
                    array_push($area_array, Common::getAreaId($value->delivery_place));
                }
            }

            $area = DB::table('gnl_areas')->where([['is_active', 1], ['is_delete', 0]])->whereIn('id', array_unique($area_array))
                ->orderBy('id', 'DESC')->get();

            $data = array(
                'area'   => $area,
                'HeadOfficeArea'   => $HeadOfficeArea,

            );

            return response()->json($data);
        }
    }

    //supplier name load function For Inventory
    public function ajaxSupplierNameLoadInv(Request $request)
    {

        if ($request->ajax()) {

            $data = DB::table('inv_suppliers')->where([['id', $request->SupplierID], ['is_active', 1], ['is_delete', 0]])->select('sup_name')->first();

            $orders = DB::table('inv_orders_m')->where([['is_approve', 1], ['order_to', $request->SupplierID], ['is_delivered', 0], ['is_completed', 0], ['is_active', 1], ['is_delete', 0]])->get();

            $data = array(
                'suplierName' => $data->sup_name,
                'orderList'   => $orders,
            );

            return response()->json($data);
        }
    }

    //supplier name load function For Fixed Asset Management
    public function ajaxSupplierNameLoadFam(Request $request)
    {

        if ($request->ajax()) {

            $data = DB::table('fam_suppliers')->where([['id', $request->SupplierID], ['is_active', 1], ['is_delete', 0]])->select('sup_name')->first();

            $orders = DB::table('fam_orders_m')->where([['is_approve', 1], ['order_to', $request->SupplierID], ['is_delivered', 0], ['is_completed', 0], ['is_active', 1], ['is_delete', 0]])->get();

            $data = array(
                'suplierName' => $data->sup_name,
                'orderList'   => $orders,
            );

            return response()->json($data);
        }
    }

    //Customer Mobile No load function
    public function ajaxCustomerMobileLoad(Request $request)
    {

        if ($request->ajax()) {

            $data = Customer::where(['customer_no' => $request->CustomerID])->select('customer_mobile')->first();

            echo $data->customer_mobile;
        }
    }

    //Customer Mobile No load function
    public function ajaxCustomerNIDLoad(Request $request)
    {

        if ($request->ajax()) {

            $data = Customer::where(['customer_no' => $request->CustomerID])->select('customer_nid')->first();

            echo $data->customer_nid;
        }
    }

    // generate customer no
    public static function ajaxGenerateCustomerNo(Request $request)
    {
        // dd($request->BranchID);
        $branchID = $request->BranchID;
        $BranchT  = 'App\\Model\\GNL\\Branch';
        $ModelT   = "App\\Model\\POS\\Customer";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "CUS" . $BranchCode;
        $record    = $ModelT::select(['id', 'customer_no'])
            ->where('branch_id', $branchID)
            ->where('customer_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('customer_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->customer_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    //Sales ID load function
    public function ajaxSalesIDLoad(Request $request)
    {

        if ($request->ajax()) {

            $data = SalesMaster::where(['sales_bill_no' => $request->selectedData])->select('id')->first();
            return $data->id;
        }
    }

    //function for generate bill no for installment sales
    public function ajaxGBillSales(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillSales($BranchId);

        return $BillNo;
    }

    //function for getBranch date
    public function ajaxGetBranchDate(Request $request)
    {
        $BranchId = $request->branch_id;

        if (isset($request->module)) {
            $Date     = Common::systemCurrentDate($BranchId, $request->module);
        } else {
            $Date     = Common::systemCurrentDate($BranchId, 'pos');
        }


        return $Date;
    }

    public function ajaxGBillShopSales(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillShopSales($BranchId);

        return $BillNo;
    }

    //function for generate bill no for inventory use
    public function ajaxGBillUses(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = INVS::generateBillUses($BranchId);

        return $BillNo;
    }

    ## function for generate bill no for Write Off
    public function ajaxGBillWaiverProduct(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateBillWaiverProduct($BranchId);

        return $BillNo;
    }

    //function for Calculate Stock Quantity for Product
    public function ajaxStockQuantity(Request $request)
    {

        $BranchID  = $request->BranchId;
        $ProductID = $request->ProductId;
        $sDate     = Common::getBranchSoftwareStartDate($BranchID, 'pos');
        $eDate     = Common::systemCurrentDate($BranchID, 'pos');

        // dd($sDate, $eDate , $BranchID);

        $Stock = POSS::stockQuantity($BranchID, $ProductID, false, $sDate, $eDate);
        // dd( $Stock);

        return $Stock;
    }

    //function for Calculate Stock Quantity for Gold Product
    public function ajaxGoldStockQuantity(Request $request)
    {

        $BranchID  = $request->BranchId;
        $ProductID = $request->ProductId;
        $ProductSizeID = $request->ProductSizeId;

        $Stock = POSS::stockSerialProductQuantity($BranchID, $ProductID, $ProductSizeID);


        return $Stock;
    }

    //function  Gold Product sales Price
    public function ajaxGoldSalesPrice(Request $request)
    {

        $BranchID  = $request->BranchId;
        $ProductID = $request->ProductId;
        $ProductSizeID = $request->ProductSizeId;
        $addOrEditFlag = $request->addOrEditFlag;
        $effDate = $request->effDate;

        $price = POSS::goldSalesPrice($BranchID, $ProductID, $ProductSizeID, $addOrEditFlag, $effDate);

        return $price;
    }

    //function for Calculate Avarage Cost Price for Gold Product
    public function ajaxGoldAvgCostPrice(Request $request)
    {
        $ProductId = $request->ProductId;
        $ProductSizeId = $request->ProductSizeId;
        $GivenQtn = $request->GivenQtn;
        $BranchId = $request->BranchId;

        $data =  POSS::stockAvailableProductForSerialBarcode($BranchId, $ProductId, $ProductSizeId);

        if ($data->count() >= $GivenQtn && $GivenQtn != 0) {
            $takeProduct = $data->take($GivenQtn);

            $AvgCostPrice = ($takeProduct->sum('unit_cost_price') / $GivenQtn);
        } else {
            $AvgCostPrice =  0;
        }

        return $AvgCostPrice;
    }

    public function ajaxStockQuantityInv(Request $request)
    {
        $BranchID  = $request->BranchId;
        $ProductID = $request->ProductId;
        $sDate     = Common::getBranchSoftwareStartDate($BranchID, 'inv');
        $eDate     = Common::systemCurrentDate($BranchID, 'inv');

        // dd($sDate, $eDate , $BranchID);

        $Stock = INVS::stockQuantity($BranchID, $ProductID, false, $sDate, $eDate);

        return $Stock;
    }

    /////////////// Fixed Asset Management ////////////
    public function ajaxStockQuantityFam(Request $request)
    {
        $BranchID  = $request->BranchId;
        $ProductID = $request->ProductId;
        $sDate     = Common::getBranchSoftwareStartDate($BranchID, 'fam');
        $eDate     = Common::systemCurrentDate($BranchID, 'fam');

        // dd($sDate, $eDate , $BranchID);

        $Stock = FAMS::stockQuantity($BranchID, $ProductID, false, $sDate, $eDate);

        return $Stock;
    }

    public function ajaxBranchOpendate(Request $request)
    {

        $BranchID   = $request->branchID;
        $moduleName = $request->moduleName;

        $branch_date = Common::getBranchSoftwareStartDate($BranchID, $moduleName);

        if ($branch_date != null) {
            $date = (new DateTime($branch_date))->format('d-m-Y');
            return $date;
        } else {
            return null;
        }
    }

    public function ajaxCustSalesDetails(Request $request)
    {
        $customerId  = $request->customerId;
        $branchId  = (isset($request->branchId)) ? $request->branchId : null;
        // $paymentSystemId  = (isset($request->paymentSystemId)) ? $request->paymentSystemId : null;

        $selectedVal = (isset($request->selectedVal)) ? $request->selectedVal : null;

        $currentDate  = (new DateTime(Common::systemCurrentDate(null, 'pos')))->format('Y-m-d');

        $salesData = DB::table('pos_sales_m')
            ->whereIn('sales_type', [2, 3])
            ->where([
                ['customer_id', $customerId],
                ['is_delete', 0], ['is_active', 1]
            ])
            // ['is_complete', 0]
            ->where(function ($query) use ($currentDate) {
                // $query->whereRaw('complete_date is null or complete_date > '. $currentDate);
                $query->whereNull('complete_date');
                $query->orWhere('complete_date', '>', $currentDate);
            })
            ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
            ->select('id', 'sales_bill_no', 'total_amount', 'installment_rate')
            ->get();

        ///////////////////////////////////////

        $salesBillArr = $salesData->pluck('sales_bill_no')->toArray();

        // $collectionData = DB::table('pos_collections')
        //     ->where([['is_delete', 0], ['is_active', 1], ['collection_date', $currentDate]])
        //     ->whereIn('sales_bill_no', $salesBillArr)
        //     ->where(function ($collectionData) use ($selectedVal) {
        //         if (!empty($selectedVal)) {
        //             $collectionData->where('sales_bill_no', '<>', $selectedVal);
        //         }
        //     })
        //     ->groupBy('sales_bill_no')
        //     ->selectRaw('IFNULL(SUM(collection_amount), 0) AS collection_amount, sales_bill_no')
        //     ->pluck('collection_amount', 'sales_bill_no')
        //     ->toArray();

        // dd($customerId,$salesData,$collectionData);

        $output = '<option value="">Select One</option>';

        foreach ($salesData as $Row) {

            $billPrint = true;
            // ## collection same date a aktai thakbe tai bill false kora
            // if (isset($collectionData[$Row->sales_bill_no])) {
            //     if ($collectionData[$Row->sales_bill_no] > 0) {
            //         $billPrint = false;
            //     }
            // }

            // if ($billPrint) {
            $selectText = '';

            if ($selectedVal != null) {
                if ($selectedVal == $Row->sales_bill_no) {
                    $selectText = 'selected="selected"';
                }
            }

            $output .= '<option value="' . $Row->sales_bill_no . '"
                    todaysColllection ="' . $billPrint . '"
                    sales_id ="' . $Row->id . '"
                    sales_payable_amount ="' . $Row->total_amount . '"
                    installment_rate="' . $Row->installment_rate . '"
                    ' . $selectText . '>' . $Row->sales_bill_no . '</option>';
            // }
        }

        echo $output;
    }

    public function ajaxPurchaseBillNoDetails(Request $request)
    {
        $supplierId  = $request->supplierId;
        $selectedVal = (isset($request->selectedVal)) ? $request->selectedVal : null;

        // Query
        $PurchaseData = DB::table('pos_purchases_m')
            ->where([['is_delete', 0], ['is_active', 1], ['supplier_id', $supplierId]])
            ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
            ->select('id', 'bill_no', 'total_amount', 'discount_rate', 'due_amount')
            ->orderBy('bill_no', 'ASC')
            ->get();

        $output = '<option value="">Select One</option>';

        foreach ($PurchaseData as $Row) {

            $selectText = '';

            if ($selectedVal != null) {
                if ($selectedVal == $Row->bill_no) {
                    $selectText = 'selected="selected"';
                }
            }

            $output .= '<option value="' . $Row->bill_no . '"
                    spayment_id ="' . $Row->id . '"
                    purchase_payable_amount ="' . $Row->total_amount . '"
                    discount_rate ="' . $Row->discount_rate . '"
                    due_amount ="' . $Row->due_amount . '"
                    ' . $selectText . '>' . $Row->bill_no . '</option>';
        }

        echo $output;
    }

    //  public function ajaxPurchaseAmt(Request $request)
    // {

    //     $purchaseBill = $request->purchaseBillNo;

    //     $PurchaseData = PurchaseMaster::where([['bill_no', $purchaseBill], ['is_delete', 0]])->first();
    //     echo $PurchaseData->paid_amount;
    // }
    public function ajaxBillCollection(Request $request)
    {
        $salesBillNo = $request->salesBill;
        $customer_id = $request->customer_id;
        $SalesCollectionType = $request->SalesCollectionType;

        $selectedVal = $request->selectedVal;




        if ($SalesCollectionType == 'bill') {
            $col_Data = Collection::whereIn('sales_type', [2, 3])->where([['sales_bill_no', $salesBillNo], ['is_delete', 0]])->get();
            $sales_Data = SalesMaster::whereIn('sales_type', [2, 3])->where([['sales_bill_no', $salesBillNo], ['is_delete', 0]])->get();
        } else {
            $col_Data = Collection::whereIn('sales_type', [2, 3])->where([['customer_id', $customer_id], ['is_delete', 0]])->get();
            $sales_Data = SalesMaster::whereIn('sales_type', [2, 3])->where([['customer_id', $customer_id], ['is_delete', 0]])->get();
        }

        if (!empty($selectedVal)) {
            $col_Data = $col_Data->where('id', '<>', $selectedVal);
        }

        $col_amount = $col_Data->sum('collection_amount');
        $sales_amount = $sales_Data->sum('total_amount');

        $data = array(
            'sales_amount' => $sales_amount,
            'col_amount' => $col_amount,
        );

        // dd($data);


        return response()->json($data);
    }

    public function getProcessingFee(Request $request)
    {
        if ($request->ajax()) {

            $proFeeData = PProcessingFee::where([['company_id', $request->companyId], ['is_delete', 0]])->first();

            if ($proFeeData) {
                return $proFeeData->amount;
            } else {
                return 0;
            }
        }
    }

    public function getBranchCustomer(Request $request)
    {

        $branchId = $request->branchId;

        $QueryData = DB::table('pos_customers')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where(function ($QueryData) use ($branchId) {
                if (!empty($branchId)) {
                    $QueryData->where('branch_id', $branchId);
                }
            })
            ->get(); // no update

        $output = '<option value="">Select One</option>';

        foreach ($QueryData as $row) {

            $output .= '<option value="' . $row->customer_no . '"
                    cname ="' . $row->customer_name . '"
                    ccode ="' . $row->customer_no . '">' . $row->customer_name . ' [' . $row->customer_no . ']' . '</option>';
        }

        return $output;
    }

    public function ajaxAutoVoucheritem(Request $request)
    {

        $misType     = $request->misType;
        $voucherType = $request->VoucherType;

        $supplier = false;

        $bank = false;

        $MisItem = DB::table('pos_mis_configuration')->where([['is_delete', 0], ['mis_type', $misType]])->get();

        // dd( );

        // if ($misType == 5 || $misType == 6 || $misType == 7 || $misType == 13) {
        if (!empty($MisItem->where('mis_accounts_load', 2)->first())) {

            $supplier = DB::table('pos_suppliers')->where([['is_delete', 0], ['is_active', 1]])->get();
            // $bank  = DB::table('pos_bank_acc')->where([['is_delete', 0], ['is_active', 1]])->get();

        }

        // if ($misType == 13) {
        if (!empty($MisItem->where('mis_accounts_load', 3)->first())) {

            // $supplier         = DB::table('pos_suppliers')->where([['is_delete', 0], ['is_active', 1]])->get();
            $paymentSystemIDS = DB::table('gnl_payment_system')->where([['is_delete', 0], ['is_active', 1], ['status', '<>', 2]])->get()->pluck('id')->toArray();

            $bank = DB::table('gnl_payment_acc')->where([['is_delete', 0], ['is_active', 1]])->whereIn('payment_system_id', $paymentSystemIDS)->get();
        }

        $data = array(
            'MisItem'  => $MisItem,
            'supplier' => $supplier,
            'bank'     => $bank,
        );

        return response()->json($data);
    }

    public function ajaxAutoVoucheritemInv(Request $request)
    {

        $misType     = $request->misType;
        $voucherType = $request->VoucherType;

        $supplier = false;

        $bank = false;

        $MisItem = DB::table('inv_mis_configuration')->where([['is_delete', 0], ['is_active', 1], ['mis_type', $misType]])->get();

        if ($misType == 5 || $misType == 6 || $misType == 7 || $misType == 13) {
            $supplier = DB::table('inv_suppliers')->where([['is_delete', 0], ['is_active', 1]])->get();
            // $bank  = DB::table('pos_bank_acc')->where([['is_delete', 0], ['is_active', 1]])->get();

        }

        if ($misType == 13) {
            $supplier         = DB::table('inv_suppliers')->where([['is_delete', 0], ['is_active', 1]])->get();
            $paymentSystemIDS = DB::table('gnl_payment_system')->where([['is_delete', 0], ['is_active', 1], ['status', '<>', 2]])->get()->pluck('id')->toArray();

            $bank = DB::table('gnl_payment_acc')->where([['is_delete', 0], ['is_active', 1]])->whereIn('payment_system_id', $paymentSystemIDS)->get();
        }

        $data = array(
            'MisItem'  => $MisItem,
            'supplier' => $supplier,
            'bank'     => $bank,
        );

        return response()->json($data);
    }

    public function getSalesBillNo(Request $request)
    {
        if ($request->ajax()) {

            $branchId   = $request->branchId;
            $CustomerId = $request->CustomerId;

            // ## Query
            $QueryData = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($QueryData) use ($branchId) {
                    if (!empty($branchId)) {
                        $QueryData->where('branch_id', $branchId);
                    }
                })
                ->where(function ($QueryData) use ($CustomerId) {
                    if (!empty($CustomerId)) {
                        $QueryData->where('customer_id', $CustomerId);
                    }
                })
                ->pluck('sales_bill_no')
                ->toArray();

            $output = '<option value="">All</option>';
            foreach ($QueryData as $value) {
                $SelectText = '';
                $output .= '<option value="' . $value . '">' . $value . '</option>';
            }

            echo $output;
        }
    }

    public function getEmployeeName(Request $request)
    {
        if ($request->ajax()) {

            $branchId  = $request->branchId;
            $posModule = (isset($request->posModule)) ? $request->posModule : false;

            // dd($posModule);

            // # Query
            if (Common::getDBConnection() == "sqlite") {

                $QueryData = DB::table('hr_employees')
                    // ->where([['is_delete', 0], ['is_active', 1]])
                    ->where([['is_delete', 0]])
                    ->where(function ($QueryData) use ($branchId) {
                        if (!empty($branchId)) {
                            $QueryData->where('branch_id', $branchId);
                        }
                    })
                    ->selectRaw('(emp_name || " [" || emp_code || "]") AS emp_name, employee_no')
                    ->pluck('emp_name', 'employee_no')
                    ->toArray();
            } else {

                if ($posModule == "true") {
                    $QueryData = DB::table('hr_employees')
                        // ->where([['is_delete', 0], ['is_active', 1]])
                        ->where([['is_delete', 0]])
                        ->where(function ($QueryData) use ($branchId) {
                            if (!empty($branchId)) {
                                $QueryData->where('branch_id', $branchId);
                            }
                        })
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, employee_no')
                        ->pluck('emp_name', 'employee_no')
                        ->toArray();
                } else {
                    $QueryData = DB::table('hr_employees')
                        // ->where([['is_delete', 0], ['is_active', 1]])
                        ->where([['is_delete', 0]])
                        ->where(function ($QueryData) use ($branchId) {
                            if (!empty($branchId)) {
                                $QueryData->where('branch_id', $branchId);
                            }
                        })
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
                        ->pluck('emp_name', 'id')
                        ->toArray();
                }
            }

            // dd($QueryData);

            $output = '<option value="">All</option>';
            foreach ($QueryData as $key => $value) {
                $SelectText = '';
                $output .= '<option value="' . $key . '">' . $value . '</option>';
            }

            echo $output;
        }
    }

    public function getCustomerName(Request $request)
    {

        if ($request->ajax()) {

            $branchId = $request->branchId;

            // Query
            $QueryData = DB::table('pos_customers')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($QueryData) use ($branchId) {
                    if (!empty($branchId)) {
                        $QueryData->where('branch_id', $branchId);
                    }
                })
                ->when(true, function ($QueryData) {
                    if (Common::getDBConnection() == "sqlite") {
                        $QueryData->selectRaw('(customer_name || " [" || customer_no || "]") AS customer_name, customer_no');
                    } else {
                        $QueryData->selectRaw('CONCAT(customer_name, " [", customer_no, "]") AS customer_name, customer_no');
                    }
                })
                ->pluck('customer_name', 'customer_no')
                ->toArray();

            $output = '<option value="">All</option>';
            foreach ($QueryData as $key => $value) {
                $SelectText = '';
                $output .= '<option value="' . $key . '">' . $value . '</option>';
            }

            echo $output;
        }
    }

    // public function ajaxVoucherAuth(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $myObj = $request->myObj;
    //         $flag = true;
    //         foreach ($myObj as $Row) {
    //             //dd($Row['ID']);
    //             // dd($Row['value']);
    //             $Id = $Row['ID'];
    //             $status = $Row['value'];
    //             $voucherData = Voucher::where('id', $Id)->first();
    //             // dd($voucherData);

    //             if ($status == 1) {
    //                 $voucherData->auth_by = Auth::id();
    //                 $voucherData->voucher_status = 1;
    //                 $isSuccess = $voucherData->update();

    //                 if (!$isSuccess) {
    //                     $flag = false;
    //                 }
    //             }
    //         }
    //         echo $flag;
    //     }
    // }

    // public function ajaxVoucherUnAuth(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $myObj = $request->myObj;

    //         $flag = true;
    //         $isMonthActive = false;
    //         foreach ($myObj as $Row) {

    //             //dd($Row['ID']);
    //             // dd($Row['value']);
    //             $Id = $Row['ID'];
    //             $status = $Row['value'];
    //             $voucherData = Voucher::where('id', $Id)->first();

    //             if ($status == 1) {

    //                 // If voucher Type is manual(v_generate_type = 0), Then check Month End
    //                 if ($voucherData->v_generate_type == 0) {
    //                     $voucherMonthYear = (new Datetime($voucherData->voucher_date))->format('Y-m');

    //                     $getMonthEndData = DB::table('acc_month_end')
    //                                     ->where([['is_delete',0], ['branch_id', $voucherData->branch_id],
    //                                         ['month_date', 'like', '%'.$voucherMonthYear.'%']])
    //                                     ->latest()->first();

    //                     if (!empty($getMonthEndData)) {
    //                         if ($getMonthEndData->is_active == 1) {
    //                             $isMonthActive = true;
    //                         }
    //                         else {
    //                             $isMonthActive = false;
    //                             $flag = false;
    //                         }
    //                     }
    //                 }

    //                 if ($isMonthActive == true) {
    //                     $voucherData->auth_by = 0;
    //                     $voucherData->voucher_status = 0;
    //                     $isSuccess = $voucherData->update();

    //                     if (!$isSuccess) {
    //                         $flag = false;
    //                     }
    //                 }

    //             }

    //         }

    //         echo $flag;
    //     }
    // }

    public function backupgetOrderPurchaseProdLoad(Request $request)
    {

        if ($request->ajax()) {

            $orderNo = (isset($request->orderNo)) ? $request->orderNo : null;

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            $output = '';



            // if($orderNo == null){
            if (empty($orderNo)) {

                $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

                $queryData = DB::table('pos_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $Row) {

                    $output .= '<option value="' . $Row->id . '"
                                        pname= "' . $Row->product_name . '"
                                        sbarcode="' . $Row->sys_barcode . '"
                                        pbarcode="' . $Row->prod_barcode . '"
                                        pcprice="' . $Row->cost_price . '"
                                        psprice="' . $Row->sale_price . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                    $output .= '</option>';
                }
            } else {

                $queryData = DB::table('pos_orders_m as pom')
                    ->where([['pom.order_no', $orderNo]])
                    ->select('pod.product_id', 'prod.product_name', 'prod.prod_barcode', 'pod.product_quantity', 'prod.cost_price as prod_cost_price')
                    ->leftjoin('pos_orders_d as pod', function ($queryData) {
                        $queryData->on('pod.order_no', '=', 'pom.order_no');
                    })
                    ->leftjoin('pos_products as prod', function ($queryData) {
                        $queryData->on('prod.id', '=', 'pod.product_id')
                            ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                    })
                    ->where(function ($queryData) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $queryData->where('prod.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $queryData->where('prod.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $queryData->where('prod.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $queryData->where('prod.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $queryData->where('prod.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $queryData->where('prod.prod_model_id', $ModelID);
                        }
                    })
                    ->addSelect([
                        'remaining_qtn' => DB::table('pos_purchases_m as ppm')
                            ->select(DB::raw('(pod.product_quantity - IFNULL(SUM(ppd.product_quantity), 0))'))
                            ->leftjoin('pos_purchases_d as ppd', function ($queryData) {
                                $queryData->on('ppd.purchase_bill_no', '=', 'ppm.bill_no');
                            })
                            ->whereColumn([['pom.order_no', 'ppm.order_no'], ['ppd.product_id', 'pod.product_id']])
                            ->where([['ppm.is_delete', 0], ['ppm.is_active', 1]])
                            ->limit(1),
                    ])
                    ->get();
                // dd($queryData);
                $output = '<option value="">Select One</option>';
                foreach ($queryData as $row) {

                    if ($row->remaining_qtn > 0) {
                        $output .= '<option value="' . $row->product_id . '" pname="' . $row->product_name . '" sbarcode="' . $row->prod_barcode . '" pcprice="' . $row->prod_cost_price . '" prod_qtn="' . $row->remaining_qtn . '" prod_order_qtn="' . $row->product_quantity . '">' . $row->product_name . ' [' . $row->prod_barcode . ']' . '</option>';
                    }
                }
            }

            echo $output;
        }
    }

    public function getOrderPurchaseProdLoad(Request $request)
    {

        if ($request->ajax()) {

            $orderNo = (isset($request->orderNo)) ? $request->orderNo : null;

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $prod_type_id = (isset($request->prod_type_id)) ? $request->prod_type_id : null;



            $excluding_pur_bill_no = (isset($request->excluding_pur_bill_no)) ? $request->excluding_pur_bill_no : null;



            $output = '';

            $ViewType = "withorder";

            // if($orderNo == null){
            if (empty($orderNo)) {

                $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];
                $ProdTypeArr    = (!empty($prod_type_id)) ? ['prod_type_id', '=', $prod_type_id] : ['prod_type_id', '<>', 3];


                $queryData = DB::table('pos_products')
                    ->selectRaw('id, product_name, cost_price, cost_price as cost_price_view, sale_price, prod_vat, sys_barcode, prod_barcode')
                    ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $ProdTypeArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();
                $ViewType = "withoutorder";
                // $output = '<option value="">Select One</option>';
                // foreach ($queryData as $Row) {

                //     $output .= '<option value="' . $Row->id . '"
                //                         pname= "' . $Row->product_name . '"
                //                         sbarcode="' . $Row->sys_barcode . '"
                //                         pbarcode="' . $Row->prod_barcode . '"
                //                         pcprice="' . $Row->cost_price . '"
                //                         psprice="' . $Row->sale_price . '" >';
                //     $output .= $Row->product_name . ' (' . $Row->prod_barcode . ')';
                //     $output .= '</option>';
                // }

                // $output = '<option value="">Select One</option>';
                // foreach ($queryData as $Row) {

                //     $output .= '<option value="' . $Row->id . '"
                //                             pname= "' . $Row->product_name . '"
                //                             sbarcode="' . $Row->sys_barcode . '"
                //                             pbarcode="' . $Row->prod_barcode . '"
                //                             pcprice="' . $Row->cost_price . '"
                //                             psprice="' . $Row->sale_price . '" >';
                //     $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']  (cp: '.Common::getDecimalValue($Row->cost_price).'tk)';
                //     $output .= '</option>';
                // }

            } else {

                $output = '<option value="">Select One</option>';

                /**
                 * Same Configuration a multi product entry dite hocche cost price change howar karone.
                 * tai branch theke ek product requisition korbe kintu cost price change howay product er
                 * notun barcode hobe, old barcode & new barcode mismatch hobe, tai branch theke kono
                 * nirdisto barcode select na kore product select korbe.
                 * requision table a jei product id ache ta only kaje asbe product er confuguration jante.
                 * db te change kora holo na ai muhurte.
                 * requisition er por issue kora hobe requisition er product er notun barcode onusare,
                 * jodi old barcode e thake tahole problem nei but new barcode hole branch ke
                 * obossoi notun barcode dewa ucit tai ai process a jete holo.
                 * issue te product load korar somoy check korche jei product select kora hoyeche,
                 *  same configuration er onno product gulo load kora hocche.
                 * jar configuration sob match korbe & product name o match korbe
                 */

                ## complete & delete check dewa hocche na karon front end theke sei check ache.

                $requisitionDetails = DB::table('pos_orders_d as prd')
                    ->where('prd.order_no', $orderNo)
                    ->join('pos_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', 0], ['pp.is_active', 1]])
                    ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $requisitionDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $requisitionDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $requisitionDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $requisitionDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_id , pp.*,SUM(prd.product_quantity) as product_quantity,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id || "*&" || pp.prod_model_id ||
                            "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_id , pp.*,SUM(prd.product_quantity) as product_quantity,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->groupBy('product_string')
                    ->get();
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('pos_purchases_m as pim')
                    ->where([['pim.order_no', $orderNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('pos_purchases_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.purchase_bill_no', '=', 'pim.bill_no');
                    })
                    ->where(function ($issueDetails) use ($excluding_pur_bill_no) {
                        if (!empty($excluding_pur_bill_no)) {
                            $issueDetails->where('pim.bill_no', '<>', $excluding_pur_bill_no);
                        }
                    })
                    ->join('pos_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', 0], ['pp.is_active', 1]])
                    ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $issueDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $issueDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $issueDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $issueDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $issueDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_id , pp.*,SUM(pid.product_quantity) as product_quantity,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_id , pp.*,SUM(pid.product_quantity) as product_quantity,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->groupBy('product_string')
                    ->get();

                // $issueDetails = $issueDetails



                // dd($issueDetails);
                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('pos_products')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        // ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                    $queryData =  $queryData->whereIn('product_string', array_unique($requisitionDetails->pluck('product_string')->toArray()));
                }

                // dd($issueProduct);

                foreach ($queryData as $key => $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }
                    $queryData[$key]->remaining_qtn = $remaining_qtn;
                    $queryData[$key]->cost_price_view = Common::getDecimalValue($row->cost_price);
                    $queryData[$key]->sale_price = Common::getDecimalValue($row->sale_price);

                    // if ($remaining_qtn > 0) {
                    //     $output .= '<option value="' . $row->id . '"
                    //                 pname="' . $row->product_name . '"
                    //                 sbarcode="' . $row->prod_barcode . '"
                    //                 pcprice="' . $row->cost_price . '"
                    //                 prod_qtn="' . $remaining_qtn . '">';

                    //     $output .= $row->product_name . ' - (' . $row->prod_barcode . ')';

                    //     $output .= '</option>';
                    // }
                }
            }

            // echo $output;.
            $data = array(
                'queryData'   => $queryData,
                'ViewType' => $ViewType,
            );

            return response()->json($data);
        }
    }

    public function getOrderPurchaseProdLoadInv(Request $request)
    {

        if ($request->ajax()) {

            $orderNo = $request->orderNo;

            $output = '<option value="">Select One</option>';

            /**
             * Same Configuration a multi product entry dite hocche cost price change howar karone.
             * tai branch theke ek product requisition korbe kintu cost price change howay product er
             * notun barcode hobe, old barcode & new barcode mismatch hobe, tai branch theke kono
             * nirdisto barcode select na kore product select korbe.
             * requision table a jei product id ache ta only kaje asbe product er confuguration jante.
             * db te change kora holo na ai muhurte.
             * requisition er por issue kora hobe requisition er product er notun barcode onusare,
             * jodi old barcode e thake tahole problem nei but new barcode hole branch ke
             * obossoi notun barcode dewa ucit tai ai process a jete holo.
             * issue te product load korar somoy check korche jei product select kora hoyeche,
             *  same configuration er onno product gulo load kora hocche.
             * jar configuration sob match korbe & product name o match korbe
             */

            ## complete & delete check dewa hocche na karon front end theke sei check ache.

            if (empty($orderNo)) {
                $queryData = DB::table('inv_orders_m as pom')
                    ->where([['pom.order_no', $orderNo], ['pom.is_completed', 0]])
                    ->select('pod.product_id', 'prod.product_name', 'prod.product_code', 'pod.product_quantity', 'prod.cost_price as prod_cost_price')
                    ->leftjoin('inv_orders_d as pod', function ($queryData) {
                        $queryData->on('pod.order_no', '=', 'pom.order_no');
                    })
                    ->leftjoin('inv_products as prod', function ($queryData) {
                        $queryData->on('prod.id', '=', 'pod.product_id')
                            ->where('prod.is_delete', 0);
                    })
                    ->addSelect([
                        'remaining_qtn' => DB::table('inv_purchases_m as ppm')
                            ->select(DB::raw('(pod.product_quantity - IFNULL(SUM(ppd.product_quantity), 0))'))
                            ->leftjoin('inv_purchases_d as ppd', function ($queryData) {
                                $queryData->on('ppd.purchase_bill_no', '=', 'ppm.bill_no');
                            })
                            ->whereColumn([['pom.order_no', 'ppm.order_no'], ['ppd.product_id', 'pod.product_id']])
                            ->where([['ppm.is_delete', 0], ['ppm.is_active', 1]])
                            ->limit(1),
                    ])
                    ->get();

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $row) {

                    if ($row->remaining_qtn > 0) {
                        $output .= '<option value="' . $row->product_id . '" pname="' . $row->product_name . '" sbarcode="' . $row->product_code . '" pcprice="' . $row->prod_cost_price . '" prod_qtn="' . $row->remaining_qtn . '">'
                            . ($row->product_code != '' ? $row->product_name . ' [' . $row->product_code . ']' : $row->product_name) .
                            '</option>';
                    }
                }
            } else {
                $requisitionDetails = DB::table('inv_orders_d as prd')
                    ->where('prd.order_no', $orderNo)
                    ->join('inv_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $requisitionDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $requisitionDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $requisitionDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $requisitionDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('inv_purchases_m as pim')
                    ->where([['pim.order_no', $orderNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('inv_purchases_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.purchase_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('inv_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $issueDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $issueDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $issueDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $issueDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $issueDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('inv_products')
                        ->where('is_delete', 0)
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || product_name)
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", product_name)
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                }

                foreach ($queryData as $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }

                    if ($remaining_qtn > 0) {
                        $output .= '<option value="' . $row->id . '"
                                    pname="' . $row->product_name . '"
                                    sbarcode="' . $row->prod_barcode . '"
                                    pcprice="' . $row->cost_price . '"
                                    prod_qtn="' . $remaining_qtn . '">';

                        $output .= $row->product_name . ' [' . $row->prod_barcode . ']';

                        $output .= '</option>';
                    }
                }

                ## issue
                $issueDetails = DB::table('inv_purchases_m as pim')
                    ->where([['pim.order_no', $orderNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('inv_purchases_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.purchase_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('inv_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $issueDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $issueDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $issueDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $issueDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $issueDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('inv_products')
                        ->where('is_delete', 0)
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || product_name)
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", product_name)
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                }

                foreach ($queryData as $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }

                    if ($remaining_qtn > 0) {
                        $output .= '<option value="' . $row->id . '"
                                    pname="' . $row->product_name . '"
                                    sbarcode="' . $row->prod_barcode . '"
                                    pcprice="' . $row->cost_price . '"
                                    prod_qtn="' . $remaining_qtn . '">';

                        $output .= $row->product_name . ' [' . $row->prod_barcode . ']';

                        $output .= '</option>';
                    }
                }
            }

            echo $output;
        }
    }
    //////////////// Fixed Asset Management ////////////////

    public function getOrderPurchaseProdLoadFam(Request $request)
    {

        if ($request->ajax()) {

            $orderNo = $request->orderNo;

            $output = '<option value="">Select One</option>';

            /**
             * Same Configuration a multi product entry dite hocche cost price change howar karone.
             * tai branch theke ek product requisition korbe kintu cost price change howay product er
             * notun barcode hobe, old barcode & new barcode mismatch hobe, tai branch theke kono
             * nirdisto barcode select na kore product select korbe.
             * requision table a jei product id ache ta only kaje asbe product er confuguration jante.
             * db te change kora holo na ai muhurte.
             * requisition er por issue kora hobe requisition er product er notun barcode onusare,
             * jodi old barcode e thake tahole problem nei but new barcode hole branch ke
             * obossoi notun barcode dewa ucit tai ai process a jete holo.
             * issue te product load korar somoy check korche jei product select kora hoyeche,
             *  same configuration er onno product gulo load kora hocche.
             * jar configuration sob match korbe & product name o match korbe
             */

            ## complete & delete check dewa hocche na karon front end theke sei check ache.

            if (empty($orderNo)) {
                $queryData = DB::table('fam_orders_m as pom')
                    ->where([['pom.order_no', $orderNo], ['pom.is_completed', 0]])
                    ->select('pod.product_id', 'prod.product_name', 'prod.product_code', 'pod.product_quantity', 'prod.cost_price as prod_cost_price')
                    ->leftjoin('fam_orders_d as pod', function ($queryData) {
                        $queryData->on('pod.order_no', '=', 'pom.order_no');
                    })
                    ->leftjoin('fam_products as prod', function ($queryData) {
                        $queryData->on('prod.id', '=', 'pod.product_id')
                            ->where('prod.is_delete', 0);
                    })
                    ->addSelect([
                        'remaining_qtn' => DB::table('fam_purchases_m as ppm')
                            ->select(DB::raw('(pod.product_quantity - IFNULL(SUM(ppd.product_quantity), 0))'))
                            ->leftjoin('fam_purchases_d as ppd', function ($queryData) {
                                $queryData->on('ppd.purchase_bill_no', '=', 'ppm.bill_no');
                            })
                            ->whereColumn([['pom.order_no', 'ppm.order_no'], ['ppd.product_id', 'pod.product_id']])
                            ->where([['ppm.is_delete', 0], ['ppm.is_active', 1]])
                            ->limit(1),
                    ])
                    ->get();

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $row) {

                    if ($row->remaining_qtn > 0) {
                        $output .= '<option value="' . $row->product_id . '" pname="' . $row->product_name . '" sbarcode="' . $row->product_code . '" pcprice="' . $row->prod_cost_price . '" prod_qtn="' . $row->remaining_qtn . '">'
                            . ($row->product_code != '' ? $row->product_name . ' [' . $row->product_code . ']' : $row->product_name) .
                            '</option>';
                    }
                }
            } else {
                $requisitionDetails = DB::table('fam_orders_d as prd')
                    ->where('prd.order_no', $orderNo)
                    ->join('fam_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $requisitionDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $requisitionDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $requisitionDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $requisitionDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('fam_purchases_m as pim')
                    ->where([['pim.order_no', $orderNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('fam_purchases_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.purchase_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('fam_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $issueDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $issueDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $issueDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $issueDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $issueDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('fam_products')
                        ->where('is_delete', 0)
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || product_name)
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", product_name)
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                }

                foreach ($queryData as $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }

                    if ($remaining_qtn > 0) {
                        $output .= '<option value="' . $row->id . '"
                                    pname="' . $row->product_name . '"
                                    sbarcode="' . $row->prod_barcode . '"
                                    pcprice="' . $row->cost_price . '"
                                    prod_qtn="' . $remaining_qtn . '">';

                        $output .= $row->product_name . ' [' . $row->prod_barcode . ']';

                        $output .= '</option>';
                    }
                }
            }

            echo $output;
        }
    }

    public function backUpgetReqProductInIssue(Request $request)
    {

        if ($request->ajax()) {
            $reqNo = (isset($request->reqNo)) ? $request->reqNo : null;

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;

            ##stock check code variable
            ## formId 15 = stockwise product load
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;


            ##stock check code variable
            $BranchID  = Common::getBranchId();
            $sDate     = null;
            $eDate     = Common::systemCurrentDate($BranchID, 'pos');
            // $checkstock = true;
            $FindStock = array();
            ##stock check code variable end

            $output = '';
            // if($reqNo == null){
            if (empty($reqNo)) {
                $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

                $queryData = DB::table('pos_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();


                ###stock check code
                if ($checkstock) {
                    $FindStock = POSS::stockQuantity_Multiple($BranchID, $queryData->pluck('id')->toArray(), $sDate, $eDate);
                }
                ###stock check code end

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $Row) {
                    ###stock check code
                    #default stock 1
                    $stock = 1;
                    if ($checkstock) {
                        $stock = $FindStock[$Row->id]['Stock'];
                    }
                    ###stock check end
                    #stock check condition
                    if ($stock >= 1) {

                        $output .= '<option value="' . $Row->id . '"
                        pname= "' . $Row->product_name . '"
                        sbarcode="' . $Row->sys_barcode . '"
                        pbarcode="' . $Row->prod_barcode . '"
                        stock="' . $stock . '"
                        pcprice="' . $Row->cost_price . '"
                        psprice="' . $Row->sale_price . '" >';
                        $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                        $output .= '</option>';
                    }
                }
            } else {

                $output = '<option value="">Select One</option>';

                /**
                 * Same Configuration a multi product entry dite hocche cost price change howar karone.
                 * tai branch theke ek product requisition korbe kintu cost price change howay product er
                 * notun barcode hobe, old barcode & new barcode mismatch hobe, tai branch theke kono
                 * nirdisto barcode select na kore product select korbe.
                 * requision table a jei product id ache ta only kaje asbe product er confuguration jante.
                 * db te change kora holo na ai muhurte.
                 * requisition er por issue kora hobe requisition er product er notun barcode onusare,
                 * jodi old barcode e thake tahole problem nei but new barcode hole branch ke
                 * obossoi notun barcode dewa ucit tai ai process a jete holo.
                 * issue te product load korar somoy check korche jei product select kora hoyeche,
                 *  same configuration er onno product gulo load kora hocche.
                 * jar configuration sob match korbe & product name o match korbe
                 */

                ## complete & delete check dewa hocche na karon front end theke sei check ache.

                $requisitionDetails = DB::table('pos_requisitions_d as prd')
                    ->where('prd.requisition_no', $reqNo)
                    ->join('pos_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', '=', 0], ['pp.is_active', '=', 1]])
                    ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $requisitionDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $requisitionDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $requisitionDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $requisitionDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    // ->selectRaw('')
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*, LOWER(REPLACE(pp.product_name, " ", "")) as product_name,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*, LOWER(REPLACE(pp.product_name, " ", "")) as product_name,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->get();

                // dd($requisitionDetails);
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('pos_issues_m as pim')
                    ->where([['pim.requisition_no', $reqNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('pos_issues_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.issue_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('pos_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', '=', 0], ['pp.is_active', '=', 1]])
                    ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $issueDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $issueDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $issueDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $issueDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $issueDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*, LOWER(REPLACE(pp.product_name, " ", "")) as product_name,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" ||
                            LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*, LOWER(REPLACE(pp.product_name, " ", "")) as product_name,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&",
                            LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->get();

                // dd($requisitionDetails,$issueDetails);

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('pos_products')
                        ->where([['is_delete', '=', 0], ['is_active', '=', 1]])
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        // ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        //->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();

                    // dd($queryData);
                }


                if ($checkstock) {
                    $FindStock = POSS::stockQuantity_Multiple($BranchID, $queryData->pluck('id')->toArray(), $sDate, $eDate);
                }


                foreach ($queryData as $row) {
                    #default stock 1
                    $stock = 1;
                    if ($checkstock) {
                        $stock = $FindStock[$row->id]['Stock'];
                    }


                    if ($row->remaining_qtn > 0) {
                        $output .= '<option value="' . $row->product_id . '" pname="' . $row->product_name . '" sbarcode="' . $row->prod_barcode . '" pcprice="' . $row->prod_cost_price . '" remainQtn="' . $row->remaining_qtn . '">' . $row->product_name . ' [' . $row->prod_barcode . ']' . '</option>';
                    }
                }
            }

            echo $output;
        }
    }

    public function getReqProductInIssue(Request $request)
    {

        if ($request->ajax()) {
            $reqNo = (isset($request->reqNo)) ? $request->reqNo : null;

            $ModelID    = (isset($request->ModelID)) ? $request->ModelID : null;
            $GroupID    = (isset($request->GroupID)) ? $request->GroupID : null;
            $CategoryID = (isset($request->CategoryID)) ? $request->CategoryID : null;
            $SubCatID   = (isset($request->SubCatID)) ? $request->SubCatID : null;
            // $CompanyID = (isset($request->CompanyID)) ? $request->CompanyID : null;
            $SupplierID = (isset($request->SupplierID)) ? $request->SupplierID : null;
            $prod_type_id = (isset($request->prod_type_id)) ? $request->prod_type_id : null;

            ##stock check code variable
            ## formId 15 = stockwise product load
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

            ##stock check code variable
            // $BranchID  = Common::getBranchId();
            $BranchID  = (isset($request->branch_id)) ? $request->branch_id : Common::getBranchId();
            $sDate     = null;
            $eDate     = Common::systemCurrentDate($BranchID, 'pos');
            // $checkstock = true;
            $FindStock = array();
            ##stock check code variable end

            $issue_bill = (isset($request->bill_no)) ? $request->bill_no : null;

            if (!empty($issue_bill)) {
                $IssueDetailsData = DB::table('pos_issues_d')->where('issue_bill_no', $issue_bill)
                    ->select('product_id', 'product_quantity')
                    ->pluck('product_quantity', 'product_id')
                    ->toArray();
            }
            //

            $ViewType = "withrequisition";
            $output = '';
            // if($reqNo == null){
            if (empty($reqNo)) {
                $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

                $ProdTypeArr    = (!empty($prod_type_id)) ? ['prod_type_id', '=', $prod_type_id] : ['prod_type_id', '<>', 3];


                $queryData = DB::table('pos_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0], ['is_active', '=', 1], $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr, $ProdTypeArr])
                    ->orderBy('product_name', 'ASC')
                    ->get();

                $ViewType = "withoutrequisition";


                ###stock check code
                $FindStock = POSS::stockQuantity_Multiple($BranchID, $queryData->pluck('id')->toArray(), $sDate, $eDate);
                ###stock check code end
                // dd($FindStock);

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $key => $Row) {
                    ###stock check code
                    #default stock
                    $stock = $FindStock[$Row->id]['Stock'];
                    // if($checkstock){
                    ##edit transfer details data if any then plus is with stocck
                    if (isset($IssueDetailsData[$Row->id])) {
                        $stock +=  $IssueDetailsData[$Row->id];
                    }
                    // }


                    $queryData[$key]->stock = $stock;

                    ###stock check end
                    #stock check condition
                    // if($stock >= 1){

                    //     $output .= '<option value="' . $Row->id . '"
                    //     pname= "' . $Row->product_name . '"
                    //     sbarcode="' . $Row->sys_barcode . '"
                    //     pbarcode="' . $Row->prod_barcode . '"
                    //     stock="' . $stock . '"
                    //     pcprice="' . $Row->cost_price . '"
                    //     psprice="' . $Row->sale_price . '" >';
                    //     $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                    //     $output .= '</option>';

                    // }

                }
            } else {

                // $output = '<option value="">Select One</option>';

                /**
                 * Same Configuration a multi product entry dite hocche cost price change howar karone.
                 * tai branch theke ek product requisition korbe kintu cost price change howay product er
                 * notun barcode hobe, old barcode & new barcode mismatch hobe, tai branch theke kono
                 * nirdisto barcode select na kore product select korbe.
                 * requision table a jei product id ache ta only kaje asbe product er confuguration jante.
                 * db te change kora holo na ai muhurte.
                 * requisition er por issue kora hobe requisition er product er notun barcode onusare,
                 * jodi old barcode e thake tahole problem nei but new barcode hole branch ke
                 * obossoi notun barcode dewa ucit tai ai process a jete holo.
                 * issue te product load korar somoy check korche jei product select kora hoyeche,
                 *  same configuration er onno product gulo load kora hocche.
                 * jar configuration sob match korbe & product name o match korbe
                 */

                ## complete & delete check dewa hocche na karon front end theke sei check ache.

                $requisitionDetails = DB::table('pos_requisitions_d as prd')
                    ->where('prd.requisition_no', $reqNo)
                    ->join('pos_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', '=', 0], ['pp.is_active', '=', 1]])
                    ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $requisitionDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $requisitionDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $requisitionDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $requisitionDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_id , pp.*, SUM(prd.product_quantity) as product_quantity,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_id , pp.*, SUM(prd.product_quantity) as product_quantity,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->groupBy('product_string')
                    ->get();


                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('pos_issues_m as pim')
                    ->where([['pim.requisition_no', $reqNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('pos_issues_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.issue_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('pos_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where([['pp.is_delete', '=', 0], ['pp.is_active', '=', 1]])
                    ->where(function ($issueDetails) use ($issue_bill) {
                        if (!empty($issue_bill)) {

                            $issueDetails->where('pid.issue_bill_no', '<>', $issue_bill);
                        }
                    })
                    ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                        if (!empty($SupplierID)) {
                            $issueDetails->where('pp.supplier_id', $SupplierID);
                        }
                        if (!empty($GroupID)) {
                            $issueDetails->where('pp.prod_group_id', $GroupID);
                        }
                        if (!empty($CategoryID)) {
                            $issueDetails->where('pp.prod_cat_id', $CategoryID);
                        }
                        if (!empty($SubCatID)) {
                            $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                        }
                        if (!empty($brandId)) {
                            $issueDetails->where('pp.prod_brand_id', $brandId);
                        }
                        if (!empty($ModelID)) {
                            $issueDetails->where('pp.prod_model_id', $ModelID);
                        }
                    })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_id , pp.*, SUM(pid.product_quantity) as product_quantity,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_id , pp.*, SUM(pid.product_quantity) as product_quantity,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", LOWER(REPLACE(pp.product_name, " ", "")))
                            as product_string');
                        }
                    })
                    ->groupBy('product_string')
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();
                //

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('pos_products')
                        ->where([['is_delete', '=', 0], ['is_active', '=', 1]])
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        // ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        //->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('* ,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            } else {
                                $query->selectRaw('* ,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", LOWER(REPLACE(product_name, " ", "")))
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                    $queryData =  $queryData->whereIn('product_string', array_unique($requisitionDetails->pluck('product_string')->toArray()));
                }

                // $FindStock = POSS::stockQuantity_Multiple($BranchID, $queryData->pluck('id')->toArray(), $sDate, $eDate);
                $FindStock = POSS::stockQuantity_Multiple($BranchID, !empty($queryData) ? $queryData->pluck('id')->toArray() : [], $sDate, $eDate);

                foreach ($queryData as $key => $row) {
                    #default stock
                    $stock = $FindStock[$row->id]['Stock'];
                    // if($checkstock){
                    ##edit transfer details data if any then plus is with stocck
                    if (isset($IssueDetailsData[$row->id])) {
                        // dd($IssueDetailsData);
                        $stock +=  $IssueDetailsData[$row->id];
                    }
                    // }


                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }


                    $queryData[$key]->stock = $stock;
                    $queryData[$key]->remaining_qtn = $remaining_qtn;

                    // #stock check condition
                    // if ($remaining_qtn > 0 &&  $stock >= 1) {
                    //     $output .= '<option value="' . $row->id . '"
                    //                 pname="' . $row->product_name . '"
                    //                 sbarcode="' . $row->prod_barcode . '"
                    //                 pcprice="' . $row->cost_price . '"
                    //                 stock="' . $stock . '"
                    //                 remainQtn="' . $remaining_qtn . '">';

                    //     $output .= $row->product_name . ' [' . $row->prod_barcode . ']';

                    //     $output .= '</option>';
                    // }


                }
            }

            $data = array(
                'queryData'   => $queryData,
                'ViewType' => $ViewType,
                'checkstock' => $checkstock,

            );

            return response()->json($data);
        }
    }

    public function getReqProductInIssueInv(Request $request)
    {

        if ($request->ajax()) {

            $reqNo = $request->reqNo;

            if (empty($reqNo)) {
                // $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                // $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                // $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                // $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                // $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

                $queryData = DB::table('inv_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0]])
                    // ,$SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr
                    ->orderBy('product_name', 'ASC')
                    ->get();

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $row) {

                    $output .= '<option value="' . $row->id . '"
                                            pname= "' . $row->product_name . '"
                                            sbarcode="' . $row->sys_barcode . '"
                                            pbarcode="' . $row->product_code . '"
                                            pcprice="' . $row->cost_price . '"
                                            psprice="' . $row->sale_price . '" >';
                    $output .= $row->product_name . ($row->product_code ? ' (' . $row->product_code . ')' : '');
                    $output .= '</option>';
                }
            } else {

                $output = '<option value="">Select One</option>';

                $requisitionDetails = DB::table('inv_requisitions_d as prd')
                    ->where('prd.requisition_no', $reqNo)
                    ->join('inv_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $requisitionDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $requisitionDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $requisitionDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $requisitionDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('inv_issues_m as pim')
                    ->where([['pim.requisition_no', $reqNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('inv_issues_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.issue_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('inv_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $issueDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $issueDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $issueDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $issueDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $issueDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('inv_products')
                        ->where('is_delete', 0)
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || product_name)
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", product_name)
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                }

                foreach ($queryData as $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }

                    if ($remaining_qtn > 0) {
                        $output .= '<option value="' . $row->id . '"
                                    pname="' . $row->product_name . '"
                                    sbarcode="' . $row->product_code . '"
                                    pcprice="' . $row->cost_price . '"
                                    remainQtn="' . $remaining_qtn . '">';

                        $output .= $row->product_name . ($row->product_code ? ' (' . $row->product_code . ')' : '');

                        $output .= '</option>';
                    }
                }
            }

            // $queryData = DB::table('inv_requisitions_m as prm')
            //     ->where([['prm.requisition_no', $reqNo], ['prm.is_complete', 0]])
            //     ->select('prd.product_id', 'prod.product_name', 'prod.cost_price', 'prod.product_code', 'prd.product_quantity')
            //     ->leftjoin('inv_requisitions_d as prd', function ($queryData) {
            //         $queryData->on('prm.requisition_no', '=', 'prd.requisition_no');
            //     })
            //     ->leftjoin('inv_products as prod', function ($queryData) {
            //         $queryData->on('prd.product_id', '=', 'prod.id')
            //             ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
            //     })
            //     ->addSelect(['remaining_qtn' => DB::table('inv_issues_m as pim')
            //             ->select(DB::raw('(prd.product_quantity - IFNULL(SUM(pid.product_quantity), 0))'))
            //             ->leftjoin('inv_issues_d as pid', function ($queryData) {
            //                 $queryData->on('pid.issue_bill_no', '=', 'pim.bill_no');
            //             })
            //             ->whereColumn([['prm.requisition_no', 'pim.requisition_no'], ['pid.product_id', 'prd.product_id']])
            //             ->where([['pim.is_delete', 0], ['pim.is_active', 1]])
            //             ->limit(1),
            //     ])
            //     ->get();

            // $output = '<option value="">Select One</option>';
            // foreach ($queryData as $row) {

            //     if ($row->remaining_qtn > 0) {
            //         $output .= '<option value="' . $row->product_id . '"
            //                         pname= "' . $row->product_name . '"
            //                         pcprice= "' . $row->cost_price . '"

            //                         sbarcode="' . $row->product_code . '"
            //                         remainQtn="' . $row->remaining_qtn . '">';
            //         $output .= $row->product_name . ($row->product_code ? ' (' . $row->product_code . ')' : '');
            //         $output .= '</option>';
            //     }
            // }

            echo $output;
        }
    }

    /////////// Fixed Asset Management //////////
    public function getReqProductInIssueFam(Request $request)
    {

        if ($request->ajax()) {

            $reqNo = $request->reqNo;

            if (empty($reqNo)) {
                // $SupplierArr = (!empty($SupplierID)) ? ['supplier_id', '=', $SupplierID] : ['supplier_id', '<>', ''];
                // $GroupArr    = (!empty($GroupID)) ? ['prod_group_id', '=', $GroupID] : ['prod_group_id', '<>', ''];
                // $CategoryArr = (!empty($CategoryID)) ? ['prod_cat_id', '=', $CategoryID] : ['prod_cat_id', '<>', ''];
                // $SubCatArr   = (!empty($SubCatID)) ? ['prod_sub_cat_id', '=', $SubCatID] : ['prod_sub_cat_id', '<>', ''];
                // $ModelArr    = (!empty($ModelID)) ? ['prod_model_id', '=', $ModelID] : ['prod_model_id', '<>', ''];

                $queryData = DB::table('fam_products')
                    ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                    ->where([['is_delete', '=', 0]])
                    // , $SupplierArr, $GroupArr, $CategoryArr, $SubCatArr, $ModelArr
                    ->orderBy('product_name', 'ASC')
                    ->get();

                $output = '<option value="">Select One</option>';
                foreach ($queryData as $Row) {

                    $output .= '<option value="' . $Row->id . '"
                                            pname= "' . $Row->product_name . '"
                                            sbarcode="' . $Row->sys_barcode . '"
                                            pbarcode="' . $Row->prod_barcode . '"
                                            pcprice="' . $Row->cost_price . '"
                                            psprice="' . $Row->sale_price . '" >';
                    $output .= $Row->product_name . ' [' . $Row->prod_barcode . ']';
                    $output .= '</option>';
                }
            } else {

                $output = '<option value="">Select One</option>';

                $requisitionDetails = DB::table('fam_requisitions_d as prd')
                    ->where('prd.requisition_no', $reqNo)
                    ->join('fam_products as pp', function ($query) {
                        $query->on('prd.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($requisitionDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $requisitionDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $requisitionDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $requisitionDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $requisitionDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $requisitionDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $requisitionDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('prd.product_quantity, prd.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();
                $requisitionProduct = $requisitionDetails->pluck('product_quantity', 'product_string')->toArray();

                ## issue
                $issueDetails = DB::table('fam_issues_m as pim')
                    ->where([['pim.requisition_no', $reqNo], ['pim.is_delete', 0], ['pim.is_active', 1]])
                    ->join('fam_issues_d as pid', function ($issueDetails) {
                        $issueDetails->on('pid.issue_bill_no', '=', 'pim.bill_no');
                    })
                    ->join('fam_products as pp', function ($query) {
                        $query->on('pid.product_id', '=', 'pp.id');
                    })
                    ->where('pp.is_delete', 0)
                    // ->where(function ($issueDetails) use ($SupplierID, $GroupID, $CategoryID, $SubCatID, $ModelID) {
                    //     if (!empty($SupplierID)) {
                    //         $issueDetails->where('pp.supplier_id', $SupplierID);
                    //     }
                    //     if (!empty($GroupID)) {
                    //         $issueDetails->where('pp.prod_group_id', $GroupID);
                    //     }
                    //     if (!empty($CategoryID)) {
                    //         $issueDetails->where('pp.prod_cat_id', $CategoryID);
                    //     }
                    //     if (!empty($SubCatID)) {
                    //         $issueDetails->where('pp.prod_sub_cat_id', $SubCatID);
                    //     }
                    //     if (!empty($brandId)) {
                    //         $issueDetails->where('pp.prod_brand_id', $brandId);
                    //     }
                    //     if (!empty($ModelID)) {
                    //         $issueDetails->where('pp.prod_model_id', $ModelID);
                    //     }
                    // })
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            (pp.prod_group_id || "*&" || pp.prod_cat_id || "*&" || pp.prod_sub_cat_id || "*&" || pp.prod_brand_id ||
                            "*&" || pp.prod_model_id || "*&" || pp.prod_size_id || "*&" || pp.prod_color_id || "*&" || pp.prod_uom_id || "*&" || pp.product_name)
                            as product_string');
                        } else {
                            $query->selectRaw('pid.product_quantity, pid.product_id , pp.*,
                            CONCAT(pp.prod_group_id, "*&", pp.prod_cat_id, "*&", pp.prod_sub_cat_id, "*&", pp.prod_brand_id,
                            "*&", pp.prod_model_id, "*&", pp.prod_size_id, "*&", pp.prod_color_id, "*&", pp.prod_uom_id, "*&", pp.product_name)
                            as product_string');
                        }
                    })
                    ->get();

                $issueProduct = $issueDetails->pluck('product_quantity', 'product_string')->toArray();

                $queryData = array();
                if (count($requisitionDetails->toArray()) > 0) {

                    $queryData = DB::table('fam_products')
                        ->where('is_delete', 0)
                        ->whereIn('prod_group_id', array_unique($requisitionDetails->pluck('prod_group_id')->toArray()))
                        ->whereIn('prod_cat_id', array_unique($requisitionDetails->pluck('prod_cat_id')->toArray()))
                        ->whereIn('prod_sub_cat_id', array_unique($requisitionDetails->pluck('prod_sub_cat_id')->toArray()))
                        ->whereIn('prod_brand_id', array_unique($requisitionDetails->pluck('prod_brand_id')->toArray()))
                        ->whereIn('prod_model_id', array_unique($requisitionDetails->pluck('prod_model_id')->toArray()))
                        ->whereIn('prod_size_id', array_unique($requisitionDetails->pluck('prod_size_id')->toArray()))
                        ->whereIn('prod_color_id', array_unique($requisitionDetails->pluck('prod_color_id')->toArray()))
                        ->whereIn('prod_uom_id', array_unique($requisitionDetails->pluck('prod_uom_id')->toArray()))
                        ->whereIn('product_name', array_unique($requisitionDetails->pluck('product_name')->toArray()))
                        // ->select(['id', 'product_name', 'cost_price', 'sale_price', 'prod_vat', 'sys_barcode', 'prod_barcode'])
                        ->when(true, function ($query) {
                            if (Common::getDBConnection() == "sqlite") {
                                $query->selectRaw('*,
                                (prod_group_id || "*&" || prod_cat_id || "*&" || prod_sub_cat_id || "*&" || prod_brand_id ||
                                "*&" || prod_model_id || "*&" || prod_size_id || "*&" || prod_color_id || "*&" || prod_uom_id || "*&" || product_name)
                                as product_string');
                            } else {
                                $query->selectRaw('*,
                                CONCAT(prod_group_id, "*&", prod_cat_id, "*&", prod_sub_cat_id, "*&", prod_brand_id,
                                "*&", prod_model_id, "*&", prod_size_id, "*&", prod_color_id, "*&", prod_uom_id, "*&", product_name)
                                as product_string');
                            }
                        })
                        ->orderBy('product_name', 'ASC')
                        ->get();
                }

                foreach ($queryData as $row) {

                    $requisitionProductQty = isset($requisitionProduct[$row->product_string]) ? $requisitionProduct[$row->product_string] : 0;
                    $remaining_qtn         = $requisitionProductQty;

                    if (count($issueProduct) > 0) {
                        $issueProductQty = isset($issueProduct[$row->product_string]) ? $issueProduct[$row->product_string] : 0;
                        $remaining_qtn   = ($requisitionProductQty - $issueProductQty);
                    }

                    if ($remaining_qtn > 0) {
                        $output .= '<option value="' . $row->id . '"
                                    pname="' . $row->product_name . '"
                                    sbarcode="' . $row->prod_barcode . '"
                                    pcprice="' . $row->cost_price . '"
                                    remainQtn="' . $remaining_qtn . '">';

                        $output .= $row->product_name . ' [' . $row->prod_barcode . ']';

                        $output .= '</option>';
                    }
                }
            }

            echo $output;
        }
    }

    public function getReqLoadIssue(Request $request)
    {

        if ($request->ajax()) {

            $branchId       = $request->branchId;
            $selRequisition = $request->selRequisition;

            $queryData = DB::table('pos_requisitions_m')
                ->where([
                    ['branch_from', $branchId],
                    ['is_approve', 1],
                    // ['is_complete', 0],
                    ['is_active', 1],
                    ['is_delete', 0]
                ])
                ->where(function ($queryData) use ($selRequisition) {
                    if (!empty($selRequisition)) {
                        $queryData->where('is_complete', 0)
                            ->orWhere('requisition_no', $selRequisition);
                    } else {
                        $queryData->where('is_complete', 0);
                    }
                })
                ->select('requisition_no')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($queryData as $row) {
                $selectText = ($selRequisition == $row->requisition_no) ? "selected" : "";

                $output .= '<option value="' . $row->requisition_no . '" ' . $selectText . ' >';
                $output .= $row->requisition_no;
                $output .= '</option>';
            }

            echo $output;
        }
    }

    public function getReqLoadIssueInv(Request $request)
    {

        if ($request->ajax()) {

            $branchId       = $request->branchId;
            $selRequisition = $request->selRequisition;

            $queryData = DB::table('inv_requisitions_m')
                ->where([
                    ['branch_from', $branchId],
                    ['is_approve', 1],
                    // ['is_complete', 0],
                    ['is_active', 1],
                    ['is_delete', 0]
                ])
                ->where(function ($queryData) use ($selRequisition) {
                    if (!empty($selRequisition)) {
                        $queryData->where('is_complete', 0)
                            ->orWhere('requisition_no', $selRequisition);
                    } else {
                        $queryData->where('is_complete', 0);
                    }
                })
                ->select('requisition_no')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($queryData as $row) {
                $selectText = ($selRequisition == $row->requisition_no) ? "selected" : "";

                $output .= '<option value="' . $row->requisition_no . '" ' . $selectText . ' >';
                $output .= $row->requisition_no;
                $output .= '</option>';
            }

            echo $output;
        }
    }

    ///////////// Fixed Asset Management /////////

    public function getReqLoadIssueFam(Request $request)
    {

        if ($request->ajax()) {

            $branchId       = $request->branchId;
            $selRequisition = $request->selRequisition;

            $queryData = DB::table('fam_requisitions_m')
                ->where([
                    ['branch_from', $branchId],
                    ['is_approve', 1],
                    // ['is_complete', 0],
                    ['is_active', 1],
                    ['is_delete', 0]
                ])
                ->where(function ($queryData) use ($selRequisition) {
                    if (!empty($selRequisition)) {
                        $queryData->where('is_complete', 0)
                            ->orWhere('requisition_no', $selRequisition);
                    } else {
                        $queryData->where('is_complete', 0);
                    }
                })
                ->select('requisition_no')
                ->get();

            $output = '<option value="">Select One</option>';
            foreach ($queryData as $row) {
                $selectText = ($selRequisition == $row->requisition_no) ? "selected" : "";

                $output .= '<option value="' . $row->requisition_no . '" ' . $selectText . ' >';
                $output .= $row->requisition_no;
                $output .= '</option>';
            }

            echo $output;
        }
    }

    public function getLoadSupplierProdByReq(Request $request)
    {
        if ($request->ajax()) {

            $supplier = $request->supplier;

            $queryData = DB::table('pos_requisitions_d as prd')
                ->where([['prd.is_ordered', 0]])
                ->select(
                    'prm.*',
                    'prd.id as prd_id',
                    'prod.supplier_id',
                    'prd.product_id',
                    'prd.product_quantity',
                    'prod.product_name',
                    'prod.sys_barcode',
                    'prod.prod_barcode',
                    'ps.sup_comp_name',
                    'brf.branch_name as branch_from_name'
                )
                ->leftjoin('pos_requisitions_m as prm', function ($queryData) {
                    $queryData->on('prd.requisition_id', '=', 'prm.id')
                        ->where([['prm.is_delete', 0], ['prm.is_active', 1]]);
                })
                ->leftjoin('pos_products as prod', function ($queryData) {
                    $queryData->on('prod.id', '=', 'prd.product_id')
                        ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
                })
                ->leftjoin('pos_suppliers as ps', function ($queryData) {
                    $queryData->on('ps.id', '=', 'prod.supplier_id')
                        ->where([['ps.is_delete', 0], ['ps.is_active', 1]]);
                })
                ->leftjoin('gnl_branchs as brf', function ($queryData) {
                    $queryData->on('prm.branch_from', '=', 'brf.id')
                        ->where('brf.is_approve', 1);
                })
                ->addSelect([
                    'remaining_qtn' => DB::table('pos_orders_d as pod')
                        ->select(DB::raw('(prd.product_quantity - IFNULL(SUM(pod.product_quantity), 0))'))
                        ->whereColumn([['prm.requisition_no', 'pod.requisition_no'], ['pod.product_id', 'prd.product_id']])
                        ->limit(1),
                ])
                ->where(function ($queryData) use ($supplier) {
                    if (!empty($supplier)) {
                        $queryData->where('prod.supplier_id', $supplier);
                    }
                })
                ->orderBy('prm.requisition_no')
                ->get();

            if (count($queryData->toArray()) == 0) {
                $dataSet = '<td colspan="10">No rows data for order</td>';
                return $dataSet;
            }

            $dataSet = '';
            $i       = 0;
            foreach ($queryData as $row) {
                $i++;
                $output = '';

                $output = '<tr>' .
                    '<td onclick="fnCheck(' . $i . ');">' .
                    '<input type="checkBox" name="order_check_box_arr[]" class="ckeckBoxCls" id="order_check_box_' . $i . '" value="' . $row->prd_id . '" supplier="' . $row->supplier_id . '" onclick="fnCheck(' . $i . ');">' .
                    '</td>' .

                    '<td>' . $i . '</td>' .

                    '<td class="text-left">' . $row->product_name . ' [' . $row->prod_barcode . ']' . '</td>' .

                    '<td>' . date('d-m-Y', strtotime($row->requisition_date)) . '</td>' .

                    '<td>' . $row->requisition_no . '</td>' .

                    '<td>' . "Head Office" . '</td>' .

                    '<td>' . $row->branch_from_name . '</td>' .

                    '<td width="10%">' .
                    '<input type="number" name="product_quantity_arr[]"  id="total_quantity_id_' . $i . '" class="form-control round textNumber" value="' . $row->remaining_qtn . '" readonly="true">' .
                    '</td>' .

                    '<td>' . $row->sup_comp_name . '</td>' .

                    '<td>' .
                    '<a href="' . url('pos/requisition/view/' . $row->id) . '" title="View" class="btnView">
                                    <i class="icon wb-eye mr-2 blue-grey-600"></i>
                                </a>' .

                    '<input type="text" name="requisition_id_arr[]" id="requisition_id_' . $i . '" value="' . $row->prd_id . '" hidden="true">

                                <input type="text" name="order_to_arr[]" id="supplier_id_' . $i . '" value="' . $row->supplier_id . '" hidden="true">

                                <input type="text" name="requisition_no_arr[]" id="requisition_no_id_' . $i . '" value="' . $row->requisition_no . '" hidden="true">

                                <input type="text" name="requisition_date_arr[]" id="requisition_date_id_' . $i . '" value="' . $row->requisition_date . '" hidden="true">

                                <input type="text" name="requisition_branch_from_arr[]" id="requisition_branch_from_id_' . $i . '" value="' . $row->branch_from . '" hidden="true">' .

                    '<input type="text" name="product_id_arr[]" id="product_id_' . $i . '" value="' . $row->product_id . '" hidden="true">' .
                    '</td>' .
                    '</tr>';

                // <input type="text" name="total_quantity_arr[]" id="product_quantity_id_'.$i.'" value="'.$row->total_quantity.'" hidden="true">

                $dataSet .= $output;
            }

            echo $dataSet;
        }
    }

    public function ajaxGetRequisitionNo(Request $request)
    {
        $BranchId = $request->BranchId;

        $reqNo = POSS::generateBillRequisiton($BranchId);

        $branch_soft_date = Common::systemCurrentDate($BranchId, 'pos');

        return [
            'reqNo'            => $reqNo,
            'branch_soft_date' => $branch_soft_date,
        ];
    }

    public function ajaxGetRequisitionNoInv(Request $request)
    {
        $BranchId = $request->BranchId;

        $reqNo = INVS::generateBillRequisiton($BranchId);

        $branch_soft_date = Common::systemCurrentDate($BranchId, 'inv');

        return [
            'reqNo'            => $reqNo,
            'branch_soft_date' => $branch_soft_date,
        ];
    }

    /////////////// Fixed Asset Management //////////
    public function ajaxGetRequisitionNoFam(Request $request)
    {
        $BranchId = $request->BranchId;

        $reqNo = FAMS::generateBillRequisiton($BranchId);

        $branch_soft_date = Common::systemCurrentDate($BranchId, 'fam');

        return [
            'reqNo'            => $reqNo,
            'branch_soft_date' => $branch_soft_date,
        ];
    }

    public function ajaxCollectionNo(Request $request)
    {
        $BranchId = $request->BranchId;

        $BillNo = POSS::generateCollectionNo($BranchId);

        return $BillNo;
    }

    public function ajaxPurchaseBillNo(Request $request)
    {
        $supplierId = $request->supplierId;

        // $SupplierPaymentBillNo = POSS::generateSupplierPaymentBillNo($supplierId);
        ## function ti nai pos service a tai null diye rekhechi
        $SupplierPaymentBillNo = null;


        return $SupplierPaymentBillNo;
    }

    public static function ajaxGetLedgerForBranch(Request $request)
    {

        $branch_id  = $request->branch_id;
        $project_id = $request->project_id;
        $acc_type   = $request->acc_type;
        $returnType = (isset($request->returnType)) ? $request->returnType : 'text';

        $ledgerHeads = ACCS::getLedgerAccount($branch_id, $project_id, null, $acc_type);

        if ($returnType == 'json') {
            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $ledgerHeads,
            ];

            return response()->json($data);
        } else {
            $output = '<option value="">Select One</option>';
            foreach ($ledgerHeads as $row) {

                $output .= '<option value="' . $row->id . '" ' . ' >';
                $output .= $row->code . ' - ' . $row->name;
                $output .= '</option>';
            }
            return $output;
        }
    }

    /////////////////////

    public function ajaxGetGroup(Request $request)
    {
        if ($request->ajax()) {

            $isActive = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;
            $prodTypeId = (isset($request->prodTypeId) && !empty($request->prodTypeId)) ? $request->prodTypeId : null;

            $groupIdFromCatArr = array();
            $groupIdFromCatArr = DB::table('pos_p_categories')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($queryData) use ($prodTypeId) {
                    if (!empty($prodTypeId)) {
                        $queryData->where('prod_type_id', $prodTypeId);
                    }
                })
                ->pluck('prod_group_id')
                ->unique()
                ->toArray();

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_groups')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_groups')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_groups')
                    ->where('is_delete', 0)
                    ->whereIn('id', $groupIdFromCatArr)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function backup_ajaxGetGroup(Request $request)
    {
        if ($request->ajax()) {

            $isActive = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_groups')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_groups')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            } else {
                $queryData = DB::table('pos_p_groups')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, group_name as field_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetCategory(Request $request)
    {
        if ($request->ajax()) {

            $groupId  = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $isActive = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $prodTypeId = (isset($request->prodTypeId) && !empty($request->prodTypeId)) ? $request->prodTypeId : null;

            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_categories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, cat_name as field_name')
                    ->orderBy('cat_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_categories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, cat_name as field_name')
                    ->orderBy('cat_name', 'ASC')
                    ->get();
            } else {
                $queryData = DB::table('pos_p_categories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $isActive, $prodTypeId) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }

                        if (!empty($prodTypeId)) {
                            $queryData->where('prod_type_id', $prodTypeId);
                        }
                    })
                    ->selectRaw('id as field_id, cat_name as field_name')
                    ->orderBy('cat_name', 'ASC')
                    ->get();
            }

            // if (Common::getDBConnection() == "sqlite") {}

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetSubCat(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_subcategories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sub_cat_name as field_name')
                    ->orderBy('sub_cat_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_subcategories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sub_cat_name as field_name')
                    ->orderBy('sub_cat_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_subcategories')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sub_cat_name as field_name')
                    ->orderBy('sub_cat_name', 'ASC')
                    ->get();
            }
            // if (Common::getDBConnection() == "sqlite") {}

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetModel(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_models')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, model_name as field_name')
                    ->orderBy('model_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_models')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, model_name as field_name')
                    ->orderBy('model_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_models')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, model_name as field_name')
                    ->orderBy('model_name', 'ASC')
                    ->get();
            }
            // if (Common::getDBConnection() == "sqlite") {}

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetBrand(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_brands')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, brand_name as field_name')
                    ->orderBy('brand_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_brands')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, brand_name as field_name')
                    ->orderBy('brand_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_brands')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, brand_name as field_name')
                    ->orderBy('brand_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetColor(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_colors')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, color_name as field_name')
                    ->orderBy('color_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_colors')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, color_name as field_name')
                    ->orderBy('color_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_colors')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, color_name as field_name')
                    ->orderBy('color_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetUom(Request $request)
    {
        if ($request->ajax()) {

            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_p_uoms')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, uom_name as field_name')
                    ->orderBy('uom_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_p_uoms')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($queryData) use ($isActive) {

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, uom_name as field_name')
                    ->orderBy('uom_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetSupplier(Request $request)
    {
        if ($request->ajax()) {

            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_suppliers')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sup_comp_name as field_name')
                    ->orderBy('sup_comp_name', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_suppliers')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sup_comp_name as field_name')
                    ->orderBy('sup_comp_name', 'ASC')
                    ->get();
            } else {

                $queryData = DB::table('pos_suppliers')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($isActive) {
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, sup_comp_name as field_name')
                    ->orderBy('sup_comp_name', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetCustomer(Request $request)
    {
        if ($request->ajax()) {

            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'inv') {

            } else if ($moduleName == 'fam') {


            } else {

                if (Common::getDBConnection() == "sqlite") {
                    $queryData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($queryData) use ($isActive) {
                            if (!empty($isActive)) {
                                $queryData->where('is_active', $isActive);
                            }
                        })
                        ->selectRaw('customer_no as field_id, (customer_name || " [M:" || customer_mobile || " NID:" || customer_nid || "]") AS field_name, ')
                        ->orderBy('customer_name', 'ASC')
                        ->get();
                } else {
                    $queryData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($queryData) use ($isActive) {
                            if (!empty($isActive)) {
                                $queryData->where('is_active', $isActive);
                            }
                        })

                        ->selectRaw('customer_no as field_id, CONCAT(customer_name, " [M:", customer_mobile, "- NID:", customer_nid, "]") AS field_name')
                        ->orderBy('customer_name', 'ASC')
                        ->get();
                }
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetProduct(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $brandId    = (isset($request->brandId) && !empty($request->brandId)) ? $request->brandId : null;
            $modelId    = (isset($request->modelId) && !empty($request->modelId)) ? $request->modelId : null;
            $supplierId = (isset($request->supplierId) && !empty($request->supplierId)) ? $request->supplierId : null;

            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;
            $prodTypeId = (isset($request->prodTypeId) && !empty($request->prodTypeId)) ? $request->prodTypeId : null;
            $sizeId = (isset($request->sizeId) && !empty($request->sizeId)) ? $request->sizeId : null;

            if ($moduleName == 'inv') {
                $queryData = DB::table('inv_products')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $brandId, $modelId, $supplierId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($brandId)) {
                            $queryData->where('prod_brand_id', $brandId);
                        }

                        if (!empty($modelId)) {
                            $queryData->where('prod_model_id', $modelId);
                        }
                        if (!empty($supplierId)) {
                            $queryData->where('supplier_id', $supplierId);
                        }
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, product_name as field_name')
                    ->orderBy('id', 'ASC')
                    ->get();
            } else if ($moduleName == 'fam') {

                $queryData = DB::table('fam_products')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $brandId, $modelId, $supplierId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($brandId)) {
                            $queryData->where('prod_brand_id', $brandId);
                        }

                        if (!empty($modelId)) {
                            $queryData->where('prod_model_id', $modelId);
                        }
                        if (!empty($supplierId)) {
                            $queryData->where('supplier_id', $supplierId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, prod_code as field_name')
                    ->orderBy('id', 'ASC')
                    ->get();
            } else {
                if (Common::getDBConnection() == "sqlite") {
                    $queryData = DB::table('pos_products')
                        ->where([['is_delete', '=', 0], ['is_active', '=', 1]])
                        ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $brandId, $modelId, $supplierId, $isActive) {
                            if (!empty($groupId)) {
                                $queryData->where('prod_group_id', $groupId);
                            }

                            if (!empty($categoryId)) {
                                $queryData->where('prod_cat_id', $categoryId);
                            }

                            if (!empty($subCatId)) {
                                $queryData->where('prod_sub_cat_id', $subCatId);
                            }

                            if (!empty($brandId)) {
                                $queryData->where('prod_brand_id', $brandId);
                            }

                            if (!empty($modelId)) {
                                $queryData->where('prod_model_id', $modelId);
                            }
                            if (!empty($supplierId)) {
                                $queryData->where('supplier_id', $supplierId);
                            }
                            if (!empty($isActive)) {
                                $queryData->where('is_active', $isActive);
                            }
                        })
                        ->selectRaw('id as field_id, (product_name || " [" || prod_barcode || "]") as field_name')
                        ->orderBy('prod_barcode', 'ASC')
                        ->get();
                } else {
                    ##Load Gold Item By Size
                    if (!empty($sizeId)) {

                        $queryData = DB::table('pos_product_details as ppd')
                            ->where([['pp.is_delete', '=', 0], ['pp.is_active', '=', 1], ['ppd.status', '=', 0]])
                            ->join('pos_products as pp', function ($masterQuery) use ($sizeId) {
                                $masterQuery->on('ppd.product_id', '=', 'pp.id')
                                    ->where([['ppd.prod_size_id', $sizeId]]);
                            })
                            ->where(function ($queryData) use ($prodTypeId, $groupId, $categoryId, $subCatId, $brandId, $modelId, $supplierId, $isActive) {
                                if (!empty($prodTypeId)) {
                                    $queryData->where('pp.prod_type_id', $prodTypeId);
                                }

                                if (!empty($groupId)) {
                                    $queryData->where('pp.prod_group_id', $groupId);
                                }

                                if (!empty($categoryId)) {
                                    $queryData->where('pp.prod_cat_id', $categoryId);
                                }

                                if (!empty($subCatId)) {
                                    $queryData->where('pp.prod_sub_cat_id', $subCatId);
                                }

                                if (!empty($brandId)) {
                                    $queryData->where('pp.prod_brand_id', $brandId);
                                }

                                if (!empty($modelId)) {
                                    $queryData->where('pp.prod_model_id', $modelId);
                                }

                                if (!empty($supplierId)) {
                                    $queryData->where('pp.supplier_id', $supplierId);
                                }

                                if (!empty($isActive)) {
                                    $queryData->where('pp.is_active', $isActive);
                                }
                            })
                            ->selectRaw('pp.id as field_id, CONCAT(pp.product_name, " [", pp.prod_barcode, "]") as field_name')
                            ->orderBy('pp.prod_barcode', 'ASC')
                            ->get();
                    } else {
                        $queryData = DB::table('pos_products')
                            ->where([['is_delete', '=', 0], ['is_active', '=', 1]])
                            ->where(function ($queryData) use ($prodTypeId, $groupId, $categoryId, $subCatId, $brandId, $modelId, $supplierId, $isActive) {
                                if (!empty($prodTypeId)) {
                                    $queryData->where('prod_type_id', $prodTypeId);
                                }

                                if (!empty($groupId)) {
                                    $queryData->where('prod_group_id', $groupId);
                                }

                                if (!empty($categoryId)) {
                                    $queryData->where('prod_cat_id', $categoryId);
                                }

                                if (!empty($subCatId)) {
                                    $queryData->where('prod_sub_cat_id', $subCatId);
                                }

                                if (!empty($brandId)) {
                                    $queryData->where('prod_brand_id', $brandId);
                                }

                                if (!empty($modelId)) {
                                    $queryData->where('prod_model_id', $modelId);
                                }

                                if (!empty($supplierId)) {
                                    $queryData->where('supplier_id', $supplierId);
                                }

                                if (!empty($isActive)) {
                                    $queryData->where('is_active', $isActive);
                                }
                            })
                            ->selectRaw('id as field_id, CONCAT(product_name, " [", prod_barcode, "]") as field_name')
                            ->orderBy('prod_barcode', 'ASC')
                            ->get();
                    }
                }
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetProductName(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $typeId     = (isset($request->typeId) && !empty($request->typeId)) ? $request->typeId : null;

            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_names')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $typeId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }

                        if (!empty($typeId)) {
                            $queryData->where('prod_type_id', $typeId);
                        }

                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, prod_name as field_name')
                    ->orderBy('id', 'ASC')
                    ->get();
            }
            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }

    public function ajaxGetProductType(Request $request)
    {
        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;

            if ($moduleName == 'fam') {

                $queryData = DB::table('fam_p_types')
                    ->where('is_delete', 0)
                    ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive) {
                        if (!empty($groupId)) {
                            $queryData->where('prod_group_id', $groupId);
                        }

                        if (!empty($categoryId)) {
                            $queryData->where('prod_cat_id', $categoryId);
                        }

                        if (!empty($subCatId)) {
                            $queryData->where('prod_sub_cat_id', $subCatId);
                        }
                        if (!empty($isActive)) {
                            $queryData->where('is_active', $isActive);
                        }
                    })
                    ->selectRaw('id as field_id, prod_type as field_name')
                    ->orderBy('id', 'ASC')
                    ->get();
            } else if ($moduleName == 'pos') {
                $queryData = DB::table("gnl_dynamic_form_value")
                    ->where([["is_delete", 0], ["is_active", 1], ['type_id', 4], ['form_id', 1]])
                    // ->select('id', 'uid','name','value_field')
                    ->selectRaw('uid as field_id, name as field_name')
                    ->orderBy('uid', 'ASC')
                    ->get();
            }

            $data = [
                'status'      => 'success',
                'message'     => '',
                'result_data' => $queryData,
            ];

            return response()->json($data);
        }
    }


    public function ajaxCurrentFiscalYear(Request $request)
    {
        if ($request->ajax()) {

            $branchId   = (isset($request->branchId) && !empty($request->branchId)) ? $request->branchId : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;
            $currentFY  = (isset($request->currentFY) && !empty($request->currentFY)) ? $request->currentFY : true;
            $fiscalYearLoad  = (isset($request->fiscalYearLoad) && !empty($request->fiscalYearLoad)) ? $request->fiscalYearLoad : "FFY";

            $branchDate    = Common::systemCurrentDate($branchId, $moduleName);
            $brOpeningDate = Common::getBranchSoftwareStartDate($branchId, $moduleName);

            if ($currentFY === true) {
                $companyId       = Common::getCompanyId();
                $current_fy_data = Common::systemFiscalYear($branchDate, $companyId, null, $moduleName, $fiscalYearLoad);

                if ($current_fy_data['id'] == 0) {
                    $data = [
                        'status'      => 'error',
                        'message'     => 'Fiscal Year not found. Please entry fiscal year first.',
                        'result_data' => '',
                    ];
                } else {
                    $data = [
                        'status'          => 'success',
                        'message'         => '',
                        'result_data'     => $current_fy_data,
                        'brOpeningDate'   => $brOpeningDate,
                        'loginSystemDate' => (new DateTime($branchDate))->format('Y-m-d'),
                    ];
                }
            } else {
                $data = [
                    'status'          => 'success',
                    'message'         => '',
                    'brOpeningDate'   => $brOpeningDate,
                    'loginSystemDate' => (new DateTime($branchDate))->format('Y-m-d'),
                ];
            }

            return response()->json($data);
        }
    }

    public function ajaxUpdatedSalePrice(Request $request)
    {
        if ($request->ajax()) {

            $selProdId = (isset($request->selProdId) && !empty($request->selProdId)) ? $request->selProdId : null;
            $salesDate = (isset($request->salesDate) && !empty($request->salesDate)) ? $request->salesDate : null;

            $salesBillNo = (isset($request->salesBillNo) && !empty($request->salesBillNo)) ? $request->salesBillNo : null;

            if (!empty($salesDate)) {
                $salesDate = (new Datetime($salesDate))->format('Y-m-d');
            }

            $flag         = false;
            $updatedPrice = ''; {
                $priceUpdate = DB::table('pos_price_updating_m')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

                $priceM = $priceUpdate->where('effective_date', '<=', $salesDate);

                if (count($priceM) > 0) {

                    $productCode = $priceM->pluck('price_updating_code');

                    $queryData = DB::table('pos_price_updating_d')
                        ->where('product_id', $selProdId)
                        ->where(function ($queryData) use ($productCode) {
                            if (count($productCode) > 0) {
                                $queryData->whereIn('price_updating_code', $productCode);
                            }
                        })
                        ->orderBy('updated_at', 'DESC')
                        ->orderBy('created_at', 'DESC')
                        ->first();

                    if ($queryData) {

                        $updatedPrice = $queryData->updated_price;
                    }
                }
            }

            $data = [
                'status'      => $updatedPrice ? 'success' : 'unsuccessful',
                'message'     => '',
                'result_data' => $updatedPrice,
            ];

            return response()->json($data);
        }
    }

    ## Function to get Sales Bill No Under Employee
    public function ajaxBillLSalesTransfer(Request $request)
    {

        if ($request->ajax()) {

            $branchId = (isset($request->branchId)) ? $request->branchId : null;

            $transferFrom = (isset($request->transferFrom)) ? $request->transferFrom : null;

            $transferTo = (isset($request->transferTo)) ? $request->transferTo : null;

            $transFromBillArr = [];
            $transToBillArr   = [];

            $transferedFromBillArr = DB::table('pos_sales_transfer')
                ->where([
                    ['is_delete', 0], ['is_active', 1],
                    ['branch_id', $branchId], ['transfer_from', $transferFrom]
                ])
                ->pluck('sales_bill_no_arr')
                ->toArray();

            $transferedToBillArr = DB::table('pos_sales_transfer')
                ->where([
                    ['is_delete', 0], ['is_active', 1],
                    ['branch_id', $branchId], ['transfer_to', $transferFrom]
                ])
                ->pluck('sales_bill_no_arr')
                ->toArray();

            foreach ($transferedFromBillArr as $tBill) {
                $billArr = explode(",", $tBill);
                foreach ($billArr as $bill) {
                    array_push($transFromBillArr, $bill);
                }
            }

            foreach ($transferedToBillArr as $tBill) {
                $billArr = explode(",", $tBill);
                foreach ($billArr as $bill) {
                    array_push($transToBillArr, $bill);
                }
            }

            ## Query
            $QueryData = DB::table('pos_sales_m')
                // ->select(['id', 'sales_bill_no'])
                ->where([['is_delete', 0], ['is_active', 1], ['is_complete', 0], ['branch_id', $branchId]])
                ->whereNotIn('sales_bill_no', $transFromBillArr)
                // ->whereIn('sales_bill_no',$transToBillArr)
                ->whereRaw('CASE WHEN transfer_to IS NOT NULL THEN transfer_to = "' . $transferFrom . '" ELSE employee_id = "' . $transferFrom . '" END')
                ->orderBy('sales_bill_no', 'ASC')
                ->pluck('sales_bill_no')
                ->toArray();

            $salesBillArr = array_unique(array_merge($QueryData, $transToBillArr));

            $output = '<option value="">Select One</option>';
            foreach ($salesBillArr as $sales_bill_no) {

                $output .= '<option value="' . $sales_bill_no . '">';
                $output .= $sales_bill_no;
                $output .= '</option>';
            }

            echo $output;
        }
    }

    ## get size load by available product id for gold product
    public function ajaxGetSizeForGoldBackup(Request $request)
    {

        $productId = $request->productId;
        // $sizeIdArr = (isset($request->sizeId)) ? substr($request->sizeId, 1, -1) : array();
        $sizeIdArr = (isset($request->sizeId)) ? json_decode($request->sizeId) : array();
        $BranchId = (isset($request->BranchId)) ? $request->BranchId : Common::getBranchId();
        $toDate = (new DateTime(Common::systemCurrentDate($BranchId, 'pos')))->format('Y-m-d');

        $sizeStockData = DB::table('pos_products as pp')
            ->where(function ($sizeStockData) use ($productId) {
                if (!empty($productId)) {
                    $sizeStockData->where('pp.id', $productId);
                }
            })
            ->where([['pp.is_delete', 0], ['pp.prod_type_id', 3]])
            ->leftjoin('pos_product_details as ppd', function ($sizeStockData) {
                $sizeStockData->on('ppd.product_id', '=', 'pp.id');
            })
            ->select('pp.id', 'ppd.prod_size_id', 'ppd.serial_barcode', 'ppd.unit_cost_price', 'ppd.unit_other_cost')
            ->groupBy('ppd.prod_size_id')
            ->get();

        $unique_size_ids = array();
        $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

        ###stock check code
        if ($checkstock) {
            $stockData = POSS::stockSerialProductQuantity_Multiple($BranchId, $sizeStockData, null, $toDate);

            foreach ($sizeStockData as $row) {
                if ($stockData[$row->id][$row->prod_size_id]['Stock'] != 0) {
                    array_push($unique_size_ids, $row->prod_size_id);
                }
            }

            if (count($sizeIdArr) > 0) {
                $unique_size_ids = array_merge($unique_size_ids, $sizeIdArr);
            }

            $unique_size_ids =  array_unique($unique_size_ids);
        }

        ###stock check code end
        if (!$checkstock) {
            $all_size_ids = $sizeStockData->pluck('prod_size_id');
            array_push($unique_size_ids, $all_size_ids);
        }

        $ProductSizeData = DB::table('pos_p_sizes as psize')
            ->where('psize.is_active', 1)
            ->whereIn('psize.id', $unique_size_ids)
            ->select('psize.size_name', 'psize.id')
            ->get();

        $data = array(
            'ProductSizeData' => !empty($ProductSizeData) ? $ProductSizeData : null,
        );

        return $data;
    }

    ## Get size load by available product id for gold product where "reportFlag = false"
    ## And all Gold products for report "reportFlag = true"
    public function ajaxGetSizeForGold(Request $request)
    {

        $productId = $request->productId;
        $reportFlag = isset($request->reportFlag) ? $request->reportFlag : false;

        ## elementArrayFlag ta use kora holo coz, notun filter a size load hoy 'psize.id as field_id, psize.size_name as field_name' select hoye
        ## Onnano jaygay size load hoy 'psize.id, psize.size_name, ppd.unit_cost_price, ppd.unit_other_cost, ppd.serial_barcode' select hoye
        $elementArrayFlag = isset($request->elementArrayFlag) ? $request->elementArrayFlag : false;

        $prodIdArr  = (isset($request->prodIdArr) && !empty($request->prodIdArr)) ? $request->prodIdArr : array();
        // $sizeIdArr = (isset($request->sizeId)) ? substr($request->sizeId, 1, -1) : array();
        $sizeIdArr = (isset($request->sizeId)) ? json_decode($request->sizeId) : array();
        $BranchId = (isset($request->BranchId)) ? $request->BranchId : Common::getBranchId();
        $toDate = (new DateTime(Common::systemCurrentDate($BranchId, 'pos')))->format('Y-m-d');

        // dd($sizeIdArr, $prodIdArr);
        $sizeStockData = DB::table('pos_products as pp')
            ->where(function ($sizeStockData) use ($productId) {
                if (!empty($productId)) {
                    $sizeStockData->where('pp.id', $productId);
                }
            })
            ->where([['pp.is_delete', 0], ['pp.prod_type_id', 3]])
            ->leftjoin('pos_product_details as ppd', function ($sizeStockData) {
                $sizeStockData->on('ppd.product_id', '=', 'pp.id');
            })
            ->select('pp.id', 'ppd.prod_size_id', 'ppd.serial_barcode', 'ppd.unit_cost_price')
            // ->selectRaw('pp.id, ppd.serial_barcode, ppd.unit_cost_price,
            // (CASE WHEN ' . $prodTypeId . ' = 3 THEN ppd.prod_size_id ELSE pp.prod_size_id END) as prod_size_id')
            ->groupBy('pp.id')
            ->groupBy('ppd.prod_size_id')
            ->get();

        $unique_size_ids = array();

        if ($reportFlag) {
            $all_size_ids = $sizeStockData->pluck('prod_size_id')->toArray();
            $unique_size_ids = $all_size_ids;
        } else {
            $checkstock =  (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 15]])->first())) ? 1 : 0;

            ###stock check code
            if ($checkstock && count($sizeStockData) > 0) {
                $stockData = POSS::stockSerialProductQuantity_Multiple($BranchId, $sizeStockData, null, $toDate);

                foreach ($sizeStockData as $row) {
                    if ($stockData[$row->id][$row->prod_size_id]['Stock'] != 0) {
                        array_push($unique_size_ids, $row->prod_size_id);
                    }
                }

                if (count($sizeIdArr) > 0) {
                    $unique_size_ids = array_merge($unique_size_ids, $sizeIdArr);
                }

                $unique_size_ids =  array_unique($unique_size_ids);
            }
        }

        ###stock check code end

        $ProductSizeData = '';
        if(count($sizeStockData) > 0){

            $ProductSizeData = DB::table('pos_p_sizes as psize')
                ->where('psize.is_active', 1)
                ->whereIn('psize.id', $unique_size_ids)
                ->join('pos_product_details as ppd', function ($sizeStockData) {
                    $sizeStockData->on('ppd.prod_size_id', '=', 'psize.id');
                })
                ->when($elementArrayFlag, function ($query) {
                    return $query->selectRaw('psize.id as field_id, psize.size_name as field_name');
                }, function ($query) {
                    return $query->selectRaw('psize.id, psize.size_name, ppd.unit_cost_price, ppd.unit_other_cost, ppd.serial_barcode');
                })
                // ->select('psize.size_name', 'psize.id','ppd.unit_cost_price', 'ppd.unit_other_cost', 'ppd.serial_barcode')
                // ->selectRaw('psize.id as field_id, psize.size_name as field_name')
                ->groupBy('ppd.prod_size_id')
                ->get();
        }


        $data = array(
            'ProductSizeData' => !empty($ProductSizeData) ? $ProductSizeData : null,
        );

        return $data;
    }

    ## get size load by product id for gold product
    public function backupajaxGetSizeForGold(Request $request)
    {

        $productId = $request->productId;
        $ProductDetailsData = DB::table('pos_product_details as ppd')
            ->where('ppd.status', 0)
            ->where('ppd.product_id', $productId)
            ->select('ppd.prod_size_id', 'ppd.serial_barcode', 'ppd.unit_cost_price')
            ->get();

        $unique_size_ids = $ProductDetailsData->pluck('prod_size_id')->unique();

        $ProductSizeData = DB::table('pos_p_sizes as psize')
            ->where('psize.is_active', 1)
            ->whereIn('psize.id', $unique_size_ids)
            ->select('psize.size_name', 'psize.id')
            ->get();
        $data = array(
            'ProductSizeData' => !empty($ProductSizeData) ? $ProductSizeData : null,
        );

        return $data;
    }

    ## get size load by group, cat, sub-cat, model for All Product
    public function ajaxGetSizeForAll(Request $request)
    {

        if ($request->ajax()) {

            $groupId    = (isset($request->groupId) && !empty($request->groupId)) ? $request->groupId : null;
            $categoryId = (isset($request->categoryId) && !empty($request->categoryId)) ? $request->categoryId : null;
            $subCatId   = (isset($request->subCatId) && !empty($request->subCatId)) ? $request->subCatId : null;
            $isActive   = (isset($request->isActive) && !empty($request->isActive)) ? $request->isActive : null;
            $moduleName = (isset($request->moduleName) && !empty($request->moduleName)) ? $request->moduleName : null;

            $prodIdArr  = (isset($request->prodIdArr) && !empty($request->prodIdArr)) ? $request->prodIdArr : array();

            $sizeIdArr = array();

            if (count($prodIdArr) > 0) {
                $sizeIdArr = DB::table('pos_product_details as ppd')
                    ->where('ppd.status', 0)
                    ->whereIn('ppd.product_id', $prodIdArr)
                    ->pluck('ppd.prod_size_id')
                    ->unique()
                    ->toArray();
            }

            $ProductSizeData = DB::table('pos_p_sizes')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($queryData) use ($groupId, $categoryId, $subCatId, $isActive, $sizeIdArr) {
                    if (!empty($groupId)) {
                        $queryData->where('prod_group_id', $groupId);
                    }

                    if (!empty($categoryId)) {
                        $queryData->where('prod_cat_id', $categoryId);
                    }

                    if (!empty($subCatId)) {
                        $queryData->where('prod_sub_cat_id', $subCatId);
                    }

                    if (!empty($isActive)) {
                        $queryData->where('is_active', $isActive);
                    }

                    if (!empty($sizeIdArr)) {
                        $queryData->whereIn('id', $sizeIdArr);
                    }
                })
                ->selectRaw('id as field_id, size_name as field_name')
                ->orderBy('size_name', 'ASC')
                ->get();

            $data = array(
                'ProductSizeData' => !empty($ProductSizeData) ? $ProductSizeData : null,
            );

            return $data;
        }
    }

    public function backupajaxGetSizeForAll(Request $request)
    {

        $productId = $request->productId;

        $ProductSizeData = DB::table('pos_p_sizes as psize')
            ->where('psize.is_active', 1)
            ->select('psize.size_name', 'psize.id')
            ->get();
        $data = array(
            'ProductSizeData' => !empty($ProductSizeData) ? $ProductSizeData : null,
        );

        return $data;
    }

    ## get size load by product id for gold product
    public function ajaxGetSalesByData(Request $request)
    {

        $branchArr = array();
        $branchId = isset($request->branch_id) && !empty($request->branch_id) ? $request->branch_id : null;


        if (!empty($branchId) && $branchId > 0) {
            $branchArr[] = $branchId;
        }

        $branchArr = count($branchArr) > 0 ? $branchArr : HRS::getUserAccesableBranchIds();

        $empArray = DB::table('pos_sales_m')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('branch_id', $branchArr)
            ->pluck('employee_id')
            ->toArray();

        $employeeData = DB::table('hr_employees')
            ->where([['is_delete', 0]])
            ->whereIn('employee_no', $empArray)
            ->orderBy('emp_code', 'ASC')
            ->get();

        $output = '<option value="">Select One</option>';
        foreach ($employeeData as $row) {
            $output .= '<option value="' . $row->employee_no . '">';
            $output .= $row->emp_name . ' [' . $row->emp_code . ']';
            $output .= '</option>';
        }

        return [$output, $employeeData];
        // return [$output, $employeeData];

    }


    public function ajaxDepartmentData(Request $request)
    {

        $data = array();

        $departments = DB::select(DB::raw("SELECT id, dept_name FROM hr_departments WHERE is_active = 1 AND is_delete = 0"));
        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $departments,
        ];

        return response()->json($data);
    }

    public function ajaxDesignationData(Request $request)
    {

        $data = array();

        $designations = DB::select(DB::raw("SELECT id, name FROM hr_designations WHERE is_active = 1 AND is_delete = 0"));
        $data = [
            'status'      => 'success',
            'message'     => '',
            'designation_data' => $designations,
        ];

        return response()->json($data);
    }


    public function ajaxFiscalYearData(Request $request)
    {
        $data = array();

        $FiscalYearData =  DB::table("gnl_fiscal_year")
            ->where([['is_delete', 0], ['is_active', 1], ['company_id', Common::getCompanyId()]])
            ->whereIn('fy_for', ['BOTH', 'LFY'])
            ->select('id', 'fy_name', 'fy_start_date', 'fy_end_date')
            ->orderBy('fy_name', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'fiscal_year_data' => $FiscalYearData,
        ];

        return response()->json($data);
    }

    ## Hall Management
    public function ajaxGetBuilding(Request $request)
    {
        $queryData = DB::table('hms_building')
            ->where([['is_delete', 0], ['is_active', 1]]) // ['is_active', 1]
            ->orderBy('id', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);

    }

    public function ajaxGetFloor(Request $request)
    {
        $buildingId = isset($request->buildingId) ?  $request->buildingId : null;

        $queryData = DB::table('hms_floor')
            ->where([['is_delete', 0], ['is_active', 1]]) //['is_active', 1]
            ->where(function ($query) use ($buildingId){
                if(!empty($buildingId)){
                    $query->where('building_id', $buildingId);
                }

            })
            ->orderBy('id', 'ASC')
            ->get();

        $config = HMS::getConfig();
        $flag = $config->count()>0;

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
            'flag'        => $flag
        ];

        return response()->json($data);

    }

    public function ajaxGetRoom(Request $request)
    {
        $buildingId = isset($request->buildingId) ?  $request->buildingId : null;
        $floorId = isset($request->floorId) ?  $request->floorId : null;

        $queryData = DB::table('hms_room')
            ->where([['is_delete', 0], ['is_active', 1]]) //['is_active', 1]
            ->where(function ($query) use ($buildingId){
                if(!empty($buildingId)){
                    $query->where('building_id', $buildingId);
                }
            })
            ->where(function ($query) use ($floorId){
                if(!empty($floorId)){
                    $query->where('floor_id', $floorId);
                }
            })
            ->orderBy('id', 'ASC')
            ->get();

        $config = HMS::getConfig();
        $flag = $config->count()>0;
        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
            'flag'        => $flag
        ];

        return response()->json($data);
    }

    public function ajaxGetSeat(Request $request)
    {
        $roomId = isset($request->roomId) ?  $request->roomId : null;

        $queryData = DB::table('hms_seat')
            ->where([['is_delete', 0], ['is_active', 1]]) //['is_active', 1]
            ->where(function ($query) use ($roomId){
                if(!empty($roomId)){
                    $query->where('room_id', $roomId);
                }

            })
            ->orderBy('id', 'ASC')
            ->get();

        $config = HMS::getConfig();
        $flag = $config->count()>0;
        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
            'flag'        => $flag
        ];

        return response()->json($data);
    }

    public function ajaxGetStudentStatus(Request $request)
    {
        $queryData = DB::table('gnl_dynamic_form_value')
            ->where([['is_delete', 0], ['is_active', 1], ['type_id', 10], ['form_id', 'HMS.1']])
            ->orderBy('order_by', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetStudentPromoStatus(Request $request)
    {
        $queryData = DB::table('gnl_dynamic_form_value')
            ->where([['is_delete', 0], ['is_active', 1], ['form_id', 'HMS.2']])
            ->orderBy('order_by', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetAcademicDepartment(Request $request)
    {
        $queryData = DB::table('hms_academic_departments')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->orderBy('id', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetAcademicStatus(Request $request)
    {
        $queryData = DB::table('hms_academic_status')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->orderBy('id', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetAcademicYear(Request $request)
    {
        $queryData = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th', 5 => '5th'];

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetAcademicSession(Request $request)
    {
        $queryData = DB::table('hms_academic_session')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->orderBy('id', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxFinancialFY(Request $request)
    {
        $data = array();

        $FiscalYearData =  DB::table("gnl_fiscal_year")
            ->where([['is_delete', 0], ['is_active', 1], ['company_id', Common::getCompanyId()]])
            ->where('fy_for', 'FFY')
            ->select('id', 'fy_name', 'fy_start_date', 'fy_end_date')
            ->orderBy('fy_name', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'fiscal_year_data' => $FiscalYearData,
        ];

        return response()->json($data);
    }

    public function ajaxGetPackages(Request $request)
    {
        $data = array();

        $packages =  DB::table("hms_hall_fee_package")
            ->where([['is_delete', 0], ['is_active', 1]])

            ->select('id', 'name_bn')
            ->orderBy('name_bn', 'ASC')
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'packages' => $packages,
        ];

        return response()->json($data);
    }
}
