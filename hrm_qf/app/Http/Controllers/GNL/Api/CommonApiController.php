<?php

namespace App\Http\Controllers\GNL\Api;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use App\Model\HR\EmployeeDesignation;
use Illuminate\Support\Facades\DB;
class CommonApiController extends Controller
{
    public function get_des_by_emp_id($empId)
    {
        try {
            $des = DB::table('hr_designations')->where([['is_active', 1], ['is_delete', 0]])
                ->find(
                    DB::table('hr_employees')->find($empId)->id
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
