<?php

namespace App\Http\Controllers\HR\Reports\StuffReports;

use DateTime;
use App\Model\HR\Employee;
use Illuminate\Http\Request;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Services\CommonService as Common;


class StuffReportController extends Controller
{

    public function getEmployeeReport()
    {
        return view('HR.Reports.StuffReports.EmployeeReports.employee');
    }

    public function loadEmployeeReport(Request $request){


        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $districtId = (empty($request->district_id)) ? null : $request->district_id;
        $upazilaId = (empty($request->upazila_id)) ? null : $request->upazila_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $dateOfBirth = (empty($request->d_o_b)) ? null : (new DateTime($request->d_o_b))->format('Y-m-d');
        $joiningDate = (empty($request->joiningDate)) ? null : date('Y-m-d', strtotime($request->joiningDate));

        // ss($request->all(), $dateOfBirth, $joiningDate);


        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        $allData = Employee::from('hr_employees as emp')

                ->where('emp.is_delete', 0)
                ->when(true, function ($query) use ($request, $selBranchArr, $districtId, $upazilaId, $employeeId, $dateOfBirth, $joiningDate) {

                    if (!empty($employeeId)) {
                        $query->where('emp.id', $employeeId);
                    }

                    if (!empty($joiningDate)) {
                        $query->where('emp.join_date', $joiningDate);
                    }

                    if (!empty($selBranchArr)) {
                        $query->whereIn('emp.branch_id', $selBranchArr);
                    }

                    if (!empty($request->designation_id)) {

                        $query->where('emp.designation_id', $request->designation_id);

                    }
                    if (!empty($request->emp_marital_status) || !empty($request->emp_religion)) {

                        $query->join('hr_emp_personal_details as pd', function($join){
                            $join->on('emp.id', '=', 'pd.emp_id');
                        });

                    }

                    if (!empty($request->emp_gender)) {

                        $query->where('emp.gender', $request->emp_gender);

                    }
                    if (!empty($request->emp_marital_status)) {

                        $query->where('pd.marital_status', $request->emp_marital_status);

                    }
                    if (!empty($request->emp_religion)) {

                        $query->where('pd.religion', $request->emp_religion);

                    }

                    if (!empty($request->department_id)) {

                        $query->where('emp.department_id', $request->department_id);

                    }

                    if (!empty($request->emp_code)) {

                        $query->where('emp.emp_code', 'LIKE', "%{$request->emp_code}%");

                    }

                    if ($request->emp_status == "0" || !empty($request->emp_status)) {

                        $query->where('emp.status', $request->emp_status);

                    }

                    if(!empty($districtId) || !empty($upazilaId)){
                        $query->join('hr_emp_personal_details as empd', function ($join) {
                            $join->on('emp.id', '=', 'empd.emp_id');
                        });
                        
                        if( !empty($districtId) && !empty($upazilaId) ){
                            $query->where('par_addr_district_id', $districtId);
                            $query->where('par_addr_thana_id', $upazilaId);

                        }elseif( !empty($districtId) && empty($upazilaId) ){
                            $query->where('par_addr_district_id', $districtId);

                        }elseif( empty($districtId) && !empty($upazilaId) ){
                            $query->where('par_addr_thana_id', $upazilaId);

                        }
                    }

                    if (!empty($dateOfBirth)) {
                        // dd($dateOfBirth);
                        $query->join('hr_emp_personal_details as empd', function ($join) {
                            $join->on('emp.id', '=', 'empd.emp_id');
                        });
                        $query->where('dob', '=', $dateOfBirth);
                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {

                        $query->whereBetween('emp.join_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);

                    } elseif (!empty($request->start_date)) {

                        $query->where('emp.join_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));

                    } elseif (!empty($request->end_date)) {

                        $query->where('emp.join_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));

                    }
                })
                ->orderBy('emp.emp_code')
                ->select('emp.*')->get();

                // ss($allData);
            $data = array();
            $sno  = 1;


            
           
            
            $district = DB::table('gnl_districts')->where('is_delete',0)->get()->toArray();
            $districtArray = array_column($district, 'district_name', 'id');
            
            $thana = DB::table('gnl_upazilas')->where('is_delete',0)->get()->toArray();
            $thanaArray = array_column($thana, 'upazila_name', 'id');

            foreach ($allData as $key => $row) {

                $empGnlInfo = DB::table('hr_emp_personal_details')->where("emp_id",$row->id)->first();


                if(!empty($empGnlInfo)){
                    $district_name = isset($districtArray[$empGnlInfo->par_addr_district_id]) ? $districtArray[$empGnlInfo->par_addr_district_id] : '';
                    $thana_name = isset($thanaArray[$empGnlInfo->par_addr_thana_id]) ? $thanaArray[$empGnlInfo->par_addr_thana_id] :'';
                    // dd($empGnlInfo, $districtArray, $thanaArray, $district_name, $thana_name);
                }else{
                    $district_name = '';
                    $thana_name = '';
                }

                


                $status = "";

                if ($row->status == 1) {
                    $status = '<span class="text-primary">Present</span>';
                } elseif ($row->status == 2) {
                    $status = '<span class="text-danger">Resigned</span>';
                } elseif ($row->status == 3) {
                    $status = '<span class="text-danger">Dismissed</span>';
                } elseif ($row->status == 4) {
                    $status = '<span class="text-danger">Terminated</span>';
                } elseif ($row->status == 5) {
                    $status = '<span class="text-danger">Retired</span>';
                }



                $data[$key]['id']               = $sno;
                $data[$key]['username']         = $row->sys_user["username"];
                $data[$key]['emp_name']         = $row->emp_name . ' [' . $row->emp_code . ']';
                $data[$key]['gender']           = $row->gender;
                $data[$key]['marital_status']   = $row->personalData['marital_status'];
                $data[$key]['blood_group']      = $row->personalData['blood_group'];
                $data[$key]['religion']         = $row->personalData['religion'];
                $data[$key]['phone_number']     = $row->personalData['mobile_no'];
                $data[$key]['branch']           = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['designation']      = $row->designation['name'];
                $data[$key]['department']       = $row->department['dept_name'];
                $data[$key]['join_date']        = date('d/m/Y', strtotime($row->join_date));
                $data[$key]['dateofbirth']        = !empty($empGnlInfo) ? date("d/m/Y", strtotime($empGnlInfo->dob)) : '';
                $data[$key]['district']         = $district_name;
                $data[$key]['thana']         = $thana_name;
                $data[$key]['status']           = $status;

                $sno++;

            }

        return view('HR.Reports.StuffReports.EmployeeReports.employee_body', compact('data'));
    }

    public function getStuffReport()
    {

        return view('HR.Reports.StuffReports.StuffReport.show');
    }

    public function loadStuffReport(Request $request)
    {
        
        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $districtId = (empty($request->district_id)) ? null : $request->district_id;
        $upazilaId = (empty($request->upazila_id)) ? null : $request->upazila_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;


        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        $branchWiseArea = array();

        $branchData = Common::fnForBranchData($selBranchArr);

        foreach ($selBranchArr as $branchId) {

            $areaName = DB::table('gnl_areas')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where(function ($areaQuery) use ($branchId) {
                    if (!empty($branchId)) {
                        $areaQuery->where('branch_arr', 'LIKE', "{$branchId}")
                            ->orWhere('branch_arr', 'LIKE', "{$branchId},%")
                            ->orWhere('branch_arr', 'LIKE', "%,{$branchId},%")
                            ->orWhere('branch_arr', 'LIKE', "%,{$branchId}");
                    }
                })
                ->select('area_name', 'area_code')
                ->first();

            if ($areaName) {
                $branchWiseArea[$branchId] = $areaName->area_name . " [" . $areaName->area_code . "]";
            }
        }

        $branchWiseArea[1] = "Head Office Region";

        $allData    = DB::table('hr_employees as emp')
            ->where('emp.is_delete', '=', 0)
            ->when(true, function ($query) use ($request, $selBranchArr, $districtId, $upazilaId, $employeeId) { //Searching
                if (!empty($employeeId)) {

                    $query->where('emp.id', $employeeId);

                }
                if (!empty($selBranchArr)) {
                    $query->whereIn('emp.branch_id', $selBranchArr);
                }
                if (!empty($request->designation_id)) {
                    $query->where('emp.designation_id', $request->designation_id);
                }
                if (!empty($request->department_id)) {
                    $query->where('emp.department_id', $request->department_id);
                }
                if (!empty($request->employee_id)) {
                    $query->where('emp.id', $request->employee_id);
                }
                if (!empty($request->emp_gender)) {
                    $query->where('emp.gender', $request->emp_gender);
                }
                if (!empty($request->emp_status)) {
                    $query->where('emp.status', $request->emp_status);
                }

                if(!empty($districtId) || !empty($upazilaId)){
                    $query->join('hr_emp_personal_details as empd', function ($join) {
                        $join->on('emp.id', '=', 'empd.emp_id');
                    });
                    
                    if( !empty($districtId) && !empty($upazilaId) ){
                        $query->where('par_addr_district_id', $districtId);
                        $query->where('par_addr_thana_id', $upazilaId);

                    }elseif( !empty($districtId) && empty($upazilaId) ){
                        $query->where('par_addr_district_id', $districtId);

                    }elseif( empty($districtId) && !empty($upazilaId) ){
                        $query->where('par_addr_thana_id', $upazilaId);

                    }
                }
            })
            ->select('emp.*')
            ->orderBy('emp.branch_id', 'ASC')
            ->orderBy('emp.emp_code', 'ASC')
            ->get();

        $designationArr = array_values($allData->pluck('designation_id')->unique()->toArray());
        $deptArr = array_values($allData->pluck('department_id')->unique()->toArray());
        $designationData = HRS::fnForDesignationData($designationArr);
        $deptData = HRS::fnForDepartmentData($deptArr);

        $data = array();
        $sno  = 1;
        $male = 0;
        $female = 0;

        foreach ($allData as $key => $row) {

            $areaName = (isset($branchWiseArea[$row->branch_id])) ? $branchWiseArea[$row->branch_id] : "";

            $branchName = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $designationName = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $deptName = (isset($deptData[$row->department_id])) ? $deptData[$row->department_id] : "";

            if (strtolower($row->gender) == 'male') {
                $male = $male + 1;
            } else {
                $female = $female + 1;
            }

            $data[$key]['id']                 = $sno;
            $data[$key]['employee_name']      = $row->emp_name . " [" . $row->emp_code . "]";
            $data[$key]['gender']             = $row->gender;
            $data[$key]['branch_id']          = $row->branch_id;
            $data[$key]['branch_name']        = $branchName;
            $data[$key]['area_name']          = $areaName;
            $data[$key]['designation_name']   = $designationName;
            $data[$key]['dept_name']          = $deptName;

            $statusFlag = "<span>Draft</span>";

            if ($row->status == 1) {
                $statusFlag = '<span style="color: #0cf041">Present</span>';
            }

            if ($row->status == 2) {
                $statusFlag = '<span style="color: #d40f0f">Resigned</span>';
            }

            if ($row->status == 3) {
                $statusFlag = '<span style="color: #0c10f0">Dismissed</span>';
            }
            if ($row->status == 4) {
                $statusFlag = '<span style="color: #0c10f0">Terminated</span>';
            }
            if ($row->status == 5) {
                $statusFlag = '<span style="color: #0c10f0">Retired</span>';
            }

            $data[$key]['status'] = $statusFlag;
            $sno++;
        }

        return view('HR.Reports.StuffReports.StuffReport.body', compact('data', 'male', 'female'));
    }

    public function getNewAppointedReport()
    {
        return view('HR.Reports.StuffReports.NewAppReport.show');
    }

    public function loadNewAppointedReport(Request $request)
    {

        if ($request->startDate == '' || $request->endDate == '') return '';

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId   = (empty($request->region_id)) ? null : $request->region_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $districtId = (empty($request->district_id)) ? null : $request->district_id;
        $upazilaId = (empty($request->upazila_id)) ? null : $request->upazila_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $selBranchArr = Common::getBranchIdsForAllSection([
            // 'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);

        $allData    = DB::table('hr_employees as emp')
            ->where('emp.is_delete', '=', 0)
            ->when(true, function ($query) use ($request, $selBranchArr, $districtId, $upazilaId, $employeeId) { //Searching
                if (!empty($employeeId)) {

                    $query->where('emp.id', $employeeId);

                }
                if (!empty($selBranchArr)) {
                    $query->whereIn('emp.branch_id', $selBranchArr);
                }
                if (!empty($request->designation_id)) {
                    $query->where('emp.designation_id', $request->designation_id);
                }
                if (!empty($request->department_id)) {
                    $query->where('emp.department_id', $request->department_id);
                }
                if (!empty($request->employee_id)) {
                    $query->where('emp.id', $request->employee_id);
                }
                if (!empty($request->emp_gender)) {
                    $query->where('emp.gender', $request->emp_gender);
                }
                if (!empty($request->emp_status)) {
                    $query->where('emp.status', $request->emp_status);
                }
                if (!empty($request->startDate) && !empty($request->endDate)) {
                    $query->whereBetween('emp.join_date', [
                        (new DateTime($request->startDate))->format('Y-m-d'),
                        (new DateTime($request->endDate))->format('Y-m-d')
                    ]);
                } elseif (!empty($request->startDate)) {
                    $query->where('emp.join_date', '>=', (new DateTime($request->startDate))->format('Y-m-d'));
                } elseif (!empty($request->endDate)) {
                    $query->where('emp.join_date', '<=', (new DateTime($request->endDate))->format('Y-m-d'));
                }

                if(!empty($districtId) || !empty($upazilaId)){
                    $query->join('hr_emp_personal_details as empd', function ($join) {
                        $join->on('emp.id', '=', 'empd.emp_id');
                    });
                    
                    if( !empty($districtId) && !empty($upazilaId) ){
                        $query->where('par_addr_district_id', $districtId);
                        $query->where('par_addr_thana_id', $upazilaId);

                    }elseif( !empty($districtId) && empty($upazilaId) ){
                        $query->where('par_addr_district_id', $districtId);

                    }elseif( empty($districtId) && !empty($upazilaId) ){
                        $query->where('par_addr_thana_id', $upazilaId);

                    }
                }
            })
            ->select('emp.*')
            ->orderBy('emp.join_date', 'ASC')
            ->orderBy('emp.emp_code', 'ASC')
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

            if (strtolower($row->gender) == 'male') {
                $male = $male + 1;
            } else {
                $female = $female + 1;
            }

            $branchName = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $designationName = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $deptName = (isset($deptData[$row->department_id])) ? $deptData[$row->department_id] : "";

            $data[$key]['id']                 = $sno;
            $data[$key]['employee_name']      = $row->emp_name . " [ " . $row->emp_code . " ]";
            $data[$key]['dept_name']          = $deptName;
            $data[$key]['desig_name']         = $designationName;
            $data[$key]['gender']             = $row->gender;
            $data[$key]['branch']             = $branchName;
            $data[$key]['join_date']          = $row->join_date ? (new DateTime($row->join_date))->format('d-m-Y') : '';

            $statusFlag = "<span>Draft</span>";

            if ($row->status == 1) {
                $statusFlag = '<span style="color: #0cf041">Present</span>';
            }

            if ($row->status == 2) {
                $statusFlag = '<span style="color: #d40f0f">Resigned</span>';
            }

            if ($row->status == 3) {
                $statusFlag = '<span style="color: #0c10f0">Dismissed</span>';
            }
            if ($row->status == 4) {
                $statusFlag = '<span style="color: #0c10f0">Terminated</span>';
            }
            if ($row->status == 5) {
                $statusFlag = '<span style="color: #0c10f0">Retired</span>';
            }

            $data[$key]['status'] = $statusFlag;
            $sno++;
        }

        return view('HR.Reports.StuffReports.NewAppReport.body', compact('data', 'male', 'female'));
    }

    public function getConsolitedReport()
    {

        return view('HR.Reports.StuffReports.ConsolitedReport.show');
    }

    public function loadConsolitedReport(Request $request)
    {

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

        $todaydate =(new Datetime())->format('Y-m-d');


        $designationData = DB::table('gnl_dynamic_form_value')
            ->where([['form_id', 3], ['type_id', 3]])
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->select('name', 'uid')
            ->orderBy('uid', 'DESC')
            ->get();

        $branchData = DB::table('gnl_branchs')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where('id', '>', 1)
            ->select('branch_name', 'branch_code', 'id')
            ->orderBy('branch_code', 'ASC')
            ->get();

        $designationRoleMapData = DB::table('hr_designation_role_mapping')->get();


        $designationIdArr = array_values($designationRoleMapData->pluck('designation_ids')->unique()->toArray());

        $employeeData = DB::table('hr_employees')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where('status', '=', 1)
            ->where('branch_id', '<>', 1)
            // ->whereRaw('designation_id in (' . implode(",", $designationIdArr) . ')')
            ->where(function($query) use ($designationIdArr){
                if( count($designationIdArr) > 0 ){
                    $query->whereRaw('designation_id in (' . implode(",", $designationIdArr) . ')');
                }

            })
            ->selectRaw('branch_id, designation_id, count(*) as total')
            ->groupBy('branch_id', 'designation_id')
            ->orderBy('branch_id', 'ASC')
            ->get();

        $actResponseData = DB::table('hr_app_active_responsibilities AS acr')
            ->where('acr.is_delete', '=', 0)
            ->where('acr.is_active', '=', 1)
            ->where('acr.branch_id', '<>', 1)
            ->where('acr.expiry_date', '>=', $todaydate)
            ->where('acr.effective_date', '<=', $todaydate)
            // ->whereRaw('acr.designation_to_promote_id in (' . implode(",", $designationIdArr) . ')')
            ->where(function($query) use ($designationIdArr){
                if( count($designationIdArr) > 0 ){
                    $query->whereRaw('designation_to_promote_id in (' . implode(",", $designationIdArr) . ')');
                }

            })
            ->selectRaw('acr.branch_id, acr.designation_to_promote_id as designation_id, count(*) as total')
            ->groupBy('acr.branch_id', 'acr.designation_to_promote_id')
            ->orderBy('acr.branch_id', 'ASC')
            ->get();

        $employeeDataBranchWise = $employeeData->groupBy('branch_id');
        $acctiveResponseDataBranchWise = $actResponseData->groupBy('branch_id');
        // dd($actResponseData,$employeeData,$employeeDataBranchWise);
        $test =array();

        foreach($designationRoleMapData as $item)
        {

            $test[$item->position_id] = explode(',',$item->designation_ids);

        }

        $accTrainee=array();
        $creditTrainee=array();
        $cook=array();
        $i =0;
        foreach($branchData as $data){
            $brEmp = isset($employeeDataBranchWise[$data->id]) ? $employeeDataBranchWise[$data->id] : '';

            $acctotal =0;
            $credittotal =0;
            $cooktotal =0;
            foreach( $designationData as $desig)
            {
                if($desig->uid == '6')
                {
                    if(isset($brEmp) && $brEmp != '')
                    {
                        foreach($brEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $credittotal = $credittotal + $emp->total;

                            }
                        }
                    }
                    $creditTrainee[$data->id] = $credittotal;
                }
                if($desig->uid == '7')
                {
                    if(isset($brEmp) && $brEmp != '')
                    {
                        foreach($brEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $acctotal = $acctotal + $emp->total;

                            }
                        }
                    }
                    $accTrainee[$data->id] = $acctotal;
                }
                if($desig->uid == '8')
                {
                    if(isset($brEmp) && $brEmp != '')
                    {
                        foreach($brEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $cooktotal = $cooktotal + $emp->total;

                            }
                        }
                    }
                    $cook[$data->id] = $cooktotal;
                }
            }

        }

        $accAct=array();
        $branchAct=array();
        $areaAct=array();
        $zonalAct=array();


        foreach($branchData as $data){
            $acResrEmp = isset($acctiveResponseDataBranchWise[$data->id]) ? $acctiveResponseDataBranchWise[$data->id] :   '';

            $accountResponseTotal =0;
            $branchResponseTotal =0;
            $areaResponseTotal =0;
            $zoneResponseTotal =0;
            foreach( $designationData as $desig)
            {
                if($desig->uid == '5')
                {
                    if(isset($acResrEmp) && $acResrEmp != '')
                    {
                        foreach($acResrEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $zoneResponseTotal = $zoneResponseTotal + $emp->total;

                            }
                        }
                    }
                    $zonalAct[$data->id] = $zoneResponseTotal;
                }
                if($desig->uid == '4')
                {
                    if(isset($acResrEmp) && $acResrEmp != '')
                    {
                        foreach($acResrEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $areaResponseTotal = $areaResponseTotal + $emp->total;

                            }
                        }
                    }
                    $areaAct[$data->id] = $areaResponseTotal;
                }
                if($desig->uid == '3')
                {
                    if(isset($acResrEmp) && $acResrEmp != '')
                    {
                        foreach($acResrEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $branchResponseTotal = $branchResponseTotal + $emp->total;

                            }
                        }
                    }
                    $branchAct[$data->id] = $branchResponseTotal;
                }
                    if($desig->uid == '2')
                {
                    if(isset($acResrEmp) && $acResrEmp != '')
                    {
                        foreach($acResrEmp as $emp){
                            if(in_array($emp->designation_id,$test[$desig->uid]))
                            {
                                $accountResponseTotal = $accountResponseTotal + $emp->total;

                            }
                        }
                    }
                    $accAct[$data->id] = $accountResponseTotal;
                }
            }

        }




            // $tt = Common::replaceZeroWithDash(0);
            // dd($accAct,$branchAct,$areaAct,$zonalAct);


        return view('HR.Reports.StuffReports.ConsolitedReport.body', compact('branchData', 'designationData', 'test', 'employeeDataBranchWise','creditTrainee','accTrainee','cook','accAct','branchAct','areaAct','zonalAct'));
    }

}
