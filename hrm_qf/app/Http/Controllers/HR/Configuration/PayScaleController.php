<?php

namespace App\Http\Controllers\HR\Configuration;

use DateTime;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Model\HR\PayScale;
use App\Services\CommonService as Common;


class PayScaleController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'name' => 'required',
            );

            $attributes = array(
                'name'        => 'Name',
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


            $totalRecords = DB::table('hr_payroll_payscale')->select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = DB::table('hr_payroll_payscale')->select('count(*) as allcount')->where('is_delete', 0)->where('name', 'like', '%' .$searchValue . '%')->count();

            $allData  = DB::table('hr_payroll_payscale')
                ->where('is_delete', 0)
                ->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $countExistRecInSS= DB::table('hr_payroll_salary_structure')->where([['is_active', 1],['is_delete', 0],['pay_scale_id', $row->id]])->count();

                // $systemDate = Common::systemCurrentDate();
                $systemDate = date('Y-m-d');
                if($systemDate > $row->eff_date_start || $countExistRecInSS > 0){
                    $IgnoreArray = ['view','delete', 'edit', 'send', 'isActive', 'message' => "Permission Denied", 'btnHide' => true];
                }else{
                    $IgnoreArray = [];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['name']        = $row->name;
                $data[$key]['eff_date_start']        = $row->eff_date_start;
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

    public function insert(Request $request)
    {

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                $pay_scale = new PayScale();
                $input_date = new DateTime($request['eff_date_start']);

                $if_exist =  PayScale::whereDate('eff_date_start', '=', $input_date->format('Y-m-d'))->exists();

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = PayScale::from('hr_payroll_payscale AS hpp')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('eff_date_start', '<', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'desc')
                        ->first();

                    if(!empty($prev_row_smaller_date)){
                        $prev_eff_date_end = clone $input_date;
                        $prev_eff_date_end = $prev_eff_date_end->modify('-1 day')->format('Y-m-d');
                        $prev_row_smaller_date->eff_date_end = $prev_eff_date_end;
                        // $prev_row_smaller_date->active_status = 0;
                        $prev_row_smaller_date->save();
                    }


                     ## Input Date theke boro date thakle current row er end date a (query row er StartDate - 1) porbe, boro date na thakle NULL porbe
                     $prev_row_greater_date = PayScale::from('hr_payroll_payscale AS hpp')
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


                    $pay_scale->name             = $request['name'];
                    $pay_scale->eff_date_start   = $input_date->format('Y-m-d');
                    $pay_scale->eff_date_end     = $input_eff_end_date;
                    // $pay_scale->active_status    = 1;
                    $pay_scale->is_active        = 1;
                    $pay_scale->is_delete        = 0;
                    $pay_scale->save();

                    DB::commit();
                }
                // ss($request->all(), $input_date, $if_exist, $prev_row_smaller_date, $prev_row_greater_date);
                // DB::table('hr_payroll_payscale')->insert([
                //     'name' => $request->name,
                //     'is_delete' => 0,
                // ]);

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

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                $pay_scale = PayScale::where('id', decrypt($request['edit_id']))->first();

                if(empty($pay_scale)){
                    return response()->json([
                        'message' => "Data not found.",
                        'status' => 'error',
                        'statusCode' => 400,
                        'result_data' => ''
                    ], 400);
                }

                $input_date = new DateTime($request['eff_date_start']);

                $if_exist = null;

                if(!$if_exist){

                    ## Input Date theke choto date thakle last choto date er row te end date (InputDate - 1) porbe, else Nothing
                    $prev_row_smaller_date = PayScale::from('hr_payroll_payscale AS hpp')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('id', '<>',  $pay_scale->id)
                        ->where('eff_date_start', '<', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'desc')
                        ->first();

                    if(!empty($prev_row_smaller_date)){
                        $prev_eff_date_end = clone $input_date;
                        $prev_eff_date_end = $prev_eff_date_end->modify('-1 day')->format('Y-m-d');
                        $prev_row_smaller_date->eff_date_end = $prev_eff_date_end;
                        // $prev_row_smaller_date->active_status = 0;
                        $prev_row_smaller_date->save();
                    }

                    ## Input Date theke boro date thakle current row er end date a (query row er StartDate - 1) porbe, boro date na thakle NULL porbe
                    $prev_row_greater_date = PayScale::from('hr_payroll_payscale AS hpp')
                        ->where('is_delete', 0)
                        ->where('is_active', 1)
                        ->where('id', '<>',  $pay_scale->id)
                        ->where('eff_date_start', '>', $input_date->format('Y-m-d'))
                        ->orderBy('eff_date_start', 'asc')
                        ->first();

                    $input_eff_end_date = null;
                    if(!empty($prev_row_greater_date)){
                        $input_eff_end_date = new DateTime($prev_row_greater_date->eff_date_start);
                        $input_eff_end_date = $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                        // $input_eff_end_date->modify('-1 day')->format('Y-m-d');
                    }


                    $pay_scale->name             = $request['name'];
                    $pay_scale->eff_date_start   = $input_date->format('Y-m-d');
                    $pay_scale->eff_date_end     = $input_eff_end_date;
                    // $pay_scale->active_status    = 1;
                    $pay_scale->is_active        = 1;
                    $pay_scale->is_delete        = 0;
                    $pay_scale->save();

                    DB::commit();

                }

                // DB::table('hr_payroll_payscale')->where('id', decrypt($request->edit_id))->update([
                //     'name' => $request->name,
                //     'is_delete' => 0,
                // ]);

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

        $delete = DB::table('hr_payroll_payscale')->where('id', decrypt($id))->update(['is_delete' => 1]);

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


    public function get(Request $request)
    {
        $editData = DB::table('hr_payroll_payscale')->find(decrypt($request->id));

        return response()->json($editData);
    }


}
