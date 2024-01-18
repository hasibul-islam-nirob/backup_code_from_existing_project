<?php

namespace App\Http\Controllers\HR\Payroll;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\PayrollConfiguration;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\HrService as HRS;
use App\Http\Controllers\HR\Others\CommonController;

class PayrollConfigurationController extends Controller
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
                'rectuitment_type' => 'required',
                'employee_type' => 'required',
                'eff_date_start' => 'required',
            );

            $attributes = array(
                // 'group_id' => 'Group',
                'company_id' => 'Company',
                'project_id' => 'Project',
                'rectuitment_type' => 'Rectuitment Type',
                'employee_type' => 'Employee Type',
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

            $totalRecords = PayrollConfiguration::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = PayrollConfiguration::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = PayrollConfiguration::from('hr_payroll_configuration AS hpcm')
                ->where('is_delete', 0)
                ->select('hpcm.*')
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

                // $data[$key]['group']                = $row->group['group_name'];
                $data[$key]['company']              = $row->company['comp_name'];
                $data[$key]['project']              = $row->project()->project_name;

                $data[$key]['rectuitment_type']     = $row->rectuitment_type == 'permanent' ? 'Permanent' : 'Non Permanent';
                $data[$key]['employee_type']        = ucfirst($row->employee_type); // ucfirst => is First Later is Capital
                $data[$key]['eff_date_start']     = (new DateTime($row->eff_date_start))->format('d-m-Y');

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041">Active</span>';
                }else {
                    $statusFlag = '<span style="color: #d40f0f">Inactive</span>';
                }
                $data[$key]['status']       = $statusFlag;

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


                $recType = !empty($request->rectuitment_type) ? $request->rectuitment_type : null;
                $empType = !empty($request->employee_type) ? $request->employee_type : null;

                $input_date = !empty($request->eff_date_start) ? new DateTime($request->eff_date_start) : new DateTime();

                if ($recType == 'permanent' && $empType == 'permanent') {

                    $checkPermanentActiveData = DB::table('hr_payroll_configuration')->where([['is_active', 1], ['is_delete', 0],['rectuitment_type', $recType],['employee_type', $empType]])->count();

                    if ($checkPermanentActiveData > 0) {
                        return response()->json(1);
                    }
                }

                $payrollConfig = new PayrollConfiguration();

                $payrollConfig->branch_id = $request->branch_id;
                // $payrollConfig->group_id = $request->group_id;
                $payrollConfig->company_id = $request->company_id;
                $payrollConfig->project_id = $request->project_id;

                $payrollConfig->rectuitment_type = $request->rectuitment_type;
                $payrollConfig->employee_type = $request->employee_type;

                $payrollConfig->eff_date_start   = $input_date->format('Y-m-d');
                $payrollConfig->exp_effective_date   = $input_date->format('Y-m-d');

                $payrollConfig->save();
    
                DB::commit();
                
                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
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


                $recType = !empty($request->rectuitment_type) ? $request->rectuitment_type : null;
                $empType = !empty($request->employee_type) ? $request->employee_type : null;

                $input_date = !empty($request->eff_date_start) ? new DateTime($request->eff_date_start) : new DateTime();

                if ($recType == 'permanent' && $empType == 'permanent') {

                    $checkPermanentActiveData = DB::table('hr_payroll_configuration')->where([['id', $request['edit_id']],['is_active', 1], ['is_delete', 0],['rectuitment_type', $recType],['employee_type', $empType]])->count();

                    if ($checkPermanentActiveData > 0) {
                        return response()->json(1);
                    }
                }

                $payrollConfig = PayrollConfiguration::where('id', $request['edit_id'])->first();

                $payrollConfig->branch_id = $request->branch_id;
                // $payrollConfig->group_id = $request->group_id;
                $payrollConfig->company_id = $request->company_id;
                $payrollConfig->project_id = $request->project_id;

                $payrollConfig->rectuitment_type = $request->rectuitment_type;
                $payrollConfig->employee_type = $request->employee_type;

                $payrollConfig->eff_date_start   = $input_date->format('Y-m-d');
                $payrollConfig->exp_effective_date   = $input_date->format('Y-m-d');

                $payrollConfig->save();
    
                DB::commit();
                
                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
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

    public function get($id)
    {
        $data =  DB::table('hr_payroll_configuration')->where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\PayrollConfiguration', $id);
    }

}
