<?php

namespace App\Http\Controllers\HR\Payroll;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\SalaryStructure;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\HrService as HRS;
use App\Services\CommonService as Common;


class SalaryStructureController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'project_id' => 'required',
                'grade' => 'required',
                'level' => 'required',
                'basic' => 'required',
                'increment' => 'required',
                'total_inc' => 'required',
                'total_basic' => 'required',
            );

            $attributes = array(
                'project_id' => 'Project',
                'grade'     => 'Grade',
                'level'     => 'Level',
                'basic'     => 'Basic',
                'increment'     => 'Increment percentage',
                'total_inc'     => 'Total increment',
                'total_basic'     => 'Total basic',
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

            $payscale_id = !empty($request->payscale_id) ? $request->payscale_id : null;
            $grade = !empty($request->grade) ? $request->grade : null;
            $level = !empty($request->level) ? $request->level : null;
            $recruitment_type_id = !empty($request->recruitment_type_id) ? $request->recruitment_type_id : null;


            $allData  = SalaryStructure::where([['is_active', 1], ['is_delete', 0]])
                        ->where(function($query) use ($searchValue, $payscale_id, $grade, $level, $recruitment_type_id){
                            if (!empty($searchValue)) {
                                $query->where('grade', 'like', '%' .$searchValue . '%');
                            }
                            if (!empty($payscale_id)) {
                                $query->where('pay_scale_id', $payscale_id);
                            }
                            if (!empty($grade)) {
                                $query->where('grade', $grade);
                            }
                            if (!empty($level)) {
                                $query->where('level', $level);
                            }
                            if (!empty($recruitment_type_id)) {
                                $query->where('recruitment_type_id', $recruitment_type_id);
                            }
                        });

            $totalRecordswithFilter = SalaryStructure::where([['is_active', 1], ['is_delete', 0]])->count();
            $totalRecords = $totalRecordswithFilter;
            $tempQueryData = clone $allData;
            if (!empty($payscale_id) || !empty($grade) || !empty($level) || !empty($recruitment_type_id) || !empty($searchValue) ) {
                $totalRecords = $tempQueryData->count();
            }


            $allData = $allData->skip($start)->take($rowperpage)->get();
            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = [];
                $payScaleData= DB::table('hr_payroll_payscale')->where([['is_active', 1],['is_delete', 0],['id', $row->pay_scale_id]])->first();
                
                $fy_start_date = new DateTime($payScaleData->eff_date_start);
                $fy_end_date = new DateTime($payScaleData->eff_date_end);
                $systemDate = new DateTime(Common::systemCurrentDate());

                if ($systemDate > $fy_start_date) {
                    $IgnoreArray = ['delete', 'edit', 'message' => "Permission Denied", 'btnHide' => true];
                }

                $data[$key]['id']           = $sno;
                $data[$key]['grade']        = $row->grade;
                $data[$key]['level']        = $row->level;
                $data[$key]['basic']        = $row->basic;
                $data[$key]['pay_scale']    = !empty($row->pay_scale()->name) ? $row->pay_scale()->name : '';
                $data[$key]['company']      = !empty($row->company()->comp_name) ? $row->company()->comp_name : '';
                $data[$key]['designations'] = !empty($row->designations()) ? $row->designations() : '';
                $data[$key]['project']      = !empty($row->project()->project_name) ? $row->project()->project_name : '';
                $data[$key]['acting_benefit_amount']        = $row->acting_benefit_amount;
                $data[$key]['recruitment_type'] = !empty($row->recruitmentType()->title) ? $row->recruitmentType()->title : '';
                $data[$key]['status']        = ($row->is_active == 1) ? 'Active' : 'Inactive';

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

        $recruitment_type_id = optional($request)->recruitment_type_id;
        $haveExistData = DB::table('hr_payroll_salary_structure')
                        ->where([['is_active', 1], ['is_delete', 0]])->get();
        $haveExistData = $haveExistData->where('grade', $request->grade)->where('level', $request->level)->where('pay_scale_id', $request->pay_scale_id)->whereBetween('recruitment_type_id', $recruitment_type_id)->count();
        if ($haveExistData > 0) {
            return response()->json([
                'message'    => 'Data already exist..',
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }


        $payscale_id = $request->pay_scale_id;
        ## PF
        $pfData = HRS::getPfData($request->company_id, $request->project_id, $payscale_id);
        $pf_rec_ids =  explode(',',optional($pfData)->rec_type_ids);
        ## WF
        $wfData = HRS::getWfData($request->company_id, $request->project_id, $request->grade, $request->level, $payscale_id);
        $wf_rec_ids =  explode(',',optional($wfData)->rec_type_ids);
        ## EPS
        $epsData = HRS::getEpsData($request->company_id, $request->project_id, $request->grade, $payscale_id);
        $eps_rec_ids =  explode(',',optional($epsData)->rec_type_ids);
        ## OSF
        $osfData = HRS::getOsfData($request->company_id, $request->project_id, $payscale_id);
        $osf_rec_ids =  explode(',',optional($osfData)->rec_type_ids);
        ## INC
        $incData = HRS::getInsuranceData($request->company_id, $request->project_id, $payscale_id);
        $inc_rec_ids =  explode(',',optional($incData)->rec_type_ids);
        $pf_id = $wf_id = $eps_id = $osf_id = $inc_id = null;

        $passport = $this->getPassport($request, 'storex');
        if ($passport['isValid']) {

            try {
                DB::beginTransaction();

                if (!empty($recruitment_type_id)) {
                    foreach ($recruitment_type_id as $key => $rec_value) {

                        if (in_array($rec_value, $pf_rec_ids)) {$pf_id = $pfData->id;}
                        if (in_array($rec_value, $wf_rec_ids)) {$wf_id = $wfData->id;}
                        if (in_array($rec_value, $eps_rec_ids)) {$eps_id = $epsData->id;}
                        if (in_array($rec_value, $osf_rec_ids)) {$osf_id = $osfData->id;}
                        if (in_array($rec_value, $inc_rec_ids)) {$inc_id = $incData->id;}

                        $id = DB::table('hr_payroll_salary_structure')->insertGetId([
                            'grade' => $request->grade,
                            'level' => $request->level,
                            'basic' => $request->basic,
                            'pay_scale_id' => $request->pay_scale_id,
                            'company_id' => $request->company_id,
                            'project_id' => $request->project_id,
                            'recruitment_type_id' => $rec_value,
                            'acting_benefit_amount' => $request->acting_benefit_amount,
                            'designations' => implode(',', $request->designations),

                            'pf_id' => $pf_id,
                            'wf_id' => $wf_id,
                            'ps_id' => $eps_id,
                            'osf_id'=> $osf_id,
                            'inc_id'=> $inc_id,

                            'is_delete' => 0,
                            'is_active' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                        ]);

                        foreach($request->inc_amount as $key => $val){
                            DB::table('hr_payroll_salary_structure_details')->insert([
                                'salary_structure_id' => $id,
                                'inc_percentage' => $request->inc_percentage[$key],
                                'amount' => $request->inc_amount[$key],
                                'no_of_inc' => $request->inc_number_of_inc[$key],
                                'data_type' => 'increment',
                            ]);
                        }

                        foreach($request->allowance_id as $key => $val){
                            DB::table('hr_payroll_salary_structure_details')->insert([
                                'salary_structure_id' => $id,
                                'amount' => $request->allowance_amount[$key],
                                'allowance_type_id' => $request->allowance_id[$key],
                                'calculation_type' => $request->allowance_calculation_type[$key],
                                'data_type' => 'allowance',
                            ]);
                        }

                    }
                }

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

    public function update(Request $request)
    {
        $payscale_id = $request->pay_scale_id;
        ## PF
        $pfData = HRS::getPfData($request->company_id, $request->project_id, $payscale_id);
        ## WF
        $wfData = HRS::getWfData($request->company_id, $request->project_id, $request->grade, $request->level, $payscale_id);
        ## EPS
        $epsData = HRS::getEpsData($request->company_id, $request->project_id, $request->grade, $payscale_id);
        ## OSF
        $osfData = HRS::getOsfData($request->company_id, $request->project_id, $payscale_id);
        ## INC
        $incData = HRS::getInsuranceData($request->company_id, $request->project_id, $payscale_id);
        $pf_id = $wf_id = $eps_id = $osf_id = $inc_id = null;

        $pf_id  = $pfData->id ;
        $wf_id  = $wfData->id ;
        $eps_id = $epsData->id;
        $osf_id = $osfData->id;
        $inc_id = $incData->id;


        $passport = $this->getPassport($request, 'storex');
        if ($passport['isValid']) {

            try {
                DB::beginTransaction();

                $id = $request->edit_id;

                DB::table('hr_payroll_salary_structure')->where('id', $id)->update([
                    'grade' => $request->grade,
                    'level' => $request->level,
                    'basic' => $request->basic,
                    'pay_scale_id' => $request->pay_scale_id,
                    'company_id' => $request->company_id,
                    'project_id' => $request->project_id,
                    'recruitment_type_id' => implode(',', $request->recruitment_type_id),
                    'acting_benefit_amount' => $request->acting_benefit_amount,
                    'designations' => implode(',', $request->designations),

                    'pf_id' => $pf_id,
                    'wf_id' => $wf_id,
                    'ps_id' => $eps_id,
                    'osf_id'=> $osf_id,
                    'inc_id'=> $inc_id,

                    'is_delete' => 0,
                    'is_active' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ]);

                DB::table('hr_payroll_salary_structure_details')->where('salary_structure_id', $id)->delete();

                foreach($request->inc_amount as $key => $val){
                    if($request->inc_percentage[$key] != '' && $request->inc_amount[$key] != '' && $request->inc_number_of_inc[$key] != ''){
                        DB::table('hr_payroll_salary_structure_details')->insert([
                            'salary_structure_id' => $id,
                            'inc_percentage' => $request->inc_percentage[$key],
                            'amount' => $request->inc_amount[$key],
                            'no_of_inc' => $request->inc_number_of_inc[$key],
                            'data_type' => 'increment',
                        ]);
                    }
                }

                foreach($request->allowance_id as $key => $val){
                    if($request->allowance_amount[$key] != '' && $request->allowance_id[$key] != '' && $request->allowance_calculation_type[$key] != ''){
                        DB::table('hr_payroll_salary_structure_details')->insert([
                            'salary_structure_id' => $id,
                            'amount' => $request->allowance_amount[$key],
                            'allowance_type_id' => $request->allowance_id[$key],
                            'calculation_type' => $request->allowance_calculation_type[$key],
                            'data_type' => 'allowance',
                        ]);
                    }

                }

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

    public function delete($id)
    {
        try{
            DB::beginTransaction();
            $delete = DB::table('hr_payroll_salary_structure')->where('id', decrypt($id))->update(['is_delete' => 1]);
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

    public function viewSalaryStructure(Request $request){

        return view('HR.Payroll.SalaryStructure.index');
    }

    public function viewSalaryStructureBody(Request $request){

        $payscaleYearsData = DB::table('hr_payroll_payscale')->where([['is_delete', 0], ['is_active', 1]])->first();
        $payscaleStartEffDate = $payscaleYearsData->eff_date_start;

        $salary_struct = DB::table('hr_payroll_salary_structure as ss')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0]])
            ->where([['ss.grade', $request->grade], ['ss.level', $request->level], ['ss.pay_scale_id', $request->payscale_id], ['ss.recruitment_type_id', $request->recruitment_type_id]])
            ->join('hr_payroll_salary_structure_details as ssd', function($join){
                $join->on('ss.id', '=', 'ssd.salary_structure_id');
            })->get();

        $allowance_id = $salary_struct->where('data_type', 'allowance')->pluck('allowance_type_id');
        $allowance = DB::table('hr_payroll_allowance')->where([['is_active', 1], ['is_delete', 0]])->whereIn('id', $allowance_id)->get();

        $headerData = [];
        $total_year = 0;
        $basic = 0;
        $incrementData = [];
        $allowance_data = [];
        $data = [];

        #####################> 18-06-2023 <##########################
        $pfId  = count($salary_struct) > 0 ? $salary_struct[0]->pf_id  : 0;
        $wfId  = count($salary_struct) > 0 ? $salary_struct[0]->wf_id  : 0;
        $epsId = count($salary_struct) > 0 ? $salary_struct[0]->ps_id  : 0;
        $osfId = count($salary_struct) > 0 ? $salary_struct[0]->osf_id : 0;
        $incId = count($salary_struct) > 0 ? $salary_struct[0]->inc_id : 0;

        $pfSettingData = HRS::query_get_hr_payroll_settings_pf_data($id = $pfId);
        $wfSettingData = HRS::query_get_hr_payroll_settings_wf_data($wfId, $request->grade, $request->level);
        $EpsSettingData = HRS::query_get_hr_payroll_settings_pension_data($epsId);
        $EpsSettingDetailsData = HRS::query_get_hr_payroll_settings_pension_details_data();
        $OsfSettingData = HRS::query_get_hr_payroll_settings_osf_data($payscaleStartEffDate, $osfId);
        $IncSettingData = HRS::query_get_hr_payroll_settings_insurance_data($payscaleStartEffDate, $incId);
        // dd($pfSettingData, $wfSettingData,$EpsSettingData);
        ####################> 18-06-2023 <###########################

        $recruitment_type = $request->recruitment_type_id;
        $deductionDataArr = HRS::query_get_hr_payroll_deduction_data($recruitment_type);
        // dd($deductionDataArr, $recruitment_type);

        if(count($salary_struct) > 0){

            $ss = SalaryStructure::where([['grade', $request->grade], ['level', $request->level], ['pay_scale_id', $request->payscale_id],['recruitment_type_id', $request->recruitment_type_id]])->first();
            if($ss == null){ return; }

            $headerData['company']        = $ss->company()->comp_name;
            $headerData['designations']   = $ss->designations();
            // $headerData['recruitment_types']   = $recruitment_type;
            $headerData['recruitment_types']   = $ss->recruitmentType()->title;
            $headerData['project']        = $ss->project()->project_name;
            $headerData['grade'] = $salary_struct[0]->grade;
            $headerData['level'] = $salary_struct[0]->level;
            $headerData['basic'] = $salary_struct[0]->basic;
            $headerData['acting_benefit_amount'] = $salary_struct[0]->acting_benefit_amount;

            $no_of_inc_arr = [];
            $increment = [];
            $basic = $salary_struct[0]->basic;
            $salary_struct = $salary_struct->groupBy('data_type');
            $incrementData = $salary_struct['increment']->sortBy('no_of_inc');

            $i = 2;
            $increment[1] = 0;
            foreach($incrementData as $n){
                for(; $i<=$n->no_of_inc + 1; $i++){
                    $increment[$i] = $n->amount;
                }
            }

            $total_year = $incrementData->last()->no_of_inc;

            foreach($allowance as $al){
                $allowance_data[$al->benifit_type_uid][$al->id] = $salary_struct['allowance']->where('allowance_type_id', $al->id)->first();
            }

            $data[1]['basic'] = $basic;

            for($y = 1; $y<=($total_year + 1); $y++){
                $data[$y]['year'] = $y;
                $data[$y + 1]['basic'] = $data[$y]['basic'] + $increment[$y];
                $data[$y]['increment'] = $increment[$y];
                $data[$y]['total_basic'] = $data[$y]['basic'] + $increment[$y];

                $allowanceTot = [];
                foreach($allowance_data as $keyBen => $benType){
                    $allowanceTot[$keyBen] = 0;
                    foreach($benType as $key => $al){
                        $data[$y]['allowance'][$keyBen][$key] = ($al->calculation_type == 2) ? $al->amount : (($al->amount * $data[$y]['total_basic'])/100);
                        $allowanceTot[$keyBen] += $data[$y]['allowance'][$keyBen][$key];
                    }
                }

                $data[$y]['total_gross_a'] = $data[$y]['total_basic']   + (isset($allowanceTot[1]) ? $allowanceTot[1] : 0);
                $data[$y]['total_gross_b'] = $data[$y]['total_gross_a'] + (isset($allowanceTot[2]) ? $allowanceTot[2] : 0);
                $data[$y]['total_gross_c'] = $data[$y]['total_gross_b'] + (isset($allowanceTot[3]) ? $allowanceTot[3] : 0);


                $deductionTot = [];
                $data[$y]['deduction'] = array();
                $totalDeduction = 0;
                foreach($deductionDataArr as $dKey => $dValue){
                    $data[$y]['deduction'][$dKey] = array();

                    //================ Provident Fund (PF) Start ====================
                    if ($dKey == 'PF') {
                        // dd($pfSettingData);
                        if ( count($pfSettingData) > 0 && !empty($pfSettingData[0])) {

                            if ($pfSettingData[0]->calculation_type == 'percentage' ) {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($pfSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? $pfSettingData[0]->calculation_amount : 0;
                            }
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ Provident Fund (PF) End ====================

                    //================ Welfare Fund  Start ====================
                    if ($dKey == 'WF') {
                        // dd('wf' ,$wfSettingData);
                        /*
                        if (count($wfSettingData) > 0 && !empty($wfSettingData[0])) {

                            if ($wfSettingData[0]->interest_rate != null ) {

                                $data[$y]['deduction'][$dKey] = !empty($wfSettingData[0]->interest_rate) ? round((($data[$y]['total_basic']) * ($wfSettingData[0]->interest_rate)) / 100) : 0;

                            }else{
                                $data[$y]['deduction'][$dKey] = !empty($wfSettingData[0]->amount) ? $wfSettingData[0]->amount : 0;
                            }
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        */

                        // dd($wfSettingData, count($wfSettingData));
                        if (count($wfSettingData) > 0 && !empty($wfSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = $wfSettingData[0]->amount;
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }

                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ Welfare Fund  End ====================


                    //================ Pension Scheme Setting Start ====================
                    if ($dKey == 'EPS') {
                        // dd($EpsSettingData);

                        if (count($EpsSettingData) > 0 && !empty($EpsSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = !empty($EpsSettingData[0]->amount) ? $EpsSettingData[0]->amount : 0;
                            $totalDeduction += $data[$y]['deduction'][$dKey];
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }

                        /*
                        $epsDetails = $EpsSettingDetailsData->where('pension_id', $EpsSettingData[0]->id)->pluck('rate')->toArray();
                        foreach($epsDetails as $epsKey => $epsValue){

                            if ( ($y-1) == $epsKey) {
                                $data[$y]['deduction'][$dKey] = !empty($epsValue) ? round((($data[$y]['total_basic']) * ($epsValue)) / 100) : 0;
                                $totalDeduction += $data[$y]['deduction'][$dKey];
                            }
                        }
                        */
                    }
                    //================ Pension Scheme Setting End ====================


                    //================ OSF Settings Start ====================
                    if ($dKey == 'OSF') {
                        // dd($OsfSettingData);
                        if (count($OsfSettingData) > 0 && !empty($OsfSettingData[0])) {
                            if ($OsfSettingData[0]->calculation_type == 'percentage' ) {
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($OsfSettingData[0]->calculation_amount)) / 100) : 0;

                            }else{
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? $OsfSettingData[0]->calculation_amount : 0;
                            }
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ OSF Settings End ====================

                    //================ Insurance Start ====================
                    if ($dKey == 'INC') {
                        // dd($IncSettingData, count($IncSettingData));

                        if (count($IncSettingData) > 0 && !empty($IncSettingData[0])) {
                            if ($IncSettingData[0]->calculation_type == 'percentage' ) {
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($IncSettingData[0]->calculation_amount)) / 100) : 0;

                            }else{
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? $IncSettingData[0]->calculation_amount : 0;
                            }
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ Insurance End ====================

                }
                $data[$y]['totalDeduction'] = $totalDeduction;

                ## Remove Element Which Not Define
                $tmpDeductioArr = $data[$y]['deduction'];
                foreach($tmpDeductioArr as $tmpKey => $tmpVal){
                    if ($tmpDeductioArr[$tmpKey] < 1 || empty($tmpDeductioArr[$tmpKey])) {
                        unset($data[$y]['deduction'][$tmpKey]);
                    }
                }
                $deductionDataArr = $data[$y]['deduction'];
            }

            unset($data[$total_year + 2]);
            // dd($data);
            return view('HR.Payroll.SalaryStructure.body', compact('allowance', 'data', 'incrementData', 'headerData','deductionDataArr'));
        }
        else{

        }

        //dd($salary_struct['increment']);
        //dd($data);
        //dd($total_year);
        //dd($allowance_data);
        //dd($incrementData);


    }

    public function getData(Request $request){

        $recruitmrntType = DB::table('hr_recruitment_types')
                    ->where([['is_active', 1],['is_delete', 0]])
                    ->whereBetween('salary_method', ['auto', 'both'])
                    ->pluck('title', 'id')
                    ->toArray();

        $data = array();

        if ($request->context == 'getData') {

            $payscaleId = !empty($request->input('payScaleId')) ? $request->input('payScaleId') : null;
            $grade = !empty($request->input('grade')) ? $request->input('grade') : null;

            $queryData = DB::table("hr_payroll_salary_structure")
                    ->where([["is_delete", 0],["is_active", 1]])
                    ->where(function($query) use($payscaleId, $grade){
                        if(!empty($payscaleId)){
                            $query->where("pay_scale_id", $payscaleId);
                        }

                        if(!empty($grade)){
                            $query->where("grade", $grade);
                        }
                    })
                    ->selectRaw("grade, level, recruitment_type_id")
                    ->get();
                    
            if($queryData){
                $data = array(
                    'grade' => array_values(array_unique($queryData->pluck('grade')->toArray())),
                    'level' => array_values(array_unique($queryData->pluck('level')->toArray())),
                    'recruitment_type_id' => array_values(array_unique($queryData->pluck('recruitment_type_id')->toArray())),
                    'recruitment_type' => $recruitmrntType,
                );
            }
        }
        
        return response()->json($data);
    }

    public function getWfData(Request $request){
        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $projectId = (empty($request->project_id)) ? null : $request->project_id;
        $gradeId = (empty($request->grade)) ? null : $request->grade;
        $levelId = (empty($request->level)) ? null : $request->level;

        $data = [
            'wfData' => HRS::getWfData($companyId, $projectId, $gradeId, $levelId)
        ];

        // dd($data);

        return response()->json($data);

    }


    ##################################################
    public function getDeducData(Request $request){

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $projectId = (empty($request->project_id)) ? null : $request->project_id;
        $gradeId = (empty($request->grade)) ? null : $request->grade;
        $levelId = (empty($request->level)) ? null : $request->level;

        ## Get Pay Scall
        $payScallData = DB::table('hr_payroll_payscale')->where([['id', $request->pay_scale_id],['is_active',1],['is_delete',0]])->first();

        $f_start_date = !empty($payScallData->eff_date_start) ? new DateTime($payScallData->eff_date_start) : null;
        $f_end_date = !empty($payScallData->eff_date_end) ? new DateTime($payScallData->eff_date_end) : null;


        ## Deduction Data Array Start
        $rec_type = !empty($request->recruitment_type_id) ? $request->recruitment_type_id[0] : 'permanent';
        $getDeductionAllData = DB::table('hr_payroll_configuration_menu')->where([['is_delete', 0], ['is_active', 1]])->orderBy('eff_date_start','desc')->first();


        $deductionArr = [];
        $getDeductionAllData = !empty($getDeductionAllData->$rec_type) ? $getDeductionAllData->$rec_type : [];
        $deductionArray = !empty($getDeductionAllData) ? explode(',', $getDeductionAllData) : [];
        $deductionArr = !empty($deductionArray) ?  array_fill_keys($deductionArray, 0) : [];

        if (empty($deductionArr)) {
            return response()->json([
                'message'    => 'Deduction data not allocated for this pay scall year',
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }

        ## PF
        if( isset($deductionArr['pf']) ){
            $pfData = HRS::getPfData($companyId, $projectId);
            $deductionArr['pf'] = !empty($pfData) ? $pfData->id : 0;
        }

        ## WF
        if( isset($deductionArr['wf']) ){
            $wfData = HRS::getWfData($companyId, $projectId, $gradeId, $levelId);
            $deductionArr['wf'] = !empty($wfData) ? $wfData->id : 0;
        }

        ## EPS
        if( isset($deductionArr['eps']) ){
            $epsData = HRS::getEpsData($companyId, $projectId);
            $deductionArr['eps'] = !empty($epsData) ? $epsData->id : 0;
        }

        ## OSF
        if( isset($deductionArr['osf']) ){
            $osfData = HRS::getOsfData($companyId, $projectId);
            $deductionArr['osf'] = !empty($osfData) ? $osfData->id : 0;
        }

        ## INC
        if( isset($deductionArr['inc']) ){
            $incData = HRS::getInsuranceData($companyId, $projectId);
            $deductionArr['inc'] = !empty($incData) ? $incData->id : 0;
        }


        foreach ($deductionArr as $key => $value) {
            if ($deductionArr[$key] == 0) {
                return response()->json([
                    'message'    => $key.' settings is not configured...',
                    'status' => 'error',
                    'statusCode'=> 400,
                    'result_data' => ''
                ], 400);
            }
            // dd($deductionArr,$key,  $value, $deductionArr[$key]);
        }
        ## Deduction Data Array End
        // dd($deductionArr);

        return response()->json($deductionArr);
    }


    public function getStatus(Request $request){

        $payscale_id = $request->pay_scale_id;
        $payScaleStartDate = HRS::getPayScaleYearStartDateByPayScaleYearID($payscale_id);

        $fromDeductionTable = DB::table('hr_payroll_configuration_menu as dm')
                            ->where([['dm.is_active', 1],['dm.is_delete', 0]])->orderBy('eff_date_start','desc')
                            ->select('id','nonpermanent','permanent')->first();

        $d_permanet =  explode(',',optional($fromDeductionTable)->permanent);
        $d_nonpermanet =  explode(',',optional($fromDeductionTable)->nonpermanent);
        $margeDedArr = array_unique(array_merge($d_permanet, $d_nonpermanet));

        $outputArr = [];

        foreach ($margeDedArr as $key => $value) {
            if ($value == 'pf') {
                $data = DB::table('hr_payroll_settings_pf')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'wf') {
                $data = DB::table('hr_payroll_settings_wf')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'eps') {
                $data = DB::table('hr_payroll_settings_pension_setting')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'bonus') {
                $data = DB::table('hr_payroll_settings_bonus')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'gat') {
                $data = DB::table('hr_payroll_settings_gratuity')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'inc') {
                $data = DB::table('hr_payroll_settings_insurance')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'loan') {
                $data = DB::table('hr_payroll_settings_loan')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'sm') {
                $data = DB::table('hr_payroll_settings_security_money')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }

            if ($value == 'osf') {
                $data = DB::table('hr_payroll_settings_osf')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('effective_date', '<=', $payScaleStartDate)
                    ->count();
                $isHave = $data > 0 ? 1 : 0;
                $outputArr[$value] = $isHave;
            }
        }

        return response()->json($outputArr);

    }

}
