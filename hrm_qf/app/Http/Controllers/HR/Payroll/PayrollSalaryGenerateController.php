<?php

namespace App\Http\Controllers\HR\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Model\HR\PayrollSalaryGenerate;
use App\Services\RoleService as Role;

class PayrollSalaryGenerateController extends Controller
{
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'project_id' => 'required',
                // 'grade' => 'required',
                // 'level' => 'required',
                // 'basic' => 'required',
                // 'increment' => 'required',
                // 'total_inc' => 'required',
                // 'total_basic' => 'required',
            );

            $attributes = array(
                // 'project_id' => 'Project',
                // 'grade'     => 'Grade',
                // 'level'     => 'Level',
                // 'basic'     => 'Basic',
                // 'increment'     => 'Increment percentage',
                // 'total_inc'     => 'Total increment',
                // 'total_basic'     => 'Total basic',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    
    public function index(Request $request)
    {

        if ($request->isMethod('post')) {

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $search_arr = $request->get('search');
            $searchValue = $search_arr['value'];

            $allData  = DB::table('hr_payroll_salary as hps')
                    ->join('gnl_fiscal_year', 'hps.fiscal_year_id', '=', 'gnl_fiscal_year.id')
                    ->join('gnl_branchs', 'hps.branch_id', '=', 'gnl_branchs.id')
                    ->where('hps.is_delete', 0)
                    ->where('gnl_fiscal_year.is_delete', 0)
                    ->where('gnl_branchs.is_delete', 0)
                    ->select(
                        'hps.*',
                        'gnl_fiscal_year.fy_name as payscale_year_name',
                        'gnl_branchs.branch_name',
                        'gnl_branchs.branch_code',
                    );

            $totalRecordswithFilter = DB::table('hr_payroll_salary')->where('is_delete', 0)->count();
            $totalRecords = $totalRecordswithFilter;
            $tempQueryData = clone $allData;
            // if (!empty($payscale_id) || !empty($grade) || !empty($level) || !empty($recruitment_type_id) || !empty($searchValue) ) {
            //     $totalRecords = $tempQueryData->count();
            // }


            $allData = $allData->skip($start)->take($rowperpage)->get();
            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {
                
                $IgnoreArray = ['edit'];

                if(!empty($row->salary_month)){
                    $y = intval((date("Y", strtotime($row->salary_month))));
                    $m = (date("F", strtotime($row->salary_month)));
                }


                $data[$key]['id']           = $sno;
                $data[$key]['month_name']   = $m.'-'.$y;
                $data[$key]['pay_scale']    = $row->payscale_year_name;
                $data[$key]['branch']       = $row->branch_name.' ['.$row->branch_code.']';

                $data[$key]['company']      = !empty($row->company->comp_name) ? $row->company->comp_name : '-';
                $data[$key]['project']      = !empty($row->project->project_name) ? $row->project->project_name : '-';

                $data[$key]['approved_by']     = !empty($row->approved_by) ? $this->findUser($row->approved_by) : '-';
                $data[$key]['approved_date']   = !empty($row->approved_date) ? date('d-m-Y', strtotime($row->approved_date)) : '-';
                $data[$key]['payment_date']    = !empty($row->payment_date) ? date('d-m-Y', strtotime($row->payment_date)) : '-';

                $data[$key]['create_by']       = $this->findUser($row->create_by);
                $data[$key]['create_at']   = !empty($row->create_at) ? date('d-m-Y H:i:s', strtotime($row->create_at)) : '-';

                if($row->status == 0){
                    $status = 'Pending';
                }elseif($row->status == 0){
                    $status = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Payment</span>';
                }else{
                    $status = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Approved</span>';
                }
                $data[$key]['status']        = $status;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;

            }

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval( $totalRecordswithFilter),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data,
            );
            return response()->json($json_data);

        }
    }

    public function insert(Request $request){
        
        ## Variable Define Start
        $salaryMonth    = (empty($request->salary_month)) ? null : $request->salary_month;
        $companyId      = (empty($request->company_id)) ? null : $request->company_id;
        $branchId       = (empty($request->branch_id)) ? null : $request->branch_id;
        $projectId      = (empty($request->project_id)) ? null : $request->project_id;
        $fiscalYearId   = (empty($request->fiscal_year_id)) ? null : $request->fiscal_year_id;
        $approvedDate   = (empty($request->approved_date)) ? null : $request->approved_date;
        ## Variable Define End


        ## Salary Details Generate 
        $salaryDetails = $this->generateSalaryDetails($request);
        $salaryDetails = "[".json_encode($salaryDetails)."]";
        // ss($salaryDetails);
        ## Salary Details Generate 

        // ss($request->all());


        $passport = $this->getPassport($request, 'storex');
        if ($passport['isValid']) {

            try {
                DB::beginTransaction();


                $id = DB::table('hr_payroll_salary')->insertGetId([
                    'salary_month' => $salaryMonth,
                    'fiscal_year_id' => $fiscalYearId, 
                    'company_id' => $companyId, 
                    'project_id' => $projectId, 
                    'branch_id' => $branchId,
                    'voucher_generate' => 0, 
                    'create_by' => Auth::user()->id, 
                    'create_at' => date('Y-m-d H:i:s'), 
                    'is_delete' => 0, 
                    'salary_details' => $salaryDetails,
                ]);


                DB::commit();

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    ## Salary Details Generate Area Start
    public function generateSalaryDetails($request){
        ## Variable Define Start
        $salaryMonthDate    = (empty($request->salary_month)) ? null : $request->salary_month;
        $companyId      = (empty($request->company_id)) ? null : $request->company_id;
        $branchId       = (empty($request->branch_id)) ? null : $request->branch_id;
        $projectId      = (empty($request->project_id)) ? null : $request->project_id;
        $fiscalYearId = (empty($request->fiscal_year_id)) ? null : $request->fiscal_year_id;
        $approvedDate   = (empty($request->approved_date)) ? null : $request->approved_date;

        $avgMonthCalculate = false;
        ## Variable Define End

        // ss($request->all());

        ## Salary Details Generate Area Start
        if ('Salary_Details') {

            ## Months Data
            $monthName = date('F', strtotime($salaryMonthDate));
            ## Pay Scale Year Data
            $payscaleYearData = DB::table('gnl_fiscal_year')->where('id', $fiscalYearId)->first();
            $startDate = $payscaleYearData->fy_start_date;
            $endDate = $payscaleYearData->fy_end_date;

            $monthDateArrWithName  = $this->coustomPayscalArr($payscaleYearData);
            $monthDatesArrData = isset($monthDateArrWithName[$monthName]) ? $monthDateArrWithName[$monthName] : [];
            if ($avgMonthCalculate) {
                $total_working_days = 30;
            }else{
                $total_working_days = count($monthDatesArrData);
            }

            if('initial_info'){
                ## Holiday Query Start
                if ("get_Holidays_query") {
                    $monthDatesKeysArr = array_keys($monthDatesArrData);
                    $monthStartDate = reset($monthDatesKeysArr);
                    $monthEndDate = end($monthDatesKeysArr);
                    $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
                    $holidays = array_flip($holidays);
                }
                ## Holiday Query End
                
                ## Branch Array Query Start
                $selBranchArr = Common::getBranchIdsForAllSection(['branchId'=> $branchId]);
                ## Branch Array Query End

                ## Attendance Rules Query Start
                if ("get_attendance_rules_query") {
                    $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
                    $lateBypassDesignation = array_column($attendanceRules->toArray(), 'attendance_bypass');
                    if(count($lateBypassDesignation) > 1){
                        $lateBypassDesignation = explode(",", implode("", call_user_func_array('array_intersect', array_map('str_split', $lateBypassDesignation))));
                    }else{
                        $lateBypassDesignation = !empty($lateBypassDesignation) ? explode(",", $lateBypassDesignation[0]) : [];
                    }
                }
                ## Attendance Rules Query End

                ## Employee Query Start
                if ("get_employee_query") {
                    $statusArray = array_column($this->GlobalRole, 'set_status');
                    $employeeData = HRS::fnForGetEmployees([
                        'branchIds' => $selBranchArr,
                        // 'departmentId' => $departmentId,
                        // 'designationId' => $designationId,
                        // 'employeeId' => $employeeId,
                        // 'joinDateTo' => $monthEndDate,
                        'org_data' => true,
                        'ignoreDesignations' => $lateBypassDesignation,
                        'statusArray' => $statusArray,
                        'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
                        'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, gender, join_date, closing_date, is_active'
                    ]);
                    $employeeIdArr = $employeeData->pluck('id')->toArray();
                    $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
                    $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
                    $empJoinDate = $employeeData->pluck('join_date')->first();
                }
                ## Employee Query End

                ## Designation Query
                $designationData = HRS::fnForDesignationData($designationIdArr, 'name');
                ## Department Query
                $departmentData = HRS::fnForDepartmentData($departmentIdArr);

                ## Attendance Query Start
                if ("get_attendance_query") {
                    $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
                    $employeeAttendanceAllData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
                    // $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
                    // asort($attendanceDates);
                }
                ## Attendance Query End

                ## Movement Query Start
                if ("get_movement_query") {
                    $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
                    $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
                }
                ## Movement Query End

                ## Leave Query Start
                if ("get_leave_query") {
                    $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
                    $leaveArr = $leaveData->groupBy('emp_id')->toArray();
                    $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
                }
                ## Leave Query End
            }


            ################################
            $salaryDetailsArray = array();
            foreach($employeeData as $empKey => $row){
                $empId = $row->id;
                // ss($row);

                if ('emp_info') {
                   
                    if (empty($row->join_date) || $row->is_active == 0) {
                        continue;
                    }

                    $totalPresent = 0;
                    ## Attendance Data By Employee
                    $employeeAttendanceData = isset($employeeAttendanceAllData[$empId]) ? $employeeAttendanceAllData[$empId] : [] ;
                    ## Attendance Data By Employee

                    ## Movement Data By Employee
                    $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
                    $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
                    $empMovementData = array_merge($empMovementData, $allEmpMovementData);
                    ## Movement Data By Employee

                    ## Leave Data By Employee
                    $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
                    $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
                    $empLeaveData = array_merge($empLeaveData, $allLeaveData);
                    $leaveData = [];
                    if (count($empLeaveData) > 0) {
                        foreach ($empLeaveData as $rowLeave) {
                            $leaveStartDate = $rowLeave->date_from;
                            $leaveEndDate = $rowLeave->date_to;
                            $leaveCatId = $rowLeave->leave_cat_id;
        
                            $tempDate = $leaveStartDate;
        
                            if ($leaveStartDate == $leaveEndDate) {
                                array_push($leaveData, $leaveStartDate);
                            } else {
                                while (($tempDate <= $leaveEndDate)) {
                                    array_push($leaveData, $tempDate);
                                    $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                }
                            }
                        }

                        if (!empty($leaveData)) {
                            $leaveData = array_flip($leaveData);
                        }
                    }
                    ## Leave Data By Employee
                    $presentAbleArr = array_merge($employeeAttendanceData, $empMovementData, $leaveData, $holidays);
                    if(count($presentAbleArr) > 0){
                        $totalPresent = count($presentAbleArr);
                    }else{
                        $totalPresent = 0;
                    }


                    ## Emp Organization Details
                    $organizationDetails = HRS::getEmpOrganizationDetails($empId);
                    $grade = optional($organizationDetails)->grade;
                    $level = optional($organizationDetails)->level;
                    $payscaleId = optional($organizationDetails)->payscal_id;
                    $recruitmentId = optional($organizationDetails)->rec_type_id;
                    $step = optional($organizationDetails)->step;
                }

                $salaryDetails = HRS::genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step, true);
                // ss($salaryDetails);
                ## Benefit Start
                $allowance = !empty($salaryDetails['allowance']) ? $salaryDetails['allowance'] : [];
                $benefitArr = [];
                foreach ($allowance as $key => $value) {
                    $benSortForm = ['ba','bb','bc'];
                    $grossBenKey =  isset($benSortForm[$key-1]) ? "total_benefit_".$benSortForm[$key-1] : '';
                    $benefitGross = 0;
                    foreach($value as $key2 => $val2){
                        $customKey2 = "benefit-".$benSortForm[$key-1]."-".$key2;
                        $benefitArr[$customKey2] = $val2;
                        $benefitGross += $val2;
                    }
                    $benefitArr[$grossBenKey] = $benefitGross;
                    // ss($allowance, $key, $value);
                }
                ## Benefit End

                ## Self Deduction Start
                $selfDeductionInfo = !empty($salaryDetails['deduction']) ? $salaryDetails['deduction'] : [];
                $selfDeductionTotal = !empty($salaryDetails['totalDeduction']) ? $salaryDetails['totalDeduction'] : 0;
                ## Self Deduction End

                $basic_salary = !empty($salaryDetails['basic']) ? $salaryDetails['basic'] : 0;
                $gross_salary_ba = isset($benefitArr['total_benefit_ba']) ? $benefitArr['total_benefit_ba'] : 0;
                $gross_salary_bb = isset($benefitArr['total_benefit_bb']) ? $benefitArr['total_benefit_bb'] : 0;
                $gross_salary_bc = isset($benefitArr['total_benefit_bc']) ? $benefitArr['total_benefit_bc'] : 0;

                if('For_Salary_Base_Calculation'){
                    $wf_org = isset($salaryDetails['wf_org']) ? $salaryDetails['wf_org'] : 0;
                    $wf_self_non_refundable = isset($salaryDetails['wf_self_non_refundable']) ? $salaryDetails['wf_self_non_refundable'] : 0;
                    $wf_self_refundable = isset($salaryDetails['wf_self_refundable']) ? $salaryDetails['wf_self_refundable'] : 0;
    
                    $pf_org = isset($salaryDetails['pf_org']) ? $salaryDetails['pf_org'] : 0;
                    $pf_self = isset($salaryDetails['pf_self']) ? $salaryDetails['pf_self'] : 0;
                    $osf_org = isset($salaryDetails['osf_org']) ? $salaryDetails['osf_org'] : 0;
                    $osf_self = isset($salaryDetails['osf_self']) ? $salaryDetails['osf_self'] : 0;
                    $insurance_org = isset($salaryDetails['insurance_org']) ? $salaryDetails['insurance_org'] : 0;
                    $insurance_self = isset($salaryDetails['insurance_self']) ? $salaryDetails['insurance_self'] : 0;
                    $eps = isset($salaryDetails['eps']) ? $salaryDetails['eps'] : 0;
                }

                if('For_Application_Base_Calculation'){
                    // $acting_benefit = isset($benefitArr['acting_benefit']) ? $benefitArr['acting_benefit'] : 0;
                    $acting_benefit = 0;
                    // $arrear = isset($benefitArr['arrear']) ? $benefitArr['arrear'] : 0;
                    $arrear = 0;
                    // $security_money = isset($salaryDetails['security_money']) ? $salaryDetails['security_money'] : 0;
                    $security_money =  0;
                    // $vehicle_loan = isset($salaryDetails['vehicle_loan']) ? $salaryDetails['vehicle_loan'] : 0;
                    // $vehicle_loan = !empty($vehicle_loan) ? array_sum($vehicle_loan) : 0;
                    $vehicle_loan = 0;
                    // $advanced_salary = isset($salaryDetails['advanced_salary']) ? $salaryDetails['advanced_salary'] : 0;
                    $advanced_salary = 0;
                    // $income_tax = isset($salaryDetails['income_tax']) ? $salaryDetails['income_tax'] : 0;
                    $income_tax = 0;
                    // $pf_loan = isset($salaryDetails['pf_loan']) ? $salaryDetails['pf_loan'] : 0;
                    $pf_loan = 0;
                    // $others = isset($salaryDetails['others']) ? $salaryDetails['others'] : 0;
                    $others = 0;
                }

                // gross_total = gross_salary + benefit-tb-16 (if have) + benefit-c-30 (if have) + acting_benefit (if have) + arrear (if have)
                $gross_total = $gross_salary_bc + $gross_salary_bb + $gross_salary_ba + $basic_salary + $acting_benefit + $arrear;
                // "org_contribution_total" =>  0, // org_contribution_total = wf_org + pf_org + insurance_org + osf_org
                $org_contribution_total = ($pf_org + $wf_org + $insurance_org + $osf_org);
                // self_deduction_total = wf_self + wf_self_refundable + pf_self + insurance_self + osf_self + eps + security_money + advanced_salary + income_tax + pf_loan + vehicle_loan + others
                $self_deduction_total = ($wf_self_non_refundable + $wf_self_refundable + $pf_self + $insurance_self + $osf_self + $eps + $security_money + $vehicle_loan + $advanced_salary + $income_tax + $pf_loan + $others);
                // total_salary = gross_total + org_contribution_total
                $total_salary = ($gross_total + $org_contribution_total);
                // total_deductions = org_contribution_total + self_deduction_total
                $total_deductions = ($org_contribution_total + $self_deduction_total);
                // net_payable_salary = total_salary - total_deductions	
                $net_payable_salary = ($total_salary - $total_deductions);

                // ss($row, $salaryDetails, $benefitArr);
                ## Salary Information Details Array
                $salaryItem = [
                    "emp_id" => $empId,
                    "emp_name" => $row->emp_name . " [" . $row->emp_code . "]",
                    "emp_code" =>  $row->emp_code,
                    "branch_id" =>  $row->branch_id,

                    "grade" => $grade,
                    "level" => $level,
                    "step" => $step,
                    "gender" =>  $row->gender,
                    "join_date" =>  $row->join_date,
                    "closing_date" =>  $row->closing_date,

                    "designation_id" => $row->designation_id,
                    "designation_name" => (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "",
                    "department_id" => $row->department_id,
                    "department_name" => (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "",

                    "working_days" => $total_working_days,
                    "present_days" => $totalPresent, // Attendance + Movement + Holiday + Leave
                    "lwp_days" => $total_working_days - $totalPresent,

                    "basic_salary" => $basic_salary,
                    "benefit_info" => $benefitArr,
                    // "benefit-ta-1" => 0, // benefit-ta-1 = benefit - Type A - 1 (HA)
                    // "benefit-ta-4" => 0, // benefit-ta-4 = benefit - Type A - 4 (TA)
                    // "benefit-ta-6" => 0,
                    // "benefit-ta-7" => 0,
                    "gross_salary_ba" => $gross_salary_ba + $basic_salary, // gross_salary = sum of all benefit type A
                    // Addition part
                    // "benefit-tb-16" => 0,
                    "gross_salary_bb" => $gross_salary_bb + $gross_salary_ba + $basic_salary, // gross_salary_ba = sum of all benefit type A and B

                    // "benefit-c-30" => 0,
                    "gross_salary_bc" => $gross_salary_bc + $gross_salary_bb + $gross_salary_ba + $basic_salary, // gross_salary_ba = sum of all benefit type A and B and C

                    "acting_benefit" => $acting_benefit, // Application Base
                    "arrear" => $arrear, // Application Base

                    "gross_total" => $gross_total, // gross_total = gross_salary + benefit-tb-16 (if have) + benefit-c-30 (if have) + acting_benefit (if have) + arrear (if have)

                    // Deduction part
                    "wf_org" => $wf_org, // Salary Base
                    "wf_self_non_refundable" => $wf_self_non_refundable, // Salary Base
                    "wf_self_refundable" => $wf_self_refundable, // Salary Base

                    "pf_org" => $pf_org, // Salary Base
                    "pf_self" => $pf_self, // Salary Base

                    "insurance_org" => $insurance_org, // Salary Base
                    "insurance_self" => $insurance_self, // Salary Base

                    "osf_org" => $osf_org, // Salary Base
                    "osf_self" => $osf_self, // Salary Base

                    "eps" =>  $eps, // Salary Base
                    
                    "security_money" => $security_money, // Application Base
                    "advanced_salary" => $advanced_salary, // Application Base
                    "income_tax" => $income_tax, // Application Base

                    "pf_loan" => $pf_loan, // Application Base
                    "vehicle_loan" => $vehicle_loan, // Application Base
                    
                    "others" => $others,

                    "org_contribution_total" =>  $org_contribution_total, // org_contribution_total = wf_org + pf_org + insurance_org + osf_org
                    // 'self_deduction_info' => $selfDeductionInfo,
                    "self_deduction_total" =>  $self_deduction_total, // self_deduction_total = wf_self + wf_self_refundable + pf_self + insurance_self + osf_self + eps + security_money + advanced_salary + income_tax + pf_loan + vehicle_loan + others

                    "total_salary" => $total_salary, // total_salary = gross_total + org_contribution_total
                    "total_deductions" => $total_deductions, // total_deductions = org_contribution_total + self_deduction_total

                    "net_payable_salary" => $net_payable_salary, // net_payable_salary = total_salary - total_deductions	
                ];

                $salaryDetailsArray[$empId] = $salaryItem;
                // ss($salaryDetails, $salaryDetailsArray);


                ##================ Start For Deposit =========================
                $deposit_date = (new DateTime())->format('Y-m-d');
                $created_at = (new DateTime())->format('Y-m-d H:i:s');
                $created_by = Auth::user()->id;
                $branch_id = $row->branch_id;
                if('For_Deposit_'){
                    DB::table('hr_payroll_deposit_wf')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'org_amount' => $wf_org,
                        'self_amount' => $wf_self_non_refundable,
                        'self_refundable_amount' => $wf_self_refundable,
                        'total_amount' => $wf_org + $wf_self_non_refundable + $wf_self_refundable,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    DB::table('hr_payroll_deposit_pf')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'org_amount' => $pf_org,
                        'self_amount' => $pf_self,
                        'total_amount' => $pf_org + $pf_self,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    DB::table('hr_payroll_deposit_osf')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'org_amount' => $osf_org,
                        'self_amount' => $osf_self,
                        'total_amount' => $osf_org + $osf_self,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    DB::table('hr_payroll_deposit_insurance')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'org_amount' => $insurance_org,
                        'self_amount' => $insurance_self,
                        'total_amount' => $insurance_org + $insurance_self,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    DB::table('hr_payroll_deposit_eps')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'self_amount' => $eps,
                        'total_amount' => $eps,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    DB::table('hr_payroll_deposit_security_money')->insert([
                        'emp_id' => $empId,
                        'deposit_date' => $deposit_date,
                        'deposit_month' => $salaryMonthDate,
                        'deposit_for_month' => $salaryMonthDate,
                        'self_amount' => $security_money,
                        'total_amount' => $security_money,
                        'deposit_branch_id' => $branch_id,
                        'created_by' => $created_by,
                        'created_at' => $created_at,
                    ]);
                    
                }
                ##================  End  For Deposit =========================

            }
            // $salaryDetailsArray = json_encode($salaryDetailsArray);
            // ss($salaryDetailsArray);

            return $salaryDetailsArray;
            ################################

        }
        ## Salary Details Generate Area End
    }
    ## Salary Details Generate Area End

    public function delete($id)
    {
        try{
            DB::beginTransaction();
            $delete = DB::table('hr_payroll_salary')->where('id', decrypt($id))->update(['is_delete' => 1]);
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
        }


        if ($delete) {
            return response()->json([
                'message'    => 'Successfully deleted',
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 500,
                'status' => 'error',
                'message'    => 'Failed to delete',
                'result_data' => ''
            ], 500);
        }
    }
    

    public function get($id){
        $salaryData =  DB::table('hr_payroll_salary')->where('id', decrypt($id))->first();

        return response()->json($salaryData);
    }

    ## Find Created By Or Approved BY Person
    public function findUser($findUserId){
        $createByInfoData = DB::table('gnl_sys_users')->where('id', $findUserId)->first();
        $createByInfo = '';
        $emp_id = optional($createByInfoData)->emp_id;
        if ($emp_id == null) {
            $createByInfo = optional($createByInfoData)->full_name;
        }else{
            $createdByEmpInfo = DB::table('hr_employees')->where('id', $emp_id)->first();
            $empName = optional($createdByEmpInfo)->emp_name;
            $empCode = optional($createdByEmpInfo)->emp_code;

            $createByInfo = $empName.' ['.$empCode.']';
        }

        return $createByInfo;
    }
    ## Find Created By Person


    public function coustomPayscalArr($val1){
        $startDate = new DateTime($val1->fy_start_date);
        $endDate = new DateTime($val1->fy_end_date);

        return HRS::getMonthNameDatesDays($startDate, $endDate);
    }
}
