<?php

namespace App\Http\Controllers\HR\Employee;

use DateTime;
use Redirect;
use Dompdf\Exception;
use App\Model\GNL\SysUser;
use App\Model\HR\Employee;
use App\Services\GnlService;
use App\Services\HrService as HRS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService as Role;
use App\Model\HR\EmployeeAccountDetails;
use App\Model\HR\EmployeeNomineeDetails;
use App\Model\HR\EmployeePersonalDetails;
use App\Model\HR\EmployeeTrainingDetails;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;
use App\Model\HR\EmployeeEducationDetails;
use App\Model\HR\EmployeeGuarantorDetails;
use App\Model\HR\EmployeeReferenceDetails;
use App\Model\HR\EmployeeExperienceDetails;
use App\Model\HR\EmployeeOrganizationDetails;

class EmployeeController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Employee
    public function index(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {

            // dd($request->all());

            $draw       = $request->get('draw');
            $start      = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr  = $request->get('columns');
            $order_arr       = $request->get('order');
            $search_arr      = $request->get('search');

            $columnIndex     = $columnIndex_arr[0]['column'];
            $columnName      = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue     = $search_arr['value'];

            $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
            $areaId   = (empty($request->area_id)) ? null : $request->area_id;
            $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
            $districtId = (empty($request->district_id)) ? null : $request->district_id;
            $upazilaId = (empty($request->upazila_id)) ? null : $request->upazila_id;

            $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData = Employee::from('hr_employees as emp')

                ->where('emp.is_delete', 0)
                // ->join('hr_emp_personal_details as empd', 'emp.id', '=', 'empd.emp_id')
                ->whereIn('emp.branch_id', $selBranchArr)
                // ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId){
                //     if(Common::isSuperUser() == true || Common::isDeveloperUser() == true){
                //         ## nothing to do
                //     }
                //     else {

                //         if(in_array(101, $statusArray)){
                //             ## All Data for Permitted Branches
                //             ## nothing to do
                //             // $query->whereIn('emp.branch_id', $selBranchArr)
                //         }
                //         elseif(in_array(102, $statusArray)){
                //             ## All Branch Data Without HO
                //             $perQuery->where('emp.branch_id' , '<>' ,1);
                //         }
                //         elseif(in_array(103, $statusArray)){
                //             ## All Data Only HO
                //             $perQuery->where('emp.branch_id', 1);
                //         }
                //         elseif(in_array(104, $statusArray)){
                //             ## All data for own department of permitted branches
                //             // $perQuery->whereIn('emp.branch_id', $tmpBranch);
                //             $perQuery->where('emp.department_id', $loginUserDeptId);
                //         }
                //         elseif(in_array(105, $statusArray)){
                //             ## All data for own department of all branches without HO
                //             $perQuery->where('emp.branch_id', '<>' , 1);
                //             $perQuery->where('emp.department_id', $loginUserDeptId);
                //         }
                //         elseif(in_array(106, $statusArray)){
                //             ## All data for own department only HO
                //             $perQuery->where([['emp.branch_id', 1],['emp.department_id', $loginUserDeptId]]);
                //         }
                //         else{
                //             // $perQuery->where('emp.created_by', $userInfo['id']);
                //             // $perQuery->orWhere('emp.id', $userInfo['id']);
                //             if (!empty($userInfo['emp_id'])) {
                //                 $perQuery->orWhere('emp.id', $userInfo['emp_id']);
                //             }
                //         }

                //     }
                // })
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $selBranchArr){
                    ## Calling Permission Query Function
                    HRS::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $selBranchArr);
                })
                ->when(true, function ($query) use ($columnName, $columnSortOrder, $request, $districtId, $upazilaId) {

                    if ($columnName == "emp_code") {

                        $query->orderBy("emp." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "emp_name") {

                        $query->orderBy("emp." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "branch") {

                        $query->join('gnl_branchs as b', function ($join) {

                            $join->on('emp.branch_id', '=', 'b.id');
                        });

                        $query->orderBy('b.branch_name', $columnSortOrder);
                    } elseif ($columnName == "designation") {

                        $query->join('hr_designations as ds', function ($join) {

                            $join->on('emp.designation_id', '=', 'ds.id');
                        });

                        $query->orderBy('ds.name', $columnSortOrder);
                    } elseif ($columnName == "department") {

                        $query->join('hr_departments as dp', function ($join) {

                            $join->on('emp.department_id', '=', 'dp.id');
                        });

                        $query->orderBy('dp.dept_name', $columnSortOrder);
                    } elseif ($columnName == "phone_number") {

                        $query->join('hr_emp_personal_details as pd', function ($join) {

                            $join->on('emp.id', '=', 'pd.emp_id');
                        });

                        $query->orderBy('pd.mobile_no', $columnSortOrder);
                    } elseif ($columnName == "join_date") {

                        $query->orderBy("emp." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "status") {

                        $query->orderBy('emp.is_active', $columnSortOrder);
                    } elseif ($columnName == "id") {

                        $query->orderBy('emp.id', 'desc');
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

                ->where(function ($query) use ($request, $searchValue) {

                    if (!empty($searchValue)) {

                        $query->where('emp_code', 'like', '%' . $searchValue . '%');
                        $query->orWhere('emp_name', 'like', '%' . $searchValue . '%');
                        $query->orWhere('status', 'like', '%' . $searchValue . '%');
                        $query->orWhere('gender', 'like', '%' . $searchValue . '%');
                        $query->orWhere('join_date', 'like', '%' . $searchValue . '%');
                        $query->orWhere('org_mobile', 'like', '%' . $searchValue . '%');
                        $query->orWhere('org_email', 'like', '%' . $searchValue . '%');
                    }

                    if (!empty($request->designation_id)) {

                        $query->where('emp.designation_id', $request->designation_id);
                    }

                    if (!empty($request->department_id)) {

                        $query->where('emp.department_id', $request->department_id);
                    }

                    if (!empty($request->emp_gender)) {

                        $query->where('emp.gender', $request->emp_gender);
                    }

                    if (!empty($request->emp_code)) {

                        $query->where('emp.emp_code', 'LIKE', "%{$request->emp_code}%");
                    }

                    if ($request->emp_status == "0" || !empty($request->emp_status)) {

                        $query->where('emp.status', $request->emp_status);
                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {

                        $query->whereBetween('emp.join_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    } elseif (!empty($request->start_date)) {

                        $query->where('emp.join_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    } elseif (!empty($request->end_date)) {

                        $query->where('emp.join_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }
                })
                ->select('emp.*');

                // dd(count($allData->get()));

            $tempQueryData = clone $allData;
            $allData = $allData->skip($start)->take($rowperpage)->get();

            $totalRecords           = Employee::where([['is_delete', 0]])
                ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
                ->count();

            $totalRecordswithFilter = $totalRecords;

            if (
                !empty($searchValue)
                || !empty($request->branch_id)
                || !empty($request->start_date)
                || !empty($request->end_date)
                || !empty($request->zone_id)
                || !empty($request->area_id)
                || !empty($request->designation_id)
                || !empty($request->department_id)
                || !empty($request->emp_gender)
                || !empty($request->emp_code)
                || !empty($request->emp_status)
            ) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            $data = array();
            $sno  = $start + 1;

            foreach ($allData as $key => $row) {

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
                }elseif ($row->status == 0) {
                    $status = '<span class="text-dark">Draft</span>';
                }

                $data[$key]['id']           = $sno;
                $data[$key]['emp_code']     = $row->emp_code;
                $data[$key]['username']     = $row->sys_user["username"];
                $data[$key]['emp_name']     = $row->emp_name;
                $data[$key]['phone_number'] = $row->personalData['mobile_no'];
                $data[$key]['branch']       = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['designation']  = $row->designation['name'];
                $data[$key]['department']   = $row->department['dept_name'];
                $data[$key]['emp_gender']   = $row->gender;
                $data[$key]['join_date']    = !empty($row->join_date) ? date('d/m/Y', strtotime($row->join_date)) : '-';
                $data[$key]['status']       = $status;
                $data[$key]['action']       = Role::roleWiseArray($this->GlobalRole, encrypt($row->id));

                $sno++;
            }

            //$totalRecordswithFilter = count($data);

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);
        } else {
            return view('HR.Employee.index');
        }
    }

    public function generateEmployeeCode(Request $request)
    {

        $codeStruct = DB::table('hr_config')->where('title', 'emp_code_format')->get();
        $codeStruct = json_decode($codeStruct[0]->content);

        //dd($codeStruct);
        $orgSl        = $empProjSl        = $proCode        = $prefix        = $yerMon        = $divider        = "";
        $requiredData = [];
        if ($codeStruct->project_emp_serial_length != null) {
            $requiredData[0] = "projectId";
        }
        if ($codeStruct->year_month_position != null) {
            $requiredData[1] = "projectId";
        }
        if ($codeStruct->org_emp_serial_length != null) {
            $currentSl = DB::table('hr_employees')->count() + 1;
            $dis       = 0;
            $n         = $currentSl;
            while ($n > 0) {
                $n = (int) ($n / 10);
                $dis++;
            }

            $zeroCode = $codeStruct->org_emp_serial_length - $dis;
            if ($zeroCode > 0) {
                $orgSl .= str_repeat('0', $zeroCode);
            }
            $orgSl .= $currentSl;
        }
        if ($codeStruct->project_emp_serial_length != null && isset($request['projectId']) && $request['projectId'] != "") {
            $currentSl = DB::table('gnl_projects')->where('id', $request['projectId'])->count() + 1;
            $dis       = 0;
            $n         = $currentSl;
            while ($n > 0) {
                $n = (int) ($n / 10);
                $dis++;
            }

            $zeroCode = $codeStruct->project_emp_serial_length - $dis;

            if ($zeroCode > 0) {
                $empProjSl .= str_repeat('0', $zeroCode);
            }
            $empProjSl .= $currentSl;
            $requiredData[0] = '';
        }

        //dd($empProjSl);

        if ($codeStruct->project_code_position != null && $request['projectId'] != "") {
            $proj    = DB::table('gnl_projects')->where('id', $request['projectId'])->first();
            $proCode = $proj->project_code;
        }

        if ($codeStruct->year_month_position != null && isset($request['joinDate']) && $request['joinDate'] != "") {

            $date            = (new DateTime($request['joinDate']))->format("Y-m-d");
            $m               = \date('m', strtotime($date));
            $y               = \date('Y', strtotime($date));
            $yerMon          = $y . $m;
            $requiredData[1] = '';
        }

        if ($codeStruct->prefix_val != null) {
            $prefix = $codeStruct->prefix_val;
        }

        if ($codeStruct->separator_val != null) {
            $divider = $codeStruct->separator_val;
        }

        if ($codeStruct->emp_code_generator == 'manual') {
            return response()->json([
                'format'       => 'manual',
                'code'         => '',
                'requiredData' => '',
            ]);
        } elseif ($codeStruct->emp_code_generator == 'automatic') {
            $codeStruct = [
                [
                    'code' => $prefix,
                    'pos'  => ($codeStruct->prefix_position != null) ? (int) $codeStruct->prefix_position : 0,
                ],
                [
                    'code' => $orgSl,
                    'pos'  => ($codeStruct->org_emp_serial_position != null) ? (int) $codeStruct->org_emp_serial_position : 0,
                ],
                [
                    'code' => $empProjSl,
                    'pos'  => ($codeStruct->project_emp_serial_position != null) ? (int) $codeStruct->project_emp_serial_position : 0,
                ],
                [
                    'code' => $yerMon,
                    'pos'  => ($codeStruct->year_month_position != null) ? (int) $codeStruct->year_month_position : 0,
                ],
                [
                    'code' => $proCode,
                    'pos'  => ($codeStruct->project_code_position != null) ? (int) $codeStruct->project_code_position : 0,
                ],
            ];

            for ($i = 0; $i < count($codeStruct) - 1; $i++) {
                for ($j = 0; $j < count($codeStruct) - $i - 1; $j++) {
                    if ($codeStruct[$j]['pos'] > $codeStruct[$j + 1]['pos']) {
                        $temp               = $codeStruct[$j];
                        $codeStruct[$j]     = $codeStruct[$j + 1];
                        $codeStruct[$j + 1] = $temp;
                    }
                }
            }
            $employeeCode = "";
            $flag         = 0;
            for ($i = 0; $i < count($codeStruct); $i++) {
                if ($codeStruct[$i]['code'] != "") {
                    $flag++;
                    if ($flag != 1) {
                        $employeeCode .= $divider;
                    }
                    $employeeCode .= $codeStruct[$i]['code'];
                }
            }

            return response()->json([
                'format'       => 'automatic',
                'code'         => $employeeCode,
                'requiredData' => implode("", $requiredData),
            ]);
        }
    }

    public function edit_draft(Request $request){

        $emp_id = decrypt($request->id);

        if ($request->isMethod('post')) {
            //dd($request->all());
            $passport = $this->getPassport($request, 'draft');

            if ($passport['isValid'] == false) {
                return;
                $notification = array(
                    'message'    => $passport['errorMsg'],
                    'alert-type' => 'error',
                    'action'     => '',
                );
                return response()->json($notification);
            } elseif ($passport['isValid'] && $passport['action'] == 'next') {

                $decId = $emp_id;
                $id = $decId;
                $edata = Employee::where('id', $decId)->first();

                if(isset($request['submittedFrom']) && $request['submittedFrom'] == 'General'){
                    $empData = [
                        'branch_id' => $request['branch_id'],
                        'emp_name' => $request['emp_name_eng'],
                        'gender'         => $request['emp_gender'],
                    ];
                    $edata->update($empData);

                    $personalData = [
                        'emp_name_bn'          => $request['emp_name_ban'],
                        'father_name_en'       => $request['emp_fathers_name_eng'],
                        'father_name_bn'       => $request['emp_fathers_name_ban'],
                        'mother_name_en'       => $request['emp_mothers_name_eng'],
                        'mother_name_bn'       => $request['emp_mothers_name_ban'],
                        'spouse_name_en'       => $request['emp_spouse_name_en'],
                        'spouse_name_bn'       => $request['emp_spouse_name_bn'],

                        // 'gender'               => $request['emp_gender'],
                        'dob'                  => (new DateTime($request['emp_dob']))->format('Y-m-d'),
                        'nid_no'               => $request['emp_nid_no'],
                        'driving_license_no'   => $request['emp_driving_license_no'],
                        'marital_status'       => $request['emp_marital_status'],
                        'num_of_children'      => $request['emp_children'],
                        'religion'             => $request['emp_religion'],
                        'blood_group'          => $request['emp_blood_group'],
                        'birth_certificate_no' => $request['emp_birth_certificate_no'],
                        'passport_no'          => $request['emp_passport_no'],
                        'tin_no'               => $request['emp_tin_no'],
                        'phone_no'             => $request['emp_phone_no'],
                        'mobile_no'            => $request['emp_mobile_no'],
                        'email'                => $request['emp_email'],
                        'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
                        'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
                        'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
                        'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
                        'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
                        'pre_addr_street'      => $request['emp_pre_addr_street'],
                        'par_addr_division_id' => $request['emp_par_addr_division_id'],
                        'par_addr_district_id' => $request['emp_par_addr_district_id'],
                        'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
                        'par_addr_union_id'    => $request['emp_par_addr_union_id'],
                        'par_addr_village_id'  => $request['emp_par_addr_village_id'],
                        'par_addr_street'      => $request['emp_par_addr_street'],
                    ];

                    if ($request->hasFile('emp_photo')) {
                        $personalData['photo'] = Common::fileUpload($request->file('emp_photo'), 'hr_employees', $id);
                    }
                    if ($request->hasFile('emp_nid_signature')) {
                        $personalData['nid_signature'] = Common::fileUpload($request->file('emp_nid_signature'), 'hr_employees', $id);
                    }
                    if ($request->hasFile('emp_signature')) {
                        $personalData['signature'] = Common::fileUpload($request->file('emp_signature'), 'hr_employees', $id);
                    }
                    EmployeePersonalDetails::where('emp_id', $decId)->update($personalData);

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization'){

                    $empData = [
                        'designation_id' => $request['org_position_id'],
                        'department_id'  => $request['org_department'],

                        'join_date'      => $request['org_join_date'] != null ? (new DateTime($request['org_join_date']))->format('Y-m-d') : null,
                        'permanent_date' => $request['org_permanent_date'] != null ? (new DateTime($request['org_permanent_date']))->format('Y-m-d') : null,
                        'basic_salary'   => $request['org_basic_salary'],
                        'prov_period'    => $request['prov_period'],
                        'org_mobile'     => $request['org_mobile'],
                        'org_email'      => $request['org_email'],
                    ];
                    $edata->update($empData);

                    $orgData = [
                        'emp_id'                   => $decId,
                        'project_id'               => $request['org_project_id'],
                        'project_type_id'          => $request['org_project_type_id'],
                        'company_id'               => Common::getCompanyId(),

                        'rec_type_id'              => $request['org_rec_type_id'],
                        'level'                    => $request['org_level'],
                        'grade'                    => $request['org_grade'],
                        'step'                     => $request['org_step'],
                        'payscal_id'               => $request['org_fiscal_year_id'],
                        'salary_structure_id'      => $request['salary_structure_id'],

                        'phone_no'                 => $request['org_phone'],
                        'fax_no'                   => $request['org_fax'],
                        'fiscal_year_id'           => $request['org_fiscal_year_id'],
                        'last_inc_date'            => $request['org_last_inc_date'] != null ? (new DateTime($request['org_last_inc_date']))->format('Y-m-d') : null,
                        'security_amount'          => $request['org_security_amount'],
                        'adv_security_amount'      => $request['org_adv_security_amount'],
                        'installment_amount'       => $request['org_installment_amount'],
                        'edps_start_month'         => $request['org_edps_start_month'],
                        'status'                   => 1,
                        'location'                 => $request['org_location'],
                        'room_no'                  => $request['org_room_no'],
                        'device_id'                => $request['org_device_id'],
                        'tot_salary'               => $request['org_tot_salary'],
                        'salary_inc_year'          => $request['org_salary_inc_year'],
                        'security_amount_location' => $request['org_security_amount_location'],
                        'edps_amount'              => $request['org_edps_amount'],
                        'edps_lifetime'            => $request['org_edps_lifetime'],
                        'no_of_installment'        => $request['org_no_of_installment'],
                        'has_house_allowance'      => $request['org_has_house_allowance'],
                        'has_travel_allowance'     => $request['org_has_travel_allowance'],
                        'has_daily_allowance'      => $request['org_has_daily_allowance'],
                        'has_medical_allowance'    => $request['org_has_medical_allowance'],
                        'has_utility_allowance'    => $request['org_has_utility_allowance'],
                        'has_mobile_allowance'     => $request['org_has_mobile_allowance'],
                        'has_welfare_fund'         => $request['org_has_welfare_fund'],
                    ];
                    EmployeeOrganizationDetails::where('emp_id', $decId)->delete();
                    EmployeeOrganizationDetails::create($orgData);

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account'){
                    EmployeeAccountDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['acc_bank_id']); $i++) {
                        $accData = [
                            'emp_id'          => $decId,
                            'bank_id'         => $request['acc_bank_id'][$i],
                            'bank_branch_id'  => $request['acc_bank_branch_id'][$i],
                            'bank_acc_type'   => $request['acc_bank_acc_type'][$i],
                            'bank_acc_number' => $request['acc_bank_acc_number'][$i],
                        ];
                        //Insert Here......................
                        EmployeeAccountDetails::create($accData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education'){
                    EmployeeEducationDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['edu_exam_title']); $i++) {
                        $eduData = [
                            'emp_id'         => $decId,
                            'exam_title'     => $request['edu_exam_title'][$i],
                            'department'     => $request['edu_department'][$i],
                            'institute_name' => $request['edu_institute_name'][$i],
                            'board'          => $request['edu_board'][$i],
                            'res_type'       => $request['edu_res_type'][$i],
                            'result'         => $request['edu_result'][$i],
                            'res_out_of'     => $request['edu_res_out_of'][$i],
                            'passing_year'   => $request['edu_passing_year'][$i],
                        ];
                        //Insert Here......................
                        EmployeeEducationDetails::create($eduData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training'){
                    EmployeeTrainingDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['train_title']); $i++) {
                        $trainData = [
                            'emp_id'        => $decId,
                            'title'         => $request['train_title'][$i],
                            'organizer'     => $request['train_organizer'][$i],
                            'country_id'    => $request['train_country_id'][$i],
                            'address'       => $request['train_address'][$i],
                            'topic'         => $request['train_topic'][$i],
                            'training_year' => $request['train_training_year'][$i],
                            'duration'      => $request['train_duration'][$i],
                        ];
                        //Insert Here......................
                        EmployeeTrainingDetails::create($trainData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience'){
                    EmployeeExperienceDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['exp_org_name']); $i++) {
                        $expData = [
                            'emp_id'             => $decId,
                            'org_name'           => $request['exp_org_name'][$i],
                            'org_type'           => $request['exp_org_type'][$i],
                            'org_location'       => $request['exp_org_location'][$i],
                            'designation'        => $request['exp_designation'][$i],
                            'department'         => $request['exp_department'][$i],
                            'job_responsibility' => $request['exp_job_responsibility'][$i],
                            'area_of_experience' => $request['exp_area_of_experience'][$i],
                            'duration'           => $request['exp_duration'][$i],
                            'start_date'         => $request['exp_start_date'][$i] != null ? (new DateTime($request['exp_start_date'][$i]))->format('Y-m-d') : null,
                            'end_date'           => $request['exp_end_date'][$i] != null ? (new DateTime($request['exp_end_date'][$i]))->format('Y-m-d') : null,
                            'address'            => $request['exp_address'][$i],
                        ];
                        //Insert Here......................
                        EmployeeExperienceDetails::create($expData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor'){
                    $govtGuar = [
                        'emp_id'          => $decId,
                        'guarantor_type'  => 'Govt',
                        'name'            => $request['govt_guar_name'],
                        'designation'     => $request['govt_guar_designation'],
                        'occupation'      => $request['govt_guar_occupation'],
                        'email'           => $request['govt_guar_email'],
                        'working_address' => $request['govt_guar_working_address'],
                        'par_address'     => $request['govt_guar_par_address'],
                        'nid'             => $request['govt_guar_nid'],
                        'relation'        => $request['govt_guar_relation'],
                        'mobile'          => $request['govt_guar_mobile'],
                        'phone'           => $request['govt_guar_phone'],
                        //'photo' => ($request->hasFile('govt_guar_photo')) ? Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId) : null,
                        //'signature' => ($request->hasFile('govt_guar_signature')) ? Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId) : null,
                    ];
                    if ($request->hasFile('govt_guar_photo')) {
                        $govtGuar['photo'] = Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId);
                    }
                    if ($request->hasFile('govt_guar_signature')) {
                        $govtGuar['signature'] = Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId);
                    }
                    if ($request['govtGuarId'] !== null) {
                        EmployeeGuarantorDetails::where('id', $request['govtGuarId'])->update($govtGuar);
                    } else {
                        EmployeeGuarantorDetails::create($govtGuar);
                    }

                    $relGuar = [
                        'emp_id'          => $decId,
                        'guarantor_type'  => 'Relative',
                        'name'            => $request['rel_guar_name'],
                        'designation'     => $request['rel_guar_designation'],
                        'occupation'      => $request['rel_guar_occupation'],
                        'email'           => $request['rel_guar_email'],
                        'working_address' => $request['rel_guar_working_address'],
                        'par_address'     => $request['rel_guar_par_address'],
                        'nid'             => $request['rel_guar_nid'],
                        'relation'        => $request['rel_guar_relation'],
                        'mobile'          => $request['rel_guar_mobile'],
                        'phone'           => $request['rel_guar_phone'],
                        //'photo' => ($request->hasFile('rel_guar_photo')) ? Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId) : null,
                        //'signature' => ($request->hasFile('rel_guar_signature')) ? Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId) : null,
                    ];
                    if ($request->hasFile('rel_guar_photo')) {
                        $relGuar['photo'] = Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId);
                    }
                    if ($request->hasFile('rel_guar_signature')) {
                        $relGuar['signature'] = Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId);
                    }
                    if ($request['relGuarId'] !== null) {
                        EmployeeGuarantorDetails::where('id', $request['relGuarId'])->update($relGuar);
                    } else {
                        EmployeeGuarantorDetails::create($relGuar);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee'){
                    $empNomDet = EmployeeNomineeDetails::where('emp_id', $decId)->get();
                    $nomId     = $empNomDet->pluck('', 'id');

                    for ($i = 0; $i < count($request['nom_name']); $i++) {
                        $nomData = [];
                        $nomData = [
                            'emp_id'     => $decId,
                            'name'       => $request['nom_name'][$i],
                            'relation'   => $request['nom_relation'][$i],
                            'percentage' => $request['nom_percentage'][$i],
                            'nid'        => $request['nom_nid'][$i],
                            'address'    => $request['nom_address'][$i],
                            'mobile'     => $request['nom_mobile'][$i],
                        ];
                        //Check files
                        if(!empty($request->file)){
                            if ($request->file('nom_photo')[$i]->getClientOriginalName() != 'not_file') {
                                $nomData['photo'] = Common::fileUpload($request->file('nom_photo')[$i], 'hr_employees', $decId);
                            }
                            if ($request->file('nom_signature')[$i]->getClientOriginalName() != 'not_file') {
                                $nomData['signature'] = Common::fileUpload($request->file('nom_signature')[$i], 'hr_employees', $decId);
                            }
                        }


                        if ($request['nomId'][$i] == null) {
                            EmployeeNomineeDetails::create($nomData);
                        } else {
                            EmployeeNomineeDetails::where('id', $request['nomId'][$i])->update($nomData);
                            $nomId[$request['nomId'][$i]] = 1;
                        }
                    }
                    foreach ($nomId as $key => $value) {
                        if ($value == null) {
                            //Delete data
                            EmployeeNomineeDetails::where('id', $key)->delete();
                        }
                    }

                }

                $notification = array(
                    'message'    => true,
                    'alert-type' => 'success',
                    'action'     => 'next',
                );
                return response()->json($notification);

            } elseif ($passport['isValid'] && $passport['action'] == 'save') {
                //Save data to database
                DB::beginTransaction();
                try {
                    $decId = $emp_id;

                    EmployeeReferenceDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['ref_name']); $i++) {
                        $refData = [
                            'emp_id'          => $decId,
                            'name'            => $request['ref_name'][$i],
                            'designation'     => $request['ref_designation'][$i],
                            'relation'        => $request['ref_relation'][$i],
                            'nid'             => $request['ref_nid'][$i],
                            'mobile'          => $request['ref_mobile'][$i],
                            'phone'           => $request['ref_phone'][$i],
                            'email'           => $request['ref_email'][$i],
                            'occupation'      => $request['ref_occupation'][$i],
                            'working_address' => $request['ref_working_address'][$i],
                        ];
                        //Insert Here......................
                        EmployeeReferenceDetails::create($refData);
                    }
                    DB::commit();
                    $notification = array(
                        'message'    => 'Success',
                        'alert-type' => 'success',
                        'action'     => 'saved',
                    );
                    return response()->json($notification);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $notification = array(
                        'message'    => 'Error...',
                        'alert-type' => 'error',
                        'action'     => '',
                        'aa'         => $e,
                    );
                    return response()->json($notification);
                }
            }
        }



        // dd($request->all(), $emp_id, $request->isMethod('post'));
    }

    public function add_draft(Request $request){

        $search = DB::table('hr_employees')->where([['emp_code', $request['emp_code']], ['is_delete', 0]])->get();
        if(count($search) > 0){
            $emp_id = optional($search->first())->id;

            if ($request->isMethod('post')) {
                //dd($request->all());
                $passport = $this->getPassport($request, 'draft');

                if ($passport['isValid'] == false) {
                    return;
                    $notification = array(
                        'message'    => $passport['errorMsg'],
                        'alert-type' => 'error',
                        'action'     => '',
                    );
                    return response()->json($notification);
                } elseif ($passport['isValid'] && $passport['action'] == 'next') {

                    $decId = $emp_id;
                    $id = $decId;
                    $edata = Employee::where('id', $decId)->first();

                    if(isset($request['submittedFrom']) && $request['submittedFrom'] == 'General'){
                        $empData = [
                            'branch_id' => $request['branch_id'],
                            'emp_name' => $request['emp_name_eng'],
                            'gender'         => $request['emp_gender'],
                        ];
                        $edata->update($empData);

                        $personalData = [
                            'emp_name_bn'          => $request['emp_name_ban'],
                            'father_name_en'       => $request['emp_fathers_name_eng'],
                            'father_name_bn'       => $request['emp_fathers_name_ban'],
                            'mother_name_en'       => $request['emp_mothers_name_eng'],
                            'mother_name_bn'       => $request['emp_mothers_name_ban'],
                            'spouse_name_en'       => $request['emp_spouse_name_en'],
                            'spouse_name_bn'       => $request['emp_spouse_name_bn'],

                            // 'gender'               => $request['emp_gender'],
                            'dob'                  => (new DateTime($request['emp_dob']))->format('Y-m-d'),
                            'nid_no'               => $request['emp_nid_no'],
                            'driving_license_no'   => $request['emp_driving_license_no'],
                            'marital_status'       => $request['emp_marital_status'],
                            'num_of_children'      => $request['emp_children'],
                            'religion'             => $request['emp_religion'],
                            'blood_group'          => $request['emp_blood_group'],
                            'birth_certificate_no' => $request['emp_birth_certificate_no'],
                            'passport_no'          => $request['emp_passport_no'],
                            'tin_no'               => $request['emp_tin_no'],
                            'phone_no'             => $request['emp_phone_no'],
                            'mobile_no'            => $request['emp_mobile_no'],
                            'email'                => $request['emp_email'],
                            'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
                            'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
                            'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
                            'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
                            'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
                            'pre_addr_street'      => $request['emp_pre_addr_street'],
                            'par_addr_division_id' => $request['emp_par_addr_division_id'],
                            'par_addr_district_id' => $request['emp_par_addr_district_id'],
                            'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
                            'par_addr_union_id'    => $request['emp_par_addr_union_id'],
                            'par_addr_village_id'  => $request['emp_par_addr_village_id'],
                            'par_addr_street'      => $request['emp_par_addr_street'],
                        ];

                        if ($request->hasFile('emp_photo')) {
                            $personalData['photo'] = Common::fileUpload($request->file('emp_photo'), 'hr_employees', $id);
                        }
                        if ($request->hasFile('emp_nid_signature')) {
                            $personalData['nid_signature'] = Common::fileUpload($request->file('emp_nid_signature'), 'hr_employees', $id);
                        }
                        if ($request->hasFile('emp_signature')) {
                            $personalData['signature'] = Common::fileUpload($request->file('emp_signature'), 'hr_employees', $id);
                        }
                        EmployeePersonalDetails::where('emp_id', $decId)->update($personalData);

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization'){

                        $empData = [
                            'designation_id' => $request['org_position_id'],
                            'department_id'  => $request['org_department'],

                            'join_date'      => $request['org_join_date'] != null ? (new DateTime($request['org_join_date']))->format('Y-m-d') : null,
                            'permanent_date' => $request['org_permanent_date'] != null ? (new DateTime($request['org_permanent_date']))->format('Y-m-d') : null,
                            'basic_salary'   => $request['org_basic_salary'],
                            'prov_period'    => $request['prov_period'],
                            'org_mobile'     => $request['org_mobile'],
                            'org_email'      => $request['org_email'],
                        ];
                        $edata->update($empData);

                        $orgData = [
                            'emp_id'                   => $decId,
                            'project_id'               => $request['org_project_id'],
                            'project_type_id'          => $request['org_project_type_id'],
                            'company_id'               => Common::getCompanyId(),

                            'rec_type_id'              => $request['org_rec_type_id'],
                            'level'                    => $request['org_level'],
                            'grade'                    => $request['org_grade'],
                            'step'                     => $request['org_step'],
                            'payscal_id'               => $request['org_fiscal_year_id'],
                            'salary_structure_id'      => $request['salary_structure_id'],

                            'phone_no'                 => $request['org_phone'],
                            'fax_no'                   => $request['org_fax'],
                            'fiscal_year_id'           => $request['org_fiscal_year_id'],
                            'last_inc_date'            => $request['org_last_inc_date'] != null ? (new DateTime($request['org_last_inc_date']))->format('Y-m-d') : null,
                            'security_amount'          => $request['org_security_amount'],
                            'adv_security_amount'      => $request['org_adv_security_amount'],
                            'installment_amount'       => $request['org_installment_amount'],
                            'edps_start_month'         => $request['org_edps_start_month'],
                            'status'                   => 1,
                            'location'                 => $request['org_location'],
                            'room_no'                  => $request['org_room_no'],
                            'device_id'                => $request['org_device_id'],
                            'tot_salary'               => $request['org_tot_salary'],
                            'salary_inc_year'          => $request['org_salary_inc_year'],
                            'security_amount_location' => $request['org_security_amount_location'],
                            'edps_amount'              => $request['org_edps_amount'],
                            'edps_lifetime'            => $request['org_edps_lifetime'],
                            'no_of_installment'        => $request['org_no_of_installment'],
                            'has_house_allowance'      => $request['org_has_house_allowance'],
                            'has_travel_allowance'     => $request['org_has_travel_allowance'],
                            'has_daily_allowance'      => $request['org_has_daily_allowance'],
                            'has_medical_allowance'    => $request['org_has_medical_allowance'],
                            'has_utility_allowance'    => $request['org_has_utility_allowance'],
                            'has_mobile_allowance'     => $request['org_has_mobile_allowance'],
                            'has_welfare_fund'         => $request['org_has_welfare_fund'],
                        ];
                        EmployeeOrganizationDetails::where('emp_id', $decId)->delete();
                        EmployeeOrganizationDetails::create($orgData);

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account'){
                        EmployeeAccountDetails::where('emp_id', $decId)->delete();
                        for ($i = 0; $i < count($request['acc_bank_id']); $i++) {
                            $accData = [
                                'emp_id'          => $decId,
                                'bank_id'         => $request['acc_bank_id'][$i],
                                'bank_branch_id'  => $request['acc_bank_branch_id'][$i],
                                'bank_acc_type'   => $request['acc_bank_acc_type'][$i],
                                'bank_acc_number' => $request['acc_bank_acc_number'][$i],
                            ];
                            //Insert Here......................
                            EmployeeAccountDetails::create($accData);
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education'){
                        EmployeeEducationDetails::where('emp_id', $decId)->delete();
                        for ($i = 0; $i < count($request['edu_exam_title']); $i++) {
                            $eduData = [
                                'emp_id'         => $decId,
                                'exam_title'     => $request['edu_exam_title'][$i],
                                'department'     => $request['edu_department'][$i],
                                'institute_name' => $request['edu_institute_name'][$i],
                                'board'          => $request['edu_board'][$i],
                                'res_type'       => $request['edu_res_type'][$i],
                                'result'         => $request['edu_result'][$i],
                                'res_out_of'     => $request['edu_res_out_of'][$i],
                                'passing_year'   => $request['edu_passing_year'][$i],
                            ];
                            //Insert Here......................
                            EmployeeEducationDetails::create($eduData);
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training'){
                        EmployeeTrainingDetails::where('emp_id', $decId)->delete();
                        for ($i = 0; $i < count($request['train_title']); $i++) {
                            $trainData = [
                                'emp_id'        => $decId,
                                'title'         => $request['train_title'][$i],
                                'organizer'     => $request['train_organizer'][$i],
                                'country_id'    => $request['train_country_id'][$i],
                                'address'       => $request['train_address'][$i],
                                'topic'         => $request['train_topic'][$i],
                                'training_year' => $request['train_training_year'][$i],
                                'duration'      => $request['train_duration'][$i],
                            ];
                            //Insert Here......................
                            EmployeeTrainingDetails::create($trainData);
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience'){
                        EmployeeExperienceDetails::where('emp_id', $decId)->delete();
                        for ($i = 0; $i < count($request['exp_org_name']); $i++) {
                            $expData = [
                                'emp_id'             => $decId,
                                'org_name'           => $request['exp_org_name'][$i],
                                'org_type'           => $request['exp_org_type'][$i],
                                'org_location'       => $request['exp_org_location'][$i],
                                'designation'        => $request['exp_designation'][$i],
                                'department'         => $request['exp_department'][$i],
                                'job_responsibility' => $request['exp_job_responsibility'][$i],
                                'area_of_experience' => $request['exp_area_of_experience'][$i],
                                'duration'           => $request['exp_duration'][$i],
                                'start_date'         => $request['exp_start_date'][$i] != null ? (new DateTime($request['exp_start_date'][$i]))->format('Y-m-d') : null,
                                'end_date'           => $request['exp_end_date'][$i] != null ? (new DateTime($request['exp_end_date'][$i]))->format('Y-m-d') : null,
                                'address'            => $request['exp_address'][$i],
                            ];
                            //Insert Here......................
                            EmployeeExperienceDetails::create($expData);
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor'){
                        $govtGuar = [
                            'emp_id'          => $decId,
                            'guarantor_type'  => 'Govt',
                            'name'            => $request['govt_guar_name'],
                            'designation'     => $request['govt_guar_designation'],
                            'occupation'      => $request['govt_guar_occupation'],
                            'email'           => $request['govt_guar_email'],
                            'working_address' => $request['govt_guar_working_address'],
                            'par_address'     => $request['govt_guar_par_address'],
                            'nid'             => $request['govt_guar_nid'],
                            'relation'        => $request['govt_guar_relation'],
                            'mobile'          => $request['govt_guar_mobile'],
                            'phone'           => $request['govt_guar_phone'],
                            //'photo' => ($request->hasFile('govt_guar_photo')) ? Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId) : null,
                            //'signature' => ($request->hasFile('govt_guar_signature')) ? Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId) : null,
                        ];
                        if ($request->hasFile('govt_guar_photo')) {
                            $govtGuar['photo'] = Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId);
                        }
                        if ($request->hasFile('govt_guar_signature')) {
                            $govtGuar['signature'] = Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId);
                        }
                        if ($request['govtGuarId'] !== null) {
                            EmployeeGuarantorDetails::where('id', $request['govtGuarId'])->update($govtGuar);
                        } else {
                            EmployeeGuarantorDetails::create($govtGuar);
                        }

                        $relGuar = [
                            'emp_id'          => $decId,
                            'guarantor_type'  => 'Relative',
                            'name'            => $request['rel_guar_name'],
                            'designation'     => $request['rel_guar_designation'],
                            'occupation'      => $request['rel_guar_occupation'],
                            'email'           => $request['rel_guar_email'],
                            'working_address' => $request['rel_guar_working_address'],
                            'par_address'     => $request['rel_guar_par_address'],
                            'nid'             => $request['rel_guar_nid'],
                            'relation'        => $request['rel_guar_relation'],
                            'mobile'          => $request['rel_guar_mobile'],
                            'phone'           => $request['rel_guar_phone'],
                            //'photo' => ($request->hasFile('rel_guar_photo')) ? Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId) : null,
                            //'signature' => ($request->hasFile('rel_guar_signature')) ? Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId) : null,
                        ];
                        if ($request->hasFile('rel_guar_photo')) {
                            $relGuar['photo'] = Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId);
                        }
                        if ($request->hasFile('rel_guar_signature')) {
                            $relGuar['signature'] = Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId);
                        }
                        if ($request['relGuarId'] !== null) {
                            EmployeeGuarantorDetails::where('id', $request['relGuarId'])->update($relGuar);
                        } else {
                            EmployeeGuarantorDetails::create($relGuar);
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee'){
                        $empNomDet = EmployeeNomineeDetails::where('emp_id', $decId)->get();
                        $nomId     = $empNomDet->pluck('', 'id');

                        for ($i = 0; $i < count($request['nom_name']); $i++) {
                            $nomData = [];
                            $nomData = [
                                'emp_id'     => $decId,
                                'name'       => $request['nom_name'][$i],
                                'relation'   => $request['nom_relation'][$i],
                                'percentage' => $request['nom_percentage'][$i],
                                'nid'        => $request['nom_nid'][$i],
                                'address'    => $request['nom_address'][$i],
                                'mobile'     => $request['nom_mobile'][$i],
                            ];
                            //Check files
                            if(!empty($request->file)){
                                if ($request->file('nom_photo')[$i]->getClientOriginalName() != 'not_file') {
                                    $nomData['photo'] = Common::fileUpload($request->file('nom_photo')[$i], 'hr_employees', $decId);
                                }
                                if ($request->file('nom_signature')[$i]->getClientOriginalName() != 'not_file') {
                                    $nomData['signature'] = Common::fileUpload($request->file('nom_signature')[$i], 'hr_employees', $decId);
                                }
                            }


                            if ($request['nomId'][$i] == null) {
                                EmployeeNomineeDetails::create($nomData);
                            } else {
                                EmployeeNomineeDetails::where('id', $request['nomId'][$i])->update($nomData);
                                $nomId[$request['nomId'][$i]] = 1;
                            }
                        }
                        foreach ($nomId as $key => $value) {
                            if ($value == null) {
                                //Delete data
                                EmployeeNomineeDetails::where('id', $key)->delete();
                            }
                        }

                    }

                    $notification = array(
                        'message'    => $passport['errorMsg'],
                        'alert-type' => 'success',
                        'action'     => 'next',
                    );
                    return response()->json($notification);
                } elseif ($passport['isValid'] && $passport['action'] == 'save') {
                    //Save data to database
                    DB::beginTransaction();
                    try {
                        $decId = $emp_id;

                        EmployeeReferenceDetails::where('emp_id', $decId)->delete();
                        for ($i = 0; $i < count($request['ref_name']); $i++) {
                            $refData = [
                                'emp_id'          => $decId,
                                'name'            => $request['ref_name'][$i],
                                'designation'     => $request['ref_designation'][$i],
                                'relation'        => $request['ref_relation'][$i],
                                'nid'             => $request['ref_nid'][$i],
                                'mobile'          => $request['ref_mobile'][$i],
                                'phone'           => $request['ref_phone'][$i],
                                'email'           => $request['ref_email'][$i],
                                'occupation'      => $request['ref_occupation'][$i],
                                'working_address' => $request['ref_working_address'][$i],
                            ];
                            //Insert Here......................
                            EmployeeReferenceDetails::create($refData);
                        }
                        DB::commit();
                        $notification = array(
                            'message'    => 'Success',
                            'alert-type' => 'success',
                            'action'     => 'saved',
                        );
                        return response()->json($notification);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $notification = array(
                            'message'    => 'Error...',
                            'alert-type' => 'error',
                            'action'     => '',
                            'aa'         => $e,
                        );
                        return response()->json($notification);
                    }
                }
            }


        }else{

            // dd($request->all());
            if ($request->isMethod('post')) {

                $passport = $this->getPassport($request, 'store');

                if ($passport['isValid'] == false) {
                    $notification = array(
                        'message'    => $passport['errorMsg'],
                        'alert-type' => 'error',
                        'action'     => '',
                    );
                    return response()->json($notification);
                } elseif ($passport['isValid'] && $passport['action'] == 'next') {

                    $emp_id = 0;

                    if(isset($request['submittedFrom']) && $request['submittedFrom'] == 'General'){

                        if($request['emp_id'] == 0){

                            $employee_no = Common::generateEmployeeNo();

                            $sysUserId = $this->insertEmpGnlSystemUserData($request, $employee_no);

                            ## insert hr_employee and return inserted id
                            $emp_id = $this->insertEmpGeneralData($request, $employee_no, $sysUserId);
                            ## $request['emp_id'] = return inserted id;
                        }
                        else {

                            $this->UpdateEmpGeneralData($request);
                            $this->updateEmpGnlSystemUserData($request);
                            $emp_id =  $request['emp_id'];
                            ## update hr_employee
                        }

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization'){

                        ## update hr_employee

                        $this->insertEmpOrganizationData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account'){
                        $this->insertEmpAccountData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education'){
                        $this->insertEmpEducationData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training'){
                        $this->insertEmpTrainingData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience'){
                        $this->insertEmpExperienceData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor'){
                        $this->insertEmpGuarantorData($request);
                        $emp_id = $request['emp_id'];

                    }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee'){
                        $this->insertEmpNomineeData($request);
                        $emp_id = $request['emp_id'];
                    }

                    $notification = array(
                        'message'    => $passport['errorMsg'],
                        'alert-type' => 'success',
                        'action'     => 'next',
                        'emp_id'     => $emp_id,
                    );
                    return response()->json($notification);

                } elseif ($passport['isValid'] && $passport['action'] == 'save') {
                    //Save data to database
                    DB::beginTransaction();
                    try {

                        ## insert referrence data
                        $this->insertEmpReferenceData($request);


                        DB::commit();
                        $notification = array(
                            'message'    => 'Success',
                            'alert-type' => 'success',
                            'action'     => 'saved',
                        );
                        return response()->json($notification);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $notification = array(
                            'message'    => 'Error...',
                            'alert-type' => 'error',
                            'action'     => '',
                            'aa'         => $e,
                        );
                        return response()->json($notification);
                    }
                }
            }
        }
    }

    public function insertEmpGnlSystemUserData($request, $employee_no){
        /* Insert Into gnl_system_user table */
        $sysUserData = [
            'company_id'       => Common::getCompanyId(),
            'branch_id'        => $request['branch_id'],
            'employee_no'      => $employee_no,
            'sys_user_role_id' => 3,
            'full_name' => $request['emp_name_eng'],
            // 'username' => $request['emp_code'],
            // 'password' => Hash::make($request['emp_code']),
            'email' => $request['emp_email'],
            'contact_no' => $request['mobile_no'],

            'is_active'        => 0,
            'is_delete'        => 0,
            'created_at'       => (new \DateTime())->format('Y-m-d H:i:s'),
            'updated_at'       => (new \DateTime())->format('Y-m-d H:i:s'),
            'created_by'       => Auth::user()->id,
            'updated_by'       => Auth::user()->id,
        ];

        $sysUserId = DB::table('gnl_sys_users')->insertGetId($sysUserData);
        /* Insert Into gnl_system_user table */

        return $sysUserId;
    }

    public function updateEmpGnlSystemUserData($request){
        /* Insert Into gnl_system_user table */
        $sysUserData = [
            'full_name' => $request['emp_name_eng']
        ];

        DB::table('gnl_sys_users')->where('emp_id', $request['emp_id'])->update($sysUserData);
        /* Insert Into gnl_system_user table */
    }

    public function getIdForAddressData($tableName, $targetColumn, $value){
        $id = null;
        $findData = DB::table($tableName)
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where($targetColumn, '=', $value)
                    ->first();

        $id = !empty($findData) ? $findData->id : null;

        if ($id == null) {

            $getId = DB::table($tableName)->insertGetId([
                $targetColumn => $value
            ]);

            $id = $getId;
        }

        // dd($id);
        return $id;
    }


    // public function getIdForAddressData($request){

    //     $pre_districtId  = $request['emp_pre_addr_district_id'];
    //     $pre_thanaVal    = $request['emp_pre_addr_thana_id'];
    //     $pre_unionVal    = $request['emp_pre_addr_union_id'];
    //     $pre_villageVal  = $request['emp_pre_addr_village_id'];

    //     $par_districtId  = $request['emp_par_addr_district_id'];
    //     $par_thanaVal    = $request['emp_par_addr_thana_id'];
    //     $par_unionVal    = $request['emp_par_addr_union_id'];
    //     $par_villageVal  = $request['emp_par_addr_village_id'];

    //     if(!empty($pre_districtId) ){

    //     }

    //     ss($request->all(), $districtId, $thanaVal, $unionVal, $villageVal);


    //     $id = null;
    //     // $findData = DB::table($tableName)
    //     //             ->where([['is_active', 1], ['is_delete', 0]])
    //     //             ->where($targetColumn, '=', $value)
    //     //             ->first();

    //     // $id = !empty($findData) ? $findData->id : null;

    //     // if ($id == null) {

    //     //     $getId = DB::table($tableName)->insertGetId([
    //     //         $targetColumn => $value
    //     //     ]);

    //     //     $id = $getId;
    //     // }

    //     dd($id);
    //     return $id;
    // }

    public function insertEmpGeneralData($request, $employee_no, $sysUserId){
        // dd($request->all());

        /* Insert into hr_employees table */

        $empData = [
            'employee_no'    => $employee_no,
            'emp_code'       => $request['emp_code'],
            'emp_name'       => $request['emp_name_eng'],
            'user_id'        => $sysUserId,
            'branch_id'      => $request['branch_id'],
            'gender'         => $request['emp_gender'],
            'status'         => 0,

            'is_active'      => 1,
            'is_delete'      => 0,
            'created_at'     => (new \DateTime())->format('Y-m-d H:i:s'),
            'updated_at'     => (new \DateTime())->format('Y-m-d H:i:s'),
            'created_by'     => Auth::user()->id,
            'updated_by'     => Auth::user()->id,
        ];
        $id        = DB::table('hr_employees')->insertGetId($empData);
        $tableName = 'hr_employees';

        /* Insert into hr_employees table */

        /* Update gnl_system_user table with emp_id */
        DB::table('gnl_sys_users')->where('employee_no', $employee_no)->update(['emp_id' => $id]);
        /* Update gnl_system_user table with emp_id */


        // ss( $this->getIdForAddressData($request));

        /* Insert into hr_emp_personal_details */
        $personalData = [
            'emp_id'               => $id,

            'emp_name_bn'          => $request['emp_name_ban'],
            'father_name_en'       => $request['emp_fathers_name_eng'],
            'father_name_bn'       => $request['emp_fathers_name_ban'],
            'mother_name_en'       => $request['emp_mothers_name_eng'],
            'mother_name_bn'       => $request['emp_mothers_name_ban'],
            'spouse_name_en'       => $request['emp_spouse_name_en'],
            'spouse_name_bn'       => $request['emp_spouse_name_bn'],

            // 'gender'               => $request['emp_gender'],
            'dob'                  => (new DateTime($request['emp_dob']))->format('Y-m-d'),
            'nid_no'               => $request['emp_nid_no'],
            'driving_license_no'   => $request['emp_driving_license_no'],
            'marital_status'       => $request['emp_marital_status'],
            'num_of_children'      => $request['emp_children'],
            'religion'             => $request['emp_religion'],
            'blood_group'          => $request['emp_blood_group'],
            'birth_certificate_no' => $request['emp_birth_certificate_no'],
            'passport_no'          => $request['emp_passport_no'],
            'tin_no'               => $request['emp_tin_no'],
            'phone_no'             => $request['emp_phone_no'],
            'mobile_no'            => $request['emp_mobile_no'],
            'email'                => $request['emp_email'],

            // 'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
            // 'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
            // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
            // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
            // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
            // 'pre_addr_street'      => $request['emp_pre_addr_street'],
            // 'par_addr_division_id' => $request['emp_par_addr_division_id'],
            // 'par_addr_district_id' => $request['emp_par_addr_district_id'],
            // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
            // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
            // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
            // 'par_addr_street'      => $request['emp_par_addr_street'],

            'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
            'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
            'pre_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_pre_addr_thana_id']),
            // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
            'pre_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_pre_addr_union_id']),
            // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
            'pre_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_pre_addr_village_id']),
            // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
            'pre_addr_street'      => $request['emp_pre_addr_street'],

            'par_addr_division_id' => $request['emp_par_addr_division_id'],
            'par_addr_district_id' => $request['emp_par_addr_district_id'],
            'par_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_par_addr_thana_id']),
            // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
            'par_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_par_addr_union_id']),
            // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
            'par_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_par_addr_village_id']),
            // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
            'par_addr_street'      => $request['emp_par_addr_street'],

            'photo'                => ($request->hasFile('emp_photo')) ? Common::fileUpload($request->file('emp_photo'), $tableName, $id) : null,
            'nid_signature'        => ($request->hasFile('emp_nid_signature')) ? Common::fileUpload($request->file('emp_nid_signature'), $tableName, $id) : null,
            'signature'            => ($request->hasFile('emp_signature')) ? Common::fileUpload($request->file('emp_signature'), $tableName, $id) : null,
            'status'               => 1,
        ];
        EmployeePersonalDetails::create($personalData);
        /* Insert into hr_emp_personal_details */
        return $id;
    }

    public function UpdateEmpGeneralData($request){
        // dd($request->all());

        /* Update into hr_employees table */
        $empData = [
            'emp_name'       => $request['emp_name_eng'],
            'branch_id'      => $request['branch_id'],
            'gender'         => $request['emp_gender'],
        ];
        DB::table('hr_employees')->where('id', $request['emp_id'])->update($empData);
        /* Update into hr_employees table */

        /* Update into hr_emp_personal_details */
        $personalData = [
            'emp_name_bn'          => $request['emp_name_ban'],
            'father_name_en'       => $request['emp_fathers_name_eng'],
            'father_name_bn'       => $request['emp_fathers_name_ban'],
            'mother_name_en'       => $request['emp_mothers_name_eng'],
            'mother_name_bn'       => $request['emp_mothers_name_ban'],
            'spouse_name_en'       => $request['emp_spouse_name_en'],
            'spouse_name_bn'       => $request['emp_spouse_name_bn'],

            // 'gender'               => $request['emp_gender'],
            'dob'                  => (new DateTime($request['emp_dob']))->format('Y-m-d'),
            'nid_no'               => $request['emp_nid_no'],
            'driving_license_no'   => $request['emp_driving_license_no'],
            'marital_status'       => $request['emp_marital_status'],
            'num_of_children'      => $request['emp_children'],
            'religion'             => $request['emp_religion'],
            'blood_group'          => $request['emp_blood_group'],
            'birth_certificate_no' => $request['emp_birth_certificate_no'],
            'passport_no'          => $request['emp_passport_no'],
            'tin_no'               => $request['emp_tin_no'],
            'phone_no'             => $request['emp_phone_no'],
            'mobile_no'            => $request['emp_mobile_no'],
            'email'                => $request['emp_email'],

            // 'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
            // 'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
            // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
            // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
            // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
            // 'pre_addr_street'      => $request['emp_pre_addr_street'],
            // 'par_addr_division_id' => $request['emp_par_addr_division_id'],
            // 'par_addr_district_id' => $request['emp_par_addr_district_id'],
            // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
            // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
            // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
            // 'par_addr_street'      => $request['emp_par_addr_street'],

            'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
            'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
            'pre_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_pre_addr_thana_id']),
            // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
            'pre_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_pre_addr_union_id']),
            // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
            'pre_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_pre_addr_village_id']),
            // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
            'pre_addr_street'      => $request['emp_pre_addr_street'],

            'par_addr_division_id' => $request['emp_par_addr_division_id'],
            'par_addr_district_id' => $request['emp_par_addr_district_id'],
            'par_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_par_addr_thana_id']),
            // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
            'par_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_par_addr_union_id']),
            // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
            'par_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_par_addr_village_id']),
            // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
            'par_addr_street'      => $request['emp_par_addr_street'],
        ];

        if ($request->hasFile('emp_photo')) {
            $personalData['photo'] = Common::fileUpload($request->file('emp_photo'), 'hr_employees', $request['emp_id']);
        }
        if ($request->hasFile('emp_nid_signature')) {
            $personalData['nid_signature'] = Common::fileUpload($request->file('emp_nid_signature'), 'hr_employees', $request['emp_id']);
        }
        if ($request->hasFile('emp_signature')) {
            $personalData['signature'] = Common::fileUpload($request->file('emp_signature'), 'hr_employees', $request['emp_id']);
        }
        EmployeePersonalDetails::where('emp_id', $request['emp_id'])->update($personalData);
        /* Update into hr_emp_personal_details */
    }

    public function insertEmpOrganizationData($request){

        $empData = [
            'designation_id' => $request['org_position_id'],
            'department_id'  => $request['org_department'],

            'join_date'      => $request['org_join_date'] != null ? (new DateTime($request['org_join_date']))->format('Y-m-d') : null,
            'permanent_date' => $request['org_permanent_date'] != null ? (new DateTime($request['org_permanent_date']))->format('Y-m-d') : null,
            'prov_period'    => $request['prov_period'],
            'basic_salary'   => $request['org_basic_salary'],
            'org_mobile'     => $request['org_mobile'],
            'org_email'      => $request['org_email'],
        ];
        DB::table('hr_employees')->where('id', $request['emp_id'])->update($empData);

        /* Insert into hr_emp_organization_details */
        $orgData = [
            'emp_id'                   => $request['emp_id'],
            'project_id'               => $request['org_project_id'],
            'project_type_id'          => $request['org_project_type_id'],
            'company_id'               => Common::getCompanyId(),

            'rec_type_id'              => $request['org_rec_type_id'],
            'level'                    => $request['org_level'],
            'grade'                    => $request['org_grade'],
            'step'                     => $request['org_step'],
            'payscal_id'               => $request['org_fiscal_year_id'],
            'salary_structure_id'      => $request['salary_structure_id'],

            'phone_no'                 => $request['org_phone'],
            'fax_no'                   => $request['org_fax'],
            'fiscal_year_id'           => $request['org_fiscal_year_id'],
            'last_inc_date'            => $request['org_last_inc_date'] != null ? (new DateTime($request['org_last_inc_date']))->format('Y-m-d') : null,
            'security_amount'          => $request['org_security_amount'],
            'adv_security_amount'      => $request['org_adv_security_amount'],
            'installment_amount'       => $request['org_installment_amount'],
            'edps_start_month'         => $request['org_edps_start_month'],
            'status'                   => 1,
            'location'                 => $request['org_location'],
            'room_no'                  => $request['org_room_no'],
            'device_id'                => $request['org_device_id'],
            'tot_salary'               => $request['org_tot_salary'],
            'salary_inc_year'          => $request['org_salary_inc_year'],
            'security_amount_location' => $request['org_security_amount_location'],
            'edps_amount'              => $request['org_edps_amount'],
            'edps_lifetime'            => $request['org_edps_lifetime'],
            'no_of_installment'        => $request['org_no_of_installment'],
            'has_house_allowance'      => $request['org_has_house_allowance'],
            'has_travel_allowance'     => $request['org_has_travel_allowance'],
            'has_daily_allowance'      => $request['org_has_daily_allowance'],
            'has_medical_allowance'    => $request['org_has_medical_allowance'],
            'has_utility_allowance'    => $request['org_has_utility_allowance'],
            'has_mobile_allowance'     => $request['org_has_mobile_allowance'],
            'has_welfare_fund'         => $request['org_has_welfare_fund'],
        ];
        EmployeeOrganizationDetails::create($orgData);
        /* Insert into hr_emp_organization_details */

    }

    public function insertEmpAccountData($request, $emp_id = null){

        // dd($request->all(), $emp_id);

        $customAccDataArr = array();

        $setOfBankIds = $request['acc_bank_id'];
        $setOfBranch = $request['acc_bank_branch_id'];

        foreach($setOfBankIds as $bankKey => $bankId){
            foreach($setOfBranch as $branchKey => $branchValue){

                if($setOfBankIds[$bankKey] == null ||  $setOfBranch[$branchKey] == null){
                    continue;
                }

                $customAccDataArr['acc_bank_id'][$bankKey] = $bankId;
                if($bankKey == $branchKey){
                    $isBranchExist = DB::table('hr_bank_branches')->where([['is_delete', 0], ['name', $branchValue]])->count();
                    if( $isBranchExist < 1 ){
                        $branchData = [
                            'bank_id'       => $bankId,
                            'name'        => $branchValue,
                        ];
                        $insertedBranchId = DB::table('hr_bank_branches')->insertGetId($branchData);
                        $customAccDataArr['acc_bank_branch_id'][$branchKey] = $insertedBranchId;

                    }else{
                        $isBranchExistId = DB::table('hr_bank_branches')->where([['is_delete', 0], ['name', $branchValue]])->first();
                        $customAccDataArr['acc_bank_branch_id'][$branchKey] = $isBranchExistId->id;
                    }

                }
            }
        }

        /* Insert into hr_emp_account_details */
        if(isset($customAccDataArr['acc_bank_id'])){
            for ($i = 0; $i < count($customAccDataArr['acc_bank_id']); $i++) {
                $accData = [
                    'emp_id'          => !empty($request['emp_id']) ? $request['emp_id'] : $emp_id,
                    'bank_id'         => $customAccDataArr['acc_bank_id'][$i],
                    'bank_branch_id'  => $customAccDataArr['acc_bank_branch_id'][$i],
                    'bank_acc_type'   => $request['acc_bank_acc_type'][$i],
                    'bank_acc_number' => $request['acc_bank_acc_number'][$i],
                    'status'          => 1,
                ];
                //Insert Here......................
                EmployeeAccountDetails::create($accData);
            }
        }
        /* Insert into hr_emp_account_details */
    }

    public function insertEmpEducationData($request){

        /* Insert into hr_emp_education_details */
        for ($i = 0; $i < count($request['edu_exam_title']); $i++) {
            $eduData = [
                'emp_id'         => $request['emp_id'],
                'exam_title'     => $request['edu_exam_title'][$i],
                'department'     => $request['edu_department'][$i],
                'institute_name' => $request['edu_institute_name'][$i],
                'board'          => $request['edu_board'][$i],
                'res_type'       => $request['edu_res_type'][$i],
                'result'         => $request['edu_result'][$i],
                'res_out_of'     => $request['edu_res_out_of'][$i],
                'passing_year'   => $request['edu_passing_year'][$i],
                'status'         => 1,
            ];
            //Insert Here......................
            EmployeeEducationDetails::create($eduData);
        }
        /* Insert into hr_emp_education_details */
    }

    public function insertEmpTrainingData($request){

        /* Insert into hr_emp_training_details */
        for ($i = 0; $i < count($request['train_title']); $i++) {
            $trainData = [
                'emp_id'        => $request['emp_id'],
                'title'         => $request['train_title'][$i],
                'organizer'     => $request['train_organizer'][$i],
                'country_id'    => $request['train_country_id'][$i],
                'address'       => $request['train_address'][$i],
                'topic'         => $request['train_topic'][$i],
                'training_year' => $request['train_training_year'][$i],
                'duration'      => $request['train_duration'][$i],
                'status'        => 1,
            ];
            //Insert Here......................
            EmployeeTrainingDetails::create($trainData);
        }
        /* Insert into hr_emp_training_details */
    }

    public function insertEmpExperienceData($request){

        /* Insert into hr_emp_experience_details */
        for ($i = 0; $i < count($request['exp_org_name']); $i++) {
            $expData = [
                'emp_id'             => $request['emp_id'],
                'org_name'           => $request['exp_org_name'][$i],
                'org_type'           => $request['exp_org_type'][$i],
                'org_location'       => $request['exp_org_location'][$i],
                'designation'        => $request['exp_designation'][$i],
                'department'         => $request['exp_department'][$i],
                'job_responsibility' => $request['exp_job_responsibility'][$i],
                'area_of_experience' => $request['exp_area_of_experience'][$i],
                'duration'           => $request['exp_duration'][$i],
                'start_date'         => $request['exp_start_date'][$i] != null ? (new DateTime($request['exp_start_date'][$i]))->format('Y-m-d') : null,
                'end_date'           => $request['exp_end_date'][$i] != null ? (new DateTime($request['exp_end_date'][$i]))->format('Y-m-d') : null,
                'address'            => $request['exp_address'][$i],
                'status'             => 1,
            ];
            //Insert Here......................
            EmployeeExperienceDetails::create($expData);
        }
        /* Insert into hr_emp_experience_details */
    }

    public function insertEmpGuarantorData($request){

        /* Insert into hr_emp_guarantor_details(Govt) */
        $govtGuar = [
            'emp_id'          =>  $request['emp_id'],
            'guarantor_type'  => 'Govt',
            'name'            => $request['govt_guar_name'],
            'designation'     => $request['govt_guar_designation'],
            'occupation'      => $request['govt_guar_occupation'],
            'email'           => $request['govt_guar_email'],
            'working_address' => $request['govt_guar_working_address'],
            'par_address'     => $request['govt_guar_par_address'],
            'nid'             => $request['govt_guar_nid'],
            'relation'        => $request['govt_guar_relation'],
            'mobile'          => $request['govt_guar_mobile'],
            'phone'           => $request['govt_guar_phone'],
            'photo'           => ($request->hasFile('govt_guar_photo')) ? Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees',  $request['emp_id']) : null,
            'signature'       => ($request->hasFile('govt_guar_signature')) ? Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees',  $request['emp_id']) : null,
            'status'          => 1,
        ];
        EmployeeGuarantorDetails::create($govtGuar);
        /* Insert into hr_emp_guarantor_details(Govt) */

        /* Insert into hr_emp_guarantor_details(Relative) */
        $relGuar = [
            'emp_id'          =>  $request['emp_id'],
            'guarantor_type'  => 'Relative',
            'name'            => $request['rel_guar_name'],
            'designation'     => $request['rel_guar_designation'],
            'occupation'      => $request['rel_guar_occupation'],
            'email'           => $request['rel_guar_email'],
            'working_address' => $request['rel_guar_working_address'],
            'par_address'     => $request['rel_guar_par_address'],
            'nid'             => $request['rel_guar_nid'],
            'relation'        => $request['rel_guar_relation'],
            'mobile'          => $request['rel_guar_mobile'],
            'phone'           => $request['rel_guar_phone'],
            'photo'           => ($request->hasFile('rel_guar_photo')) ? Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees',  $request['emp_id']) : null,
            'signature'       => ($request->hasFile('rel_guar_signature')) ? Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees',  $request['emp_id']) : null,
            'status'          => 1,
        ];
        EmployeeGuarantorDetails::create($relGuar);
        /* Insert into hr_emp_guarantor_details(Relative) */
    }

    public function insertEmpNomineeData($request){
       /* Insert into hr_emp_nominee_details */
        for ($i = 0; $i < count($request['nom_name']); $i++) {
            $nomData = [
                'emp_id'     => $request['emp_id'],
                'name'       => $request['nom_name'][$i],
                'relation'   => $request['nom_relation'][$i],
                'percentage' => $request['nom_percentage'][$i],
                'nid'        => $request['nom_nid'][$i],
                'address'    => $request['nom_address'][$i],
                'mobile'     => $request['nom_mobile'][$i],
                'photo'      => ($request->hasFile('nom_photo')) ? Common::fileUpload($request->file('nom_photo')[$i], 'hr_employees', $request['emp_id']) : null,
                'signature'  => ($request->hasFile('nom_signature')) ? Common::fileUpload($request->file('nom_signature')[$i], 'hr_employees', $request['emp_id']) : null,
                'status'     => 1,
            ];
            //Insert Here......................
            EmployeeNomineeDetails::create($nomData);
        }
        /* Insert into hr_emp_nominee_details */

    }

    public function insertEmpReferenceData($request){
        /* Insert into hr_emp_reference_details */
        for ($i = 0; $i < count($request['ref_name']); $i++) {
            $refData = [
                'emp_id'          => $request['emp_id'],
                'name'            => $request['ref_name'][$i],
                'designation'     => $request['ref_designation'][$i],
                'relation'        => $request['ref_relation'][$i],
                'nid'             => $request['ref_nid'][$i],
                'mobile'          => $request['ref_mobile'][$i],
                'phone'           => $request['ref_phone'][$i],
                'email'           => $request['ref_email'][$i],
                'occupation'      => $request['ref_occupation'][$i],
                'working_address' => $request['ref_working_address'][$i],
            ];
            //Insert Here......................
            EmployeeReferenceDetails::create($refData);
        }
        /* Insert into hr_emp_reference_details */
    }

    // Add and Store Employee
    public function add(Request $request)
    {
        // dd($request->all());
        if ($request->isMethod('post')) {

            $passport = $this->getPassport($request, 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['errorMsg'],
                    'alert-type' => 'error',
                    'action'     => '',
                );
                return response()->json($notification);
            } elseif ($passport['isValid'] && $passport['action'] == 'next') {

                $emp_id = 0;

                if(isset($request['submittedFrom']) && $request['submittedFrom'] == 'General'){

                    if($request['emp_id'] == 0){

                        $employee_no = Common::generateEmployeeNo();

                        $sysUserId = $this->insertEmpGnlSystemUserData($request, $employee_no);

                        ## insert hr_employee and return inserted id
                        $emp_id = $this->insertEmpGeneralData($request, $employee_no, $sysUserId);
                        ## $request['emp_id'] = return inserted id;
                    }
                    else {

                        $this->UpdateEmpGeneralData($request);
                        $this->updateEmpGnlSystemUserData($request);
                        $emp_id =  $request['emp_id'];
                        ## update hr_employee
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization'){

                    ## update hr_employee

                    $this->insertEmpOrganizationData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account'){
                    $this->insertEmpAccountData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education'){
                    $this->insertEmpEducationData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training'){
                    $this->insertEmpTrainingData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience'){
                    $this->insertEmpExperienceData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor'){
                    $this->insertEmpGuarantorData($request);
                    $emp_id = $request['emp_id'];

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee'){
                    $this->insertEmpNomineeData($request);
                    $emp_id = $request['emp_id'];
                }

                $notification = array(
                    'message'    => $passport['errorMsg'],
                    'alert-type' => 'success',
                    'action'     => 'next',
                    'emp_id'     => $emp_id,
                );
                return response()->json($notification);

            } elseif ($passport['isValid'] && $passport['action'] == 'save') {
                //Save data to database
                DB::beginTransaction();
                try {

                    // dd($request->all());
                    ## update hr_employee
                    /* Update Employee Status */
                    $empData = [
                        'status'  => 1,
                    ];
                    DB::table('hr_employees')->where('id', $request['emp_id'])->update($empData);
                    /* Update Employee Status */

                    ## insert system User Data
                    $sysUserData = [
                        'username' => $request['emp_code'],
                        'password' => Hash::make($request['emp_code']),
                        'is_active'        => 1,
                    ];
                    DB::table('gnl_sys_users')->where('emp_id', $request['emp_id'])->update($sysUserData);

                    ## insert referrence data
                    $this->insertEmpReferenceData($request);


                    DB::commit();
                    $notification = array(
                        'message'    => 'Success',
                        'alert-type' => 'success',
                        'action'     => 'saved',
                    );
                    return response()->json($notification);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $notification = array(
                        'message'    => 'Error...',
                        'alert-type' => 'error',
                        'action'     => '',
                        'aa'         => $e,
                    );
                    return response()->json($notification);
                }
            }
        } else {
            $divisions     = DB::table('gnl_divisions')->where([['is_delete', 0], ['is_active', 1]])->get();
            $banks         = DB::table('hr_banks')->where([['is_delete', 0]])->get();
            $hrConfig      = DB::table('hr_config')->where('title', 'employeeRequiredFields')->get();
            $requiredField = json_decode($hrConfig[0]->content);
            $orgLevel      = DB::table('hr_config')->where('title', 'level')->first();
            $orgGrade      = DB::table('hr_config')->where('title', 'grade')->first();
            $orgDepartment = DB::table('hr_departments')->where([['is_delete', 0], ['is_active', 1]])->get();
            $orgPosition   = DB::table('hr_designations')->where('is_delete', 0)->get();
            $orgFiscalYear = DB::table('gnl_fiscal_year')->where([['is_delete', 0], ['is_active', 1]])->get();
            $orgProject    = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1]])->get();
            $recType       = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get();
            $relation      = DB::table('hr_relationships')->where('status', 1)->get();

            $data = [
                'divisions'     => $divisions,
                'requiredField' => $requiredField,
                'banks'         => $banks,
                'orgLevel'      => $orgLevel,
                'recType'       => $recType,
                'relation'      => $relation,
                'orgGrade'      => $orgGrade,
                'orgDepartment' => $orgDepartment,
                'orgPosition'   => $orgPosition,
                'orgFiscalYear' => $orgFiscalYear,
                'orgProject'    => $orgProject,
                'hasPayRoll'    => $this->hasPayRoll(),
            ];

            return view('HR.Employee.add', compact('data'));
        }
    }

    // Edit Employee
    public function edit(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {
            //dd($request->all());
            $passport = $this->getPassport($request, 'edit');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['errorMsg'],
                    'alert-type' => 'error',
                    'action'     => '',
                );
                return response()->json($notification);
            } elseif ($passport['isValid'] && $passport['action'] == 'next') {

                $decId = decrypt($id);
                $edata = Employee::where('id', $decId)->first();

                if(isset($request['submittedFrom']) && $request['submittedFrom'] == 'General'){
                    $empData = [
                        'branch_id' => $request['branch_id'],
                        'emp_name' => $request['emp_name_eng'],
                        'gender'         => $request['emp_gender'],
                    ];
                    $edata->update($empData);

                    $personalData = [
                        'emp_name_bn'          => $request['emp_name_ban'],
                        'father_name_en'       => $request['emp_fathers_name_eng'],
                        'father_name_bn'       => $request['emp_fathers_name_ban'],
                        'mother_name_en'       => $request['emp_mothers_name_eng'],
                        'mother_name_bn'       => $request['emp_mothers_name_ban'],
                        'spouse_name_en'       => $request['emp_spouse_name_en'],
                        'spouse_name_bn'       => $request['emp_spouse_name_bn'],

                        // 'gender'               => $request['emp_gender'],
                        'dob'                  => (new DateTime($request['emp_dob']))->format('Y-m-d'),
                        'nid_no'               => $request['emp_nid_no'],
                        'driving_license_no'   => $request['emp_driving_license_no'],
                        'marital_status'       => $request['emp_marital_status'],
                        'num_of_children'      => $request['emp_children'],
                        'religion'             => $request['emp_religion'],
                        'blood_group'          => $request['emp_blood_group'],
                        'birth_certificate_no' => $request['emp_birth_certificate_no'],
                        'passport_no'          => $request['emp_passport_no'],
                        'tin_no'               => $request['emp_tin_no'],
                        'phone_no'             => $request['emp_phone_no'],
                        'mobile_no'            => $request['emp_mobile_no'],
                        'email'                => $request['emp_email'],

                        // 'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
                        // 'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
                        // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
                        // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
                        // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
                        // 'pre_addr_street'      => $request['emp_pre_addr_street'],
                        // 'par_addr_division_id' => $request['emp_par_addr_division_id'],
                        // 'par_addr_district_id' => $request['emp_par_addr_district_id'],
                        // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
                        // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
                        // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
                        // 'par_addr_street'      => $request['emp_par_addr_street'],

                        'pre_addr_division_id' => $request['emp_pre_addr_division_id'],
                        'pre_addr_district_id' => $request['emp_pre_addr_district_id'],
                        'pre_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_pre_addr_thana_id']),
                        // 'pre_addr_thana_id'    => $request['emp_pre_addr_thana_id'],
                        'pre_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_pre_addr_union_id']),
                        // 'pre_addr_union_id'    => $request['emp_pre_addr_union_id'],
                        'pre_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_pre_addr_village_id']),
                        // 'pre_addr_village_id'  => $request['emp_pre_addr_village_id'],
                        'pre_addr_street'      => $request['emp_pre_addr_street'],

                        'par_addr_division_id' => $request['emp_par_addr_division_id'],
                        'par_addr_district_id' => $request['emp_par_addr_district_id'],
                        'par_addr_thana_id'    => $this->getIdForAddressData('gnl_upazilas', 'upazila_name', $request['emp_par_addr_thana_id']),
                        // 'par_addr_thana_id'    => $request['emp_par_addr_thana_id'],
                        'par_addr_union_id'    => $this->getIdForAddressData('gnl_unions', 'union_name', $request['emp_par_addr_union_id']),
                        // 'par_addr_union_id'    => $request['emp_par_addr_union_id'],
                        'par_addr_village_id'  => $this->getIdForAddressData('gnl_villages', 'village_name', $request['emp_par_addr_village_id']),
                        // 'par_addr_village_id'  => $request['emp_par_addr_village_id'],
                        'par_addr_street'      => $request['emp_par_addr_street'],
                    ];

                    if ($request->hasFile('emp_photo')) {
                        $personalData['photo'] = Common::fileUpload($request->file('emp_photo'), 'hr_employees', $id);
                    }
                    if ($request->hasFile('emp_nid_signature')) {
                        $personalData['nid_signature'] = Common::fileUpload($request->file('emp_nid_signature'), 'hr_employees', $id);
                    }
                    if ($request->hasFile('emp_signature')) {
                        $personalData['signature'] = Common::fileUpload($request->file('emp_signature'), 'hr_employees', $id);
                    }
                    EmployeePersonalDetails::where('emp_id', $decId)->update($personalData);

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization'){

                    $empData = [
                        'designation_id' => $request['org_position_id'],
                        'department_id'  => $request['org_department'],

                        'join_date'      => $request['org_join_date'] != null ? (new DateTime($request['org_join_date']))->format('Y-m-d') : null,
                        'permanent_date' => $request['org_permanent_date'] != null ? (new DateTime($request['org_permanent_date']))->format('Y-m-d') : null,
                        'basic_salary'   => $request['org_basic_salary'],
                        'prov_period'    => $request['prov_period'],
                        'org_mobile'     => $request['org_mobile'],
                        'org_email'      => $request['org_email'],
                    ];
                    $edata->update($empData);

                    $orgData = [
                        'emp_id'                   => $decId,
                        'project_id'               => $request['org_project_id'],
                        'project_type_id'          => $request['org_project_type_id'],
                        'company_id'               => Common::getCompanyId(),

                        'rec_type_id'              => $request['org_rec_type_id'],
                        'level'                    => $request['org_level'],
                        'grade'                    => $request['org_grade'],
                        'step'                     => $request['org_step'],
                        'payscal_id'               => $request['org_fiscal_year_id'],
                        'salary_structure_id'      => $request['salary_structure_id'],

                        'phone_no'                 => $request['org_phone'],
                        'fax_no'                   => $request['org_fax'],
                        'fiscal_year_id'           => $request['org_fiscal_year_id'],
                        'last_inc_date'            => $request['org_last_inc_date'] != null ? (new DateTime($request['org_last_inc_date']))->format('Y-m-d') : null,
                        'security_amount'          => $request['org_security_amount'],
                        'adv_security_amount'      => $request['org_adv_security_amount'],
                        'installment_amount'       => $request['org_installment_amount'],
                        'edps_start_month'         => $request['org_edps_start_month'],
                        'status'                   => 1,
                        'location'                 => $request['org_location'],
                        'room_no'                  => $request['org_room_no'],
                        'device_id'                => $request['org_device_id'],
                        'tot_salary'               => $request['org_tot_salary'],
                        'salary_inc_year'          => $request['org_salary_inc_year'],
                        'security_amount_location' => $request['org_security_amount_location'],
                        'edps_amount'              => $request['org_edps_amount'],
                        'edps_lifetime'            => $request['org_edps_lifetime'],
                        'no_of_installment'        => $request['org_no_of_installment'],
                        'has_house_allowance'      => $request['org_has_house_allowance'],
                        'has_travel_allowance'     => $request['org_has_travel_allowance'],
                        'has_daily_allowance'      => $request['org_has_daily_allowance'],
                        'has_medical_allowance'    => $request['org_has_medical_allowance'],
                        'has_utility_allowance'    => $request['org_has_utility_allowance'],
                        'has_mobile_allowance'     => $request['org_has_mobile_allowance'],
                        'has_welfare_fund'         => $request['org_has_welfare_fund'],
                    ];
                    EmployeeOrganizationDetails::where('emp_id', $decId)->delete();
                    EmployeeOrganizationDetails::create($orgData);

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account'){

                    // ss($request->all());

                    EmployeeAccountDetails::where('emp_id', $decId)->delete();
                    $this->insertEmpAccountData($request, $decId);

                    // EmployeeAccountDetails::where('emp_id', $decId)->delete();
                    // for ($i = 0; $i < count($request['acc_bank_id']); $i++) {
                    //     $accData = [
                    //         'emp_id'          => $decId,
                    //         'bank_id'         => $request['acc_bank_id'][$i],
                    //         'bank_branch_id'  => $request['acc_bank_branch_id'][$i],
                    //         'bank_acc_type'   => $request['acc_bank_acc_type'][$i],
                    //         'bank_acc_number' => $request['acc_bank_acc_number'][$i],
                    //     ];
                    //     //Insert Here......................
                    //     EmployeeAccountDetails::create($accData);
                    // }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education'){
                    EmployeeEducationDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['edu_exam_title']); $i++) {
                        $eduData = [
                            'emp_id'         => $decId,
                            'exam_title'     => $request['edu_exam_title'][$i],
                            'department'     => $request['edu_department'][$i],
                            'institute_name' => $request['edu_institute_name'][$i],
                            'board'          => $request['edu_board'][$i],
                            'res_type'       => $request['edu_res_type'][$i],
                            'result'         => $request['edu_result'][$i],
                            'res_out_of'     => $request['edu_res_out_of'][$i],
                            'passing_year'   => $request['edu_passing_year'][$i],
                        ];
                        //Insert Here......................
                        EmployeeEducationDetails::create($eduData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training'){
                    EmployeeTrainingDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['train_title']); $i++) {
                        $trainData = [
                            'emp_id'        => $decId,
                            'title'         => $request['train_title'][$i],
                            'organizer'     => $request['train_organizer'][$i],
                            'country_id'    => $request['train_country_id'][$i],
                            'address'       => $request['train_address'][$i],
                            'topic'         => $request['train_topic'][$i],
                            'training_year' => $request['train_training_year'][$i],
                            'duration'      => $request['train_duration'][$i],
                        ];
                        //Insert Here......................
                        EmployeeTrainingDetails::create($trainData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience'){
                    EmployeeExperienceDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['exp_org_name']); $i++) {
                        $expData = [
                            'emp_id'             => $decId,
                            'org_name'           => $request['exp_org_name'][$i],
                            'org_type'           => $request['exp_org_type'][$i],
                            'org_location'       => $request['exp_org_location'][$i],
                            'designation'        => $request['exp_designation'][$i],
                            'department'         => $request['exp_department'][$i],
                            'job_responsibility' => $request['exp_job_responsibility'][$i],
                            'area_of_experience' => $request['exp_area_of_experience'][$i],
                            'duration'           => $request['exp_duration'][$i],
                            'start_date'         => $request['exp_start_date'][$i] != null ? (new DateTime($request['exp_start_date'][$i]))->format('Y-m-d') : null,
                            'end_date'           => $request['exp_end_date'][$i] != null ? (new DateTime($request['exp_end_date'][$i]))->format('Y-m-d') : null,
                            'address'            => $request['exp_address'][$i],
                        ];
                        //Insert Here......................
                        EmployeeExperienceDetails::create($expData);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor'){
                    $govtGuar = [
                        'emp_id'          => $decId,
                        'guarantor_type'  => 'Govt',
                        'name'            => $request['govt_guar_name'],
                        'designation'     => $request['govt_guar_designation'],
                        'occupation'      => $request['govt_guar_occupation'],
                        'email'           => $request['govt_guar_email'],
                        'working_address' => $request['govt_guar_working_address'],
                        'par_address'     => $request['govt_guar_par_address'],
                        'nid'             => $request['govt_guar_nid'],
                        'relation'        => $request['govt_guar_relation'],
                        'mobile'          => $request['govt_guar_mobile'],
                        'phone'           => $request['govt_guar_phone'],
                        //'photo' => ($request->hasFile('govt_guar_photo')) ? Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId) : null,
                        //'signature' => ($request->hasFile('govt_guar_signature')) ? Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId) : null,
                    ];
                    if ($request->hasFile('govt_guar_photo')) {
                        $govtGuar['photo'] = Common::fileUpload($request->file('govt_guar_photo'), 'hr_employees', $decId);
                    }
                    if ($request->hasFile('govt_guar_signature')) {
                        $govtGuar['signature'] = Common::fileUpload($request->file('govt_guar_signature'), 'hr_employees', $decId);
                    }
                    if ($request['govtGuarId'] !== null) {
                        EmployeeGuarantorDetails::where('id', $request['govtGuarId'])->update($govtGuar);
                    } else {
                        EmployeeGuarantorDetails::create($govtGuar);
                    }

                    $relGuar = [
                        'emp_id'          => $decId,
                        'guarantor_type'  => 'Relative',
                        'name'            => $request['rel_guar_name'],
                        'designation'     => $request['rel_guar_designation'],
                        'occupation'      => $request['rel_guar_occupation'],
                        'email'           => $request['rel_guar_email'],
                        'working_address' => $request['rel_guar_working_address'],
                        'par_address'     => $request['rel_guar_par_address'],
                        'nid'             => $request['rel_guar_nid'],
                        'relation'        => $request['rel_guar_relation'],
                        'mobile'          => $request['rel_guar_mobile'],
                        'phone'           => $request['rel_guar_phone'],
                        //'photo' => ($request->hasFile('rel_guar_photo')) ? Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId) : null,
                        //'signature' => ($request->hasFile('rel_guar_signature')) ? Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId) : null,
                    ];
                    if ($request->hasFile('rel_guar_photo')) {
                        $relGuar['photo'] = Common::fileUpload($request->file('rel_guar_photo'), 'hr_employees', $decId);
                    }
                    if ($request->hasFile('rel_guar_signature')) {
                        $relGuar['signature'] = Common::fileUpload($request->file('rel_guar_signature'), 'hr_employees', $decId);
                    }
                    if ($request['relGuarId'] !== null) {
                        EmployeeGuarantorDetails::where('id', $request['relGuarId'])->update($relGuar);
                    } else {
                        EmployeeGuarantorDetails::create($relGuar);
                    }

                }elseif(isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee'){
                    $empNomDet = EmployeeNomineeDetails::where('emp_id', $decId)->get();
                    $nomId     = $empNomDet->pluck('', 'id');

                    for ($i = 0; $i < count($request['nom_name']); $i++) {
                        $nomData = [];
                        $nomData = [
                            'emp_id'     => $decId,
                            'name'       => $request['nom_name'][$i],
                            'relation'   => $request['nom_relation'][$i],
                            'percentage' => $request['nom_percentage'][$i],
                            'nid'        => $request['nom_nid'][$i],
                            'address'    => $request['nom_address'][$i],
                            'mobile'     => $request['nom_mobile'][$i],
                        ];
                        //Check files
                        if ($request->file('nom_photo')[$i]->getClientOriginalName() != 'not_file') {
                            $nomData['photo'] = Common::fileUpload($request->file('nom_photo')[$i], 'hr_employees', $decId);
                        }
                        if ($request->file('nom_signature')[$i]->getClientOriginalName() != 'not_file') {
                            $nomData['signature'] = Common::fileUpload($request->file('nom_signature')[$i], 'hr_employees', $decId);
                        }

                        if ($request['nomId'][$i] == null) {
                            EmployeeNomineeDetails::create($nomData);
                        } else {
                            EmployeeNomineeDetails::where('id', $request['nomId'][$i])->update($nomData);
                            $nomId[$request['nomId'][$i]] = 1;
                        }
                    }
                    foreach ($nomId as $key => $value) {
                        if ($value == null) {
                            //Delete data
                            EmployeeNomineeDetails::where('id', $key)->delete();
                        }
                    }

                }

                $notification = array(
                    'message'    => $passport['errorMsg'],
                    'alert-type' => 'success',
                    'action'     => 'next',
                    'emp_id'     => $decId,
                );
                return response()->json($notification);
            } elseif ($passport['isValid'] && $passport['action'] == 'save') {

                //Save data to database
                // DB::beginTransaction();
                try {
                    $decId = decrypt($id);

                    $edata = Employee::where('id', $decId)->first();
                    $empData = [
                        'status' => 1,
                    ];
                    $edata->update($empData);

                    // dd($decId, $request->all());

                    ## insert system User Data
                    $sysUserData = [
                        'username' => $request['emp_code'],
                        'password' => Hash::make($request['emp_code']),
                        'is_active'        => 1,
                    ];
                    DB::table('gnl_sys_users')->where('emp_id', $decId)->update($sysUserData);

                    EmployeeReferenceDetails::where('emp_id', $decId)->delete();
                    for ($i = 0; $i < count($request['ref_name']); $i++) {
                        $refData = [
                            'emp_id'          => $decId,
                            'name'            => $request['ref_name'][$i],
                            'designation'     => $request['ref_designation'][$i],
                            'relation'        => $request['ref_relation'][$i],
                            'nid'             => $request['ref_nid'][$i],
                            'mobile'          => $request['ref_mobile'][$i],
                            'phone'           => $request['ref_phone'][$i],
                            'email'           => $request['ref_email'][$i],
                            'occupation'      => $request['ref_occupation'][$i],
                            'working_address' => $request['ref_working_address'][$i],
                        ];
                        //Insert Here......................
                        EmployeeReferenceDetails::create($refData);
                    }
                    // DB::commit();
                    $notification = array(
                        'message'    => 'Success',
                        'alert-type' => 'success',
                        'action'     => 'saved',
                    );
                    return response()->json($notification);
                } catch (\Exception $e) {
                    // DB::rollBack();
                    $notification = array(
                        'message'    => 'Error...',
                        'alert-type' => 'error',
                        'action'     => '',
                        'aa'         => $e,
                    );
                    return response()->json($notification);
                }
            }
        } else {
            $emp        = Employee::where('id', decrypt($id))->first();

            $empPerData = EmployeePersonalDetails::from('hr_emp_personal_details as empdt')
            ->where('empdt.emp_id', $emp->id)
            ->leftJoin('gnl_upazilas as preUp', 'empdt.pre_addr_thana_id', '=','preUp.id')
            ->leftJoin('gnl_upazilas as parUp', 'empdt.par_addr_thana_id', '=','parUp.id')
            ->leftJoin('gnl_unions as preUn', 'empdt.pre_addr_union_id', '=','preUn.id')
            ->leftJoin('gnl_unions as parUn', 'empdt.par_addr_union_id', '=','parUn.id')
            ->leftJoin('gnl_villages as preVil', 'empdt.pre_addr_village_id', '=','preVil.id')
            ->leftJoin('gnl_villages as parVil', 'empdt.par_addr_village_id', '=','parVil.id')
            ->select(
                'empdt.*',
                'preUp.upazila_name as pre_addr_thana_name',
                'parUp.upazila_name as par_addr_thana_name',
                'preUn.union_name as pre_addr_union_name',
                'parUn.union_name as par_addr_union_name',
                'preVil.village_name as pre_addr_village_name',
                'parVil.village_name as par_addr_village_name'
            )
            ->get();

            // dd($emp, $empPerData, decrypt($id));


            $divisions = GnlService::getDivisions();
            // get present address
            $filters['division_id'] = (isset($empPerData[0]['pre_addr_division_id'])) ? $empPerData[0]['pre_addr_division_id'] : 0;
            $filters['district_id'] = (isset($empPerData[0]['pre_addr_district_id'])) ? $empPerData[0]['pre_addr_district_id'] : 0;
            $filters['upazila_id']  = (isset($empPerData[0]['pre_addr_thana_id'])) ? $empPerData[0]['pre_addr_thana_id'] : 0;
            $filters['union_id']    = (isset($empPerData[0]['pre_addr_union_id'])) ? $empPerData[0]['pre_addr_union_id'] : 0;

            $preAddress['districts'] = GnlService::getDistricts($filters);
            $preAddress['upazilas']  = GnlService::getUpazilas($filters);
            $preAddress['unions']    = GnlService::getUnions($filters);
            $preAddress['villages']  = GnlService::getVillages($filters);

            // get permanent address
            $filters['division_id'] = (isset($empPerData[0]['par_addr_division_id'])) ? $empPerData[0]['par_addr_division_id'] : 0;
            $filters['district_id'] = (isset($empPerData[0]['par_addr_district_id'])) ? $empPerData[0]['par_addr_district_id'] : 0;
            $filters['upazila_id']  = (isset($empPerData[0]['par_addr_thana_id'])) ? $empPerData[0]['par_addr_thana_id'] : 0;
            $filters['union_id']    = (isset($empPerData[0]['par_addr_union_id'])) ? $empPerData[0]['par_addr_union_id'] : 0;

            $perAddress['districts'] = GnlService::getDistricts($filters);
            $perAddress['upazilas']  = GnlService::getUpazilas($filters);
            $perAddress['unions']    = GnlService::getUnions($filters);
            $perAddress['villages']  = GnlService::getVillages($filters);

            $sameAsPreesentAddress = false;
            if (
                isset($empPerData[0]) && $empPerData[0]['pre_addr_division_id'] == $empPerData[0]['par_addr_division_id']
                && $empPerData[0]['pre_addr_district_id'] == $empPerData[0]['par_addr_district_id']
                && $empPerData[0]['pre_addr_thana_id'] == $empPerData[0]['par_addr_thana_id']
                && $empPerData[0]['pre_addr_union_id'] == $empPerData[0]['par_addr_union_id']
                && $empPerData[0]['pre_addr_village_id'] == $empPerData[0]['par_addr_village_id']
                && $empPerData[0]['pre_addr_street'] == $empPerData[0]['par_addr_street']
            ) {
                $sameAsPreesentAddress = true;
            }

            $empOrgData     = EmployeeOrganizationDetails::where('emp_id', $emp->id)->get();

            $empAccData     = EmployeeAccountDetails::from('hr_emp_account_details as empacc')
            ->join('hr_bank_branches as hrb', 'empacc.bank_branch_id', '=','hrb.id')
            ->where([['empacc.emp_id', $emp->id], ['hrb.is_delete', 0]])
            ->select('empacc.*', 'hrb.name as bank_branch_name')
            ->get();

            $empEduData     = EmployeeEducationDetails::where('emp_id', $emp->id)->get();
            $empTrainData   = EmployeeTrainingDetails::where('emp_id', $emp->id)->get();
            $empExpData     = EmployeeExperienceDetails::where('emp_id', $emp->id)->get();
            $empGuarData    = EmployeeGuarantorDetails::where('emp_id', $emp->id)->get();
            $empNomData     = EmployeeNomineeDetails::where('emp_id', $emp->id)->get();
            $empRefData     = EmployeeReferenceDetails::where('emp_id', $emp->id)->get();
            $empSysUserData = SysUser::where('id', $emp->user_id)->get();

            $empData = [
                'emp'            => $emp,
                'empPerData'     => $empPerData,
                'empOrgData'     => $empOrgData,
                'empAccData'     => $empAccData,
                'empEduData'     => $empEduData,
                'empTrainData'   => $empTrainData,
                'empExpData'     => $empExpData,
                'empGuarData'    => $empGuarData,
                'empNomData'     => $empNomData,
                'empRefData'     => $empRefData,
                'empSysUserData' => $empSysUserData,
            ];

            $banks         = DB::table('hr_banks')->where([['is_delete', 0]])->get();
            $hrConfig      = DB::table('hr_config')->where('title', 'employeeRequiredFields')->get();
            $requiredField = json_decode($hrConfig[0]->content);
            $orgLevel      = DB::table('hr_config')->where('title', 'level')->first();
            $orgGrade      = DB::table('hr_config')->where('title', 'grade')->first();
            $orgDepartment = DB::table('hr_departments')->where([['is_delete', 0], ['is_active', 1]])->get();
            $orgPosition   = DB::table('hr_designations')->where('is_delete', 0)->get();
            $orgFiscalYear = DB::table('gnl_fiscal_year')->where([['is_delete', 0], ['is_active', 1]])->get();
            $orgProject    = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1]])->get();
            $recType       = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get();
            $relation      = DB::table('hr_relationships')->where('status', 1)->get();

            $data = [
                'divisions'             => $divisions,
                'requiredField'         => $requiredField,
                'banks'                 => $banks,
                'orgLevel'              => $orgLevel,
                'orgGrade'              => $orgGrade,
                'recType'               => $recType,
                'relation'              => $relation,
                'orgDepartment'         => $orgDepartment,
                'orgPosition'           => $orgPosition,
                'orgFiscalYear'         => $orgFiscalYear,
                'orgProject'            => $orgProject,
                'preAddress'            => $preAddress,
                'perAddress'            => $perAddress,
                'sameAsPreesentAddress' => $sameAsPreesentAddress,
                'hasPayRoll'            => $this->hasPayRoll(),
                'editPage'              => true,
            ];

            return view('HR.Employee.edit', compact('data', 'empData'));
        }
    }

    public function getPassport($request, $operationType = null)
    {

        // dd($request->all(), $operationType);
        if($operationType == 'draft' && $request['submittedFrom'] == 'Reference'){
            return array(
                'isValid'  => true,
                'errorMsg' => null,
                'action'   => 'save',
            );
        }elseif($operationType == 'draft' && $request['submittedFrom'] != 'Reference'){
            return array(
                'isValid'  => true,
                'errorMsg' => null,
                'action'   => 'next',
            );
        }

        $hrConfig                  = DB::table('hr_config')->where('title', 'employeeRequiredFields')->get();
        $hrConfigObj               = json_decode($hrConfig[0]->content);
        $GLOBALS['requiredFields'] = $hrConfigObj;

        $requiredField = [];
        $attributes    = [];
        $submitFrom    = '';
        if (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Reference') {
            $submitFrom    = 'Reference';
            $requiredField = [
                'ref_name'              => $this->isRequired('ref_name') . '|array|min:1',
                'ref_designation'       => $this->isRequired('ref_designation') . '|array|min:1',
                'ref_relation'          => $this->isRequired('ref_relation') . '|array|min:1',
                'ref_nid'               => $this->isRequired('ref_nid') . '|array|min:1',
                'ref_mobile'            => $this->isRequired('ref_mobile') . '|array|min:1',
                'ref_phone'             => $this->isRequired('ref_phone') . '|array|min:1',
                'ref_email'             => $this->isRequired('ref_email') . '|array|min:1',
                'ref_occupation'        => $this->isRequired('ref_occupation') . '|array|min:1',
                'ref_working_address'   => $this->isRequired('ref_working_address') . '|array|min:1',

                'ref_name.*'            => $this->isRequired('ref_name'),
                'ref_designation.*'     => $this->isRequired('ref_designation'),
                'ref_relation.*'        => $this->isRequired('ref_relation'),
                'ref_nid.*'             => $this->isRequired('ref_nid'),
                'ref_mobile.*'          => $this->isRequired('ref_mobile'),
                'ref_phone.*'           => $this->isRequired('ref_phone'),
                'ref_email.*'           => $this->isRequired('ref_email'),
                'ref_occupation.*'      => $this->isRequired('ref_occupation'),
                'ref_working_address.*' => $this->isRequired('ref_working_address'),
            ];

            if ($operationType == 'store') {
                $requiredField['branch_id'] = 'required';
            }
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'General') {
            $submitFrom    = 'General';

            $requiredField = [
                // 'emp_code'                 => $this->isRequired('emp_code'),
                'emp_name_eng'             => $this->isRequired('emp_name_eng'),
                'emp_name_ban'             => $this->isRequired('emp_name_ban'),
                'emp_fathers_name_eng'     => $this->isRequired('emp_fathers_name_eng'),
                'emp_fathers_name_ban'     => $this->isRequired('emp_fathers_name_ban'),
                'emp_mothers_name_eng'     => $this->isRequired('emp_mothers_name_eng'),
                'emp_mothers_name_ban'     => $this->isRequired('emp_mothers_name_ban'),

                'spouse_name_eng'          => $this->isRequired('emp_spouse_name_eng'),
                'spouse_name_ban'          => $this->isRequired('emp_spouse_name_ban'),

                'emp_gender'               => $this->isRequired('emp_gender'),
                'emp_dob'                  => $this->isRequired('emp_dob'),
                'emp_nid_no'               => $this->isRequired('emp_nid_no'),
                'emp_marital_status'       => $this->isRequired('emp_marital_status'),
                'num_of_children'          => $this->isRequired('emp_children'),
                'emp_religion'             => $this->isRequired('emp_religion'),
                'emp_blood_group'          => $this->isRequired('emp_blood_group'),
                'emp_birth_certificate_no' => $this->isRequired('emp_birth_certificate_no'),
                'emp_passport_no'          => $this->isRequired('emp_passport_no'),
                'emp_tin_no'               => $this->isRequired('emp_tin_no'),
                'emp_phone_no'             => $this->isRequired('emp_phone_no'),
                'emp_mobile_no'            => $this->isRequired('emp_mobile_no'),
                'emp_email'                => $this->isRequired('emp_email'),
                'emp_pre_addr_division_id' => $this->isRequired('emp_pre_addr_division_id'),
                'emp_pre_addr_district_id' => $this->isRequired('emp_pre_addr_district_id'),
                'emp_pre_addr_thana_id'    => $this->isRequired('emp_pre_addr_thana_id'),
                'emp_pre_addr_union_id'    => $this->isRequired('emp_pre_addr_union_id'),
                'emp_pre_addr_village_id'  => $this->isRequired('emp_pre_addr_village_id'),
                'emp_pre_addr_street'      => $this->isRequired('emp_pre_addr_street'),
                'emp_par_addr_division_id' => $this->isRequired('emp_par_addr_division_id'),
                'emp_par_addr_district_id' => $this->isRequired('emp_par_addr_district_id'),
                'emp_par_addr_thana_id'    => $this->isRequired('emp_par_addr_thana_id'),
                'emp_par_addr_union_id'    => $this->isRequired('emp_par_addr_union_id'),
                'emp_par_addr_village_id'  => $this->isRequired('emp_par_addr_village_id'),
                'emp_par_addr_street'      => $this->isRequired('emp_par_addr_street'),

                // 'emp_photo'                => ($operationType == 'edit') ? 'nullable' : (($request->hasFile('emp_photo')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_nid_signature')),
                // 'emp_nid_signature'        => ($operationType == 'edit') ? 'nullable' : (($request->hasFile('emp_nid_signature')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_photo')),
                // 'emp_signature'            => ($operationType == 'edit') ? 'nullable' : (($request->hasFile('emp_signature')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_signature')),



                /* 'emp_photo' => ($request->hasFile('emp_photo')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_nid_signature'),
                'emp_nid_signature' => ($request->hasFile('emp_nid_signature')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_photo'),
                'emp_signature' => ($request->hasFile('emp_signature')) ? 'mimes:jpeg,bmp,png,gif,svg' : $this->isRequired('emp_signature'), */
            ];

        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Organization') {
            $submitFrom = 'Organization';

            $hasPayRoll = $this->hasPayRoll();


            $requiredField = [

                'org_project_id'               => $this->isRequired('org_project_id'),
                'org_project_type_id'          => $this->isRequired('org_project_type_id'),
                // 'org_position_id'              => $this->isRequired('org_position_id'),
                'org_join_date'                => $this->isRequired('org_join_date'),
                'org_basic_salary'             => ($hasPayRoll) ? $this->isRequired('org_basic_salary') : "nullable",
                'org_mobile'                   => $this->isRequired('org_mobile'),
                'org_email'                    => $this->isRequired('org_email'),
                'org_rec_type_id'              => $this->isRequired('org_rec_type_id'),
                //'org_level' => $this->isRequired('org_level'),
                'org_level'                    => ($hasPayRoll) ? $this->isRequired('org_level') : "nullable",
                'org_phone'                    => $this->isRequired('org_phone'),
                'org_fax'                      => $this->isRequired('org_fax'),
                'org_location'                 => $this->isRequired('org_location'),
                'org_department'               => $this->isRequired('org_department'),
                'org_room_no'                  => $this->isRequired('org_room_no'),
                'org_device_id'                => $this->isRequired('org_device_id'),

                'org_fiscal_year_id'           => ($hasPayRoll) ? $this->isRequired('org_fiscal_year_id') : "nullable",
                'org_grade'                    => ($hasPayRoll) ? $this->isRequired('org_grade') : "nullable",
                'org_step'                    => ($hasPayRoll) ? $this->isRequired('org_step') : "nullable",


            ];
            if ($operationType == 'store') {
                $requiredField['org_position_id'] = $this->isRequired('org_position_id');
            }
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Account') {
            $submitFrom = 'Account';
            $requiredField = [
                'acc_bank_id'         => $this->isRequired('acc_bank_id'),
                'acc_bank_branch_id'  => $this->isRequired('acc_bank_branch_id'),
                'acc_bank_acc_type'   => $this->isRequired('acc_bank_acc_type'),
                'acc_bank_acc_number' => $this->isRequired('acc_bank_acc_number'),
            ];
            $attributes = [];
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Education') {
            $submitFrom    = 'Education';
            $requiredField = [
                'edu_exam_title'       => $this->isRequired('edu_exam_title') . '|array|min:1',
                'edu_department'       => $this->isRequired('edu_department') . '|array|min:1',
                'edu_institute_name'   => $this->isRequired('edu_institute_name') . '|array|min:1',
                'edu_board'            => $this->isRequired('edu_board') . '|array|min:1',
                'edu_res_type'         => $this->isRequired('edu_res_type') . '|array|min:1',
                'edu_result'           => $this->isRequired('edu_result') . '|array|min:1',
                'edu_res_out_of'       => $this->isRequired('edu_res_out_of') . '|array|min:1',
                'edu_passing_year'     => $this->isRequired('edu_passing_year') . '|array|min:1',

                'edu_exam_title.*'     => $this->isRequired('edu_exam_title'),
                'edu_department.*'     => $this->isRequired('edu_department'),
                'edu_institute_name.*' => $this->isRequired('edu_institute_name'),
                'edu_board.*'          => $this->isRequired('edu_board'),
                'edu_res_type.*'       => $this->isRequired('edu_res_type'),
                'edu_result.*'         => $this->isRequired('edu_result'),
                'edu_res_out_of.*'     => $this->isRequired('edu_res_out_of'),
                'edu_passing_year.*'   => $this->isRequired('edu_passing_year'),
            ];
            $attributes = [];
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Training') {
            $submitFrom    = 'Training';
            $requiredField = [
                'train_title'           => $this->isRequired('train_title') . '|array|min:1',
                'train_organizer'       => $this->isRequired('train_organizer') . '|array|min:1',
                'train_country_id'      => $this->isRequired('train_country_id') . '|array|min:1',
                'train_address'         => $this->isRequired('train_address') . '|array|min:1',
                'train_topic'           => $this->isRequired('train_topic') . '|array|min:1',
                'train_training_year'   => $this->isRequired('train_training_year') . '|array|min:1',
                'train_duration'        => $this->isRequired('train_duration') . '|array|min:1',

                'train_title.*'         => $this->isRequired('train_title'),
                'train_organizer.*'     => $this->isRequired('train_organizer'),
                'train_country_id.*'    => $this->isRequired('train_country_id'),
                'train_address.*'       => $this->isRequired('train_address'),
                'train_topic.*'         => $this->isRequired('train_topic'),
                'train_training_year.*' => $this->isRequired('train_training_year'),
                'train_duration.*'      => $this->isRequired('train_duration'),
            ];
            $attributes = [];
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Experience') {
            $submitFrom    = 'Experience';
            $requiredField = [
                'exp_org_name'             => $this->isRequired('exp_org_name') . '|array|min:1',
                'exp_org_type'             => $this->isRequired('exp_org_type') . '|array|min:1',
                'exp_org_location'         => $this->isRequired('exp_org_location') . '|array|min:1',
                'exp_designation'          => $this->isRequired('exp_designation') . '|array|min:1',
                'exp_department'           => $this->isRequired('exp_department') . '|array|min:1',
                'exp_job_responsibility'   => $this->isRequired('exp_job_responsibility') . '|array|min:1',
                'exp_area_of_experience'   => $this->isRequired('exp_area_of_experience') . '|array|min:1',
                'exp_duration'             => $this->isRequired('exp_duration') . '|array|min:1',
                'exp_start_date'           => $this->isRequired('exp_start_date') . '|array|min:1',
                'exp_end_date'             => $this->isRequired('exp_end_date') . '|array|min:1',
                'exp_address'              => $this->isRequired('exp_address') . '|array|min:1',

                'exp_org_name.*'           => $this->isRequired('exp_org_name'),
                'exp_org_type.*'           => $this->isRequired('exp_org_type'),
                'exp_org_location.*'       => $this->isRequired('exp_org_location'),
                'exp_designation.*'        => $this->isRequired('exp_designation'),
                'exp_department.*'         => $this->isRequired('exp_department'),
                'exp_job_responsibility.*' => $this->isRequired('exp_job_responsibility'),
                'exp_area_of_experience.*' => $this->isRequired('exp_area_of_experience'),
                'exp_duration.*'           => $this->isRequired('exp_duration'),
                'exp_start_date.*'         => $this->isRequired('exp_start_date'),
                'exp_end_date.*'           => $this->isRequired('exp_end_date'),
                'exp_address.*'            => $this->isRequired('exp_address'),
            ];
            $attributes = [];
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Guarantor') {
            $submitFrom    = 'Guarantor';
            $requiredField = [
                'govt_guar_name'            => $this->isRequired('govt_guar_name'),
                'govt_guar_designation'     => $this->isRequired('govt_guar_designation'),
                'govt_guar_occupation'      => $this->isRequired('govt_guar_occupation'),
                'govt_guar_email'           => $this->isRequired('govt_guar_email'),
                'govt_guar_working_address' => $this->isRequired('govt_guar_working_address'),
                'govt_guar_par_address'     => $this->isRequired('govt_guar_par_address'),
                'govt_guar_nid'             => $this->isRequired('govt_guar_nid'),
                'govt_guar_relation'        => $this->isRequired('govt_guar_relation'),
                'govt_guar_mobile'          => $this->isRequired('govt_guar_mobile'),
                'govt_guar_phone'           => $this->isRequired('govt_guar_phone'),
                'govt_guar_photo'           => ($operationType == 'edit') ? 'nullable' : $this->isRequired('govt_guar_photo'),
                'govt_guar_signature'       => ($operationType == 'edit') ? 'nullable' : $this->isRequired('govt_guar_signature'),

                'rel_guar_name'             => $this->isRequired('rel_guar_name'),
                'rel_guar_designation'      => $this->isRequired('rel_guar_designation'),
                'rel_guar_occupation'       => $this->isRequired('rel_guar_occupation'),
                'rel_guar_email'            => $this->isRequired('rel_guar_email'),
                'rel_guar_working_address'  => $this->isRequired('rel_guar_working_address'),
                'rel_guar_par_address'      => $this->isRequired('rel_guar_par_address'),
                'rel_guar_nid'              => $this->isRequired('rel_guar_nid'),
                'rel_guar_relation'         => $this->isRequired('rel_guar_relation'),
                'rel_guar_mobile'           => $this->isRequired('rel_guar_mobile'),
                'rel_guar_phone'            => $this->isRequired('rel_guar_phone'),
                'rel_guar_photo'            => ($operationType == 'edit') ? 'nullable' : $this->isRequired('rel_guar_photo'),
                'rel_guar_signature'        => ($operationType == 'edit') ? 'nullable' : $this->isRequired('rel_guar_signature'),
            ];
            $attributes = [];
        } elseif (isset($request['submittedFrom']) && $request['submittedFrom'] == 'Nominee') {
            $submitFrom    = 'Nominee';
            $requiredField = [
                'nom_name'         => $this->isRequired('nom_name') . '|array|min:1',
                'nom_relation'     => $this->isRequired('nom_relation') . '|array|min:1',
                'nom_percentage'   => $this->isRequired('nom_percentage') . '|array|min:1',
                'nom_nid'          => $this->isRequired('nom_nid') . '|array|min:1',
                'nom_address'      => $this->isRequired('nom_address') . '|array|min:1',
                'nom_mobile'       => $this->isRequired('nom_mobile') . '|array|min:1',

                'nom_photo'        => ($operationType == 'edit') ? 'nullable' : $this->isRequired('nom_photo') . '|array|min:1',
                'nom_signature'    => ($operationType == 'edit') ? 'nullable' : $this->isRequired('nom_signature') . '|array|min:1',

                'nom_name.*'       => $this->isRequired('nom_name'),
                'nom_relation.*'   => $this->isRequired('nom_relation'),
                'nom_percentage.*' => $this->isRequired('nom_percentage'),
                'nom_nid.*'        => $this->isRequired('nom_nid'),
                'nom_address.*'    => $this->isRequired('nom_address'),
                'nom_mobile.*'     => $this->isRequired('nom_mobile'),
                'nom_photo.*'      => ($operationType == 'edit') ? 'nullable' : $this->isRequired('nom_photo'),
                'nom_signature.*'  => ($operationType == 'edit') ? 'nullable' : $this->isRequired('nom_signature'),
            ];
            $attributes = [];
        }

        //dd($request->all(), $requiredField);
        /* if($operationType != 'edit'){
            $requiredField['branch_id'] = 'required';
            $requiredField['org_position_id'] = $this->isRequired('org_position_id');
        } */
        $validator = Validator::make($request->all(), $requiredField);
        $errorMsg = null;

        if ($validator->fails()) {
            $errorMsg = implode(' <br /> ', $validator->errors()->all());
        }

        // dd($request->all());
        //Check duplicate username
        $errorMsg = (isset($request['org_username']) && $request['org_username'] != null) ? ($this->isDuplicateUsername($request['org_username']) ? $errorMsg . '<br/> Duplicate username' : $errorMsg) : $errorMsg;

        $errorMsg = ($operationType == 'store' && isset($request['emp_code'])) ? ($this->isDuplicateEmployeeCode($request['emp_code'], $request['emp_id']) ? $errorMsg . '<br/> Duplicate employee code' : $errorMsg) : $errorMsg;

        $isValid = $errorMsg == null;
        $action  = ($submitFrom == 'Reference') ? 'save' : 'next';

        return array(
            'isValid'  => $isValid,
            'errorMsg' => $errorMsg,
            'action'   => $action,
        );
    }

    public function isDuplicateUsername($username): bool
    {
        return count(SysUser::where('username', $username)->get()) > 0;
    }

    public function isDuplicateEmployeeCode($emp_code, $editId = null): bool
    {

        if($editId == 0){
            $search = DB::table('hr_employees')->where([['emp_code', $emp_code], ['is_delete', 0]])->get();
        }
        else {
            $search = DB::table('hr_employees')->where([['emp_code', $emp_code], ['is_delete', 0], ['id', '<>', $editId]])->get();
        }
        // dd($search, $editId);


        if (!empty($search) && count($search) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isRequired($fieldName)
    {
        $validation = 'nullable';
        if (isset($GLOBALS['requiredFields']->$fieldName)) {
            if ($GLOBALS['requiredFields']->$fieldName == 'required') {
                $validation = 'required';
            }
        }

        return $validation;
    }

    public function profileUpdate(Request $request)
    {
        $userAuth = Auth::user();
        $emp_id = $userAuth["emp_id"];

        if (empty($emp_id)) {
            $notification = array(
                'message'    => 'Employee not found',
                'alert-type' => 'error',
            );

            return redirect('hr')->with($notification);
        }

        return $this->edit($request, encrypt($emp_id));
    }

    public function profileView()
    {
        $userAuth = Auth::user();
        $emp_id = $userAuth["emp_id"];

        if (empty($emp_id)) {
            $notification = array(
                'message'    => 'Employee not found',
                'alert-type' => 'error',
            );

            return redirect('hr')->with($notification);
        }

        return $this->view(encrypt($emp_id));
    }

    public function getDivisionData(Request $request){

        $division = DB::table('gnl_divisions AS gdiv')
                ->where([['gdiv.is_active', 1], ['gdiv.is_delete', 0]])
                ->orderBy('gdiv.id','asc')->select('id','division_name')
                ->get();
        return response()->json($division);



    }

    
    public function getDistrictData(Request $request){
        $division = DB::table('gnl_divisions')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('division_name', 'LIKE', "%{$request->divisionData}%")
                ->orderBy('id','asc')->select('id','division_name')
                ->first();
        $divisionId = optional($division)->id;

        $district = DB::table('gnl_districts')
                ->where([['is_active', 1], ['is_delete', 0],['division_id', $divisionId]])
                ->select('id','district_name')
                ->get();

        return response()->json($district);

    }

    public function getUpazilaData(Request $request){

        if($request['context'] == 'getSalesBills'){
            $val = !empty($request['query_text']) ? $request['query_text'] : '';
            $upazilaList = DB::table('gnl_upazilas')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('upazila_name', 'LIKE', "%{$val}%")
                ->pluck('upazila_name', 'id')
                ->toArray();

            // dd($upazilaList);

            return json_encode($upazilaList);
        };

        $districtId = !empty($request->districtData) ? $request->districtData : null;
        $findDistrictData = DB::table('gnl_upazilas')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('upazila_name', '=', $request->upazilaData)
                    ->first();

        if(empty($findDistrictData) && $request->upazilaData != null){
            $data = [
                'district_id' => $districtId,
                'upazila_name' => $request->upazilaData
            ];
            $insertedId = DB::table('gnl_upazilas')->insertGetId($data);
        }



        $upazila = DB::table('gnl_upazilas')
                ->where([['is_active', 1], ['is_delete', 0],['district_id', $districtId]])
                ->select('id','upazila_name')
                ->get();

        return response()->json($upazila);

    }

    public function getUnionData(Request $request){

        $upazila = DB::table('gnl_upazilas')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('upazila_name', 'LIKE', "%{$request->upazilaData}%")
                ->orderBy('id','asc')->select('id','upazila_name')
                ->first();
        $upazilaId = optional($upazila)->id;

        $findUnionData = DB::table('gnl_unions')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('union_name', '=', $request->unionData)
                    ->first();

        if(empty($findUnionData) && $request->unionData != null){
            $data = [
                'upazila_id' => $upazilaId,
                'union_name' => $request->unionData
            ];
            $insertedId = DB::table('gnl_unions')->insertGetId($data);
        }


        $union = DB::table('gnl_unions')
                // ->where([['is_active', 1], ['is_delete', 0]])
                ->where([['is_active', 1], ['is_delete', 0],['upazila_id', $upazilaId]])
                ->select('id','union_name')
                ->get();

        // dd($upazila, $upazilaId, $union);

        return response()->json($union);

    }


    public function getVillageData(Request $request){

        $union = DB::table('gnl_unions')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('union_name', 'LIKE', "%{$request->unionData}%")
                ->orderBy('id','asc')->select('id','union_name')
                ->first();
        $unionId = optional($union)->id;

        $findVillageData = DB::table('gnl_villages')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('village_name', '=', $request->villageData)
                    ->first();

        if(empty($findVillageData) && $request->villageData != null){
            $data = [
                'union_id' => $unionId,
                'village_name' => $request->villageData
            ];
            $insertedId = DB::table('gnl_villages')->insertGetId($data);
        }

        $village = DB::table('gnl_villages')
                // ->where([['is_active', 1], ['is_delete', 0]])
                ->where([['is_active', 1], ['is_delete', 0],['union_id', $unionId]])
                ->select('id','village_name')
                ->get();

        // dd($union, $unionId, $village);

        return response()->json($village);

    }


    //View Employee
    public function view($id = null)
    {
        $emp = Employee::where('id', decrypt($id))->first();

        // $empPerData            = EmployeePersonalDetails::where('emp_id', $emp->id)->get();

        $empPerData = EmployeePersonalDetails::from('hr_emp_personal_details as empdt')
            ->where('emp_id', $emp->id)
            ->leftJoin('gnl_upazilas as preUp', 'empdt.pre_addr_thana_id', '=','preUp.id')
            ->leftJoin('gnl_upazilas as parUp', 'empdt.par_addr_thana_id', '=','parUp.id')
            ->leftJoin('gnl_unions as preUn', 'empdt.pre_addr_union_id', '=','preUn.id')
            ->leftJoin('gnl_unions as parUn', 'empdt.par_addr_union_id', '=','parUn.id')
            ->leftJoin('gnl_villages as preVil', 'empdt.pre_addr_village_id', '=','preVil.id')
            ->leftJoin('gnl_villages as parVil', 'empdt.par_addr_village_id', '=','parVil.id')
            ->select('empdt.*',
                'preUp.upazila_name as pre_addr_thana_name',
                'parUp.upazila_name as par_addr_thana_name',
                'preUn.union_name as pre_addr_union_name',
                'parUn.union_name as par_addr_union_name',
                'preVil.village_name as pre_addr_village_name',
                'parVil.village_name as par_addr_village_name'
            )
            ->get();

        $divisions             = GnlService::getDivisions();
        $preAddress            = [];
        $perAddress            = [];
        $sameAsPreesentAddress = false;

        if (count($empPerData) > 0) {
            // get present address
            $filters['division_id'] = $empPerData[0]['pre_addr_division_id'];
            $filters['district_id'] = $empPerData[0]['pre_addr_district_id'];
            $filters['upazila_id']  = $empPerData[0]['pre_addr_thana_id'];
            $filters['union_id']    = $empPerData[0]['pre_addr_union_id'];

            $preAddress['districts'] = GnlService::getDistricts($filters);
            $preAddress['upazilas']  = GnlService::getUpazilas($filters);
            $preAddress['unions']    = GnlService::getUnions($filters);
            $preAddress['villages']  = GnlService::getVillages($filters);

            // get permanent address
            $filters['division_id'] = $empPerData[0]['par_addr_division_id'];
            $filters['district_id'] = $empPerData[0]['par_addr_district_id'];
            $filters['upazila_id']  = $empPerData[0]['par_addr_thana_id'];
            $filters['union_id']    = $empPerData[0]['par_addr_union_id'];

            $perAddress['districts'] = GnlService::getDistricts($filters);
            $perAddress['upazilas']  = GnlService::getUpazilas($filters);
            $perAddress['unions']    = GnlService::getUnions($filters);
            $perAddress['villages']  = GnlService::getVillages($filters);

            if (
                $empPerData[0]['pre_addr_division_id'] == $empPerData[0]['par_addr_division_id']
                && $empPerData[0]['pre_addr_district_id'] == $empPerData[0]['par_addr_district_id']
                && $empPerData[0]['pre_addr_thana_id'] == $empPerData[0]['par_addr_thana_id']
                && $empPerData[0]['pre_addr_union_id'] == $empPerData[0]['par_addr_union_id']
                && $empPerData[0]['pre_addr_village_id'] == $empPerData[0]['par_addr_village_id']
                && $empPerData[0]['pre_addr_street'] == $empPerData[0]['par_addr_street']
            ) {
                $sameAsPreesentAddress = true;
            }
        }

        $empOrgData     = EmployeeOrganizationDetails::where('emp_id', $emp->id)->get();

        // $empAccData     = EmployeeAccountDetails::where('emp_id', $emp->id)->get();
        $empAccData     = EmployeeAccountDetails::from('hr_emp_account_details as empacc')
            ->join('hr_bank_branches as hrb', 'empacc.bank_branch_id', '=','hrb.id')
            ->where([['empacc.emp_id', $emp->id], ['hrb.is_delete', 0]])
            ->select('empacc.*', 'hrb.name as bank_branch_name')
            ->get();


        $empEduData     = EmployeeEducationDetails::where('emp_id', $emp->id)->get();
        $empTrainData   = EmployeeTrainingDetails::where('emp_id', $emp->id)->get();
        $empExpData     = EmployeeExperienceDetails::where('emp_id', $emp->id)->get();
        $empGuarData    = EmployeeGuarantorDetails::where('emp_id', $emp->id)->get();
        $empNomData     = EmployeeNomineeDetails::where('emp_id', $emp->id)->get();
        $empRefData     = EmployeeReferenceDetails::where('emp_id', $emp->id)->get();
        $empSysUserData = SysUser::where('id', $emp->user_id)->get();

        $projectType = DB::table('gnl_project_types')->where('id', $emp->project_type_id)->get();
        // $bankBranch  = DB::table('hr_bank_branches')->where('id', (isset($empAccData[0]['bank_branch_id'])) ? $empAccData[0]['bank_branch_id'] : -1)->get();
        $bankBranch = $empAccData;

        $empData = [
            'emp'            => $emp,
            'empPerData'     => $empPerData,
            'empOrgData'     => $empOrgData,
            'empAccData'     => $empAccData,
            'empEduData'     => $empEduData,
            'empTrainData'   => $empTrainData,
            'empExpData'     => $empExpData,
            'empGuarData'    => $empGuarData,
            'empNomData'     => $empNomData,
            'empRefData'     => $empRefData,
            'empSysUserData' => $empSysUserData,
        ];

        $banks         = DB::table('hr_banks')->where([['is_delete', 0]])->get();
        $hrConfig      = DB::table('hr_config')->where('title', 'employeeRequiredFields')->get();
        $requiredField = json_decode($hrConfig[0]->content);
        $orgLevel      = DB::table('hr_config')->where('title', 'level')->first();
        $orgGrade      = DB::table('hr_config')->where('title', 'grade')->first();
        $orgDepartment = DB::table('hr_departments')->where([['is_delete', 0], ['is_active', 1]])->get();
        $orgPosition   = DB::table('hr_designations')->where('is_delete', 0)->get();
        $orgFiscalYear = DB::table('gnl_fiscal_year')->where([['is_delete', 0], ['is_active', 1]])->get();
        $orgProject    = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1]])->get();
        $recType       = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get();
        $relation      = DB::table('hr_relationships')->where('status', 1)->get();

        $data = [
            'divisions'             => $divisions,
            'requiredField'         => $requiredField,
            'banks'                 => $banks,
            'orgLevel'              => $orgLevel,
            'orgGrade'              => $orgGrade,
            'recType'               => $recType,
            'relation'              => $relation,
            'orgDepartment'         => $orgDepartment,
            'orgPosition'           => $orgPosition,
            'orgFiscalYear'         => $orgFiscalYear,
            'orgProject'            => $orgProject,
            'preAddress'            => $preAddress,
            'perAddress'            => $perAddress,
            'sameAsPreesentAddress' => $sameAsPreesentAddress,
            'viewPage'              => true,
            'bankBranch'            => $bankBranch,
            'projectType'           => $projectType,
            'hasPayRoll'            => $this->hasPayRoll(),
        ];

        //dd($empData);
        return view('HR.Employee.view', compact('data', 'empData'));
    }

    // Soft Delete Employee
    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $EmployeeData = Employee::where('id', decrypt($request->RowID))->first();

            $lastDelData = Employee::where('emp_code', $EmployeeData->emp_code)->orderBy('is_delete', 'DESC')->first();

            DB::table('gnl_sys_users')->where('emp_id', decrypt($request->RowID))->update([
                'is_delete' => 1
            ]);

            $EmployeeData->is_delete = (++$lastDelData->is_delete);

            $delete = $EmployeeData->save();

            DB::commit();

            return [
                'message' => 'Successfully Deleted',
                'status'  => 'success',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'Failed to Delete',
                'status' => 'error',
            ];
        }
    }

    // Parmanent Delete Employee
    // public function destroy($id = null)
    // {
    //     $EmployeeData = Employee::where('employee_no', $id)->first();
    //     $delete       = $EmployeeData->delete();

    //     if ($delete) {
    //         $notification = array(
    //             'message'    => 'Successfully Deleted',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     } else {
    //         $notification = array(
    //             'message'    => 'Unsuccessful to Delete',
    //             'alert-type' => 'error',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }

    // Publish/Unpublish Employee
    public function isActive($id = null)
    {
        $EmployeeData = Employee::where('employee_no', $id)->first();

        if ($EmployeeData->is_active == 1) {
            $EmployeeData->is_active = 0;
        } else {
            $EmployeeData->is_active = 1;
        }

        $Status = $EmployeeData->save();
        if ($Status) {
            $notification = array(
                'message'    => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message'    => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function hasPayRoll()
    {
        ## formId 12 = payroll
        return (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 12]])->first())) ? true : false;
    }

    public static function getEmployee($empId)
    {
        return Employee::find($empId);
    }


    public function getPayScale(Request $request)
    {

        // $joinDate = !empty($request->joinDate) ? (new DateTime($request->joinDate))->format('Y-m-d') : null;

        // $payscaleData = DB::table('hr_payroll_payscale')
        //     ->where([['is_delete', 0], ['is_active', 1], ['eff_date_start', '<=', $joinDate]])->orderBy('eff_date_start', 'desc')->get();
        $payscaleData = DB::table('hr_payroll_payscale')
            ->where([['is_delete', 0], ['is_active', 1]])->orderBy('eff_date_start', 'desc')->get();

            // dd($payscaleData, $request->all(), $joinDate);
        return response()->json($payscaleData);
    }


    public function getSalaryInformation(Request $request)
    {

        // dd($request->all());
        $step = !empty($request->org_step) ? $request->org_step : null;
        $grade = !empty($request->org_grade) ? $request->org_grade : null;
        $level = !empty($request->org_level) ? $request->org_level : null;
        $payscaleId = !empty($request->org_fiscal_year_id) ? $request->org_fiscal_year_id : null;
        $recruitmentId = !empty($request->org_rec_type_id) ? $request->org_rec_type_id : null;

        $securityMoney = DB::table('hr_payroll_settings_security_money')
            ->where([['is_delete', 0], ['is_active', 1], ['grade_id', $grade], ['level_id', $level]])->first();

        $data = array();
        $data = [
            'salaryInfo' => HRS::genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step),
            'securityMoney' => $securityMoney,
        ];

        // dd($data);

        return response()->json($data);

        // dd($request->all(), HRS::genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step));

        // HRS::genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step);

    }


    public function getStepData(Request $request){

        $grade = !empty($request->org_grade) ? $request->org_grade : null;
        $level = !empty($request->org_level) ? $request->org_level : null;

        $query = DB::table('hr_payroll_salary_structure as ss')
            ->join('hr_payroll_salary_structure_details as ssd', 'ss.id', '=', 'ssd.salary_structure_id')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0], ['ss.grade', $grade], ['ss.level', $level]])
            ->select('ss.grade', 'ss.level', 'ssd.no_of_inc')
            ->first();

        $data = array();
        $data = [
            'steps' => $query
        ];

        return response()->json($data);
    }
    
    public function getBranchData(Request $request){

        $bankId = !empty($request->bankId) ? $request->bankId : null;

        $masterQuery = DB::table('hr_bank_branches')
                        ->join('hr_banks', 'hr_banks.id', 'hr_bank_branches.bank_id')
                        ->select('hr_banks.name AS bank_name','hr_bank_branches.*')
                        ->where('hr_bank_branches.is_delete', 0)
                        // ->where('hr_banks.id', $request->bankId)
                        ->where(function($query) use ($bankId){
                            if (!empty($bankId)) {
                                // $query->where('hr_banks.id', $bankId);
                            }
                        })
                        ->orderBy('hr_bank_branches.id', 'DESC')->select('hr_bank_branches.id','hr_bank_branches.name')
                        ->get()->toArray();

        return response()->json($masterQuery);
    }


    public function getData(Request $request){

        $term = $request->input('term');
        $context = $request->input('context');

        if($context == 'searchDistrict'){
            if(!empty($term)){
                $district = DB::table('gnl_districts')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('district_name', 'LIKE', "%{$term}%")
                    ->pluck('district_name','id')
                    ->toArray();
    
                $result = array_map(function($key, $value) {
                    return ["id" => $key, "text" => $value];
                }, array_keys($district), $district);
    
                $result = array_values($result);
                
                return response()->json($result);
            }else{
                $data = [
                    'id' => '',
                    'text' => 'All'
                ];
                return response()->json($data);
            }
        }

        if($context == 'searchUpazila'){
            if(!empty($term)){
                $upazilaList = DB::table('gnl_upazilas')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('upazila_name', 'LIKE', "%{$term}%")
                    ->pluck('upazila_name', 'id')
                    ->toArray();
    
                $result = array_map(function($key, $value) {
                    return ["id" => $key, "text" => $value];
                }, array_keys($upazilaList), $upazilaList);
    
                $result = array_values($result);
                
                return response()->json($result);
            }else{
                $data = [
                    'id' => '',
                    'text' => 'All'
                ];
                return response()->json($data);
            }
        }

    }

}
