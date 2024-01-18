<?php

namespace App\Services;

use App\Model\FAM\Product;
use App\Model\GNL\Branch;
use App\Services\CommonService as Common;
use DateTime;
use Illuminate\Support\Facades\DB;

class FamService
{

    /**
     * Stock Count for Product
     */

    public static function stockQuantity($branchID, $ProductID, $returnArray = false, $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = new DateTime($startDate);
            // $fromDate = $fromDate->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = new DateTime($endDate);
            // $toDate = $toDate->format('Y-m-d');
        } else {
            $toDate = new DateTime(Common::systemCurrentDate());
            // $toDate = new DateTime();

            // $toDate = $toDate->format('Y-m-d');
        }

        // dd($fromDate, $toDate);

        if ($branchID >= 1 && !empty($ProductID)) {

            $Stock          = 0;
            $PreOB          = 0;
            $OpeningBalance = 0;
            $Purchase       = 0;
            $PurchaseReturn = 0;
            $Issue          = 0;
            $IssueReturn    = 0;
            $TransferIn     = 0;
            $TransferOut    = 0;
            $Sales          = 0;
            $SalesReturn    = 0;
            $Adjustment     = 0;
            $waiverProduct  = 0;

            /* Model Load */
            $POpeningBalance       = 'App\\Model\\FAM\\POBStockDetails';
            $PurchaseDetails       = 'App\\Model\\FAM\\PurchaseDetails';
            $PurchaseReturnDetails = 'App\\Model\\FAM\\PurchaseReturnDetails';
            $IssueDetails          = 'App\\Model\\FAM\\IssueDetails';
            $IssueReturnDetails    = 'App\\Model\\FAM\\IssueReturnDetails';
            $TransferDetails       = 'App\\Model\\FAM\\TransferDetails';
            $SalesDetails          = 'App\\Model\\FAM\\SalesDetails';
            $SaleReturnd           = 'App\\Model\\FAM\\SaleReturnd';

            // Opening Balance Count
            $OpeningBalance = DB::table('fam_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                ->where(function ($OpeningBalance) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('fam_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                        ->where('obd.product_id', $ProductID);
                })
                ->sum('obd.product_quantity');

