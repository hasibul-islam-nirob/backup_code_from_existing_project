<?php

namespace App\Http\Controllers\HR\Api;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use App\Model\HR\EmployeeResign;
use App\Model\HR\EmployeeDesignation;
use App\Model\HR\AllApproval;
use App\Services\CommonService as Common;
use App\Services\HrService;
use App\Services\RoleService as Role;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommonApiController extends Controller
{
    public function get_des_by_emp_id($empId)
    {
        try {
            $des = EmployeeDesignation::where([['is_active', 1], ['is_delete', 0]])
                ->find(
                    Employee::find($empId)->designation_id
                );

            if (!empty($des)) {
                return response()->json([
                    'message'    => "Data fetched successfully!!",
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => $des,
                ], 200);
            } else {
                return response()->json([
                    'message'    => "No data found!!",
                    'status' => 'error',
                    'statusCode' => 400,
                    'result_data' => ''
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'    => "Internal Server Error. Try Again!!",
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
