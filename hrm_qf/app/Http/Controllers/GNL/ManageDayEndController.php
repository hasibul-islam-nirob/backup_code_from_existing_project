<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use Illuminate\Http\Request;
use Laminas\Diactoros\Module;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;

class ManageDayEndController extends Controller
{
    public function index(Request $req)
    {
        if(Common::isSuperUser() == false && Common::isDeveloperUser() == false){
            $notification = array(
                'message' => 'You are not authorised this location.',
                'alert-type' => 'error',
            );

            return Redirect::back()->with($notification);
        }

        $branchList = DB::table('gnl_branchs')
            ->where([
                ['is_delete', 0],
            ])
            ->orderBy('branch_code')
            ->select('id', 'branch_name', 'branch_code')
            ->get();

        $modules = DB::table('gnl_sys_modules')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
                ['id', '>', 1],
            ])
            ->get();

        $data = array(
            'branchList' => $branchList,
            'moduleList' => $modules,
        );
        return view('GNL.ManageDayEnd.index', $data);
    }

    public function getInfo(Request $req)
    {
        $html = "No data Found !";
        $toDate = "";
        $fromDate = "";

        // if(Common::isSuperUser() == false && Common::isDeveloperUser() == false){
        //     return response()->json([
        //         'info' => "You are not authorised this location.",
        //         'minDate' => $fromDate,
        //         'maxDate' => $toDate,
        //     ]);
        // }

        if ($req->module == 'mfn') {
            $mfnDayEnds = DB::table('mfn_day_end')
                ->where([
                    ['branchId', $req->branchId],
                    ['isActive', 0]
                ])
                ->select('date')
                ->orderBy('date')
                ->get();

            if ($mfnDayEnds->count() > 0) {
                $fromDate = date('d-m-Y', strtotime($mfnDayEnds->first()->date));
                $toDate = date('d-m-Y', strtotime($mfnDayEnds->last()->date));

                $html = "Microfinance DayEnd exist from " . $fromDate . " to " . $toDate;
            } else {
                $html = 'No Day end exists';
            }
        } else if ($req->module == 'acc') {
            $accDayEnds = DB::table('acc_day_end')
                ->where([
                    ['branch_id', $req->branchId],
                    ['is_active', 0],
                    ['is_delete', 0]
                ])
                ->select('branch_date')
                ->orderBy('branch_date')
                ->get();
            if ($accDayEnds->count() > 0) {
                $fromDate = date('d-m-Y', strtotime($accDayEnds->first()->branch_date));
                $toDate = date('d-m-Y', strtotime($accDayEnds->last()->branch_date));

                $html = "Accounting DayEnd exist from " . $fromDate . " to " . $toDate;
            } else {
                $html = 'No Day end exists';
            }
        } else if ($req->module == 'pos') {
            $posDayEnds = DB::table('pos_day_end')
                ->where([
                    ['branch_id', $req->branchId],
                    ['is_active', 0],
                    ['is_delete', 0]
                ])
                ->select('branch_date')
                ->orderBy('branch_date')
                ->get();
            if ($posDayEnds->count() > 0) {
                $fromDate = date('d-m-Y', strtotime($posDayEnds->first()->branch_date));
                $toDate = date('d-m-Y', strtotime($posDayEnds->last()->branch_date));

                $html = "POS DayEnd exist from " . $fromDate . " to " . $toDate;
            } else {
                $html = 'No Day end exists';
            }
        } else if ($req->module == 'inv') {
            $invDayEnds = DB::table('inv_day_end')
                ->where([
                    ['branch_id', $req->branchId],
                    ['is_active', 0],
                    ['is_delete', 0]
                ])
                ->select('branch_date')
                ->orderBy('branch_date')
                ->get();
            if ($invDayEnds->count() > 0) {
                $fromDate = date('d-m-Y', strtotime($invDayEnds->first()->branch_date));
                $toDate = date('d-m-Y', strtotime($invDayEnds->last()->branch_date));

                $html = "Inventory DayEnd exist from " . $fromDate . " to " . $toDate;
            } else {
                $html = 'No Day end exists';
            }
        }

        return response()->json([
            'info' => $html,
            'minDate' => $fromDate,
            'maxDate' => $toDate,
        ]);
    }

    public function delete(Request $req)
    {

        if(Common::isSuperUser() == false && Common::isDeveloperUser() == false){
            return response()->json([
                'alert-type' => 'error',
                'message' => 'You are not authorised this location.',
            ]);
        }

        $fromDate = date('Y-m-d', strtotime($req->fromDate));

        //validation
        $validation = $this->validateInput($req);
        if ($validation['alert-type'] == 'success') {

            DB::beginTransaction();
            try {
                if ($req->module == 'mfn') {
                    DB::table('mfn_day_end')
                        ->where('branchId', $req->branchId)
                        ->where('date', '>', $fromDate)
                        ->delete();

                    $lastWorkingDay = DB::table('mfn_day_end')
                        ->where('branchId', $req->branchId)
                        ->where('date', '<=', $fromDate)
                        ->orderBy('date', 'DESC')
                        ->limit(1)
                        ->select('date')
                        ->first()->date;

                    DB::table('mfn_month_end')
                        ->where('branchId', $req->branchId)
                        ->where('date', '>=', $lastWorkingDay)
                        ->delete();

                    //active $fromDate or last working day
                    DB::table('mfn_day_end')
                        ->where('branchId', $req->branchId)
                        ->where('date', $lastWorkingDay)
                        ->update(['isActive' => 1]);
                } else if ($req->module == 'acc') {
                    ////deleting day ends
                    DB::table('acc_day_end')
                        ->where('branch_id', $req->branchId)
                        ->where('branch_date', '>', $fromDate)
                        // ->update(['is_active' => 0, 'is_delete' => 1]);
                        ->delete();

                    $lastWorkingDay = DB::table('acc_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '<=', $fromDate],
                            ['is_delete', 0]
                        ])
                        ->orderBy('branch_date', 'DESC')
                        ->limit(1)
                        ->select('branch_date')
                        ->first()->branch_date;

                    //last working day active
                    DB::table('acc_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '=', $lastWorkingDay],
                            ['is_delete', 0]
                        ])
                        ->update(['is_active' => 1]);

                    ###############################################

                    $lastWorkingDayMonth = (new DateTime($lastWorkingDay))->format('Y-m-01');
                    $lastWorkingDayMonthEnd = (new DateTime($lastWorkingDay))->format('Y-m-31');

                    ## working month er upore delete korte hobe, working month ta is_active 1 hobe. acc, pos, inv te month-date a month er first date porche
                    DB::table('acc_month_end')
                        ->where('branch_id', $req->branchId)
                        ->where('month_date', '>', $lastWorkingDayMonth)
                        ->delete();

                    $lastMonth = DB::table('acc_month_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['month_date', '<=', $lastWorkingDayMonthEnd],
                            ['is_delete', 0]
                        ])
                        ->orderBy('month_date', 'DESC')
                        ->limit(1)
                        ->select('id')
                        ->first();

                    if ($lastMonth) {
                        DB::table('acc_month_end')
                            ->where('id', $lastMonth->id)
                            ->update(['is_active' => 1]);
                    }

                    ####################################################
                    $companyId = Common::getCompanyId();
                    $fiscalYearData = Common::systemFiscalYear($lastWorkingDay, $companyId);

                    if (!empty($fiscalYearData['id'])) {
                        DB::table('acc_year_end')
                            ->where([
                                ['branch_id', $req->branchId],
                                ['fiscal_year_id', '>', $fiscalYearData['id']]
                            ])
                            ->delete();

                        $lastYear = DB::table('acc_year_end')
                            ->where([
                                ['branch_id', $req->branchId],
                                ['fiscal_year_id', $fiscalYearData['id']],
                                ['is_delete', 0]
                            ])
                            ->orderBy('date', 'DESC')
                            ->limit(1)
                            ->select('id')
                            ->first();

                        if ($lastYear) {
                            DB::table('acc_year_end')
                                ->where('id', $lastYear->id)
                                ->update(['is_active' => 1]);
                        }
                    }
                } else if ($req->module == 'pos') {
                    ////deleting day ends
                    DB::table('pos_day_end')
                        ->where('branch_id', $req->branchId)
                        ->where('branch_date', '>', $fromDate)
                        // ->update(['is_active' => 0, 'is_delete' => 1]);
                        ->delete();

                    $lastWorkingDay = DB::table('pos_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '<=', $fromDate],
                            ['is_delete', 0]
                        ])
                        ->orderBy('branch_date', 'DESC')
                        ->limit(1)
                        ->select('branch_date')
                        ->first()->branch_date;

                    //last working day active
                    DB::table('pos_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '=', $lastWorkingDay],
                            ['is_delete', 0]
                        ])
                        ->update(['is_active' => 1]);

                    ###############################################

                    $lastWorkingDayMonth = (new DateTime($lastWorkingDay))->format('Y-m-01');
                    $lastWorkingDayMonthEnd = (new DateTime($lastWorkingDay))->format('Y-m-31');

                    ## working month er upore delete korte hobe, working month ta is_active 1 hobe. acc, pos, inv te month-date a month er first date porche
                    DB::table('pos_month_end')
                        ->where('branch_id', $req->branchId)
                        ->where('month_date', '>', $lastWorkingDayMonth)
                        ->delete();

                    $lastMonth = DB::table('pos_month_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['month_date', '<=', $lastWorkingDayMonthEnd],
                            ['is_delete', 0]
                        ])
                        ->orderBy('month_date', 'DESC')
                        ->limit(1)
                        ->select('id')
                        ->first();

                    if ($lastMonth) {
                        DB::table('pos_month_end')
                            ->where('id', $lastMonth->id)
                            ->update(['is_active' => 1]);
                    }
                } else if ($req->module == 'inv') {
                    ////deleting day ends
                    DB::table('inv_day_end')
                        ->where('branch_id', $req->branchId)
                        ->where('branch_date', '>', $fromDate)
                        // ->update(['is_active' => 0, 'is_delete' => 1]);
                        ->delete();

                    $lastWorkingDay = DB::table('inv_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '<=', $fromDate],
                            ['is_delete', 0]
                        ])
                        ->orderBy('branch_date', 'DESC')
                        ->limit(1)
                        ->select('branch_date')
                        ->first()->branch_date;

                    //last working day active
                    DB::table('inv_day_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['branch_date', '=', $lastWorkingDay],
                            ['is_delete', 0]
                        ])
                        ->update(['is_active' => 1]);

                    ###############################################

                    $lastWorkingDayMonth = (new DateTime($lastWorkingDay))->format('Y-m-01');
                    $lastWorkingDayMonthEnd = (new DateTime($lastWorkingDay))->format('Y-m-31');

                    ## working month er upore delete korte hobe, working month ta is_active 1 hobe. acc, pos, inv te month-date a month er first date porche
                    DB::table('inv_month_end')
                        ->where('branch_id', $req->branchId)
                        ->where('month_date', '>', $lastWorkingDayMonth)
                        ->delete();

                    $lastMonth = DB::table('inv_month_end')
                        ->where([
                            ['branch_id', $req->branchId],
                            ['month_date', '<=', $lastWorkingDayMonthEnd],
                            ['is_delete', 0]
                        ])
                        ->orderBy('month_date', 'DESC')
                        ->limit(1)
                        ->select('id')
                        ->first();

                    if ($lastMonth) {
                        DB::table('inv_month_end')
                            ->where('id', $lastMonth->id)
                            ->update(['is_active' => 1]);
                    }
                }

                DB::commit();

                return response()->json([
                    'alert-type' => 'success',
                    'message' => 'Day Ends and Month Ends deleted',
                ]);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                $notification = array(
                    'alert-type' => 'error',
                    'message'    => 'Something went wrong',
                );

                return response()->json($notification);
            }
        } else {
            return response()->json($validation);
        }
        //delete all the dayend in this date range
        return response()->json(['success' => true]);
    }

    public function validateInput(Request $req)
    {
        $fromDate = date('Y-m-d', strtotime($req->fromDate));

        $errorMsg = [];

        if ($req->branchId == '') {
            array_push($errorMsg, 'Brnach Must be selected');
        }
        if ($req->module == '') {
            array_push($errorMsg, 'Module must be selected');
        }

        if (count($errorMsg) > 0) {
            return [
                'alert-type' => 'error',
                // 'message' => array_join($errorMsg, ',\n'),
                'message' => join(',\n', $errorMsg),
            ];
        } else {
            return [
                'alert-type' => 'success',
            ];
        }
    }
}
