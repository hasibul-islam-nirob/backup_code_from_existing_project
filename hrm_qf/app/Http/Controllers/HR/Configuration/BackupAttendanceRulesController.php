<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use App\Model\HR\AttendanceRules;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BackupAttendanceRulesController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index()
    {
        $attendanceRules = AttendanceRules::get();

        return view('HR.Configuration.AttendanceRules.index', compact('attendanceRules'));
    }

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'start_time' => 'required',
                'end_time' => 'required',
                'ext_start_time' => 'required',
            );

            $attributes = array(
                'start_time'     => 'Application Type',
                'end_time'        => 'Reason',
                'ext_start_time'        => 'Extended Entry Time',
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

    public function add(Request $request){

        if ($request->isMethod('post')) {

            $passport = $this->getPassport($request, 'store');
            
            if ($passport['isValid']) {

                try {
                    // comment for temporary
                    // AttendanceRules::truncate();

                    AttendanceRules::insert([
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'ext_start_time' => $request->ext_start_time,
                    ]);

                    return response()->json([
                        'message'    => $passport['message'],
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json([
                        'message'    => "Internal Server Error. Try Again!!",
                        'status' => 'error',
                        'statusCode' => 500,
                        'result_data' => '',
                        'error'  => $e->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'error',
                    'statusCode' => 400,
                    'result_data' => ''
                ], 400);
            }

        }else{
            return view('HR.Configuration.AttendanceRules.add');
        }
    }

    public function update(Request $request)
    {
        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            try {
                // comment for temporary
                // AttendanceRules::truncate();

                AttendanceRules::insert([
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'ext_start_time' => $request->ext_start_time,
                ]);

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function get()
    {
        $data = AttendanceRules::first();
        return response()->json($data);
    }
}
