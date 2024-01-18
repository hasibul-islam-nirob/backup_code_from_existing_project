<?php

namespace App\Http\Controllers\HR\Configuration;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\AttendanceRules;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HR\Others\CommonController;


class AttendanceRulesController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'start_time'     => 'required',
                'end_time'       => 'required',
                // 'late_accept_minute'  => 'required',
                // 'early_accept_minute' => 'required',
                // 'ot_cycle_minute'     => 'required',
                // 'attendance_bypass[]' => 'required',
                'eff_date_start' => 'required',
            );

            $attributes = array(
                'start_time'     => 'Start Time',
                'end_time'       => 'End Time',
                // 'late_accept_minute'  => 'Late Accept Minute',
                // 'early_accept_minute' => 'Early Accept Minute',
                // 'ot_cycle_minute'     => 'OT Cycle Minute',
                // 'attendance_bypass[]' => 'Attendance Bypass',
                'eff_date_start' => 'Effective Date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();
            $CurrentSystemDate = (new DateTime())->format('Y-m-d');

            if ($requestData->is_active == 1 && $requestData->eff_date_start < $CurrentSystemDate) { // only view
                $IgnoreArray = ['delete', 'edit'=> false, 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit', 'send', 'btnHide' => true];
            }

            $errorMsg = $IgnoreArray;

            //dd($errorMsg);
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
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

            $totalRecords = AttendanceRules::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = AttendanceRules::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = AttendanceRules::from('hr_attendance_rules AS har')
                ->where('is_delete', 0)
                ->select('har.*')
                ->get();

            $data = array();
            $sno = $start + 1;
            
            foreach ($allData as $key => $row) {

                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['start_time']         = (new DateTime($row->start_time))->format('h:i a');      //Hour:Minute am/pm
                $data[$key]['end_time']           = (new DateTime($row->end_time))->format('h:i a');        //Hour:Minute am/pm
                // $data[$key]['ext_start_time']     = (new DateTime($row->ext_start_time))->format('h:i a');  //Hour:Minute am/pm

                $data[$key]['late_accept_minute']     = $row->late_accept_minute.' Minutes';
                $data[$key]['early_accept_minute']     = $row->early_accept_minute.' Minutes';
                $data[$key]['ot_cycle_minute']     = $row->ot_cycle_minute.' Minutes';

                // $data[$key]['leave_allow']     = !empty($row->leave_allow) ? $row->leave_allow : '-';
                // $data[$key]['lp_accept']     =  !empty($row->lp_accept) ? $row->lp_accept : '-';
                // $data[$key]['acction_for_lp']     =  !empty($row->acction_for_lp) ? $row->acction_for_lp : '-';

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

    public function insert(Request $request, $status)
    {
        
        $passport = $this->getPassport($request, 'store');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                
                $att_rules = new AttendanceRules();
                // $input_date = (new DateTime($request['eff_date_start']))->format('Y-m-d');
                $input_date = new DateTime($request['eff_date_start']);
                $if_exist = AttendanceRules::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = AttendanceRules::from('hr_attendance_rules')
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
                    $prev_row_greater_date = AttendanceRules::from('hr_attendance_rules')
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
                   
    
                    $att_rules->start_time       = $request['start_time'];
                    $att_rules->end_time         = $request['end_time'];
                    $att_rules->ext_start_time   = $request['ext_start_time'] ;

                    $att_rules->late_accept_minute   =  !empty($request['late_accept_minute']) ? $request['late_accept_minute'] : 0;
                    $att_rules->early_accept_minute   =  !empty($request['early_accept_minute']) ? $request['early_accept_minute'] : 0;
                    $att_rules->ot_cycle_minute   =  !empty($request['ot_cycle_minute']) ? $request['ot_cycle_minute'] : 0;

                    $designationArr = !empty($request->attendance_bypass) ? implode(',', $request->attendance_bypass) : [];
                    $att_rules->attendance_bypass   = $designationArr;

                    $lateBypass = !empty($request->late_bypass) ? implode(',', $request->late_bypass) : [];
                    $att_rules->late_bypass   = $lateBypass;


                    $att_rules->eff_date_start   = $input_date->format('Y-m-d');
                    $att_rules->eff_date_end     = $input_eff_end_date;
                    $att_rules->is_active        = ($status == 'send') ? 1 : 0;
                    $att_rules->save();
    
                    if ($status == 'send') {
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
                // dd($e);
                DB::rollback();
                return response()->json([
                    'message' => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function update(Request $request, $status)
    {

        $passport = $this->getPassport($request, 'update');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                $att_rules = AttendanceRules::where('id', decrypt($request['attendance_rule_id']))->first();

                if(empty($att_rules)){
                    return response()->json([
                        'message' => "Data not found.",
                        'status' => 'error',
                        'statusCode' => 400,
                        'result_data' => ''
                    ], 400); 
                }
            
                $input_date = new DateTime($request['eff_date_start']);
                // $if_exist = AttendanceRules::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();
                $if_exist = null;

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = AttendanceRules::from('hr_attendance_rules AS har')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('id', '<>',  $att_rules->id)
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
                    $prev_row_greater_date = AttendanceRules::from('hr_attendance_rules AS har')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('id', '<>',  $att_rules->id)
                        ->where('eff_date_start', '>', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'asc')
                        ->first();

                    $input_eff_end_date = null;
                    if(!empty($prev_row_greater_date)){
                        $input_eff_end_date = new DateTime($prev_row_greater_date->eff_date_start);
                        $input_eff_end_date = $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                        // $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                    }

                    // $designationArr = implode(', ', $request->attendance_bypass);
    
                    $att_rules->start_time       = $request['start_time'];
                    $att_rules->end_time         = $request['end_time'];
                    $att_rules->ext_start_time   = $request['ext_start_time'];

                    $att_rules->late_accept_minute   = $request['late_accept_minute'];
                    $att_rules->early_accept_minute   = $request['early_accept_minute'];
                    $att_rules->ot_cycle_minute   = $request['ot_cycle_minute'];

                    $designationArr = !empty($request->attendance_bypass) ? implode(',', $request->attendance_bypass) : [];
                    $att_rules->attendance_bypass   = $designationArr;

                    $lateBypass = !empty($request->late_bypass) ? implode(',', $request->late_bypass) : [];
                    $att_rules->late_bypass   = $lateBypass;

                    $att_rules->eff_date_start   = $input_date->format('Y-m-d');
                    $att_rules->eff_date_end     = $input_eff_end_date;
                    $att_rules->is_active        = ($status == 'send') ? 1 : 0;
    
                    if ($status == 'send') {
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
    
                    $att_rules->save();
    
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
                // dd($e);

                DB::rollback();
                return response()->json([
                    'message' => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function view($id)
    {
        $data = AttendanceRules::where('id', decrypt($id))->where('is_delete', 0)->first();

        $data->start_time         = (new DateTime($data->start_time))->format('h:i a');      //Hour:Minute am/pm
        $data->end_time           = (new DateTime($data->end_time))->format('h:i a');        //Hour:Minute am/pm
        $data->ext_start_time     = (new DateTime($data->ext_start_time ))->format('h:i a');  //Hour:Minute am/pm
        $data->eff_date_start     = (new DateTime($data->eff_date_start))->format('d-m-Y');

        if($data->eff_date_end != null){
            $end_date = (new DateTime($data->eff_date_end))->format('d-m-Y');
            $data->eff_date_end = $end_date;
        } else {
            $data->eff_date_end = null;
        }

        return response()->json($data);
    }

    public function delete($id){

        return CommonController::delete_application('\\App\\Model\\HR\\AttendanceRules', $id);
    }
}
