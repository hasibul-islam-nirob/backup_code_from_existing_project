<?php

namespace App\Services;

use App\Model\GNL\Branch;
use App\Model\INV\Product;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use DateTime;
use Illuminate\Support\Facades\DB;

class InvService
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
            $Uses          = 0;
            $UsesReturn    = 0;
            $Adjustment     = 0;
            $waiverProduct  = 0;

            /* Model Load */
            $POpeningBalance       = 'App\\Model\\INV\\POBStockDetails';
            $PurchaseDetails       = 'App\\Model\\INV\\PurchaseDetails';
            $PurchaseReturnDetails = 'App\\Model\\INV\\PurchaseReturnDetails';
            $IssueDetails          = 'App\\Model\\INV\\IssueDetails';
            $IssueReturnDetails    = 'App\\Model\\INV\\IssueReturnDetails';
            $TransferDetails       = 'App\\Model\\INV\\TransferDetails';
            $UsesDetails          = 'App\\Model\\INV\\UsesDetails';
            $UsesReturnd           = 'App\\Model\\INV\\UseReturnDetails';

            ## Opening Balance Count
            $OpeningBalance = DB::table('inv_ob_stock_m as obm')
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
                ->join('inv_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                        ->where('obd.product_id', $ProductID);
                })
                ->sum('obd.product_quantity');

            ## Purchase Balance Count
            $Purchase = DB::table('inv_purchases_m as pm')
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
                ->join('inv_purchases_d as pd', function ($Purchase) use ($ProductID) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                        ->where('pd.product_id', $ProductID);
                })
                ->sum('pd.product_quantity');

            ## Purchase Return Count
            $PurchaseReturn = DB::table('inv_purchases_r_m as prm')
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
                ->join('inv_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                        ->where('prd.product_id', $ProductID);
                })
                ->sum('prd.product_quantity');



            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Issue Balance Count
                $Issue = DB::table('inv_issues_m as im')
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
                    ->join('inv_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                // dd($Issue);

                // Issue Return Count
                $IssueReturn = DB::table('inv_issues_r_m as irm')
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
                    ->join('inv_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
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
                $Issue = DB::table('inv_issues_m as im')
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
                    ->join('inv_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                // dd($Issue);

                // Issue Return Count
                $IssueReturn = DB::table('inv_issues_r_m as irm')
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
                    ->join('inv_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    ->sum('ird.product_quantity');

                // TransferIn Balance Count
                $TransferIn = DB::table('inv_transfers_m as ptm')
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
                    ->join('inv_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                // TransferOut Return Count
                $TransferOut = DB::table('inv_transfers_m as ptm')
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
                    ->join('inv_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                // Sales Balance Count
                $Uses = DB::table('inv_use_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($Uses) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Uses->whereBetween('psm.uses_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Uses->where('psm.uses_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Uses->where('psm.uses_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_d as psd', function ($Uses) use ($ProductID) {
                        $Uses->on('psd.uses_bill_no', 'psm.uses_bill_no')
                            ->where('psd.product_id', $ProductID);
                    })
                    ->sum('psd.product_quantity');

                // SaleReturnd Return Count
                $UsesReturn = DB::table('inv_use_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                    ->where(function ($UsesReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_return_d as psrd', function ($UsesReturn) use ($ProductID) {
                        $UsesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
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

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn + $Issue - $IssueReturn + $TransferIn - $TransferOut - $Uses + $UsesReturn + $Adjustment - $waiverProduct);
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
                    'Sales'          => $Uses,
                    'SalesReturn'    => $UsesReturn,
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


    public static function stockQuantity_Multiple($branchID, $ProductID = [], $startDate = null, $endDate = null, $checkToDate= true)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Uses + UsesReturn +- Adjustment
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
            if($checkToDate == false){
                $toDate = null ;
            }else{
                $toDate = (new DateTime(Common::systemCurrentDate($branchID)))->format('Y-m-d');
            }          
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
            $UsesC          = 0;
            $UsesReturnC    = 0;
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
            $UsesA          = array();
            $UsesReturnA    = array();
            $AdjustmentA     = array();
            $waiverProductA  = array();

            $stockArr      = array();
            $AllStockArray = array();

            $productSearch = false;
            if (count($ProductID) < 951) {
                $productSearch = true;
            }

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
                $OpeningBalance = DB::table('inv_ob_stock_m as obm')
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
                    ->join('inv_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // Purchase Balance Count
                $Purchase = DB::table('inv_purchases_m as pm')
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
                    ->join('inv_purchases_d as pd', function ($Purchase) use ($ProductID, $productSearch) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Purchase->whereIn('pd.product_id', $ProductID);
                            }
                        } else {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::table('inv_purchases_r_m as prm')
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
                    ->join('inv_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                            }
                        } else {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                // Issue Balance Count
                $Issue = DB::table('inv_issues_m as im')
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
                    ->join('inv_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // Issue Return Count
                $IssueReturn = DB::table('inv_issues_r_m as irm')
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
                    ->join('inv_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();
                // dd($IssueReturn);

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }
                

                // ## Uses Balance Count
               
                $Uses = DB::table('inv_use_m as psm')
                    ->where('psm.branch_id', 1)
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1]]) //  ['psm.branch_id', '<>', 1]
                    ->where(function ($Uses) use ( $fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $Uses->whereBetween('psm.uses_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Uses->where('psm.uses_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Uses->where('psm.uses_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_d as psd', function ($Uses) use ($ProductID, $productSearch) {
                        $Uses->on('psd.uses_bill_no', 'psm.uses_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Uses->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $Uses->whereIn('psd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Uses')
                    ->groupBy('psd.product_id')
                    ->get();
                    


                ## Uses Return
                $UsesReturn = DB::table('inv_use_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                    ->where(function ($UsesReturn) use ( $fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_return_d as psrd', function ($UsesReturn) use ($ProductID, $productSearch) {
                        $UsesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $UsesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        } else {
                            $UsesReturn->whereIn('psrd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as UsesReturn')
                    ->groupBy('psrd.product_id')
                    ->get();
                

                ## Uses Count
                foreach ($Uses as $Row) {
                    $UsesA[$Row->product_id] = $Row->Uses;
                }

                // ## SaleReturnd Return Count
                foreach ($UsesReturn as $Row) {
                    $UsesReturnA[$Row->product_id] = $Row->UsesReturn;
                }


                // Adjustment Audit  Count
                $Adjustment = DB::table('inv_audit_m as am')
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
                    ->join('inv_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = Product::where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID, $productSearch) {
                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $productData->whereIn('id', $ProductID);
                            }
                        } else {
                            $productData->whereIn('id', $ProductID);
                        }
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    
                    $usesC = ((isset($UsesA[$row->id]) ? $UsesA[$row->id] : 0));

                    $UsesReturnC = ((isset($UsesReturnA[$row->id]) ? $UsesReturnA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC
                            - $usesC + $UsesReturnC);


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
                        'uses'           => $usesC,
                        'usesReturn'     => $UsesReturnC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
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
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],
                            'uses'           => $stockArr[$key]['uses'],
                        ];
                    }
                }
            } else {

                // ## Opening Balance Count
                $OpeningBalance = DB::table('inv_ob_stock_m as obm')
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
                    ->join('inv_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // Purchase Balance Count
                $Purchase = DB::table('inv_purchases_m as pm')
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
                    ->join('inv_purchases_d as pd', function ($Purchase) use ($ProductID, $productSearch) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Purchase->whereIn('pd.product_id', $ProductID);
                            }
                        } else {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::table('inv_purchases_r_m as prm')
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
                    ->join('inv_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                            }
                        } else {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                // ## Issue Balance Count
                $Issue = DB::table('inv_issues_m as im')
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
                    ->join('inv_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // ## Issue Return Count
                $IssueReturn = DB::table('inv_issues_r_m as irm')
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
                    ->join('inv_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                // ## TransferIn Balance Count
                $TransferIn = DB::table('inv_transfers_m as ptm')
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
                    ->join('inv_transfers_d as ptd', function ($TransferIn) use ($ProductID, $productSearch) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferIn->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferIn->whereIn('ptd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferIn as $Row) {
                    $TransferInA[$Row->product_id] = $Row->TransferIn;
                }

                // ## TransferOut Return Count
                $TransferOut = DB::table('inv_transfers_m as ptm')
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
                    ->join('inv_transfers_d as ptd', function ($TransferOut) use ($ProductID, $productSearch) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferOut->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferOut->whereIn('ptd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferOut as $Row) {
                    $TransferOutA[$Row->product_id] = $Row->TransferOut;
                }

                // ## Uses Balance Count

               
                $Uses = DB::table('inv_use_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                    ->where(function ($Uses) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Uses->where('psm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Uses->whereBetween('psm.uses_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Uses->where('psm.uses_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Uses->where('psm.uses_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_d as psd', function ($Uses) use ($ProductID, $productSearch) {
                        $Uses->on('psd.uses_bill_no', 'psm.uses_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Uses->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $Uses->whereIn('psd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Uses')
                    ->groupBy('psd.product_id')
                    ->get();

                ## Uses Return
                $UsesReturn = DB::table('inv_use_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                    ->where(function ($UsesReturn) use ($branchID, $fromDate, $toDate) {
                        if (!empty($branchID)) {
                            $UsesReturn->where('psrm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $UsesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('inv_use_return_d as psrd', function ($UsesReturn) use ($ProductID, $productSearch) {
                        $UsesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $UsesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        } else {
                            $UsesReturn->whereIn('psrd.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as UsesReturn')
                    ->groupBy('psrd.product_id')
                    ->get();
                

                ## Uses Count
                foreach ($Uses as $Row) {
                    $UsesA[$Row->product_id] = $Row->Uses;
                }

                // ## SaleReturnd Return Count
                foreach ($UsesReturn as $Row) {
                    $UsesReturnA[$Row->product_id] = $Row->UsesReturn;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::table('inv_audit_m as am')
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
                    ->join('inv_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }

                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = Product::where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID, $productSearch) {
                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $productData->whereIn('id', $ProductID);
                            }
                        } else {
                            $productData->whereIn('id', $ProductID);
                        }
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $TransferInC  = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
                    $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

                    $UsesC       = ((isset($UsesA[$row->id]) ? $UsesA[$row->id] : 0));
                    $UsesReturnC = ((isset($UsesReturnA[$row->id]) ? $UsesReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $UsesC + $UsesReturnC + $AdjustmentC - $waiverProductC);

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
                        'Uses'          => $UsesC,
                        'UsesReturn'    => $UsesReturnC,
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
                        //     'Uses' => $stockArr[$key]['Uses'] + $PreOBArr[$key]['Uses'],
                        //     'UsesReturn' => $stockArr[$key]['UsesReturn'] + $PreOBArr[$key]['UsesReturn'],
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
                            'Uses'          => $stockArr[$key]['Uses'],
                            'UsesReturn'    => $stockArr[$key]['UsesReturn'],
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


    public static function generateBillPurchase($branchID = null)
    {
        $BranchT         = 'App\\Model\\GNL\\Branch';
        $PurchaseMasterT = 'App\\Model\\INV\\PurchaseMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "INV-PUR" . $BranchCode;

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
        $PurchaseReturnMasterT = 'App\\Model\\INV\\PurchaseReturnMaster';

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
        $PaymentT = 'App\\Model\\INV\\Payment';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
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

    public static function generateBillIssue($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $IssuemT = 'App\\Model\\INV\\IssueMaster';

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
        $IssueReturnmT = 'App\\Model\\INV\\IssueReturnMaster';

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
        $ModelT  = "App\\Model\\INV\\TransferMaster";

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
        $ModelT  = "App\\Model\\INV\\UsesMaster";

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
        $SalesReturn = 'App\\Model\\INV\\UseReturnMaster';

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
        $ModelT  = "App\\Model\\INV\\POBStockMaster";

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
        $RequisitionM = 'App\\Model\\INV\\RequisitionMaster';

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
        $RequisitionM = 'App\\Model\\INV\\EmployeeRequisitionMaster';

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
        $OrderM  = 'App\\Model\\INV\\OrderMaster';

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
        $ModelT  = "App\\Model\\INV\\DayEnd";

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
        $ModelT  = "App\\Model\\INV\\MonthEnd";

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

        if (Common::checkActivatedModule('inv')) {
            $moduleFlag = true;
        }
        return false;
    }


    public static function fnForProductSettingsWise($productId = null, $groupId = null, $catId = null, $subCatId = null,
        $brandId = null, $modelId = null, $supplierId = null, $companyID = null) {
        $selectProduct = array();

        if (!empty($productId)) {
            $selectProduct = [$productId];

        } else {
            $productQuery = DB::table('inv_products')
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
}
