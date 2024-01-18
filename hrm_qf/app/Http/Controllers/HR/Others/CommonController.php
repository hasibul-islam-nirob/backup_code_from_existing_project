<?php

namespace App\Http\Controllers\HR\Others;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Process\ApplicationProcessController;
use App\Model\HR\Employee;
use App\Services\HrService as HRS;
use App\Services\HtmlService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getDistricts(Request $req)
    {
        $districts = DB::table('gnl_districts')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->divisionId != '' ? $districts->where('division_id', $req->divisionId) : false;

        $districts = $districts->pluck('district_name', 'id')->all();

        return response()->json($districts);
    }

    public function getUpazilas(Request $req)
    {
        $upazilas = DB::table('gnl_upazilas')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->districtId != '' ? $upazilas->where('district_id', $req->districtId) : false;

        $upazilas = $upazilas->pluck('upazila_name', 'id')->all();

        return response()->json($upazilas);
    }

    public function getUnions(Request $req)
    {
        $unions = DB::table('gnl_unions')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->upazilaId != '' ? $unions->where('upazila_id', $req->upazilaId) : false;

        $unions = $unions->pluck('union_name', 'id')->all();

        return response()->json($unions);
    }

    public function getVillages(Request $req)
    {
        $villages = DB::table('gnl_villages')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->unionId != '' ? $villages->where('union_id', $req->unionId) : false;

        $villages = $villages->pluck('village_name', 'id')->all();

        return response()->json($villages);
    }

    public function getBanks(Request $req){
        $banks = DB::table('hr_banks')
            ->where([
                ['is_delete', 0],
            ]);

        $req->id != '' ? $banks->where('id', $req->id) : false;

        $banks = $banks->pluck('name', 'id')->all();

        return response()->json($banks);
    }

    public function getBankBranches(Request $req){
        $branch = DB::table('hr_bank_branches')
            ->where([
                ['is_delete', 0],
                ['bank_id', $req->bankId]
            ]);

        //$req->bankId != '' ? $branch->where('bank_id', $req->bankId) : false;

        $branch = $branch->pluck('name', 'id')->all();

        return response()->json($branch);
    }

    public function getProjectType(Request  $req){
        $projTypes = DB::table('gnl_project_types')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
                ['project_id', $req->projectId]
            ]);

        //$req->bankId != '' ? $branch->where('bank_id', $req->bankId) : false;

        $projTypes = $projTypes->pluck('project_type_name', 'id')->all();

        return response()->json($projTypes);
    }

    /*Get employee by branch*/
    // public function get_employee_by_branch(Request $request, $empId){
    //     if ($request->isMethod('get')){
    //         $employees = HrService::getEmployeeByBranch($empId);
    //         if ($employees['status'] == true){
    //             return response()->json($employees['data'],200);
    //         }
    //         else{
    //             return response()->json(['error' => 'Internal Server Error'],500);
    //         }
    //     }
    //     else{
    //         return response()->json(['error' => 'Method Not Allowed'],405);
    //     }
    // }
    /*Get employee by branch*/

    public function get_employees_options_by_branch(Request $request, $branchId){
        $branchArr = [
            'branchId'=>$branchId
        ];
        return response()->json(HRS::getOptionsForEmployee($branchArr));
    }

    public function search_employee_and_get_options(Request $request){

        $branchId = null; $departmentId = null; $designationId = null; $empCode = null;
        if(isset($request['branch_id']) && $request['branch_id'] != ''){
            $branchId = $request['branch_id'];
        }
        if(isset($request['department_id']) && $request['department_id'] != ''){
            $departmentId = $request['department_id'];
        }
        if(isset($request['designation_id']) && $request['designation_id'] != ''){
            $designationId = $request['designation_id'];
        }
        return response()->json(HtmlService::searchEmployeeAndGetOptions($branchId, $departmentId, $designationId, $empCode));
    }

    /* For approval */
    public static function get_first_approval($eventId, $permission_for, $applicant){

        // ss($eventId, $permission_for, $applicant->department_id, $applicant->designation_id);
        // return DB::table('hr_reporting_boss_config')
        //     ->where([['event_id', $eventId], ['permission_for', $permission_for]])
        //     ->where([['department_for_id', $applicant->department_id], ['designation_for_id', $applicant->designation_id]])
        //     ->where('level', 1)
        //     ->first();

        return  DB::table('hr_reporting_boss_config')
            ->where([['event_id', $eventId], ['permission_for', $permission_for]])
            ->where([['department_for_id', $applicant->department_id], ['designation_for_id', $applicant->designation_id]])
            ->where([['level', 1],['is_delete', 0],['is_active', 1]])
            ->first();

    }

    public static function get_stage($config){
        return $config->designation_id . "-" .
                $config->department_id . "-" .
                (($config->employee_from == null) ? 'ho' : $config->employee_from) . "-" .
                $config->data_modification;
    }

    public static function get_application($model, $applicationId, $with=null){

        $id = decrypt($applicationId);
        $queryData = 0;

        if($with == null){
            $queryData = $model::find($id);
        }
        else{
            $queryData = $model::with($with)->find($id);
        }


        if ($queryData) {
            $responseData = [
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => $queryData
            ];
            return response()->json($responseData, 200);
        } else {
            $responseData = [
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => ''
            ];
            return response()->json($responseData, 500);
        }
    }

    public static function send_application($model, $applicationId, $eventId){

        $appl    = $model::find(decrypt($applicationId));

        $applicant = Employee::find($appl->emp_id);


        if (empty($applicant)) {
            
            (new ApplicationProcessController)->approve($appl, $eventId);
            $appl->current_stage = null;
            $appl->is_active = 1;

        }else{
            $permission_for = ($appl->branch_id == 1) ? "ho" : "bo";

            $first_approval = self::get_first_approval($eventId, $permission_for, $applicant);
    
            if (empty($first_approval)) {
    
                (new ApplicationProcessController)->approve($appl, $eventId);
    
                return response()->json([
                    'message'    => "Application Sent and Approved!!",
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);
            }
    
            $appl->current_stage = self::get_stage($first_approval);
            $appl->is_active = 3;
        }

        
        $send = $appl->save();

        if ($send) {
            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Successfully send',
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'statusCode' => 500,
                'message' => 'Error to send application',
                'result_data' => ''
            ], 500);
        }
    }

    public static function delete_application($model, $applicationId){
        $targetRow            = $model::where('id', decrypt($applicationId))->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();

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
    /* For approval */
}
