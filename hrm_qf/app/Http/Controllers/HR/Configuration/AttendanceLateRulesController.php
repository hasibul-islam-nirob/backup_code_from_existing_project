<?php

namespace App\Http\Controllers\HR\Configuration;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\AttendanceLateRules;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HR\Others\CommonController;
use App\Services\HrService as HRS;

class AttendanceLateRulesController extends Controller
{
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'start_time'     => 'required',
                // 'end_time'       => 'required',
                // 'late_accept_minute'  => 'required',
                // 'early_accept_minute' => 'required',
                // 'ot_cycle_minute'     => 'required',
                // // 'attendance_bypass[]' => 'required',
                'eff_date_start' => 'required',
            );

            $attributes = array(
                // 'start_time'     => 'Start Time',
                // 'end_time'       => 'End Time',
                // 'late_accept_minute'  => 'Late Accept Minute',
                // 'early_accept_minute' => 'Early Accept Minute',
                // 'ot_cycle_minute'     => 'OT Cycle Minute',
                // // 'attendance_bypass[]' => 'Attendance Bypass',
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
                $IgnoreArray = ['delete', 'edit' => false, 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit'  => false, 'send', 'btnHide' => true];
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

            $totalRecords = AttendanceLateRules::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = AttendanceLateRules::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = AttendanceLateRules::from('hr_attendance_late_rules AS har')
            ->where('is_delete', 0)
            ->select('har.*')
            ->get();

            $data = array();
            $sno = $start + 1;
            
            foreach ($allData as $key => $row) {
                // $d = json_decode($row->lp_breakdown);
                // ss($d, $row);
                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }


                $data[$key]['id']                 = $sno;
                // $data[$key]['start_time']         = (new DateTime($row->start_time))->format('h:i a');      //Hour:Minute am/pm
                // $data[$key]['end_time']           = (new DateTime($row->end_time))->format('h:i a');        //Hour:Minute am/pm

                // $designationDataAray = array();
                // if (!empty($row->late_bypass)) {
                    
                //     $baypass = $row->late_bypass;
                //     $arr = explode(",", $baypass);

                //     $designationData = HRS::fnForDesignationData($arr);

                //     foreach($designationData as $item){
                //         array_push($designationDataAray, $item);
                //     }
                    
                   
                    
                // }
                

                ## ===== LP Break Down Start =========
                // $lpDay = array();
                // $lpNumber = array();
                // if (!empty($row->lp_breakdown)) {
                //     $json_str1 =  $row->lp_breakdown;
                //     $decode_data = json_decode($json_str1);
                //     foreach ($decode_data as $obj) {
                //         foreach ($obj as $key2 => $value) {
                //             array_push($lpDay, $key2);
                //             array_push($lpNumber, $value);
                //         }
                //     }
                // }
                
                ## ===== LP Break Down End =========

                ## ===== LP Deduction Start =======
                // $deductFrom = array();
                // $deductNumber = array();
                // if (!empty($row->lp_deduction)) {
                //     $json_str2 =  $row->lp_deduction;
                //     $decode_data = json_decode($json_str2);
                //     foreach ($decode_data as $obj) {
                //         foreach ($obj as $key3 => $value) {
                //             array_push($deductFrom, $key3);
                //             array_push($deductNumber, $value);
                //         }
                //     }
                // }
                
                ## ===== LP Deduction End =========

                // $data[$key]['lp_breakdown_day']     =  $lpDay;
                // $data[$key]['lp_breakdown_number']  =  $lpNumber;
                // $data[$key]['lp_deduct_from']     =  $deductFrom;
                // $data[$key]['lp_deduct_number']  =  $deductNumber;
                // $data[$key]['late_bypass']     =  $designationDataAray;
                $data[$key]['title']     =  "Late rules effective for";

                $data[$key]['eff_date_start']     = (new DateTime($row->eff_date_start))->format('d-m-Y');
                $data[$key]['eff_date_end']       = (new DateTime($row->eff_date_end))->format('d-m-Y');
                $data[$key]['action']             = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row['id']), $IgnoreArray);

                $sno++;
            }
            // dd($data);
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
                
                $att_rules = new AttendanceLateRules();
                // $input_date = (new DateTime($request['eff_date_start']))->format('Y-m-d');
                $input_date = new DateTime($request['eff_date_start']);
                $if_exist = AttendanceLateRules::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                if(!$if_exist){
                    
                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = AttendanceLateRules::from('hr_attendance_late_rules AS har')
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
                    $prev_row_greater_date = AttendanceLateRules::from('hr_attendance_late_rules AS har')
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
                   

                    ###########################################
                    // $lateBypass = !empty($request->attendance_bypass) ? implode(',', $request->attendance_bypass) : [];
                    

                    if ( !empty($request->lateDeductionName) || !empty($request->lateDeductionNum) ) {
                        $jsonDeductionKeys = json_encode($request->lateDeductionName);
                        $jsonDeductionNum = json_encode($request->lateDeductionNum);
                        // Parse the JSON-encoded arrays 
                        $deduction_array1 = json_decode($jsonDeductionKeys);
                        $deduction_array2 = json_decode($jsonDeductionNum);
            
                        // Create an empty array to hold the key-value pairs
                        $breakdown_key_value_pairs = array();
            
                        // Iterate over the first array and use each value as a key in a new JSON object
                        for ($i = 0; $i < count($deduction_array1); $i++) {
                            $key = $deduction_array1[$i];
                            
                            // For each key, retrieve the corresponding value from the second array
                            $value = $deduction_array2[$i];
                            
                            // Create a new JSON object with the key-value pair
                            $json_obj = new \stdClass();
                            $json_obj->$key = $value;
                            
                            // Add the new JSON object to the array of key-value pairs
                            $deduction_key_value_pairs[] = $json_obj;
                        }
            
                        // Encode the array of key-value pairs into a JSON string
                        $deduction_json_output = json_encode($deduction_key_value_pairs);
            
                        // ss($request->all(), $jsonDeductionKeys, $jsonDeductionNum, $deduction_json_output);
                    }else{
                        $deduction_json_output = [];
                    }
            
                    if ( !empty($request->lateBreakdownName) || !empty($request->lateBreakdownNum) ) {
                        $jsonBreakdownKeys = json_encode($request->lateBreakdownName);
                        $jsonBreakdownNum = json_encode($request->lateBreakdownNum);
                        // Parse the JSON-encoded arrays
                        $breakdown_array1 = json_decode($jsonBreakdownKeys);
                        $breakdown_array2 = json_decode($jsonBreakdownNum);
            
                        // Create an empty array to hold the key-value pairs
                        $breakdown_key_value_pairs = array();
            
                        // Iterate over the first array and use each value as a key in a new JSON object
                        for ($i = 0; $i < count($breakdown_array1); $i++) {
                            $key = $breakdown_array1[$i];
                            
                            // For each key, retrieve the corresponding value from the second array
                            $value = $breakdown_array2[$i];
                            
                            // Create a new JSON object with the key-value pair
                            $json_obj = new \stdClass();
                            $json_obj->$key = $value;
                            
                            // Add the new JSON object to the array of key-value pairs
                            $breakdown_key_value_pairs[] = $json_obj;
                        }
            
                        // Encode the array of key-value pairs into a JSON string
                        $breakdown_json_output = json_encode($breakdown_key_value_pairs);
            
                        // ss($request->all(), $jsonBreakdownKeys, $jsonBreakdownNum, $breakdown_json_output);
                    }else{
                        $breakdown_json_output = [];
                    }

                    // ss($breakdown_json_output, $deduction_json_output);

                    // $att_rules->late_bypass   = $lateBypass;
                    $att_rules->lp_breakdown = $breakdown_json_output;
                    $att_rules->lp_deduction   = $deduction_json_output;
                    ######################################
                    


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



    public function update(Request $request, $status)
    {

        // ss($request->all(), $request->eff_date_start);
        $passport = $this->getPassport($request, 'update');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
               
                $att_rules = AttendanceLateRules::where('id', decrypt($request['attendance_late_rule_id']))->first();


                if(empty($att_rules)){
                    return response()->json([
                        'message' => "Data not found.",
                        'status' => 'error',
                        'statusCode' => 400,
                        'result_data' => ''
                    ], 400); 
                }
            
                $input_date = new DateTime($request['eff_date_start']);
                $if_exist = AttendanceLateRules::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = AttendanceLateRules::from('hr_attendance_late_rules AS har')
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
                    $prev_row_greater_date = AttendanceLateRules::from('hr_attendance_late_rules AS har')
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

    
                    ## --------------------------
                    if ( !empty($request->lateDeductionName) || !empty($request->lateDeductionNum) ) {
                        $jsonDeductionKeys = json_encode($request->lateDeductionName);
                        $jsonDeductionNum = json_encode($request->lateDeductionNum);
                        // Parse the JSON-encoded arrays 
                        $deduction_array1 = json_decode($jsonDeductionKeys);
                        $deduction_array2 = json_decode($jsonDeductionNum);

                        // Create an empty array to hold the key-value pairs
                        $breakdown_key_value_pairs = array();

                        // Iterate over the first array and use each value as a key in a new JSON object
                        for ($i = 0; $i < count($deduction_array1); $i++) {
                            $key = $deduction_array1[$i];
                            
                            // For each key, retrieve the corresponding value from the second array
                            $value = $deduction_array2[$i];
                            
                            // Create a new JSON object with the key-value pair
                            $json_obj = new \stdClass();
                            $json_obj->$key = $value;
                            
                            // Add the new JSON object to the array of key-value pairs
                            $deduction_key_value_pairs[] = $json_obj;
                        }

                        // Encode the array of key-value pairs into a JSON string
                        $deduction_json_output = json_encode($deduction_key_value_pairs);

                        // ss($request->all(), $jsonDeductionKeys, $jsonDeductionNum, $deduction_json_output);
                    }else{
                        $deduction_json_output = [];
                    }

                    if ( !empty($request->lateBreakdownName) || !empty($request->lateBreakdownNum) ) {
                        $jsonBreakdownKeys = json_encode($request->lateBreakdownName);
                        $jsonBreakdownNum = json_encode($request->lateBreakdownNum);
                        // Parse the JSON-encoded arrays
                        $breakdown_array1 = json_decode($jsonBreakdownKeys);
                        $breakdown_array2 = json_decode($jsonBreakdownNum);

                        // Create an empty array to hold the key-value pairs
                        $breakdown_key_value_pairs = array();

                        // Iterate over the first array and use each value as a key in a new JSON object
                        for ($i = 0; $i < count($breakdown_array1); $i++) {
                            $key = $breakdown_array1[$i];
                            
                            // For each key, retrieve the corresponding value from the second array
                            $value = $breakdown_array2[$i];
                            
                            // Create a new JSON object with the key-value pair
                            $json_obj = new \stdClass();
                            $json_obj->$key = $value;
                            
                            // Add the new JSON object to the array of key-value pairs
                            $breakdown_key_value_pairs[] = $json_obj;
                        }

                        // Encode the array of key-value pairs into a JSON string
                        $breakdown_json_output = json_encode($breakdown_key_value_pairs);

                        // ss($request->all(), $jsonBreakdownKeys, $jsonBreakdownNum, $breakdown_json_output);
                    }else{
                        $breakdown_json_output = [];
                    }

                    // ss($breakdown_json_output, $deduction_json_output);
                    ## --------------------------

                    ###########################################
                    // $lateBypass = !empty($request->attendance_bypass) ? implode(',', $request->attendance_bypass) : [];
                    // $att_rules->late_bypass   = $lateBypass;
                    $att_rules->lp_breakdown = $breakdown_json_output;
                    $att_rules->lp_deduction   = $deduction_json_output;



                    ######################################

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
        $data = AttendanceLateRules::where('id', decrypt($id))->where('is_delete', 0)->first();

        $data->eff_date_start     = (new DateTime($data->eff_date_start))->format('d-m-Y');

        if($data->eff_date_end != null){
            $end_date = (new DateTime($data->eff_date_end))->format('d-m-Y');
            $data->eff_date_end = $end_date;
        } else {
            $data->eff_date_end = null;
        }

        // dd($data);
        return response()->json($data);
    }


    public function delete($id){

        return CommonController::delete_application('\\App\\Model\\HR\\AttendanceLateRules', $id);
    }


}
