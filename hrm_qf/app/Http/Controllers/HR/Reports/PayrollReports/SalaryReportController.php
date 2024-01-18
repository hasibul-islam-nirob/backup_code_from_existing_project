<?php

namespace App\Http\Controllers\HR\Reports\PayrollReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use DateTime;

class SalaryReportController extends Controller
{
    public function index(){
        return view('HR.Reports.PayrollReports.SalaryReport.index');
    }


    public function loadSalary(Request $request){
        // ss($request->all());

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $project_id = (empty($request->project_id)) ? null : $request->project_id;
        $groups_id = (empty($request->groups_id)) ? null : $request->groups_id;
        $salary_month = (empty($request->salary_month)) ? null : $request->salary_month;
        $approved_by = (empty($request->approved_by)) ? null : $request->approved_by;
        $approved_date = (empty($request->approved_date)) ? null : $request->approved_date;
        $payment_date = (empty($request->payment_date)) ? null : $request->payment_date;
        $create_by = (empty($request->create_by)) ? null : $request->create_by;
        $status = (empty($request->status)) ? null : $request->status;


        $salaryData =  DB::table('hr_payroll_salary')->get();

        $allowanceInfo = DB::table('hr_payroll_allowance')->where([['is_active',1],['is_delete',0]])->get();

        $deductionDataArr = HRS::query_get_hr_payroll_deduction_data();

        // ss($salaryData);
        return view('HR.Reports.PayrollReports.SalaryReport.body', compact('salaryData','allowanceInfo','deductionDataArr'));

    }
}
