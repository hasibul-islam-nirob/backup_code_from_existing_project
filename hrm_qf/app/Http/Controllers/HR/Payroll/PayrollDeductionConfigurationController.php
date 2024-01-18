<?php

namespace App\Http\Controllers\HR\Payroll;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\PayrollDeductionConfigModel;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\HrService as HRS;
use App\Http\Controllers\HR\Others\CommonController;
use App\Services\CommonService as Common;

class PayrollDeductionConfigurationController extends Controller
{
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'group_id' => 'required',
                'company_id' => 'required',
                'project_id' => 'required',
                // 'rectuitment_type' => 'required',
                'permanent' => 'required',
                'nonpermanent' => 'required',
                'eff_date_start' => 'required',
            );

            $attributes = array(
                // 'group_id' => 'Group',
                'company_id' => 'Company',
                'project_id' => 'Project',
                // 'rectuitment_type' => 'Rectuitment Type',
                'permanent' => 'Permanent',
                'nonpermanent' => 'Non Permanent',
                'eff_date_start' => 'Effective Date',
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

            $totalRecords = PayrollDeductionConfigModel::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = PayrollDeductionConfigModel::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = PayrollDeductionConfigModel::from('hr_payroll_configuration_menu AS hpcm')
                ->where('is_delete', 0)
                ->select('hpcm.*')
                ->get();

            $data = array();
            $sno = $start + 1;
            
            foreach ($allData as $key => $row) {

                $systemDate = Common::systemCurrentDate();
                if($systemDate > $row->effective_date){
                    $IgnoreArray = ['view','delete', 'edit', 'send', 'isActive', 'message' => "Permission Denied", 'btnHide' => true];
                }else{
                    $IgnoreArray = [];
                }

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['company']              = $row->company['comp_name'];
                $data[$key]['project']              = $row->project()->project_name;
                $data[$key]['permanent']            = strtoupper($row->permanent);
                $data[$key]['nonpermanent']         = strtoupper($row->nonpermanent);

                $data[$key]['eff_date_start']     = (new DateTime($row->eff_date_start))->format('d-m-Y');
                $data[$key]['eff_date_end']       = (new DateTime($row->eff_date_end))->format('d-m-Y');
                $data[$key]['action']             = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row['id']), $IgnoreArray);

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

    public function insert(Request $request)
    {

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();

            try {

                
                
                $payrollMenuConfig = new PayrollDeductionConfigModel();
                // $input_date = (new DateTime($request['eff_date_start']))->format('Y-m-d');
                $input_date = new DateTime($request['eff_date_start']);
                $if_exist = PayrollDeductionConfigModel::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = PayrollDeductionConfigModel::from('hr_payroll_configuration_menu')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('eff_date_start', '<', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'desc')
                        ->first();
                        
                    if(!empty($prev_row_smaller_date)){
                        $prev_eff_date_end = clone $input_date;
                        $prev_eff_date_end = $prev_eff_date_end->modify('-1 day')->format('Y-m-d');
                        $prev_row_smaller_date->eff_date_end = $prev_eff_date_end;
                        $prev_row_smaller_date->save();
                    }

                    ## Input Date theke boro date thakle current row er end date a (query row er StartDate - 1) porbe, boro date na thakle NULL porbe
                    $prev_row_greater_date = PayrollDeductionConfigModel::from('hr_payroll_configuration_menu')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('eff_date_start', '>', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'asc')
                        ->first();

                    $input_eff_end_date = null;
                    if(!empty($prev_row_greater_date)){
                        $input_eff_end_date = new DateTime($prev_row_greater_date->eff_date_start);
                        $input_eff_end_date = $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                        // $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                    }
                   
    
                    $payrollMenuConfig->branch_id = $request->branch_id;
                    // $payrollMenuConfig->group_id = $request->group_id;
                    $payrollMenuConfig->company_id = $request->company_id;
                    $payrollMenuConfig->project_id = $request->project_id;
                    $payrollMenuConfig->rectuitment_type = $request->rectuitment_type;

                    $permanentArr = !empty($request->permanent) ? implode(',', $request->permanent) : [];
                    $payrollMenuConfig->permanent   = $permanentArr;

                    $nonpermanentArr = !empty($request->nonpermanent) ? implode(',', $request->nonpermanent) : [];
                    $payrollMenuConfig->nonpermanent   = $nonpermanentArr;

                    $payrollMenuConfig->eff_date_start   = $input_date->format('Y-m-d');
                    $payrollMenuConfig->exp_effective_date   = $input_date->format('Y-m-d');
                    $payrollMenuConfig->eff_date_end     = $input_eff_end_date;
                    $payrollMenuConfig->is_active        =  1;
    
                    if ($payrollMenuConfig->is_active) {
                        $passport = $this->getPassport($request, 'send');
    
                        if (!$passport['isValid']) {
                            return response()->json([
                                'message' => $passport['message'],
                                'status' => 'error',
                                'statusCode' => 400,
                                'result_data' => ''
                            ], 400); 
                        }   
                    }
    
                    $payrollMenuConfig->save();
    
                    DB::commit();
                }

                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'if_exist_start_date' => $if_exist,
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

    public function update(Request $request)
    {

        // ss($request->all());

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();

            try {

                $payrollMenuConfig = PayrollDeductionConfigModel::where('id', $request['edit_id'])->first();

                // ss($payrollMenuConfig);

                // $input_date = (new DateTime($request['eff_date_start']))->format('Y-m-d');
                $input_date = new DateTime($request['eff_date_start']);
                $if_exist = PayrollDeductionConfigModel::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                $if_exist = null;
                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = PayrollDeductionConfigModel::from('hr_payroll_configuration_menu')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('eff_date_start', '<', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'desc')
                        ->first();
                        
                    if(!empty($prev_row_smaller_date)){
                        $prev_eff_date_end = clone $input_date;
                        $prev_eff_date_end = $prev_eff_date_end->modify('-1 day')->format('Y-m-d');
                        $prev_row_smaller_date->eff_date_end = $prev_eff_date_end;
                        $prev_row_smaller_date->save();
                    }

                    ## Input Date theke boro date thakle current row er end date a (query row er StartDate - 1) porbe, boro date na thakle NULL porbe
                    $prev_row_greater_date = PayrollDeductionConfigModel::from('hr_payroll_configuration_menu')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('eff_date_start', '>', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'asc')
                        ->first();

                    $input_eff_end_date = null;
                    if(!empty($prev_row_greater_date)){
                        $input_eff_end_date = new DateTime($prev_row_greater_date->eff_date_start);
                        $input_eff_end_date = $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                        // $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                    }
                   
    
                    $payrollMenuConfig->branch_id = $request->branch_id;
                    // $payrollMenuConfig->group_id = $request->group_id;
                    $payrollMenuConfig->company_id = $request->company_id;
                    $payrollMenuConfig->project_id = $request->project_id;
                    $payrollMenuConfig->rectuitment_type = $request->rectuitment_type;

                    $permanentArr = !empty($request->permanent) ? implode(',', $request->permanent) : [];
                    $payrollMenuConfig->permanent   = $permanentArr;

                    $nonpermanentArr = !empty($request->nonpermanent) ? implode(',', $request->nonpermanent) : [];
                    $payrollMenuConfig->nonpermanent   = $nonpermanentArr;

                    $payrollMenuConfig->eff_date_start   = $input_date->format('Y-m-d');
                    $payrollMenuConfig->exp_effective_date   = $input_date->format('Y-m-d');
                    $payrollMenuConfig->eff_date_end     = $input_eff_end_date;
                    $payrollMenuConfig->is_active        =  1;
    
                    if ($payrollMenuConfig->is_active) {
                        $passport = $this->getPassport($request, 'send');
    
                        if (!$passport['isValid']) {
                            return response()->json([
                                'message' => $passport['message'],
                                'status' => 'error',
                                'statusCode' => 400,
                                'result_data' => ''
                            ], 400); 
                        }   
                    }
    
                    $payrollMenuConfig->save();
    
                    DB::commit();
                }

                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'if_exist_start_date' => $if_exist,
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

    public function getData(Request $request){
        ss($request->all());
       
        return response()->json($data);
    }


    public function get($id)
    {
        $data =  DB::table('hr_payroll_configuration_menu')->where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\PayrollDeductionConfigModel', $id);
    }

    public function getStatus($value){

        $deductionDataArray = [
            'pf' => 'Provident Fund (PF)',
            'wf' => 'Welfare Fund (WF)',
            'eps' => 'Employee Pension (EPS)',
            'bonus' => 'Bonus',
            'gat' => 'Gratuity',
            'inc' => 'Insurance',
            'loan' => 'Loan',
            'sm' => 'Security Money',
            'osf' => 'Organization Sustanable Fund (OSF)',
        ];

        $dataHave = [
            'msg' => '',
            'msgVal' => 1,
        ];

        $dataNotHave = [
            'msg' => $deductionDataArray[$value],
            'msgVal' => 0,
        ];

        if ($value == 'pf') {
            $haveData = DB::table('hr_payroll_settings_pf')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'wf') {
            $haveData = DB::table('hr_payroll_settings_wf')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'eps') {
            $haveData = DB::table('hr_payroll_settings_pension_setting')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'bonus') {
            $haveData = DB::table('hr_payroll_settings_bonus')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'gat') {
            $haveData = DB::table('hr_payroll_settings_gratuity')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'inc') {
            $haveData = DB::table('hr_payroll_settings_insurance')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'loan') {
            $haveData = DB::table('hr_payroll_settings_loan')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'sm') {
            $haveData = DB::table('hr_payroll_settings_security_money')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }

        if ($value == 'osf') {
            $haveData = DB::table('hr_payroll_settings_osf')->where([['is_active', 1], ['is_delete', 0]])->count();
            if ($haveData > 0) {
                return response()->json($dataHave);
            }else{
                return response()->json($dataNotHave);
            }
        }


    }

}
