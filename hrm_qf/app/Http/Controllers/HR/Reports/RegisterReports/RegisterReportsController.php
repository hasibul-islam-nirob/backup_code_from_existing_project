<?php
namespace App\Http\Controllers\HR\Reports\RegisterReports;

use Illuminate\Support\Facades\DB;
use DateTime;
use App\Model\HR\Employee;
use Illuminate\Http\Request;
use App\Model\HR\EmployeeLeave;
use App\Model\HR\EmployeeResign;
use App\Model\HR\EmployeeDismiss;
use App\Model\HR\EmployeeDemotion;
use App\Model\HR\EmployeeTransfer;
use App\Model\HR\EmployeePromotion;
use App\Model\HR\EmployeeTerminate;
use App\Http\Controllers\Controller;
use App\Model\HR\EmployeeRetirement;
use Illuminate\Support\Facades\Auth;
use App\Services\CommonService as Common;
use App\Model\HR\EmployeeContractConclude;
use App\Model\HR\EmployeeActiveResponsibility;
use App\Http\Controllers\HR\Applications\EmployeeLeaveController;
use App\Model\HR\EmployeeMovement;
use App\Services\HrService as HRS;
class RegisterReportsController extends Controller{

    public function getEmpResign(){
        return view('HR.Reports.RegisterReports.EmpResign.show');
    }

    public function getEmpLeave(){
        return view('HR.Reports.RegisterReports.EmpLeave.show');
    }

    public function getEmpMovement(){
        return view('HR.Reports.RegisterReports.EmpMovement.show');
    }

    public function getEmpTransfer(){
        return view('HR.Reports.RegisterReports.EmpTransfer.show');
    }

    public function getEmpTerminate(){
        return view('HR.Reports.RegisterReports.EmpTerminate.show');
    }

    public function getEmpPromotion(){
        return view('HR.Reports.RegisterReports.EmpPromotion.show');
    }

    public function getEmpDemotion(){
        return view('HR.Reports.RegisterReports.EmpDemotion.show');
    }

    public function getEmpDismiss(){
        return view('HR.Reports.RegisterReports.EmpDismiss.show');
    }

    public function getEmpRetirement(){
        return view('HR.Reports.RegisterReports.EmpRetirement.show');
    }

    public function getEmpIncrement(){
        return view('HR.Reports.RegisterReports.EmpIncrement.show');
    }

    public function getEmpIncrementHeld(){
        return view('HR.Reports.RegisterReports.EmpIncrementHeld.show');
    }

    public function getActiveResponsibility(){
        return view('HR.Reports.RegisterReports.ActiveResponsibility.show');
    }

    public function getActiveResponsibilityExtend(){
        return view('HR.Reports.RegisterReports.ActiveResponsibilityExtend.show');
    }

    public function getEmpContractConclude(){
        return view('HR.Reports.RegisterReports.EmpContractConclude.show');
    }

    public function getEmpContractExtend(){
        return view('HR.Reports.RegisterReports.EmpContractExtend.show');
    }

    // #####Load Table

