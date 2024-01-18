<?php

namespace App\Http\Controllers\HR\Payroll;

use App\Model\HR\PayrollPayScaleMigration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\HrService as HRS;
use App\Http\Controllers\HR\Others\CommonController;
use App\Services\CommonService as Common;
use App\Services\RoleService as Role;

class PayrollPayScaleMigrationController extends Controller
{
    
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                
            );

            $attributes = array(
               
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

            $branch_id       = (empty($request->branch_id)) ? null : $request->branch_id;
            $department_id   = (empty($request->department_id)) ? null : $request->department_id;
            $designation_id  = (empty($request->designation_id)) ? null : $request->designation_id;


            $totalRecords = PayrollPayScaleMigration::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = PayrollPayScaleMigration::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            // $allData = PayrollPayScaleMigration::from('hr_payroll_pay_scale_migration AS hppsm')
            //     ->where('is_delete', 0)
            //     ->select('hppsm.*')
            //     ->get();
            $allData = DB::table('hr_payroll_pay_scale_migration as apmgt')
                    ->where([['apmgt.is_active', 1], ['apmgt.is_delete', 0]])
                    ->join('gnl_branchs as br', 'apmgt.branch_id', '=', 'br.id')
                    ->join('hr_employees as emp', 'apmgt.emp_id', '=', 'emp.id')
                    ->join('hr_departments as dpt', 'apmgt.department_id', '=', 'dpt.id')
                    ->join('hr_designations as desg', 'apmgt.designation_id', '=', 'desg.id')
                    ->join('hr_payroll_payscale as pays', 'apmgt.old_payscale_id', '=', 'pays.id')
                    ->join('hr_recruitment_types as rec', 'apmgt.rectuitment_type_id', '=', 'rec.id')
                    ->where(function($query) use ($branch_id, $department_id, $designation_id, $searchValue){
                        if (!empty($branch_id)) {
                            $query->where('apmgt.branch_id', $branch_id);
                        }
                        if (!empty($department_id)) {
                            $query->where('apmgt.department_id', $department_id);
                        }
                        if (!empty($designation_id)) {
                            $query->where('apmgt.designation_id', $designation_id);
                        }
                        if(!empty($searchValue)){
                            $query->where('emp.emp_name', 'LIKE', "%{$searchValue}%");
                            $query->orWhere('dpt.short_name', 'LIKE', "%{$searchValue}%");
                            $query->orWhere('desg.name', 'LIKE', "%{$searchValue}%");
                            $query->orWhere('br.branch_name', 'LIKE', "%{$searchValue}%");
                            $query->orWhere('apmgt.effective_date', 'LIKE', "%{$searchValue}%");
                        }
                    })
                    ->select('apmgt.*', 'br.branch_name as br_name', 'emp.emp_name as emp_name', 'dpt.short_name as dpt_name', 'desg.name as des_name', 'pays.name as pays_name', 'rec.title as r_title')
                    ->get();


            $totalRecordswithFilter = count($allData);

            $data = array();
            $sno = $start + 1;
            
            foreach ($allData as $key => $row) {

                $oldPayScale = DB::table('hr_payroll_payscale')->where('id', $row->old_payscale_id)->first();
                $newPayScale = DB::table('hr_payroll_payscale')->where('id', $row->new_payscale_id)->first();

                $systemDate = Common::systemCurrentDate();
                $IgnoreArray = [];
                if($systemDate > $row->effective_date){
                    $IgnoreArray = ['view','delete', 'edit', 'send', 'isActive', 'message' => "Permission Denied", 'btnHide' => true];
                }

                $passport = $this->getPassport($row, 'index');
                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                   = $sno;
                $data[$key]['emp_name']             = $row->emp_name;
                $data[$key]['designation']          = $row->des_name;
                $data[$key]['department']           = $row->dpt_name;
                $data[$key]['branch']               = $row->br_name;
                $data[$key]['recruitment']          = $row->r_title;

                $data[$key]['grade']                = $row->grade;
                $data[$key]['level']                = $row->level;
                $data[$key]['step']                 = $row->step;
                $data[$key]['salary_structure_id']  = $row->salary_structure_id;
                
                $data[$key]['oldPayScale']          = optional($oldPayScale)->name;
                $data[$key]['newPayScale']          = optional($newPayScale)->name;
                
                $data[$key]['effective_date']  = $row->effective_date;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;
            }
            
            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);
        }
    }


    public function insert(Request $request){

        // ss($request->all());

        $empId = !empty($request->employee_id) ? $request->employee_id : '';
        $query = DB::table('hr_employees')->where([['is_active', 1], ['is_delete', 0], ['id', $empId]])->first();
        $designation_id = optional($query)->designation_id;
        $department_id = optional($query)->department_id;

        $branch_id = !empty($request->branch_id) ? $request->branch_id : '';
        $af_grade = !empty($request->af_grade) ? $request->af_grade : '';
        $af_level = !empty($request->af_level) ? $request->af_level : '';
        $af_step = !empty($request->af_org_step) ? $request->af_org_step : '';
        $old_payscale_id = !empty($request->be_payscal_year_id) ? $request->be_payscal_year_id : '';
        $new_payscale_id = !empty($request->af_org_payscale_year_id) ? $request->af_org_payscale_year_id : '';
        $salaray_structure_id = !empty($request->salaray_structure_id) ? $request->salaray_structure_id : '';

        $recruitmentId = !empty($request->rectuitment_type_id) ? $request->rectuitment_type_id : '';

        $payScaleData = DB::table('hr_payroll_payscale')
                        ->where([['is_active', 1], ['is_delete', 0], ['id', $new_payscale_id]])->first();
        $effective_date = optional($payScaleData)->eff_date_start;

        // ss($request->all(), $request->employee_id);

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = DB::table('hr_payroll_pay_scale_migration')->insertGetId([
                    'emp_id' => $request->employee_id,
                    'branch_id' => $branch_id,
                    'department_id' => $department_id,
                    'designation_id' => $designation_id,
                    'rectuitment_type_id' => $recruitmentId,
                    'grade' => $af_grade,
                    'level' => $af_level,
                    'step' => $af_step,
                    'old_payscale_id' => $old_payscale_id,
                    'new_payscale_id' => $new_payscale_id,
                    'salary_structure_id' => $salaray_structure_id,
                    'effective_date' => $effective_date,

                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id
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

        // ss($request->all());
    }

    public function update(Request $request){

        $id = !empty($request->edit_id) ? $request->edit_id : '';
        $empId = !empty($request->employee_id) ? $request->employee_id : '';
        $query = DB::table('hr_employees')->where([['is_active', 1], ['is_delete', 0], ['id', $empId]])->first();
        $designation_id = optional($query)->designation_id;
        $department_id = optional($query)->department_id;

        $branch_id = !empty($request->branch_id) ? $request->branch_id : '';
        $af_grade = !empty($request->af_grade) ? $request->af_grade : '';
        $af_level = !empty($request->af_level) ? $request->af_level : '';
        $af_step = !empty($request->af_org_step) ? $request->af_org_step : '';
        $old_payscale_id = !empty($request->be_payscal_year_id) ? $request->be_payscal_year_id : '';
        $new_payscale_id = !empty($request->af_org_payscale_year_id) ? $request->af_org_payscale_year_id : '';
        $salaray_structure_id = !empty($request->salaray_structure_id) ? $request->salaray_structure_id : '';

        $recruitmentId = !empty($request->rectuitment_type_id) ? $request->rectuitment_type_id : '';

        $payScaleData = DB::table('hr_payroll_payscale')
                        ->where([['is_active', 1], ['is_delete', 0], ['id', $new_payscale_id]])->first();
        $effective_date = optional($payScaleData)->eff_date_start;

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = DB::table('hr_payroll_pay_scale_migration')->where('id', $id)->update([
                    'emp_id' => $request->employee_id,
                    'branch_id' => $branch_id,
                    'department_id' => $department_id,
                    'designation_id' => $designation_id,
                    'rectuitment_type_id' => $recruitmentId,
                    'grade' => $af_grade,
                    'level' => $af_level,
                    'step' => $af_step,
                    'old_payscale_id' => $old_payscale_id,
                    'new_payscale_id' => $new_payscale_id,
                    'salary_structure_id' => $salaray_structure_id,
                    'effective_date' => $effective_date,

                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id
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

    public function getBeforeSsData(Request $request){

        $empId = !empty($request->empId) ? $request->empId : null;
        $query = DB::table('hr_employees as emp')
            ->where([['emp.is_active', 1], ['emp.is_delete', 0], ['emp.id', $empId]])
            ->join('hr_emp_organization_details as org', 'emp.id', '=', 'org.emp_id')
            ->select('emp.*','org.emp_id','org.rec_type_id','org.level','org.grade','org.step','org.payscal_id','org.salary_structure_id')
            ->first();

        // dd($query);

        $grade = optional($query)->grade;
        $level = optional($query)->level;
        $old_payscaleId = optional($query)->payscal_id;
        $recruitmentId = optional($query)->rec_type_id;
        $step = optional($query)->step;

        $Before_SS_Data = HRS::genarateSalaryStructure($grade, $level, $old_payscaleId, $recruitmentId, $step);

        $data = array();
        $data = [
            'be_grade' => $grade,
            'be_level' => $level,
            'be_payscal_id' => $old_payscaleId,
            'recruitmentId' => $recruitmentId,
            'be_steps' => $step,
            'Before_SS_Data' => $Before_SS_Data,
        ];

        return response()->json($data);
        // dd(11, $Before_SS_Data);

    }

    public function getAfterSsData(Request $request){

        $empId = !empty($request->empId) ? $request->empId : null;
        $query = DB::table('hr_employees as emp')
            ->where([['emp.is_active', 1], ['emp.is_delete', 0], ['emp.id', $empId]])
            ->join('hr_emp_organization_details as org', 'emp.id', '=', 'org.emp_id')
            ->select('emp.*','org.emp_id','org.rec_type_id','org.level','org.grade','org.step','org.payscal_id','org.salary_structure_id')
            ->first();

        $recruitmentId = optional($query)->rec_type_id;

        $grade = !empty($request->af_grade) ? $request->af_grade : null;
        $level = !empty($request->af_level) ? $request->af_level : null;
        $step = !empty($request->af_step) ? $request->af_step : null;
        $payscaleId = !empty($request->af_payscale) ? $request->af_payscale : null;

        $After_SS_Data = HRS::genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step);

        $data = array();
        $data = [
            'After_SS_Data' => $After_SS_Data,
        ];

        // dd($data);
        return response()->json($data);

    }

    public function get($id){
        

        $getData = PayrollPayScaleMigration::find($id);

        $empId = optional($getData)->emp_id;
        $new_salary_structure_id = optional($getData)->salary_structure_id;
        $new_payscale_id = optional($getData)->new_payscale_id;

        $new_grade = optional($getData)->grade;
        $new_level = optional($getData)->level;
        $new_recruitmentId = optional($getData)->rectuitment_type_id;
        $new_step = optional($getData)->step;

        $After_SS_Data = HRS::genarateSalaryStructure($new_grade, $new_level, $new_payscale_id, $new_recruitmentId, $new_step);
        

        $query = DB::table('hr_employees as emp')
            ->where([['emp.is_active', 1], ['emp.is_delete', 0], ['emp.id', $empId]])
            ->join('hr_emp_organization_details as org', 'emp.id', '=', 'org.emp_id')
            ->select('emp.*','org.emp_id','org.rec_type_id','org.level','org.grade','org.step','org.payscal_id','org.salary_structure_id')
            ->first();
        
        $old_salary_structure_id = optional($query)->salary_structure_id;
        $old_payscale_id = optional($query)->payscal_id;
        $old_grade = optional($query)->grade;
        $old_level = optional($query)->level;
        $old_recruitmentId = optional($query)->rec_type_id;
        $old_step = optional($query)->step;

        $Before_SS_Data = HRS::genarateSalaryStructure($old_grade, $old_level, $old_payscale_id, $old_recruitmentId, $old_step);


        $data = array();
        $data = [
            'id' => $getData->id,
            'empID' => $empId,
            'be_grade' => $old_grade,
            'be_level' => $old_level,
            'be_payscal_id' => $old_payscale_id,
            'be_recruitmentId' => $old_recruitmentId,
            'be_steps' => $old_step,
            'Before_SS_Data' => $Before_SS_Data,

            'af_grade' => $new_grade,
            'af_level' => $new_level,
            'af_payscal_id' => $new_payscale_id,
            'af_recruitmentId' => $new_recruitmentId,
            'af_steps' => $new_step,
            'After_SS_Data' => $After_SS_Data,

        ];

        return response()->json($data);

        dd($getData, $Before_SS_Data, $After_SS_Data);

        dd($id, $getData, $query);
    }

    public function delete($id)
    {
        $delete = DB::table('hr_payroll_pay_scale_migration')->where('id', decrypt($id))->update(['is_delete' => 1]);

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
}