            // Purchase Balance Count
            $Purchase = DB::table('fam_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('fam_purchases_d as pd', function ($Purchase) use ($ProductID) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                        ->where('pd.product_id', $ProductID);
                })
                ->sum('pd.product_quantity');

            // Purchase Return Count

            $PurchaseReturn = DB::table('fam_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('fam_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                        ->where('prd.product_id', $ProductID);
                })
                ->sum('prd.product_quantity');

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('fam_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('fam_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                        ->where('psd.product_id', $ProductID);
                })
                ->sum('psd.product_quantity');

            ## Adjustment Audit  Count
            $Adjustment = DB::table('fam_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('fam_audit_d as ad', function ($Adjustment) use ($ProductID) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code')
                        ->where('ad.product_id', $ProductID);

                })
                ->sum('ad.product_quantity');

            ##/* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Issue Balance Count
                $Issue = DB::table('fam_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                // dd($Issue);

                // Issue Return Count
                $IssueReturn = DB::table('fam_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    ->sum('ird.product_quantity');

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone $fromDate;
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockQuantity($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn - $Issue + $IssueReturn + $Adjustment - $waiverProduct);
            } else {
                // dd($fromDate, $toDate);
                // Issue Balance Count
                $Issue = DB::table('fam_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_to', $branchID]])

                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                // dd($Issue);

                // Issue Return Count
                $IssueReturn = DB::table('fam_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    ->sum('ird.product_quantity');

                // TransferIn Balance Count
                $TransferIn = DB::table('fam_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_to', $branchID]])
                    ->where(function ($TransferIn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                // TransferOut Return Count
                $TransferOut = DB::table('fam_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_from', $branchID]])
                    ->where(function ($TransferOut) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                // Sales Balance Count
                $Sales = DB::table('fam_use_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($Sales) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Sales->whereBetween('psm.uses_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Sales->where('psm.uses_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Sales->where('psm.uses_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_use_d as psd', function ($Sales) use ($ProductID) {
                        $Sales->on('psd.uses_bill_no', 'psm.uses_bill_no')
                            ->where('psd.product_id', $ProductID);
                    })
                    ->sum('psd.product_quantity');

                // SaleReturnd Return Count
                $SalesReturn = DB::table('fam_use_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                    ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_use_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                        $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                            ->where('psrd.product_id', $ProductID);
                    })
                    ->sum('psrd.product_quantity');

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone $fromDate;
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockQuantity($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn + $Issue - $IssueReturn + $TransferIn - $TransferOut - $Sales + $SalesReturn + $Adjustment - $waiverProduct);
            }

            if ($returnArray) {
                $stockDetails = array();

                $stockDetails = [
                    'Stock'          => $Stock,
                    'PreOB'          => $PreOB,
                    'OpeningBalance' => $OpeningBalance + $PreOB,
                    'Purchase'       => $Purchase,
                    'PurchaseReturn' => $PurchaseReturn,
                    'Issue'          => $Issue,
                    'IssueReturn'    => $IssueReturn,
                    'TransferIn'     => $TransferIn,
                    'TransferOut'    => $TransferOut,
                    'Sales'          => $Sales,
                    'SalesReturn'    => $SalesReturn,
                    'Adjustment'     => $Adjustment,
                    'waiverProduct'  => $waiverProduct,
                ];

                return $stockDetails;

            } else {
                return $Stock;
            }

        } else {
            return "Error";
        }
    }

    public static function fnForEmployeeData($employeeArr = [])
    {
        $employeeData = Common::fnForEmployeeData($employeeArr);

        return $employeeData;
    }

    /** old commit theke ana hoise bug fix korar jonno ja schedule report a create hoise */
    public static function GetRegisterReport_backup($Data,$selBranchArr, $startDate, $endDate){

        // 
       
        $productids = $Data->pluck('id')->toArray();

        $onedateback = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($startDate))));
        $DepreciationData = DB::table('fam_depreciation_d as d')
                                
                                ->leftjoin('fam_depreciation_m as m', function ($DepreciationData){
                                    $DepreciationData->on('m.bill_no', 'd.bill_no');
                                }) 
                                ->whereIn('m.branch_id', $selBranchArr)
                                ->where('m.is_delete', 0)
                                ->whereIn('d.product_id', $productids)
                                ->get();
        $DepreciationDataOB=  $DepreciationData->where('from_date', '<=', $onedateback);  
        $DepreciationData = $DepreciationData->where('from_date', '>=', $startDate)->where('to_date', '<=', $endDate);                    
                               

        $SalesData = DB::table('fam_sales_d as d')
                        ->leftjoin('fam_sales_m as m', function ($SalesData){
                            $SalesData->on('m.sales_bill_no', 'd.sales_bill_no');
                        })
                        ->whereIn('m.branch_id', $selBranchArr)
                        ->where('m.is_delete', 0)
                        ->whereIn('d.product_id', $productids)
                        ->get();
        $SalesDataOB=  $SalesData->where('sales_date', '<=', $onedateback);
        $SalesData = $SalesData->where('sales_date', '>=', $startDate)->where('sales_date', '<=', $endDate);
        

        $WriteoffData = DB::table('fam_writeoff_d as d')
                        ->leftjoin('fam_writeoff_m as m', function ($WriteoffData) {
                            $WriteoffData->on('m.bill_no', 'd.bill_no');
                        }) 
                        ->whereIn('m.branch_id', $selBranchArr)
                        ->where('m.is_delete', 0)
                        ->whereIn('d.product_id', $productids)
                        ->get();     
        $WriteoffDataOB=  $WriteoffData->where('writeoff_date', '<=', $onedateback);
        $WriteoffData = $WriteoffData->where('writeoff_date', '>=', $startDate)->where('writeoff_date', '<=', $endDate);
                   
        $TransferInData = DB::table('fam_transfers_d as d')
                        ->leftjoin('fam_transfers_m as m', function ($TransferInData){
                            $TransferInData->on('m.bill_no', 'd.bill_no');
                        })
                        ->where('m.is_delete', 0)
                        ->whereIn('m.branch_to', $selBranchArr)
                        ->whereIn('d.product_id', $productids)
                        ->get();

        $TransferInDataOB=  $TransferInData->where('date', '<=', $onedateback);
        $TransferInData = $TransferInData->where('date', '>=', $startDate)->where('date', '<=', $endDate);
                  
        $TransferOutData = DB::table('fam_transfers_d as d')
                        ->leftjoin('fam_transfers_m as m', function ($TransferOutData) {
                            $TransferOutData->on('m.bill_no', 'd.bill_no');
                        })
                        ->where('m.is_delete', 0)
                        ->whereIn('m.branch_from', $selBranchArr)
                        ->whereIn('d.product_id', $productids)
                        ->get();

        $TransferOutDataOB=  $TransferOutData->where('date', '<=', $onedateback);
        $TransferOutData = $TransferOutData->where('date', '>=', $startDate)->where('date', '<=', $endDate);
                      
        $PurchaseData = DB::table('fam_purchases_d as d')
                        ->leftjoin('fam_purchases_m as m', function ($PurchaseData) {
                            $PurchaseData->on('m.bill_no', 'd.purchase_bill_no');
                        })
                        ->where('m.is_delete', 0)
                        ->whereIn('m.branch_id', $selBranchArr)
                        ->whereIn('d.product_id', $productids)
                        ->get();
        $PurchaseDataOB=  $PurchaseData->where('purchase_date', '<=', $onedateback);
        $PurchaseData = $PurchaseData->where('purchase_date', '>=', $startDate)->where('purchase_date', '<=', $endDate);
                                      
                                
                    //   dd($PurchaseDataOB, $onedateback); 


        foreach ($Data as $key => $product){

            # ob query and calculation
                $SalesQueryOB = $SalesDataOB->where('product_id',$product->id)->first();
                $WriteoffQueryOB = $WriteoffDataOB->where('product_id',$product->id)->first();
                $DepreciationQueryOB = $DepreciationDataOB->where('product_id',$product->id);
                $TransferOutQueryOB = $TransferOutDataOB->where('product_id',$product->id)->first();
                $TransferInQueryOB = $TransferInDataOB->where('product_id',$product->id)->first();
                $PurchaseQueryOB = $PurchaseDataOB->where('product_id',$product->id)->first();
                $purchaseOB = $transferInOB= 0; 
                if(!empty($TransferInQueryOB)){
                    $transferInOB = $TransferInQueryOB->cost_price;
                    $purchaseOB = 0; 
                }
                if(!empty($PurchaseQueryOB)){
                    $transferInOB =  0; 
                    $purchaseOB = (!empty($PurchaseQueryOB))? $PurchaseQueryOB->unit_cost_price : 0;
                }
                $salesOB = (!empty($SalesQueryOB))? $SalesQueryOB->disposal_amount : 0;
                $adjustmentOut_FromSalesOB = (!empty($SalesQueryOB))? $SalesQueryOB->accumulated_dep  : 0 ;
                $adjustmentInOB = (!empty($SalesQueryOB))? ($SalesQueryOB->disposal_amount - $product->resale_value) : 0 ;
                if(!empty($WriteoffQueryOB)){
                    $adjustmentOut_FromSalesOB = $salesOB = $adjustmentInOB  = 0;
                    $adjustmentOut_FromWriteoffOB = ($WriteoffQueryOB->accumulated_dep + $WriteoffQueryOB->disposal_amount);
                
                }else{
                    $adjustmentOut_FromWriteoffOB = 0 ;
                }

                $adjustmentOutOB =  $adjustmentOut_FromSalesOB +  $adjustmentOut_FromWriteoffOB;
                $transferOutOB = (!empty($TransferOutQueryOB))? $TransferOutQueryOB->cost_price : 0;

            ## During query and calculation
                $SalesQuery = $SalesData->where('product_id',$product->id)->first();
                $WriteoffQuery = $WriteoffData->where('product_id',$product->id)->first();
                $DepreciationQuery = $DepreciationData->where('product_id',$product->id);
                $TransferOutQuery = $TransferOutData->where('product_id',$product->id)->first();
                $TransferInQuery = $TransferInData->where('product_id',$product->id)->first();
                $PurchaseQuery = $PurchaseData->where('product_id',$product->id)->first();

                $purchase =$transferIn = 0; 
                if(!empty($TransferInQuery)){
                    $transferIn = $TransferInQuery->cost_price;
                    $purchase = 0; 
                    $purchaseOB = $transferInOB= 0;
                }
                if(!empty($PurchaseQuery)){
                    $transferIn =  0; 
                    $purchase = (!empty($PurchaseQuery))? $PurchaseQuery->unit_cost_price : 0;
                    $purchaseOB = $transferInOB= 0;
                }
            
            
                $sales = (!empty($SalesQuery))? $SalesQuery->disposal_amount : 0;
                $adjustmentOut_FromSales = (!empty($SalesQuery))? $SalesQuery->accumulated_dep  : 0 ;
                $adjustmentIn = (!empty($SalesQuery))? ($SalesQuery->disposal_amount - $product->resale_value) : 0 ;

                if(!empty($WriteoffQuery)){
                    $adjustmentOut_FromSales = $sales = $adjustmentIn  = 0;
                    $adjustmentOut_FromWriteoff= ($WriteoffQuery->accumulated_dep + $WriteoffQuery->disposal_amount);
                
                }else{
                    $adjustmentOut_FromWriteoff = 0 ;
                }

                $adjustmentOut =  $adjustmentOut_FromSales +  $adjustmentOut_FromWriteoff;
                $transferOut = (!empty($TransferOutQuery))? $TransferOutQuery->cost_price : 0;


            ## cost section Data making
            $Data[$key]->purchase = $purchase;
            $Data[$key]->adjustmentIn = $adjustmentIn;
            $Data[$key]->transferIn = $transferIn;

            $Data[$key]->sales = $sales;
            $Data[$key]->adjustmentOut = $adjustmentOut;
            $Data[$key]->transferOut = $transferOut;

            $Data[$key]->openingCost = $purchaseOB + $adjustmentInOB + $transferInOB - $salesOB - $adjustmentOutOB - $transferOutOB;
            $Data[$key]->closingCost = $Data[$key]->openingCost  + $Data[$key]->purchase + $Data[$key]->adjustmentIn + $Data[$key]->transferIn - $Data[$key]->sales - $Data[$key]->adjustmentOut - $Data[$key]->transferOut;
            
                    

             ## depriciatio section
             
             ## opening depriciation section 
                $disposalOB =  $adjustmentDepOB = 0;
                if(!empty($SalesQueryOB) || !empty($WriteoffQueryOB)){ // 
                    if(!empty($SalesQueryOB)){
                        $depreciationOB = $SalesQueryOB->accumulated_dep;
                        $disposalOB = $SalesQueryOB->disposal_amount;
                        $adjustmentDepOB = $depreciationOB  +  $disposalOB;
                    }
                    if(!empty($WriteoffQueryOB)){
                        $depreciationOB = $WriteoffQueryOB->accumulated_dep;
                        $disposalOB = $WriteoffQueryOB->disposal_amount;
                        $adjustmentDepOB = $depreciationOB  +  $disposalOB;
                    }
                    
                }else{ // running product 
                    
                    $depreciationOB = (!empty($DepreciationQueryOB))? $DepreciationQueryOB->sum('amount') : 0;
                }


             ## during depriciation section 
                $disposal =  $adjustmentDep = 0;
                if(!empty($SalesQuery) || !empty($WriteoffQuery)){ // 
                    if(!empty($SalesQuery)){
                        $depreciation = $SalesQuery->accumulated_dep;
                        $disposal = $SalesQuery->disposal_amount;
                        $adjustmentDep = $depreciation  +  $disposal;
                    }
                    if(!empty($WriteoffQuery)){
                        $depreciation = $WriteoffQuery->accumulated_dep;
                        $disposal = $WriteoffQuery->disposal_amount;
                        $adjustmentDep = $depreciation  +  $disposal;
                    }
                    #if had any wrife off or sales data then make ob variables 0
                    $disposalOB =  $adjustmentDepOB =$depreciationOB = 0;
                }else{ // running product 
                    
                    $depreciation = (!empty($DepreciationQuery))? $DepreciationQuery->sum('amount') : 0;
                }

            ## Dep  section Data making
             $Data[$key]->depreciation = $depreciation;
             $Data[$key]->disposal = $disposal;
             $Data[$key]->transferInDep = $transferIn;
             $Data[$key]->transferOutDep = $transferOut;
             $Data[$key]->adjustmentDep = $adjustmentDep;

             $Data[$key]->OpeningDep = $depreciationOB + $disposalOB + $transferInOB - $transferOutOB - $adjustmentDepOB;
             $Data[$key]->closingDep =  $Data[$key]->OpeningDep + $Data[$key]->depreciation + $Data[$key]->disposal + $Data[$key]->transferInDep - $Data[$key]->transferOutDep - $Data[$key]->adjustmentDep;

        }
        
        
        return $Data;
                        
        
                        

    }


    public static function GetRegisterStatus($IDs, $selBranchArr, $startDate, $endDate)
    {

        //
        if (!is_array($IDs)) {
            $IDs = array($IDs);
        }

        $productids = $IDs;

        $onedateback      = date('Y-m-d', (strtotime('-1 day', strtotime($startDate))));
        $DepreciationData = DB::table('fam_depreciation_d as d')

            ->leftjoin('fam_depreciation_m as m', function ($DepreciationData) {
                $DepreciationData->on('m.bill_no', 'd.bill_no');
            })
            ->whereIn('m.branch_id', $selBranchArr)
            ->where('m.is_delete', 0)
            ->whereIn('d.product_id', $productids)
            ->get();
        $DepreciationDataOB = $DepreciationData->where('from_date', '<=', $onedateback);
        $DepreciationData   = $DepreciationData->where('from_date', '>=', $startDate)->where('to_date', '<=', $endDate);

        $SalesData = DB::table('fam_sales_d as d')
            ->leftjoin('fam_sales_m as m', function ($SalesData) {
                $SalesData->on('m.sales_bill_no', 'd.sales_bill_no');
            })
            ->whereIn('m.branch_id', $selBranchArr)
            ->where('m.is_delete', 0)
            ->whereIn('d.product_id', $productids)
            ->get();
        $SalesDataOB = $SalesData->where('sales_date', '<=', $onedateback);
        $SalesData   = $SalesData->where('sales_date', '>=', $startDate)->where('sales_date', '<=', $endDate);

        $WriteoffData = DB::table('fam_writeoff_d as d')
            ->leftjoin('fam_writeoff_m as m', function ($WriteoffData) {
                $WriteoffData->on('m.bill_no', 'd.bill_no');
            })
            ->whereIn('m.branch_id', $selBranchArr)
            ->where('m.is_delete', 0)
            ->whereIn('d.product_id', $productids)
            ->get();
        $WriteoffDataOB = $WriteoffData->where('writeoff_date', '<=', $onedateback);
        $WriteoffData   = $WriteoffData->where('writeoff_date', '>=', $startDate)->where('writeoff_date', '<=', $endDate);

        $TransferInData = DB::table('fam_transfers_d as d')
            ->leftjoin('fam_transfers_m as m', function ($TransferInData) {
                $TransferInData->on('m.bill_no', 'd.bill_no');
            })
            ->where('m.is_delete', 0)
            ->whereIn('m.branch_to', $selBranchArr)
            ->whereIn('d.product_id', $productids)
            ->get();

        $TransferInDataOB = $TransferInData->where('date', '<=', $onedateback);
        $TransferInData   = $TransferInData->where('date', '>=', $startDate)->where('date', '<=', $endDate);

        $TransferOutData = DB::table('fam_transfers_d as d')
            ->leftjoin('fam_transfers_m as m', function ($TransferOutData) {
                $TransferOutData->on('m.bill_no', 'd.bill_no');
            })
            ->where('m.is_delete', 0)
            ->whereIn('m.branch_from', $selBranchArr)
            ->whereIn('d.product_id', $productids)
            ->get();

        $TransferOutDataOB = $TransferOutData->where('date', '<=', $onedateback);
        $TransferOutData   = $TransferOutData->where('date', '>=', $startDate)->where('date', '<=', $endDate);

        $PurchaseData = DB::table('fam_purchases_d as d')
            ->leftjoin('fam_purchases_m as m', function ($PurchaseData) {
                $PurchaseData->on('m.bill_no', 'd.purchase_bill_no');
            })
            ->where('m.is_delete', 0)
            ->whereIn('m.branch_id', $selBranchArr)
            ->whereIn('d.product_id', $productids)
            ->get();
        $PurchaseDataOB = $PurchaseData->where('purchase_date', '<=', $onedateback);
        $PurchaseData   = $PurchaseData->where('purchase_date', '>=', $startDate)->where('purchase_date', '<=', $endDate);

        $ProductData = DB::table('fam_products as p')
            ->where('p.is_delete', 0)
            ->whereIn('p.id', $productids)
            ->select('id', 'resale_value')
            ->get();
        //   dd($PurchaseDataOB, $onedateback);
        $DataStatus = [];

        foreach ($productids as $key => $product) {
            $data['product_id'] = $product;
            # ob query and calculation
            $SalesQueryOB        = $SalesDataOB->where('product_id', $product)->first();
            $WriteoffQueryOB     = $WriteoffDataOB->where('product_id', $product)->first();
            $DepreciationQueryOB = $DepreciationDataOB->where('product_id', $product);
            $TransferOutQueryOB  = $TransferOutDataOB->where('product_id', $product)->first();
            $TransferInQueryOB   = $TransferInDataOB->where('product_id', $product)->first();
            $PurchaseQueryOB     = $PurchaseDataOB->where('product_id', $product)->first();
            $resale_value        = !empty($ProductData->where('id', $product)->first()) ? $ProductData->where('id', $product)->first()->resale_value : 0;
            $purchaseOB          = $transferInOB          = 0;
            if (!empty($TransferInQueryOB)) {
                $transferInOB = $TransferInQueryOB->cost_price;
                $purchaseOB   = 0;
            }
            if (!empty($PurchaseQueryOB)) {
                $transferInOB = 0;
                $purchaseOB   = (!empty($PurchaseQueryOB)) ? $PurchaseQueryOB->unit_cost_price : 0;
            }
            $salesOB                   = (!empty($SalesQueryOB)) ? $SalesQueryOB->disposal_amount : 0;
            $adjustmentOut_FromSalesOB = (!empty($SalesQueryOB)) ? $SalesQueryOB->accumulated_dep : 0;
            $adjustmentInOB            = (!empty($SalesQueryOB)) ? ($SalesQueryOB->disposal_amount - $resale_value) : 0;
            if (!empty($WriteoffQueryOB)) {
                $adjustmentOut_FromSalesOB    = $salesOB    = $adjustmentInOB    = 0;
                $adjustmentOut_FromWriteoffOB = ($WriteoffQueryOB->accumulated_dep + $WriteoffQueryOB->disposal_amount);

            } else {
                $adjustmentOut_FromWriteoffOB = 0;
            }

            $adjustmentOutOB = $adjustmentOut_FromSalesOB + $adjustmentOut_FromWriteoffOB;
            $transferOutOB   = (!empty($TransferOutQueryOB)) ? $TransferOutQueryOB->cost_price : 0;

            ## During query and calculation
            $SalesQuery        = $SalesData->where('product_id', $product)->first();
            $WriteoffQuery     = $WriteoffData->where('product_id', $product)->first();
            $DepreciationQuery = $DepreciationData->where('product_id', $product);
            $TransferOutQuery  = $TransferOutData->where('product_id', $product)->first();
            $TransferInQuery   = $TransferInData->where('product_id', $product)->first();
            $PurchaseQuery     = $PurchaseData->where('product_id', $product)->first();

            $purchase = $transferIn = 0;
            if (!empty($TransferInQuery)) {
                $transferIn = $TransferInQuery->cost_price;
                $purchase   = 0;
                $purchaseOB = $transferInOB = 0;
            }
            if (!empty($PurchaseQuery)) {
                $transferIn = 0;
                $purchase   = (!empty($PurchaseQuery)) ? $PurchaseQuery->unit_cost_price : 0;
                $purchaseOB = $transferInOB = 0;
            }

            $sales                   = (!empty($SalesQuery)) ? $SalesQuery->disposal_amount : 0;
            $adjustmentOut_FromSales = (!empty($SalesQuery)) ? $SalesQuery->accumulated_dep : 0;
            $adjustmentIn            = (!empty($SalesQuery)) ? ($SalesQuery->disposal_amount - $resale_value) : 0;

            if (!empty($WriteoffQuery)) {
                $adjustmentOut_FromSales    = $sales    = $adjustmentIn    = 0;
                $adjustmentOut_FromWriteoff = ($WriteoffQuery->accumulated_dep + $WriteoffQuery->disposal_amount);

            } else {
                $adjustmentOut_FromWriteoff = 0;
            }

            $adjustmentOut = $adjustmentOut_FromSales + $adjustmentOut_FromWriteoff;
            $transferOut   = (!empty($TransferOutQuery)) ? $TransferOutQuery->cost_price : 0;

            ## cost section Data making
            $data['purchase']     = $purchase;
            $data['adjustmentIn'] = $adjustmentIn;
            $data['transferIn']   = $transferIn;

            $data['sales']         = $sales;
            $data['adjustmentOut'] = $adjustmentOut;
            $data['transferOut']   = $transferOut;

            $data['openingCost'] = $purchaseOB + $adjustmentInOB + $transferInOB - $salesOB - $adjustmentOutOB - $transferOutOB;
            $data['closingCost'] = $data['openingCost'] + $data['purchase'] + $data['adjustmentIn'] + $data['transferIn'] - $data['sales'] - $data['adjustmentOut'] - $data['transferOut'];

            ## depriciatio section

            ## opening depriciation section
            $disposalOB = $adjustmentDepOB = 0;
            if (!empty($SalesQueryOB) || !empty($WriteoffQueryOB)) { //
                if (!empty($SalesQueryOB)) {
                    $depreciationOB  = $SalesQueryOB->accumulated_dep;
                    $disposalOB      = $SalesQueryOB->disposal_amount;
                    $adjustmentDepOB = $depreciationOB + $disposalOB;
                }
                if (!empty($WriteoffQueryOB)) {
                    $depreciationOB  = $WriteoffQueryOB->accumulated_dep;
                    $disposalOB      = $WriteoffQueryOB->disposal_amount;
                    $adjustmentDepOB = $depreciationOB + $disposalOB;
                }

            } else { // running product

                $depreciationOB = (!empty($DepreciationQueryOB)) ? $DepreciationQueryOB->sum('amount') : 0;
            }

            ## during depriciation section
            $disposal = $adjustmentDep = 0;
            if (!empty($SalesQuery) || !empty($WriteoffQuery)) { //
                if (!empty($SalesQuery)) {
                    $depreciation  = $SalesQuery->accumulated_dep;
                    $disposal      = $SalesQuery->disposal_amount;
                    $adjustmentDep = $depreciation + $disposal;
                }
                if (!empty($WriteoffQuery)) {
                    $depreciation  = $WriteoffQuery->accumulated_dep;
                    $disposal      = $WriteoffQuery->disposal_amount;
                    $adjustmentDep = $depreciation + $disposal;
                }
                #if had any wrife off or sales data then make ob variables 0
                $disposalOB = $adjustmentDepOB = $depreciationOB = 0;
            } else { // running product

                $depreciation = (!empty($DepreciationQuery)) ? $DepreciationQuery->sum('amount') : 0;
            }

            ## Dep  section Data making
            $data['depreciation']   = $depreciation;
            $data['disposal']       = $disposal;
            $data['transferInDep']  = $transferIn;
            $data['transferOutDep'] = $transferOut;
            $data['adjustmentDep']  = $adjustmentDep;

            $data['OpeningDep'] = $depreciationOB + $disposalOB + $transferInOB - $transferOutOB - $adjustmentDepOB;
            $data['closingDep'] = $data['OpeningDep'] + $data['depreciation'] + $data['disposal'] + $data['transferInDep'] - $data['transferOutDep'] - $data['adjustmentDep'];

            array_push($DataStatus, $data);
        }

        return $DataStatus;

    }

    // public static function stockQuantity_Multiple($branchID, $ProductID = [], $returnArray = false, $startDate = null, $endDate = null)
    // {
    //     /**
    //      * Algorithm Stock Count For H/O
    //      * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
    //      */
    //     /**
    //      * Algorithm Stock Count For Branch
    //      * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
    //      */

    //     config()->set('database.connections.mysql.strict', false);
    //     DB::reconnect();

    //     $fromDate = null;
    //     $toDate = null;

    //     if ($startDate == '') {
    //         $startDate = null;
    //     }

    //     if ($endDate == '') {
    //         $endDate = null;
    //     }

    //     if (!empty($startDate)) {
    //         $fromDate = new DateTime($startDate);
    //     }

    //     if (!empty($endDate)) {
    //         $toDate = new DateTime($endDate);
    //     } else {
    //         $toDate = new DateTime(Common::systemCurrentDate());
    //     }

    //     // dd( $fromDate , $toDate);

    //     // $branchID >= 1 &&

    //     if (!empty($ProductID)) {

    //         $StockC = 0;
    //         $PreOBC = 0;
    //         $OpeningBalanceC = 0;
    //         $PurchaseC = 0;
    //         $PurchaseReturnC = 0;
    //         $IssueC = 0;
    //         $IssueReturnC = 0;
    //         $TransferInC = 0;
    //         $TransferOutC = 0;
    //         $SalesC = 0;
    //         $SalesReturnC = 0;
    //         $AdjustmentC = 0;

    //         $StockA = array();
    //         $PreOBA = array();
    //         $OpeningBalanceA = array();
    //         $PurchaseA = array();
    //         $PurchaseReturnA = array();
    //         $IssueA = array();
    //         $IssueReturnA = array();
    //         $TransferInA = array();
    //         $TransferOutA = array();
    //         $SalesA = array();
    //         $SalesReturnA = array();
    //         $AdjustmentA = array();

    //         $stockArr = array();
    //         $AllStockArray = array();

    //         /* Model Load */
    //         $POpeningBalance = 'App\\Model\\FAM\\POBStockDetails';
    //         $PurchaseDetails = 'App\\Model\\FAM\\PurchaseDetails';
    //         $PurchaseReturnDetails = 'App\\Model\\FAM\\PurchaseReturnDetails';
    //         $IssueDetails = 'App\\Model\\FAM\\IssueDetails';
    //         $IssueReturnDetails = 'App\\Model\\FAM\\IssueReturnDetails';
    //         $TransferDetails = 'App\\Model\\FAM\\TransferDetails';
    //         $SalesDetails = 'App\\Model\\FAM\\SalesDetails';
    //         $SaleReturnd = 'App\\Model\\FAM\\SaleReturnd';

    //         /* Branch ID 1 for Head Office Branch */
    //         if ($branchID == 1) {

    //             // Opening Balance Count
    //             $OpeningBalance = DB::table('fam_ob_stock_m as obm')
    //                 ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
    //                 ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $OpeningBalance->where('obm.opening_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
    //                     $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
    //                         ->whereIn('obd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
    //                 ->groupBy('obd.product_id')
    //                 ->get();

    //             foreach ($OpeningBalance as $Row) {
    //                 $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
    //             }

    //             // Purchase Balance Count
    //             $Purchase = DB::table('fam_purchases_m as pm')
    //                 ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
    //                 ->where(function ($Purchase) use ($fromDate, $toDate) {

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $Purchase->where('pm.purchase_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $Purchase->where('pm.purchase_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_purchases_d as pd', function ($Purchase) use ($ProductID) {
    //                     $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
    //                         ->whereIn('pd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
    //                 ->groupBy('pd.product_id')
    //                 ->get();
    //             foreach ($Purchase as $Row) {
    //                 $PurchaseA[$Row->product_id] = $Row->Purchase;
    //             }

    //             // Purchase Return Count
    //             $PurchaseReturn = DB::table('fam_purchases_r_m as prm')
    //                 ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
    //                 ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $PurchaseReturn->where('prm.return_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
    //                     $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
    //                         ->whereIn('prd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
    //                 ->groupBy('prd.product_id')
    //                 ->get();

    //             foreach ($PurchaseReturn as $Row) {
    //                 $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
    //             }

    //             // Issue Balance Count
    //             $Issue = DB::table('fam_issues_m as im')
    //                 ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_from', $branchID]])
    //                 ->where(function ($Issue) use ($fromDate, $toDate) {

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $Issue->where('im.issue_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $Issue->where('im.issue_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_issues_d as isd', function ($Issue) use ($ProductID) {
    //                     $Issue->on('isd.issue_bill_no', 'im.bill_no')
    //                         ->whereIn('isd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
    //                 ->groupBy('isd.product_id')
    //                 ->get();

    //             foreach ($Issue as $Row) {
    //                 $IssueA[$Row->product_id] = $Row->Issue;
    //             }

    //             // Issue Return Count
    //             $IssueReturn = DB::table('fam_issues_r_m as irm')
    //                 ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
    //                 ->where(function ($IssueReturn) use ($fromDate, $toDate) {

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $IssueReturn->where('irm.return_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $IssueReturn->where('irm.return_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
    //                     $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
    //                         ->whereIn('ird.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
    //                 ->groupBy('ird.product_id')
    //                 ->get();
    //             // dd($IssueReturn);

    //             foreach ($IssueReturn as $Row) {
    //                 $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
    //             }

    //             $productData = Product::where([['is_delete', 0], ['is_active', 1]])->whereIn('id', $ProductID)->get();

    //             foreach ($productData as $row) {

    //                 $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));
    //                 $PurchaseC = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
    //                 $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));
    //                 $IssueC = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
    //                 $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

    //                 $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC);

    //                 $stockArr[$row->id] = [
    //                     'Stock' => $StockC,
    //                     'PreOB' => $PreOBC,
    //                     'OpeningBalance' => $OpeningBalanceC,
    //                     'Purchase' => $PurchaseC,
    //                     'PurchaseReturn' => $PurchaseReturnC,
    //                     'Issue' => $IssueC,
    //                     'IssueReturn' => $IssueReturnC,
    //                     'Adjustment' => $AdjustmentC,
    //                 ];
    //             }

    //             $PreOBArr = array();
    //             $AllStockArray = $stockArr;

    //             // dd(  $AllStockArray );

    //             if (!empty($fromDate) && !empty($toDate)) {

    //                 $tempDate = clone $fromDate;
    //                 $NewDate = $tempDate->modify('-1 day');
    //                 // dd( $NewDate);

    //                 $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));

    //                 foreach (array_keys($stockArr + $PreOBArr) as $key) {

    //                     // $AllStockArray[$key] = [
    //                     //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
    //                     //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
    //                     //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
    //                     //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
    //                     //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
    //                     //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
    //                     //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
    //                     //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
    //                     // ];

    //                     $AllStockArray[$key] = [
    //                         'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
    //                         'PreOB' => $stockArr[$key]['PreOB'],
    //                         'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
    //                         'Purchase' => $stockArr[$key]['Purchase'],
    //                         'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
    //                         'Issue' => $stockArr[$key]['Issue'],
    //                         'IssueReturn' => $stockArr[$key]['IssueReturn'],
    //                         'Adjustment' => $stockArr[$key]['Adjustment'],
    //                     ];
    //                 }
    //             }
    //         } else {

    //             // dd($branchID);

    //             // Opening Balance Count
    //             $OpeningBalance = DB::table('fam_ob_stock_m as obm')
    //                 ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
    //                 ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $OpeningBalance->where('obm.branch_id', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $OpeningBalance->where('obm.opening_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
    //                     $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
    //                         ->whereIn('obd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
    //                 ->groupBy('obd.product_id')
    //                 ->get();

    //             foreach ($OpeningBalance as $Row) {
    //                 $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
    //             }

    //             // Issue Balance Count
    //             $Issue = DB::table('fam_issues_m as im')
    //                 ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_to', '<>', 1]])
    //                 ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $Issue->where('im.branch_to', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $Issue->where('im.issue_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $Issue->where('im.issue_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_issues_d as isd', function ($Issue) use ($ProductID) {
    //                     $Issue->on('isd.issue_bill_no', 'im.bill_no')
    //                         ->whereIn('isd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
    //                 ->groupBy('isd.product_id')
    //                 ->get();

    //             foreach ($Issue as $Row) {
    //                 $IssueA[$Row->product_id] = $Row->Issue;
    //             }

    //             // Issue Return Count
    //             $IssueReturn = DB::table('fam_issues_r_m as irm')
    //                 ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
    //                 ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $IssueReturn->where('irm.branch_from', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $IssueReturn->where('irm.return_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $IssueReturn->where('irm.return_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
    //                     $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
    //                         ->whereIn('ird.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
    //                 ->groupBy('ird.product_id')
    //                 ->get();

    //             foreach ($IssueReturn as $Row) {
    //                 $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
    //             }

    //             // TransferIn Balance Count
    //             $TransferIn = DB::table('fam_transfers_m as ptm')
    //                 ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_to', '<>', 1]])
    //                 ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $TransferIn->where('ptm.branch_to', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $TransferIn->where('ptm.transfer_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
    //                     $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
    //                         ->whereIn('ptd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
    //                 ->groupBy('ptd.product_id')
    //                 ->get();

    //             foreach ($TransferIn as $Row) {
    //                 $TransferInA[$Row->product_id] = $Row->TransferIn;
    //             }

    //             // TransferOut Return Count
    //             $TransferOut = DB::table('fam_transfers_m as ptm')
    //                 ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_from', '<>', 1]])
    //                 ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $TransferOut->where('ptm.branch_from', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $TransferOut->where('ptm.transfer_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
    //                     $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
    //                         ->whereIn('ptd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
    //                 ->groupBy('ptd.product_id')
    //                 ->get();

    //             foreach ($TransferOut as $Row) {
    //                 $TransferOutA[$Row->product_id] = $Row->TransferOut;
    //             }

    //             // Sales Balance Count
    //             $Sales = DB::table('fam_use_m as psm')
    //                 ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
    //                 ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

    //                     if (!empty($branchID)) {
    //                         $Sales->where('psm.branch_id', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $Sales->where('psm.sales_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $Sales->where('psm.sales_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_use_d as psd', function ($Sales) use ($ProductID) {
    //                     $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
    //                         ->whereIn('psd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
    //                 ->groupBy('psd.product_id')
    //                 ->get();

    //             foreach ($Sales as $Row) {
    //                 $SalesA[$Row->product_id] = $Row->Sales;
    //             }

    //             // SaleReturnd Return Count
    //             $SalesReturn = DB::table('fam_use_return_m as psrm')
    //                 ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
    //                 ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
    //                     if (!empty($branchID)) {
    //                         $SalesReturn->where('psrm.branch_id', $branchID);
    //                     }

    //                     if (!empty($fromDate) && !empty($toDate)) {
    //                         $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
    //                     }

    //                     if (!empty($fromDate) && empty($toDate)) {
    //                         $SalesReturn->where('psrm.return_date', '>=', $fromDate);
    //                     }

    //                     if (empty($fromDate) && !empty($toDate)) {
    //                         $SalesReturn->where('psrm.return_date', '<=', $toDate);
    //                     }
    //                 })
    //                 ->leftjoin('fam_use_return_d as psrd', function ($SalesReturn) use ($ProductID) {
    //                     $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
    //                         ->whereIn('psrd.product_id', $ProductID);
    //                 })
    //                 ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
    //                 ->groupBy('psrd.product_id')
    //                 ->get();

    //             foreach ($SalesReturn as $Row) {
    //                 $SalesReturnA[$Row->product_id] = $Row->SalesReturn;
    //             }

    //             $productData = Product::where([['is_delete', 0], ['is_active', 1]])->whereIn('id', $ProductID)->get();

    //             foreach ($productData as $row) {

    //                 $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

    //                 $IssueC = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
    //                 $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

    //                 $TransferInC = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
    //                 $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

    //                 $SalesC = ((isset($SalesA[$row->id]) ? $SalesA[$row->id] : 0));
    //                 $SalesReturnC = ((isset($SalesReturnA[$row->id]) ? $SalesReturnA[$row->id] : 0));

    //                 $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC);

    //                 $stockArr[$row->id] = [
    //                     'Stock' => $StockC,
    //                     'PreOB' => $PreOBC,
    //                     'OpeningBalance' => $OpeningBalanceC,
    //                     'Purchase' => $PurchaseC,
    //                     'PurchaseReturn' => $PurchaseReturnC,
    //                     'Issue' => $IssueC,
    //                     'IssueReturn' => $IssueReturnC,
    //                     'TransferIn' => $TransferInC,
    //                     'TransferOut' => $TransferOutC,
    //                     'Sales' => $SalesC,
    //                     'SalesReturn' => $SalesReturnC,
    //                     'Adjustment' => $AdjustmentC,
    //                 ];
    //             }

    //             $PreOBArr = array();
    //             $AllStockArray = $stockArr;

    //             if (!empty($fromDate) && !empty($toDate)) {

    //                 $tempDate = clone $fromDate;
    //                 $NewDate = $tempDate->modify('-1 day');

    //                 $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));

    //                 foreach (array_keys($stockArr + $PreOBArr) as $key) {

    //                     // $AllStockArray[$key] = [
    //                     //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
    //                     //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
    //                     //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
    //                     //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
    //                     //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
    //                     //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
    //                     //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
    //                     //     'TransferIn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
    //                     //     'TransferOut' => $stockArr[$key]['TransferOut'] + $PreOBArr[$key]['TransferOut'],
    //                     //     'Sales' => $stockArr[$key]['Sales'] + $PreOBArr[$key]['Sales'],
    //                     //     'SalesReturn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
    //                     //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
    //                     // ];

    //                     $AllStockArray[$key] = [
    //                         'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
    //                         'PreOB' => $stockArr[$key]['PreOB'],
    //                         'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
    //                         'Purchase' => $stockArr[$key]['Purchase'],
    //                         'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
    //                         'Issue' => $stockArr[$key]['Issue'],
    //                         'IssueReturn' => $stockArr[$key]['IssueReturn'],
    //                         'TransferIn' => $stockArr[$key]['TransferIn'],
    //                         'TransferOut' => $stockArr[$key]['TransferOut'],
    //                         'Sales' => $stockArr[$key]['Sales'],
    //                         'SalesReturn' => $stockArr[$key]['SalesReturn'],
    //                         'Adjustment' => $stockArr[$key]['Adjustment'],
    //                     ];
    //                 }
    //             }
    //         }

    //         return $AllStockArray;

    //     } else {
    //         return "Error";
    //     }
    // }

    public static function fnForProductSettingsWise($productId = null, $groupId = null, $catId = null, $subCatId = null,
        $brandId = null, $modelId = null, $supplierId = null, $companyID = null) {
        $selectProduct = array();

        if (!empty($productId)) {
            $selectProduct = [$productId];

        } else {
            $productQuery = DB::table('fam_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($productQuery) use ($companyID) {
                    if (!empty($companyID)) {
                        $productQuery->where('company_id', $companyID);
                    }
                })
                ->where(function ($productQuery) use ($groupId) {
                    if (!empty($groupId)) {
                        $productQuery->where('prod_group_id', $groupId);
                    }
                })
                ->where(function ($productQuery) use ($catId) {
                    if (!empty($catId)) {
                        $productQuery->where('prod_cat_id', $catId);
                    }
                })
                ->where(function ($productQuery) use ($subCatId) {
                    if (!empty($subCatId)) {
                        $productQuery->where('prod_sub_cat_id', $subCatId);
                    }
                })
                ->where(function ($productQuery) use ($brandId) {
                    if (!empty($brandId)) {
                        $productQuery->where('prod_brand_id', $brandId);
                    }
                })
                ->where(function ($productQuery) use ($modelId) {
                    if (!empty($modelId)) {
                        $productQuery->where('prod_model_id', $modelId);
                    }
                })
                ->where(function ($productQuery) use ($supplierId) {
                    if (!empty($supplierId)) {
                        $productQuery->where('supplier_id', $supplierId);
                    }
                })
                ->pluck('id')
                ->toArray();

            $selectProduct = (!empty($productQuery) && count($productQuery) > 0) ? $productQuery : array();
        }

        return $selectProduct;
    }

    public static function fnForProductData($productArr = [])
    {
        $productData = array();
        if (count($productArr) > 0) {

            $productData = DB::table('fam_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $productArr)
                ->selectRaw('prod_code, id')
                ->pluck('prod_code', 'id')
                ->toArray();

        }

        return $productData;
    }

    public static function fnForSupplierData($supplierArr = [])
    {
        $supplierData = array();
        if (count($supplierArr) > 0) {
            $supplierData = DB::table('fam_suppliers')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $supplierArr)
                ->selectRaw('sup_comp_name, id')
                ->pluck('sup_comp_name', 'id')
                ->toArray();
        }

        return $supplierData;
    }

    public static function stockQuantity_Multiple($branchID, $ProductID = [], $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }
        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        // $branchID >= 1 &&

        if (!empty($ProductID)) {

            $StockC          = 0;
            $PreOBC          = 0;
            $OpeningBalanceC = 0;
            $PurchaseC       = 0;
            $PurchaseReturnC = 0;
            $IssueC          = 0;
            $IssueReturnC    = 0;
            $TransferInC     = 0;
            $TransferOutC    = 0;
            $SalesC          = 0;
            $SalesReturnC    = 0;
            $AdjustmentC     = 0;
            $waiverProductC  = 0;

            $StockA          = array();
            $PreOBA          = array();
            $OpeningBalanceA = array();
            $PurchaseA       = array();
            $PurchaseReturnA = array();
            $IssueA          = array();
            $IssueReturnA    = array();
            $TransferInA     = array();
            $TransferOutA    = array();
            $SalesA          = array();
            $SalesReturnA    = array();
            $AdjustmentA     = array();
            $waiverProductA  = array();

            $stockArr      = array();
            $AllStockArray = array();

            /* Model Load */
            // $POpeningBalance = 'App\\Model\\POS\\POBStockDetails';
            // $PurchaseDetails = 'App\\Model\\POS\\PurchaseDetails';
            // $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            // $IssueDetails = 'App\\Model\\POS\\Issued';
            // $IssueReturnDetails = 'App\\Model\\POS\\IssueReturnd';
            // $TransferDetails = 'App\\Model\\POS\\TransferDetails';
            // $SalesDetails = 'App\\Model\\POS\\SalesDetails';
            // $SaleReturnd = 'App\\Model\\POS\\SaleReturnd';

            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Opening Balance Count
                $OpeningBalance = DB::table('fam_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                    ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                            ->whereIn('obd.product_id', $ProductID);
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                ## Purchase Balance Count
                $Purchase = DB::table('fam_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_purchases_d as pd', function ($Purchase) use ($ProductID) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                            ->whereIn('pd.product_id', $ProductID);
                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                ## Purchase Return Count
                $PurchaseReturn = DB::table('fam_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                            ->whereIn('prd.product_id', $ProductID);
                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                ## Issue Balance Count
                $Issue = DB::table('fam_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->whereIn('isd.product_id', $ProductID);
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                ## Issue Return Count
                $IssueReturn = DB::table('fam_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->whereIn('ird.product_id', $ProductID);
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();
                // dd($IssueReturn);

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('fam_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('fam_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                            ->whereIn('psd.product_id', $ProductID);
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                ## Adjustment Audit  Count
                $Adjustment = DB::table('fam_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_audit_d as ad', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code')
                            ->whereIn('ad.product_id', $ProductID);

                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = Product::where([['is_delete', 0], ['is_active', 1]])->whereIn('id', $ProductID)->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                            'Purchase'       => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'] + $PreOBArr[$key]['waiverProduct'],
                        ];

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                        //     'Purchase' => $stockArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'],
                        // ];
                    }
                }
            } else {

                // ## Opening Balance Count
                $OpeningBalance = DB::table('fam_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
                    ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $OpeningBalance->where('obm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                            ->whereIn('obd.product_id', $ProductID);
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                ## Purchase Balance Count
                $Purchase = DB::table('fam_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_purchases_d as pd', function ($Purchase) use ($ProductID) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                            ->whereIn('pd.product_id', $ProductID);
                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                ## Purchase Return Count
                $PurchaseReturn = DB::table('fam_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                            ->whereIn('prd.product_id', $ProductID);
                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                ## Issue Balance Count
                $Issue = DB::table('fam_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_to', '<>', 1]])
                    ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Issue->where('im.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->whereIn('isd.product_id', $ProductID);
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // ## Issue Return Count
                $IssueReturn = DB::table('fam_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
                    ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $IssueReturn->where('irm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->whereIn('ird.product_id', $ProductID);
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                // ## TransferIn Balance Count
                $TransferIn = DB::table('fam_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_to', '<>', 1]])
                    ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferIn->where('ptm.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->whereIn('ptd.product_id', $ProductID);
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferIn as $Row) {
                    $TransferInA[$Row->product_id] = $Row->TransferIn;
                }

                // ## TransferOut Return Count
                $TransferOut = DB::table('fam_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_from', '<>', 1]])
                    ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferOut->where('ptm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->whereIn('ptd.product_id', $ProductID);
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferOut as $Row) {
                    $TransferOutA[$Row->product_id] = $Row->TransferOut;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('fam_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('fam_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                            ->whereIn('psd.product_id', $ProductID);
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                ## Adjustment Audit  Count
                $Adjustment = DB::table('fam_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('fam_audit_d as ad', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code')
                            ->whereIn('ad.product_id', $ProductID);

                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = Product::where([['is_delete', 0], ['is_active', 1]])->whereIn('id', $ProductID)->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $TransferInC  = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
                    $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

                    $SalesC       = ((isset($SalesA[$row->id]) ? $SalesA[$row->id] : 0));
                    $SalesReturnC = ((isset($SalesReturnA[$row->id]) ? $SalesReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'TransferIn'     => $TransferInC,
                        'TransferOut'    => $TransferOutC,
                        'Sales'          => $SalesC,
                        'SalesReturn'    => $SalesReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    // dd($stockArr, $PreOBArr);

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                        //     'TransferIn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
                        //     'TransferOut' => $stockArr[$key]['TransferOut'] + $PreOBArr[$key]['TransferOut'],
                        //     'Sales' => $stockArr[$key]['Sales'] + $PreOBArr[$key]['Sales'],
                        //     'SalesReturn' => $stockArr[$key]['SalesReturn'] + $PreOBArr[$key]['SalesReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                        // ];

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                            'Purchase'       => $stockArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                            'TransferIn'     => $stockArr[$key]['TransferIn'],
                            'TransferOut'    => $stockArr[$key]['TransferOut'],
                            'Sales'          => $stockArr[$key]['Sales'],
                            'SalesReturn'    => $stockArr[$key]['SalesReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],
                        ];
                    }
                }
            }

            return $AllStockArray;

        } else {
            return "Error";
        }
    }

    public static function generateProductCode($branchID = null, $product_id = null, $project_id = null, $prod_type_id = null)
    {

        $CompanyID = Common::getCompanyId();

        // $CompanyCodeQuery = DB::table('gnl_companies')->where([['is_delete', 0], ['is_active', 1], ['id', $CompanyID]])
        //     ->select('comp_code')
        //     ->first();
        ## formId 10 = CompanyPrefix
        $CompanyCodeQuery = (!empty(DB::table('gnl_company_config')->where([['company_id', $CompanyID], ['form_id', 10]])->first()->form_value)) ? DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 10]])->first()->form_value : 'GR';

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        $ProjectCodeQuery = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1], ['id', $project_id]])
            ->select('project_code')
            ->first();

        $ProductTypeCodeQuery = DB::table('fam_p_types')->where([['is_delete', 0], ['is_active', 1], ['id', $prod_type_id]])
            ->select('prod_type_code')
            ->first();

        $CompanyCode     = $CompanyCodeQuery;
        $BranchCode      = sprintf("%04d", $BranchCodeQuery->branch_code);
        $ProjectCode     = sprintf("%02d", $ProjectCodeQuery->project_code);
        $ProductTypeCode = sprintf("%03d", $ProductTypeCodeQuery->prod_type_code);

        $CompanyRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['company_id', $CompanyID]])->orderBy('prod_code', 'DESC')->first();

        ##Com ass find
        if ($CompanyRecord) {
            $OldBillNoArray = explode('-', $CompanyRecord->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[1];
            $CompanyAssetNo = sprintf("%05d", ($OldBillNoA + 1));
        } else {
            $CompanyAssetNo = sprintf("%05d", 1);
        }

        ##Project ass find
        $ProjectRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['project_id', $project_id]])->orderBy('prod_code', 'DESC')->first();

        // dd($ProjectRecord);
        if ($ProjectRecord) {
            $OldBillNoArray = explode('-', $ProjectRecord->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[3];
            // dd($OldBillNoA);
            $ProjectAssetNo = sprintf("%05d", ($OldBillNoA + 1));

        } else {
            $ProjectAssetNo = sprintf("%05d", 1);
        }

        ##Branch ass find
        $recordBranch = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['branch_id', $branchID]])->orderBy('prod_code', 'DESC')->first();

        if ($recordBranch) {
            $OldBillNoArray = explode('-', $recordBranch->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[5];
            $BranchAssetNo  = sprintf("%05d", ($OldBillNoA + 1));
        } else {
            $BranchAssetNo = sprintf("%05d", 1);
        }

        ##Product Type ass find
        $ProductTypeRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['prod_type_id', $prod_type_id]])->orderBy('prod_code', 'DESC')->first();

        if ($ProductTypeRecord) {
            $OldBillNoArray     = explode('-', $ProductTypeRecord->prod_code);
            $OldBillNoA         = (int) $OldBillNoArray[7];
            $ProductTypeAssetNo = sprintf("%05d", ($OldBillNoA + 1));
        } else {
            $ProductTypeAssetNo = sprintf("%05d", 1);
        }

        $BillNo = $CompanyCode . '-' . $CompanyAssetNo . '-' . $ProjectCode . '-' . $ProjectAssetNo . '-' . $BranchCode . '-' . $BranchAssetNo . '-' . $ProductTypeCode . '-' . $ProductTypeAssetNo;

        return $BillNo;
    }

    public static function generateProductCodeForTransfer($branchID = null, $product_id = null, $project_id = null, $prod_type_id = null)
    {

        $CompanyID = Common::getCompanyId();

        // $CompanyCodeQuery = DB::table('gnl_companies')->where([['is_delete', 0], ['is_active', 1], ['id', $CompanyID]])
        //     ->select('comp_code')
        //     ->first();
        ## formId 10 = CompanyPrefix
        $CompanyCodeQuery = (!empty(DB::table('gnl_company_config')->where([['company_id', $CompanyID], ['form_id', 10]])->first())) ? DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 10]])->first()->form_value : 'GR';

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        $ProjectCodeQuery = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1], ['id', $project_id]])
            ->select('project_code')
            ->first();

        $ProductTypeCodeQuery = DB::table('fam_p_types')->where([['is_delete', 0], ['is_active', 1], ['id', $prod_type_id]])
            ->select('prod_type_code')
            ->first();

        $CompanyCode     = $CompanyCodeQuery;
        $BranchCode      = sprintf("%04d", $BranchCodeQuery->branch_code);
        $ProjectCode     = sprintf("%02d", $ProjectCodeQuery->project_code);
        $ProductTypeCode = sprintf("%03d", $ProductTypeCodeQuery->prod_type_code);

        $CompanyRecord = DB::table('fam_temp_product_code')->where([['company_id', $CompanyID]])->orderBy('prod_code', 'DESC')->first();
        if (empty($CompanyRecord)) {
            $CompanyRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['company_id', $CompanyID]])->orderBy('prod_code', 'DESC')->first();
        }

        ##Com ass find
        if ($CompanyRecord) {
            $OldBillNoArray = explode('-', $CompanyRecord->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[1];
            $CompanyAssetNo = sprintf("%05d", ($OldBillNoA + 1));
        } else {
            $CompanyAssetNo = sprintf("%05d", 1);
        }

        ##Project ass find
        $ProjectRecord = DB::table('fam_temp_product_code')->where([['project_id', $project_id]])->orderBy('prod_code', 'DESC')->first();
        if (empty($ProjectRecord)) {
            $ProjectRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['project_id', $project_id]])->orderBy('prod_code', 'DESC')->first();
        }

        // dd($ProjectRecord);
        if ($ProjectRecord) {
            $OldBillNoArray = explode('-', $ProjectRecord->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[3];
            // dd($OldBillNoA);
            $ProjectAssetNo = sprintf("%04d", ($OldBillNoA + 1));

        } else {
            $ProjectAssetNo = sprintf("%04d", 1);
        }

        ##Branch ass find
        $recordBranch = DB::table('fam_temp_product_code')->where([['branch_id', $branchID]])->orderBy('prod_code', 'DESC')->first();
        if (empty($recordBranch)) {
            $recordBranch = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['branch_id', $branchID]])->orderBy('prod_code', 'DESC')->first();
        }

        if ($recordBranch) {
            $OldBillNoArray = explode('-', $recordBranch->prod_code);
            $OldBillNoA     = (int) $OldBillNoArray[5];
            $BranchAssetNo  = sprintf("%04d", ($OldBillNoA + 1));
        } else {
            $BranchAssetNo = sprintf("%04d", 1);
        }

        ##Product Type ass find
        $ProductTypeRecord = DB::table('fam_temp_product_code')->where([['prod_type_id', $prod_type_id]])->orderBy('prod_code', 'DESC')->first();
        if (empty($ProductTypeRecord)) {
            $ProductTypeRecord = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['prod_type_id', $prod_type_id]])->orderBy('prod_code', 'DESC')->first();
        }
        if ($ProductTypeRecord) {
            $OldBillNoArray     = explode('-', $ProductTypeRecord->prod_code);
            $OldBillNoA         = (int) $OldBillNoArray[7];
            $ProductTypeAssetNo = sprintf("%04d", ($OldBillNoA + 1));
        } else {
            $ProductTypeAssetNo = sprintf("%04d", 1);
        }

        $BillNo = $CompanyCode . '-' . $CompanyAssetNo . '-' . $ProjectCode . '-' . $ProjectAssetNo . '-' . $BranchCode . '-' . $BranchAssetNo . '-' . $ProductTypeCode . '-' . $ProductTypeAssetNo;

        return $BillNo;
    }

    public static function generateProductCodeBackup($branchID = null, $product_id = null, $project_id = null, $prod_type_id = null)
    {

        $CompanyID = Common::getCompanyId();

        $CompanyCodeQuery = DB::table('gnl_companies')->where([['is_delete', 0], ['is_active', 1], ['id', $CompanyID]])
            ->select('comp_code')
            ->first();
        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();
        $ProjectCodeQuery = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1], ['id', $project_id]])
            ->select('project_code')
            ->first();

        $ProductTypeCodeQuery = DB::table('fam_p_types')->where([['is_delete', 0], ['is_active', 1], ['id', $prod_type_id]])
            ->select('prod_type_code')
            ->first();

        $CompanyCode     = sprintf("%02d", $CompanyCodeQuery->comp_code);
        $BranchCode      = sprintf("%04d", $BranchCodeQuery->branch_code);
        $ProjectCode     = sprintf("%03d", $ProjectCodeQuery->project_code);
        $ProductTypeCode = sprintf("%04d", $ProductTypeCodeQuery->prod_type_code);

        $ProductData = DB::table('fam_products')->where([['is_delete', 0], ['is_active', 1], ['prod_name_id', $product_id]])->orderBy('prod_code', 'DESC')
            ->get();

        ##Com ass find
        $CompanyRecord = $ProductData->where('company_id', $CompanyID)->first();
        if ($CompanyRecord) {
            $OldBillNoA     = substr($CompanyRecord->prod_code, 3, 4);
            $OldBillNoA     = (int) $OldBillNoA;
            $CompanyAssetNo = sprintf("%04d", ($OldBillNoA + 1));
        } else {
            $CompanyAssetNo = sprintf("%04d", 1);
        }
        // dd($ProductData);
        ##Project ass find
        $ProjectRecord = $ProductData->where('project_id', $project_id)->first();
        // dd($ProjectRecord);
        if ($ProjectRecord) {
            $OldBillNoA = substr($ProjectRecord->prod_code, 12, 4);
            $OldBillNoA = (int) $OldBillNoA;
            // dd($OldBillNoA);
            $ProjectAssetNo = sprintf("%04d", ($OldBillNoA + 1));

        } else {
            $ProjectAssetNo = sprintf("%04d", 1);
        }

        ##Branch ass find
        $recordBranch = $ProductData->where('branch_id', $branchID)->first();
        if ($recordBranch) {
            $OldBillNoA    = substr($recordBranch->prod_code, 22, 4);
            $OldBillNoA    = (int) $OldBillNoA;
            $BranchAssetNo = sprintf("%04d", ($OldBillNoA + 1));
        } else {
            $BranchAssetNo = sprintf("%04d", 1);
        }

        ##Product Type ass find
        $ProductTypeRecord = $ProductData->where('prod_type_id', $prod_type_id)->first();
        if ($ProductTypeRecord) {
            $OldBillNoA         = substr($ProductTypeRecord->prod_code, 32, 4);
            $OldBillNoA         = (int) $OldBillNoA;
            $ProductTypeAssetNo = sprintf("%04d", ($OldBillNoA + 1));
        } else {
            $ProductTypeAssetNo = sprintf("%04d", 1);
        }

        $BillNo = $CompanyCode . '-' . $CompanyAssetNo . '-' . $ProjectCode . '-' . $ProjectAssetNo . '-' . $BranchCode . '-' . $BranchAssetNo . '-' . $ProductTypeCode . '-' . $ProductTypeAssetNo;

        return $BillNo;
    }

    public static function generateBillPurchase($branchID = null)
    {
        $BranchT         = 'App\\Model\\GNL\\Branch';
        $PurchaseMasterT = 'App\\Model\\FAM\\PurchaseMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();
        // dd(        $BranchCodeQuery);
        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "FAM-PUR" . $BranchCode;

        $record = $PurchaseMasterT::select(['id', 'bill_no'])
            ->where('branch_id', $branchID)
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }

    public static function generateBillPurchaseReturn($branchID = null)
    {
        $BranchT               = 'App\\Model\\GNL\\Branch';
        $PurchaseReturnMasterT = 'App\\Model\\FAM\\PurchaseReturnMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IN-PR" . $BranchCode;

        $record = $PurchaseReturnMasterT::where('branch_id', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generatePaymentBillNo($branchID = null)
    {
        $BranchT  = 'App\\Model\\GNL\\Branch';
        $PaymentT = 'App\\Model\\FAM\\Payment';

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "PAY" . $BranchCode;

        $record = $PaymentT::select(['id', 'payment_no'])
            ->where('branch_id', $branchID)
            ->where('payment_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('payment_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->payment_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }

    public static function generateBillSales($branchID = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "SL" . $BranchCode;

        $record = DB::table('fam_sales_m')->where('branch_id', $branchID)
            ->select(['id', 'sales_bill_no'])
            ->where('sales_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('sales_bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->sales_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }
    public static function generateDepreciation($total_cost = 0, $resale_value = 0, $year = 0, $month = 0)
    {
        $tt_year = $year;

        if ($month > 0) {
            $tt_year += ($month / 12);
        }

        $dep_amount = round(($total_cost - $resale_value) / $tt_year);

        $dep_percentage = ($dep_amount / ($total_cost - $resale_value)) * 100;

        // dd($dep_percentage );
        $data = array(
            'dep_amount'     => $dep_amount,
            'dep_percentage' => $dep_percentage,
        );
        return $data;
    }
    public static function generateBillDepreciation($branchID = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "DEP" . $BranchCode;

        $record = DB::table('fam_depreciation_m')->where('branch_id', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillWriteOff($branchID = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "WRO" . $BranchCode;

        $record = DB::table('fam_writeoff_m')->where('branch_id', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }
    public static function generateBillAdditionalCharge($branchID = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "AC" . $BranchCode;

        $record = DB::table('fam_add_charge_m')->where('branch_id', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillIssue($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $IssuemT = 'App\\Model\\FAM\\IssueMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IN-IS" . $BranchCode;

        $record = $IssuemT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillIssueReturn($branchID = null)
    {
        $BranchT       = 'App\\Model\\GNL\\Branch';
        $IssueReturnmT = 'App\\Model\\FAM\\IssueReturnMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IN-IR" . $BranchCode;

        $record = $IssueReturnmT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillTransfer($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\FAM\\TransferMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IN-TR" . $BranchCode;

        $record = $ModelT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillUses($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\FAM\\UsesMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IN-US" . $BranchCode;
        $record    = $ModelT::select(['id', 'uses_bill_no'])
            ->where('branch_id', $branchID)
            ->where('uses_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('uses_bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->uses_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillUsesReturn($BranchId = null)
    {
        $BranchT     = 'App\\Model\\GNL\\Branch';
        $SalesReturn = 'App\\Model\\FAM\\UseReturnMaster';

        $BranchCode = $BranchT::where(['is_delete' => 0, 'is_approve' => 1, 'id' => $BranchId])
            ->select('branch_code')
            ->first();

        $PreBillNo = "IN-UR" . $BranchCode->branch_code;

        $record = $SalesReturn::where('branch_id', $BranchId)
            ->select(['id', 'return_bill_no'])
            ->where('return_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('return_bill_no', 'DESC')
            ->first();

        if ($record) {

            $OldBillNoA = explode($PreBillNo, $record->return_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillPOBS($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\FAM\\POBStockMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "IN-OB" . $BranchCode;
        $record    = $ModelT::select(['id', 'ob_no'])
            ->where('branch_id', $branchID)
            ->where('ob_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('ob_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->ob_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillRequisiton($branchID = null)
    {
        $BranchT      = 'App\\Model\\GNL\\Branch';
        $RequisitionM = 'App\\Model\\FAM\\RequisitionMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreReqNo = "IN-RQ" . $BranchCode;

        $record = $RequisitionM::select('id', 'requisition_no')
            ->where('branch_from', $branchID)
            ->where('requisition_no', 'LIKE', "{$PreReqNo}%")
            ->orderBy('requisition_no', 'DESC')
            ->first();

        if ($record) {
            $OldReqNoA = explode($PreReqNo, $record->requisition_no);
            $ReqNo     = $PreReqNo . sprintf("%05d", ($OldReqNoA[1] + 1));
        } else {
            $ReqNo = $PreReqNo . sprintf("%05d", 1);
        }

        return $ReqNo;
    }

    public static function generateBillRequisitonEmp($branchID = null)
    {
        $BranchT      = 'App\\Model\\GNL\\Branch';
        $RequisitionM = 'App\\Model\\FAM\\EmployeeRequisitionMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreReqNo = "IN-RQ" . $BranchCode;

        $record = $RequisitionM::select('id', 'requisition_no')
            ->where('branch_id', $branchID)
            ->where('requisition_no', 'LIKE', "{$PreReqNo}%")
            ->orderBy('requisition_no', 'DESC')
            ->first();

        if ($record) {
            $OldReqNoA = explode($PreReqNo, $record->requisition_no);
            $ReqNo     = $PreReqNo . sprintf("%05d", ($OldReqNoA[1] + 1));
        } else {
            $ReqNo = $PreReqNo . sprintf("%05d", 1);
        }

        return $ReqNo;
    }

    public static function generateBillOrder($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $OrderM  = 'App\\Model\\FAM\\OrderMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreOrderNo = "IN-OR" . $BranchCode;

        $record = $OrderM::select('id', 'order_no')
            ->where('order_from', $branchID)
            ->where('order_no', 'LIKE', "{$PreOrderNo}%")
            ->orderBy('order_no', 'DESC')
            ->first();

        if ($record) {
            $OldOrderNo = explode($PreOrderNo, $record->order_no);
            $OrderNo    = $PreOrderNo . sprintf("%05d", ($OldOrderNo[1] + 1));
        } else {
            $OrderNo = $PreOrderNo . sprintf("%05d", 1);
        }

        return $OrderNo;
    }

    public static function generateDayendNo($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\FAM\\DayEnd";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "IN-DE" . $BranchCode;
        $record    = $ModelT::select(['id', 'dayend_no'])
            ->where('branch_id', $branchID)
            ->where('dayend_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('dayend_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->dayend_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateMonthendNo($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\FAM\\MonthEnd";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "IN-ME" . $BranchCode;
        $record    = $ModelT::select(['id', 'monthend_no'])
            ->where('branch_id', $branchID)
            ->where('monthend_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('monthend_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->monthend_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    ## This function is used to check if tx exists under an employee
    ## before transfer/termination
    public static function checkTransactionForEmployee($employeeId, $action = "terminating")
    {
        $moduleFlag = false;
        $errMessage = false;

        if (Common::checkActivatedModule('fam')) {
            $moduleFlag = true;
        }
        return false;
    }
}