    public function loadEmpResign(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        /**
         * inner join use karone jesob employee er designation & department select kora thakbe na tader data load hobe na.
         */

        $allData    = DB::table('hr_app_resigns AS apl')
                    ->join('hr_employees AS em','apl.emp_id', '=', 'em.id')
                    ->where('apl.is_delete','=',0)
                    ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                        if (!empty($selBranchArr)) {
                            $query->whereIn('apl.branch_id', $selBranchArr);
                        }
                        if (!empty($request->designation_id)) {
                            $query->where('em.designation_id', $request->designation_id);
                        }
                        if (!empty($request->department_id)) {
                            $query->where('em.department_id', $request->department_id);
                        }
                        if(!empty($request->emp_gender)){
                            $query->where('em.gender', $request->emp_gender);
                        }
                        if(!empty($request->employee_id)){
                            $query->where('apl.emp_id', $request->employee_id);
                        }
                        if (!empty($request->resign_code)) {
                            $query->where('apl.resign_code', 'LIKE', "%{$request->resign_code}%");
                        }
                        if ($request->appl_status == "0" || !empty($request->appl_status)) {
                            $query->where('apl.is_active', $request->appl_status);
                        }
                        // dd($request->all());
                        if (!empty($request->startDate) && !empty($request->endDate)) {
                            $query->whereBetween('apl.resign_date', [(new DateTime($request->startDate))->format('Y-m-d'),
                            (new DateTime($request->endDate))->format('Y-m-d')]);
                        } elseif (!empty($request->startDate)) {
                            $query->where('apl.resign_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));

                        } elseif (!empty($request->endDate)) {
                            $query->where('apl.resign_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                        }

                    })
                    ->select('apl.*','em.emp_name','em.department_id','em.designation_id','em.id','em.emp_code','em.gender')
                    ->orderBy('apl.resign_date', 'ASC')
                    ->orderBy('em.emp_code', 'ASC')
                    ->get();


            $designationArr = array_values($allData->pluck('designation_id')->unique()->toArray());
            $deptArr = array_values($allData->pluck('department_id')->unique()->toArray());
            $branchArr = array_values($allData->pluck('branch_id')->unique()->toArray());

            $designationData = HRS::fnForDesignationData($designationArr);
            $deptData = HRS::fnForDepartmentData($deptArr);
            $branchData = Common::fnForBranchData($selBranchArr);


            $data = array();
            $sno  = 1;
            $male = 0;
            $female = 0;
            foreach ($allData as $key => $row) {


                if(strtolower($row->gender) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }


                $branchName = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
                $designationName = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
                $deptName = (isset($deptData[$row->department_id])) ? $deptData[$row->department_id] : "";

                $data[$key]['id']                 = $sno;
                $data[$key]['resign_code']        = $row->resign_code;
                $data[$key]['reason']             = $row->reason;
                $data[$key]['employee_name']      = $row->emp_name . " [ " . $row->emp_code . " ]";
                $data[$key]['dept_name']          = $deptName;
                $data[$key]['desig_name']         = $designationName;
                $data[$key]['gender']             = $row->gender;
                $data[$key]['branch']             = $branchName;
                $data[$key]['resign_date']        = (new DateTime($row->resign_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }


        return view('HR.Reports.RegisterReports.EmpResign.body', compact('data','male','female'));

    }

    public function loadEmpLeave(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $startDate = isset($request['startDate']) ? date('Y-m-d', strtotime($request['startDate'])) : null;
        $endDate = isset($request['endDate']) ? date('Y-m-d', strtotime($request['endDate'])) : null;

        // ss($request->all());

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);
        // ss($employeeArray, 0);
        ## Get Employee Permission Wise Array End

            $allData  = EmployeeLeave::from('hr_app_leaves AS apl')
                ->join('hr_leave_category', 'hr_leave_category.id', 'apl.leave_cat_id')
                ->where('apl.is_delete', 0)

                // ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request) {
                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);
                        }, $chunkedValues);

                    }else{
                        $perQuery->orWhereIn('apl.emp_id', $employeeArray);
                    }
                    $perQuery->orWhere('apl.emp_id', 0);
                })

                ->when(true, function ($query) use ($request,$selBranchArr, $startDate, $endDate) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }

                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }

                    if (!empty($request->leave_cat_id)) {
                        $r = $query->join('hr_leave_category as hlc', function ($join) use ($request) {
                            $join->on('apl.leave_cat_id', '=', 'hlc.id')
                            ->where('hlc.id', $request->leave_cat_id);
                        });
                    }

                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', 0);
                        $query->orWhere('apl.emp_id', $request->employee_id);
                        
                    }
                    if (!empty($request->leave_code)) {
                        $query->where('apl.leave_code', 'LIKE', "%{$request->leave_code}%");
                    }

                    $status = $request->status;
                    if (!empty($status)) {
                        $query->where('apl.is_active', $status);
                    }

                    if (!empty($startDate) && !empty($endDate)) {
                        $query->whereBetween('apl.leave_date', [$startDate, $endDate]);
                    }
                    if (!empty($startDate) && empty($endDate)) {
                        $query->where('apl.leave_date', '>=', $startDate);
                    }
                    if (!empty($endDate) && empty($startDate)) {
                        $query->where('apl.leave_date', '<=', $endDate);
                    }


                })
                ->select('hr_leave_category.name AS leave_name','apl.*')
                ->orderBy('leave_date','desc')->get();
                // ss($request->all());
            $data = array();
            $sno  = 1;
            $male =0;
            $female =0;
            foreach ($allData as $key => $row) {
                if(strtolower($row->employee['gender']) == 'male'){
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']               = $sno;
                $data[$key]['leave_code']       = $row->leave_code;
                $data[$key]['employee_name']    =  !empty($row->employee['emp_name']) ? $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]" : 'All Employes';
                $data[$key]['gender']           = $row->employee['gender'];
                $data[$key]['branch']           = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['leave_date']       = (new DateTime($row->leave_date))->format('d-m-Y');
                $data[$key]['date_from']        = (new DateTime($row->date_from))->format('d-m-Y');
                $data[$key]['date_to']          = (new DateTime($row->date_to))->format('d-m-Y');
                $data[$key]['leave_cat']        = $row->leave_name;

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f"></i>Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }
            $data = !empty($data) ? $data : [];

        return view('HR.Reports.RegisterReports.EmpLeave.body', compact('data','male','female'));
    }

    public function loadEmpMovement(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $branchTo = (empty($request->branch_to_id)) ? null : $request->branch_to_id;
        $movement_area = (empty($request->se_movement_area)) ? null : $request->se_movement_area;
        $startDate = isset($request['startDate']) ? date('Y-m-d', strtotime($request['startDate'])) : null;
        $endDate = isset($request['endDate']) ? date('Y-m-d', strtotime($request['endDate'])) : null;
        // dd($startDate, $endDate);
        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        
        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request, "amp");
        // array_push($employeeArray, 0);
        // dd($employeeArray);
        ## Get Employee Permission Wise Array End

    
            $allData  = EmployeeMovement::from('hr_app_movements AS apm')
                ->where('apm.is_delete', 0)
                ->whereIn('apm.branch_id', $selBranchArr)

                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apm.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apm.emp_id', $employeeArray);
                    }
                    $perQuery->orWhere('apm.emp_id', 0);
                })
                // ->where(function ($query) use ($startDate, $endDate) {
                //     if (!empty($startDate) && !empty($endDate)) {
                //         $query->whereIn('apm.movement_date', [$startDate, $endDate]);
                //     }
                //     if (!empty($startDate) && empty($endDate)) {
                //         $query->where('apm.movement_date', '>=', $startDate);
                //     }elseif (empty($startDate) && !empty($endDate)) {
                //         $query->where('apm.movement_date', '<=', $endDate);
                //     }
                // })
                ->where(function ($query) use ($startDate, $endDate) {
                    if (!empty($startDate) && !empty($endDate)) {
                        $query->whereBetween('apm.movement_date', [$startDate, $endDate]);
                    } elseif (!empty($startDate)) {
                        $query->where('apm.movement_date', '>=', $startDate);
                    } elseif (!empty($endDate)) {
                        $query->where('apm.movement_date', '<=', $endDate);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apm.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apm.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apm.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apm.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    
                })
                ->where(function ($query3) use ($movement_area, $branchTo, $request) {

                    if(!empty($branchTo) && !empty($movement_area)){
                        $query3->where('apm.location_to', 'like', '%' . $movement_area . '%');
                        $query3->where('apm.location_to_branch', '=', $branchTo);
                    }
                    elseif( empty($branchTo) && !empty($movement_area)){
                        $query3->where('apm.location_to', 'like', '%' . $movement_area . '%');
                    }


                    if(!empty($request->employee_id)){
                        $query3->where('apm.emp_id', $request->employee_id);
                        $query3->orWhere('apm.emp_id', 0);

                    }
                    if (!empty($request->appm_code)) {
                        $query3->where('apm.movement_code', 'LIKE', "%{$request->appm_code}%");
                    }

                    $status = $request->status;
                    if (!empty($status)) {
                        $query3->where('apm.is_active', $status);
                    }

                })
                ->orderBy('apm.movement_date','desc')
                ->select('apm.*')->get();
                
                // dd($allData);
            // $data = array();
            $branchToAfterQuery = array_filter($allData->pluck("location_to_branch")->unique()->toArray());

            $branchData = Common::getBranchIdsForAllSection([
                'branchArr' => $branchToAfterQuery,
                'fnReturn' => 'array2D'
            ]);
            $sno  = 1;
            $male =0;
            $female =0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male'){
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $reasonData = '';
                if( !empty($row->reason) && $row->reason =='official' ){
                    $reasonData = 'Official';
                }elseif( !empty($row->reason) && $row->reason =='personal' ){
                    $reasonData = 'Personal';
                }else{
                    $reasonData = 'Others';
                }

                $data[$key]['id']               = $sno;
                $data[$key]['movement_code']    = $row->movement_code;
                $data[$key]['employee_name']    = !empty($row->employee['emp_name']) ? $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]" : 'All Employees';
                $data[$key]['gender']           = $row->employee['gender'];
                $data[$key]['branch']           = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['appl_date']       = (new DateTime($row->appl_date))->format('d-m-Y');
                $data[$key]['movement_date']       = (new DateTime($row->movement_date))->format('d-m-Y');
                // $data[$key]['start_time']        = (new DateTime($row->start_time))->format('h:ia');
                $data[$key]['start_time']        = date('h:ia', strtotime($row->start_time));
                $data[$key]['end_time']          = date('h:ia', strtotime($row->end_time));
                if (isset($branchData[$row->location_to_branch])) {
                    $data[$key]['location_to_branch'] = $branchData[$row->location_to_branch]  . " - ". $row->location_to;
                } else {
                    $data[$key]['location_to_branch'] = $row->location_to;
                }
                $data[$key]['reason']          = $reasonData;
                // $data[$key]['application_for'] = !empty($row->application_for) ? $row->application_for : 'others';

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f"></i>Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }
            $data = !empty($data) ? $data : [];
        return view('HR.Reports.RegisterReports.EmpMovement.body', compact('data','male','female'));
    }

    public function loadEmpTransfer(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeTransfer::from('hr_app_transfers as apl')
                ->where('apl.is_delete', 0)
                // ->whereIn('apl.branch_id', $selBranchArr)

                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if(!empty($request->branch_from)){
                        $query->where('apl.branch_id', $request->branch_from);
                    }
                    if (!empty($request->se_transfer_code)) {
                        $query->where('apl.transfer_code', 'LIKE', "%{$request->se_transfer_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->start_date) && !empty($request->end_date)) {
                        $query->whereBetween('apl.transfer_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    } elseif (!empty($request->start_date)) {
                        $query->where('apl.transfer_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    } elseif (!empty($request->end_date)) {
                        $query->where('apl.transfer_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }

                })
                ->select('apl.*')->get();

        $data = array();
        $sno  = 1;
        $male =0;
        $female =0;
        foreach ($allData as $key => $row) {

            if(isset($row->employee['gender']) && strtolower($row->employee['gender']) == 'male')
            {
                $male = $male +1;
            }else{
                $female = $female + 1;
            }

            $data[$key]['id']                 = $sno;
            $data[$key]['transfer_code']      = $row->transfer_code;
            $data[$key]['branch_from']        = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
            $data[$key]['branch_to']          = $row->branch_to['branch_name'] . " [" . $row->branch_to['branch_code'] . "]" ;
            $data[$key]['gender']             = $row->employee['gender'];
            $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
            $data[$key]['transfer_date']      = (new DateTime($row->transfer_date))->format('d-m-Y');
            $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
            $data[$key]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');

            $statusFlag = "<span>Draft</span>";

            if ($row->is_active == 1) {
                $statusFlag = '<span style="color: #0cf041">Approved</span>';
            }

            if ($row->is_active == 2) {
                $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
            }

            if ($row->is_active == 3) {
                $statusFlag = '<span style="color: #0c10f0">Processing</span>';
            }

            $data[$key]['status'] = $statusFlag;
            $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpTransfer.body', compact('data','male','female'));

    }

    public function loadEmpTerminate(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeTerminate::from('hr_app_terminates AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_terminate_code)) {
                        $query->where('apl.terminate_code', 'LIKE', "%{$request->se_terminate_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }

                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.terminate_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.terminate_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.terminate_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;
            $male =0;
            $female=0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['terminate_code']     = $row->terminate_code;
                $data[$key]['reason']             = $row->reason;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['gender']             = $row->employee['gender'];
                $data[$key]['terminate_date']     = (new DateTime($row->terminate_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpTerminate.body', compact('data','male','female'));

    }

    public function loadEmpPromotion(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeePromotion::from('hr_app_promotions AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_promotion_code)) {
                        $query->where('apl.promotion_code', 'LIKE', "%{$request->se_promotion_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }

                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.promotion_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.promotion_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.promotion_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;
            $male = 0;
            $female= 0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['promotion_code']     = $row->promotion_code;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['gender']             = $row->employee['gender'];
                $data[$key]['current_designation'] = $row->current_designation['name'];
                $data[$key]['designation_to_promote'] = $row->designation_to_promote['name'];
                $data[$key]['promotion_date']      = (new DateTime($row->promotion_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date']     = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpPromotion.body', compact('data','male','female'));

    }

    public function loadEmpDemotion(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeDemotion::from('hr_app_demotions AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_demotion_code)) {
                        $query->where('apl.demotion_code', 'LIKE', "%{$request->se_demotion_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }

                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.demotion_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.demotion_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.demotion_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;
            $male =0;
            $female =0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['demotion_code']     = $row->demotion_code;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['gender']             = $row->employee['gender'];
                $data[$key]['current_designation'] = $row->current_designation['name'];
                $data[$key]['designation_to_demote'] = $row->designation_to_demote['name'];
                $data[$key]['demotion_date']      = (new DateTime($row->demotion_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date']     = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }



        return view('HR.Reports.RegisterReports.EmpDemotion.body', compact('data','male','female'));

    }

    public function loadEmpDismiss(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeDismiss::from('hr_app_dismisses AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_dismiss_code)) {
                        $query->where('apl.dismiss_code', 'LIKE', "%{$request->se_dismiss_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.dismiss_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.dismiss_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.dismiss_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;
            $male =0;
            $female=0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['dismiss_code']       = $row->dismiss_code;
                $data[$key]['reason']             = $row->reason;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['gender']             = $row->employee['gender'];
                $data[$key]['dismiss_date']       = (new DateTime($row->dismiss_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date'] = (new DateTime($row->effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpDismiss.body', compact('data','male','female'));

    }

    public function loadEmpRetirement(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeRetirement::from('hr_app_retirements AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if (!empty($request->emp_gender)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.gender', $request->emp_gender);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_retirement_code)) {
                        $query->where('apl.retirement_code', 'LIKE', "%{$request->se_retirement_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.retirement_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.retirement_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.retirement_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;
            $male =0;
            $female =0;
            foreach ($allData as $key => $row) {

                if(strtolower($row->employee['gender']) == 'male')
                {
                    $male = $male +1;
                }else{
                    $female = $female + 1;
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['retirement_code']    = $row->retirement_code;
                $data[$key]['reason']             = $row->reason;
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['gender']             = $row->employee['gender'];
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['retirement_date']    = (new DateTime($row->retirement_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpRetirement.body', compact('data','male','female'));
    }

    public function loadEmpIncrement(Request $request){


                // return '<div class="row pt-10 text-center">
                //             <div class="col-lg-12">
                //                 <h3>Under Construction !!</h3>
                //                 <p>Please contact with support team.</p>
                //             </div>
                //         </div>';

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeResign::from('hr_app_resigns AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->resign_code)) {
                        $query->where('apl.resign_code', 'LIKE', "%{$request->resign_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.resign_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.resign_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.resign_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

                $data[$key]['id']                 = $sno;
                $data[$key]['resign_code']        = $row->resign_code;
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['resign_date']        = (new DateTime($row->resign_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        // return view('HR.Reports.RegisterReports.EmpIncrement.body', compact('data'));
        return view('HR.Reports.RegisterReports.EmpIncrement.body', compact('data'));

    }

    public function loadEmpIncrementHeld(Request $request){

        // return '<div class="row pt-10 text-center">
        //             <div class="col-lg-12">
        //                 <h3>Under Construction !!</h3>
        //                 <p>Please contact with support team.</p>
        //             </div>
        //         </div>';

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeResign::from('hr_app_resigns AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->contract_conclude_code)) {
                        $query->where('apl.contract_conclude_code', 'LIKE', "%{$request->contract_conclude_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.contract_conclude_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.contract_conclude_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.contract_conclude_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

                $data[$key]['id']                     = $sno;
                $data[$key]['contract_conclude_code'] = $row->contract_conclude_code;
                $data[$key]['employee_name']          = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['branch']                 = $row->branch['branch_name'] . "[" . $row->branch['branch_code'] . "]";
                $data[$key]['contract_conclude_date'] = (new DateTime($row->contract_conclude_date))->format('d-m-Y');
                $data[$key]['effective_date']         = (new DateTime($row->effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        // return view('HR.Reports.RegisterReports.EmpResign.body', compact('data'));
        return view('HR.Reports.RegisterReports.EmpIncrementHeld.body', compact('data'));

    }

    public function loadActiveResponsibility(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeActiveResponsibility::from('hr_app_active_responsibilities AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_active_responsibility_code)) {
                        $query->where('apl.active_responsibility_code', 'LIKE', "%{$request->se_active_responsibility_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.active_responsibility_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.active_responsibility_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.active_responsibility_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

               $data[$key]['id']                         = $sno;
               $data[$key]['active_responsibility_code'] = $row->active_responsibility_code;
               $data[$key]['branch']                     = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
               $data[$key]['employee_name']              = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
               $data[$key]['current_designation']        = $row->current_designation['name'];
               $data[$key]['designation_to_promote']     = $row->designation_to_promote['name'];
               $data[$key]['active_responsibility_date'] = (new DateTime($row->active_responsibility_date))->format('d-m-Y');
               $data[$key]['effective_date']             = (new DateTime($row->effective_date))->format('d-m-Y');
               $data[$key]['exp_effective_date']         = (new DateTime($row->exp_effective_date))->format('d-m-Y');
               $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.ActiveResponsibility.body', compact('data'));

    }

    public function loadEmpContractConclude(Request $request){

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeContractConclude::from('hr_app_contract_concludes AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching

                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->se_contract_conclude_code)) {
                        $query->where('apl.contract_conclude_code', 'LIKE', "%{$request->se_contract_conclude_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.contract_conclude_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.contract_conclude_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.contract_conclude_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

                $data[$key]['id']                      = $sno;
                $data[$key]['contract_conclude_code']  = $row->contract_conclude_code;
                $data[$key]['reason']                  = $row->reason;
                $data[$key]['employee_name']           = $row->employee['emp_name'] . " (" . $row->employee['emp_code'] . ")";
                $data[$key]['branch']                  = $row->branch['branch_name'] . " (" . $row->branch['branch_code'] . ")";
                $data[$key]['contract_conclude_date']  = (new DateTime($row->contract_conclude_date))->format('d-m-Y');
                $data[$key]['effective_date']          = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['exp_effective_date']      = (new DateTime($row->exp_effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        return view('HR.Reports.RegisterReports.EmpContractConclude.body', compact('data'));

    }

    public function loadEmpContractExtend(Request $request){

        // return '<div class="row pt-10 text-center">
        //             <div class="col-lg-12">
        //                 <h3>Under Construction !!</h3>
        //                 <p>Please contact with support team.</p>
        //             </div>
        //         </div>';

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        ## Get Employee ID Permission Wise Array Start
        $employeeArray = $this->GetEmployeeIdPermissionWiseArray($selBranchArr, $request);

        $allData  = EmployeeResign::from('hr_app_resigns AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function ($perQuery) use ($employeeArray, $request, $selBranchArr) {

                    if(!empty($employeeArray) && count($employeeArray) > 800 ){

                        $chunkedValues = array_chunk($employeeArray, 800);
                        array_map(function ($chankValue) use ($perQuery) {
                            $perQuery->orWhereIn('apl.emp_id', $chankValue);

                        }, $chunkedValues);

                    }else{
                        $perQuery->whereIn('apl.emp_id', $employeeArray);
                    }
                })
                ->when(true, function ($query) use ($request,$selBranchArr) { //Searching
                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->contract_conclude_code)) {
                        $query->where('apl.contract_conclude_code', 'LIKE', "%{$request->contract_conclude_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->startDate) && !empty($request->endDate)) {
                        $query->whereBetween('apl.contract_conclude_date', [(new DateTime($request->startDate))->format('Y-m-d'), (new DateTime($request->endDate))->format('Y-m-d')]);
                    } elseif (!empty($request->startDate)) {
                        $query->where('apl.contract_conclude_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                    } elseif (!empty($request->endDate)) {
                        $query->where('apl.contract_conclude_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                    }

                })->select('apl.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

                $data[$key]['id']                      = $sno;
                $data[$key]['contract_conclude_code']  = $row->contract_conclude_code;
                $data[$key]['employee_name']           = $row->employee['emp_name'] . " (" . $row->employee['emp_code'] . ")";
                $data[$key]['branch']                  = $row->branch['branch_name'] . " (" . $row->branch['branch_code'] . ")";
                $data[$key]['contract_conclude_date']  = (new DateTime($row->contract_conclude_date))->format('d-m-Y');
                $data[$key]['effective_date']          = (new DateTime($row->effective_date))->format('d-m-Y');

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f">Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0">Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
                $sno++;
            }

        // return view('HR.Reports.RegisterReports.EmpResign.body', compact('data'));
        return view('HR.Reports.RegisterReports.EmpContractExtend.body', compact('data'));

    }


    ## Get Employee ID Permission Wise Array Start
    public function GetEmployeeIdPermissionWiseArray($selBranchArr, $request, $alies){
        $statusArray = array_column($this->GlobalRole, 'set_status');
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $request->department_id,
            'designationId' => $request->designation_id,
            'employeeId' => $request->employee_id,
            'fromDate' => $request->start_date,
            'alies'    => $alies,
            // 'ignoreDesignations' => $attendanceBypassDesignation,
            'statusArray' => $statusArray,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date, closing_date'
        ]);
        return $employeeData->pluck('id')->toArray();
    }
    ## Get Employee Permission Wise Array End

}
