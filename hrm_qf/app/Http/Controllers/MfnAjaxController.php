<?php

namespace App\Http\Controllers;

use App\Model\POS\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CommonService as Common;

class MfnAjaxController extends Controller
{

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

    public function ajaxSelectBoxLoadForMember(Request $request)
    {

        if ($request->ajax()) {

            $FeildVal     = $request->FeildVal;
            $TableName    = base64_decode($request->TableName);
            $WhereColumn  = base64_decode($request->WhereColumn);
            $SelectColumn = base64_decode($request->SelectColumn);
            $SelectArr    = explode(',', $SelectColumn);


            $PrimaryKey   = $SelectArr[0];
            $DisplayKey   = $SelectArr[1];
            $DisplayCode  = $SelectArr[2];
            $SelectedVal = $request->SelectedVal;

            // Query
            $QueryData = DB::table($TableName)
                ->where([$WhereColumn => $FeildVal, 'is_delete' => 0])
                ->where("status", "<>", 3)
                ## closing member der blacklist korte pare. USHA ED sir er feedback
                // ->whereDate('closingDate', '=', '0000-00-00')
                ->select([$PrimaryKey, $DisplayKey, $DisplayCode])
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
                $output .= '<option value="' . $Row->$PrimaryKey . '" ' . $SelectText . '>' . $Row->$DisplayKey. " [". $Row->$DisplayCode . ']</option>';
            }

            echo $output;
        }
    }

    public function ajaxSelectBoxLoadForMemberDetails(Request $request)
    {

        if ($request->ajax()) {

            $memberID = $request->memberID;
            $samityID = $request->samityID;
            // Query
            $queryData = DB::table('mfn_members as mb')
                ->where(['mb.id' => $memberID, 'mb.is_delete' => 0])
                ->where("mb.status", "<>", 3)
                ## closing member der blacklist korte pare. USHA ED sir er feedback
                // ->whereDate('mb.closingDate', '=', '0000-00-00')
                ->leftjoin('mfn_loan_products as lp', function ($queryData) {
                    $queryData->on('mb.primaryProductId', '=', 'lp.id')
                        ->where([['lp.is_delete', 0]]);
                })
                ->when(true, function($query) {
                    if (Common::getDBConnection() == "sqlite"){
                        $query->selectRaw('mb.name , mb.memberCode, (lp.name || " [" || lp.productCode || "]") As productName');
                    }
                    else {
                        $query->selectRaw('mb.name , mb.memberCode ,CONCAT(lp.name," [", lp.productCode,"]") As productName');
                    }
                })
            // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            $queryData2 = DB::table('mfn_samity as samity')
                ->where(['samity.id' => $samityID, 'samity.is_delete' => 0])
                ->leftjoin('mfn_working_areas as warea', function ($queryData2) {
                    $queryData2->on('samity.workingAreaId', '=', 'warea.id')
                        ->where([['samity.is_delete', 0]]);
                })
                ->select('warea.name as workingArea')
            // ->orderBy([$SelectArr[1] => 'ASC'])
                ->get();

            $savAccs = DB::table('mfn_savings_accounts AS savAcc')
                ->leftJoin('mfn_savings_product AS savP', 'savP.id', 'savAcc.savingsProductId')
                ->where([
                    ['savAcc.is_delete', 0],
                    ['savAcc.memberId', $memberID],
                    ['savAcc.closingDate', '0000-00-00'],
                ])
                ->select('savP.name AS savingsProduct', 'savAcc.*')
                ->get();

            $deposits = DB::table('mfn_savings_deposit')
                ->where([
                    ['is_delete', 0],
                ])
                ->whereIn('accountId', $savAccs->pluck('id')->toArray())
                ->groupBy('accountId')
                ->select(DB::raw("accountId, SUM(amount) AS amount"))
                ->get();

            $withdraws = DB::table('mfn_savings_withdraw')
                ->where([
                    ['is_delete', 0],
                ])
                ->whereIn('accountId', $savAccs->pluck('id')->toArray())
                ->groupBy('accountId')
                ->select(DB::raw("accountId, SUM(amount) AS amount"))
                ->get();

            foreach ($savAccs as $key => $savAcc) {
                $savAccs[$key]->totalDeposit   = $deposits->where('accountId', $savAcc->id)->sum('amount');
                $savAccs[$key]->totalWithdraw  = $withdraws->where('accountId', $savAcc->id)->sum('amount');
                $savAccs[$key]->savingsBalance = $savAccs[$key]->totalDeposit - $savAccs[$key]->totalWithdraw;
            }

            $response = array(
                "productName" => $queryData[0]->productName,
                "workingArea" => $queryData2[0]->workingArea,
                "savings"     => $savAccs->toarray(),
            );
            echo json_encode($response);
        }
    }

    public function ajaxSystemCurrentDate(Request $request)
    {

        if ($request->ajax()) {

            $sysDate = DB::table('mfn_day_end')
                ->where([
                    ['branchId', $request->branchID],
                    ['isActive', 1],
                ])
                ->max('date');

            if ($sysDate == null) {
                $sysDate = DB::table('gnl_branchs')
                    ->where('id', $request->branchID)
                    ->select('mfn_start_date')
                    ->pluck('mfn_start_date')
                    ->first();
                    // ->mis_soft_start_date;
            }
            echo json_encode($sysDate);
        }
    }

    public function ajaxCustDataLoad(Request $request)
    {

        if ($request->ajax()) {

            $data = Customer::where(['id' => $request->selectedData])->select('customer_name', 'customer_mobile', 'customer_nid')->first();

            return response()->json(array(
                'id' => $request->selectedData,
                'customer_name' => $data->customer_name,
                'customer_mobile' => $data->customer_mobile,
                'customer_nid' => $data->customer_nid));
        }
    }

    ## This function for Mfn Product Category
    public function ajaxGetLoanProductCategory(Request $request)
    {
        $queryData = DB::table('mfn_loan_product_category as mlpc')
                        ->where('is_delete', 0)
                        ->when(true, function($query) {
                            if (Common::getDBConnection() == "sqlite"){
                                $query->select('id', 'name', DB::raw("(mlpc.name || ' [' || mlpc.id || ']') as prodCat"));
                            }
                            else {
                                $query->select('id', 'name', DB::raw("CONCAT(mlpc.name, ' [', mlpc.id, ']') as prodCat"));
                            }
                        })
                        ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetLoanRepaymentFrequency(Request $request)
    {
        $queryData = DB::table('mfn_loan_repayment_frequency')
                        ->where([['is_delete', 0],['status', 1]])
                        ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $queryData,
        ];

        return response()->json($data);
    }

    public function ajaxGetMfnProductType(Request $request)
    {
        // dd('hello');

        $productType = DB::table('mfn_savings_product_type')->get();
        $data = [
        'status'      => 'success',
        'message'     => '',
        'result_data' => $productType,
        ];

        return response()->json($data);
    }

    public function ajaxGetFundingOrg(Request $request)
    {
        $element = DB::table('mfn_funding_orgs as forg')
            ->where('is_delete', 0)
            ->when(true, function($query) {
                if (Common::getDBConnection() == "sqlite"){
                    $query->select('id', 'name', DB::raw("(forg.name || ' [' || forg.id || ']') as fundingOrg"));
                }
                else {
                    $query->select('id', 'name', DB::raw("CONCAT(forg.name, ' [', forg.id, ']') as fundingOrg"));
                }
            })
            ->get();

        $data = [
            'status'      => 'success',
            'message'     => '',
            'result_data' => $element,
            ];

        return response()->json($data);
    }

    public function ajaxGetLoanStatus(Request $request)
    {
        $loanStatus = DB::table('mfn_loan_status')
            ->where('is_delete', 0)
            ->select('id', 'name')
            ->get();
            $data = [
                'status'        =>'success',
                'message'       => '',
                'result_data'   => $loanStatus,
            ];
        return response()->json($data);
    }

    public function ajaxGetMfnTransactionType(Request $request)
    {
        // dd('hello');

        $transactionType = DB::table('mfn_savings_transaction_types')
        ->where([['status', 1]])
        ->get();

        $data = [
        'status'      => 'success',
        'message'     => '',
        'result_data' => $transactionType,
        ];

        return response()->json($data);
    }
}
