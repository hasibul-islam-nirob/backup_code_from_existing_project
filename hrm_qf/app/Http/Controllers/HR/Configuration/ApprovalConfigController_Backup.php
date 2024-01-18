<?php

namespace App\Http\Controllers\HR\Configuration;

use DateTime;
use Dotenv\Regex\Result;
use Illuminate\Http\Request;
use App\Model\HR\ApprovalConfig;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\HR\Process\ApplicationProcessController;
use App\Model\HR\SalaryIncrement;

class ApprovalConfigController extends Controller
{
    public function index(Request $request, $moduleId, $eventId)
    {
        $this->current_route_name = Route::getCurrentRoute()->action['prefix'];

        $RolePermissionAll = (!empty(session()->get('LoginBy.user_role.role_permission'))) ? session()->get('LoginBy.user_role.role_permission') : array();

        $this->GlobalRole = (isset($RolePermissionAll[$this->current_route_name])) ? $RolePermissionAll[$this->current_route_name] : array();

        if ($request->isMethod('post')) {

            $allData  = ApprovalConfig::where([['event_id', $eventId],['is_active', 1], ['is_delete', 0]])
                ->groupBy('designation_for_id')
                ->get();

            $data      = array();
            $totalData = count($allData);

            $totalFiltered = $totalData;
            $index         = 0;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $data[$index]['id']                 = $row->id;
                $data[$index]['sl']                 = $index + 1;
                $data[$index]['designation_for']    = $row->designation_for['name'];
                $data[$index]['permission_for']    = $row->permission_for;
                $data[$index]['created_at']    = (new DateTime($row->created_at))->format('d-m-Y h:i:s A');

                $data[$index]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, $row->event_id . '-' . $row->designation_for_id . '-' . $row->permission_for, $IgnoreArray);
                $index++;
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
            );
            return response()->json($json_data);
        } else {

            $module = DB::table('gnl_sys_modules')->where('id', $moduleId)->first();
            $moduleName = !empty($module->module_name) ? $module->module_name : '';

            $events = DB::table('hr_reporting_boss_event')->where('id', $eventId)->first();
            $eventName = !empty($events->event_title) ? $events->event_title : '';
            return view('HR.Configuration.ApprovalConfig.index', compact('moduleName','eventName'));
        }
    }

    public function getPassport($request, $operationType)
    {
        $errorMsg = null;
        $rules    = array();
        $attributes = array();

        if ($operationType == 'store' || $operationType == 'update') {

            foreach ($request['department_for'] as $key => $depId) {

                $prefix = $request['designation_for'] . '_' . $depId . '_';

                $rules[$prefix . 'ho_designation'] = 'required|array|min:1';
                $rules[$prefix . 'ho_designation.*'] = 'required';

                $rules[$prefix . 'ho_department'] = 'required|array|min:1';
                $rules[$prefix . 'ho_department.*'] = 'required';

                $rules[$prefix . 'bo_designation'] = 'required|array|min:1';
                $rules[$prefix . 'bo_designation.*'] = 'required';

                $rules[$prefix . 'bo_department'] = 'required|array|min:1';
                $rules[$prefix . 'bo_department.*'] = 'required';

                $rules[$prefix . 'bo_from'] = 'required|array|min:1';
                $rules[$prefix . 'bo_from.*'] = 'required';


                $attributes[$prefix . 'ho_designation'] = 'Designation from head office';
                $attributes[$prefix . 'ho_designation.*'] = 'Designation from head office';

                $attributes[$prefix . 'ho_department'] = 'Department from head office';
                $attributes[$prefix . 'ho_department.*'] = 'Department from head office';

                $attributes[$prefix . 'bo_designation'] = 'Designation from branch office';
                $attributes[$prefix . 'bo_designation.*'] = 'Designation from branch office';

                $attributes[$prefix . 'bo_department'] = 'Department from branch office';
                $attributes[$prefix . 'bo_department.*'] = 'Department from branch office';

                $attributes[$prefix . 'bo_from'] = 'Employee from';
                $attributes[$prefix . 'bo_from.*'] = 'Employee from';
            }

            $validator = Validator::make($request->all(), $rules, [], $attributes);

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

    public function get_modules()
    {
        $module = DB::table('gnl_sys_modules')->where([['is_active', 1], ['is_delete', 0]])->get();
        return view('HR.Configuration.ApprovalConfig.module', compact('module'));
    }

    public function get($con)
    {
        $conArr = explode('-', $con);
        try {
            $rBossCon = ApprovalConfig::where([
                ['event_id', $conArr[0]],
                ['designation_for_id', $conArr[1]],
                ['is_active', 1],
                ['is_delete', 0]
            ])
                ->with('department', 'designation', 'designation_for', 'department_for')
                ->get();

            if (!empty($rBossCon)) {
                return response()->json([
                    'message'    => "Data fetched successfully!!",
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => $rBossCon->groupBy('department_for_id'),
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

    public function update(Request $request)
    {
        //dd($request->all());

        if ($request->ajax()) {
            $passport = $this->getPassport($request, 'update');
            if (!$passport['isValid']) {
                return response()->json(
                    [
                        'message'    => $passport['message'],
                        'status' => 'error',
                        'statusCode' => 400,
                        'result_data' => ''
                    ],
                    400
                );
            }
            DB::beginTransaction();
            try {
                ApprovalConfig::where('event_id', $request['event'])
                    ->where('designation_for_id', $request['designation_for'])
                    ->whereIn('department_for_id', $request['department_for'])
                    ->delete();

                foreach ($request['department_for'] as $key => $depId) {

                    $prefix = $request['designation_for'] . '_' . $depId . '_';

                    foreach ($request[$prefix . 'ho_level'] as $key => $levItem) {
                        $conf = ApprovalConfig::create([
                            'event_id' => $request['event'],
                            'permission_for' => "ho",
                            'designation_for_id' => $request['designation_for'],
                            'department_for_id' => $depId,
                            'level' => $levItem,
                            'department_id' =>  $request[$prefix . 'ho_department'][$key],
                            'designation_id' => $request[$prefix . 'ho_designation'][$key],
                            'data_modification' => $request[$prefix . 'ho_data-modification'][$key],
                            'employee_from' => null,
                        ]);
                    }

                    foreach ($request[$prefix . 'bo_level'] as $key => $levItem) {
                        $conf = ApprovalConfig::create([
                            'event_id' => $request['event'],
                            'permission_for' => "bo",
                            'designation_for_id' => $request['designation_for'],
                            'department_for_id' => $depId,
                            'level' => $levItem,
                            'department_id' =>  $request[$prefix . 'bo_department'][$key],
                            'designation_id' => $request[$prefix . 'bo_designation'][$key],
                            'data_modification' => $request[$prefix . 'bo_data-modification'][$key],
                            'employee_from' => $request[$prefix . 'bo_from'][$key],
                        ]);
                    }
                }

                DB::commit();
                return response()->json(
                    [
                        'message'    => "Data updated successfully!!",
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ],
                    200
                );
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(
                    [
                        'message'    => "Internal Server Error. Try Again!!",
                        'status' => 'error',
                        'statusCode' => 500,
                        'result_data' => '',
                        'error'  => $e->getMessage(),
                    ],
                    500
                );
            }
        }
    }

    public function insert(Request $request)
    {
        if ($request->ajax()) {
            ss($request->all());
            $passport = $this->getPassport($request, 'store');
            if (!$passport['isValid']) {
                return response()->json(
                    [
                        'message'    => $passport['message'],
                        'status' => 'error',
                        'statusCode' => 400,
                        'result_data' => ''
                    ],
                    400
                );
            }

            DB::beginTransaction();
            try {

                foreach ($request['department_for'] as $key => $depId) {

                    $prefix = $request['designation_for'] . '_' . $depId . '_';

                    foreach ($request[$prefix . 'ho_level'] as $key => $levItem) {
                        $conf = ApprovalConfig::create([
                            'event_id' => $request['event'],
                            'permission_for' => "ho",
                            'designation_for_id' => $request['designation_for'],
                            'department_for_id' => $depId,
                            'level' => $levItem,
                            'department_id' =>  $request[$prefix . 'ho_department'][$key],
                            'designation_id' => $request[$prefix . 'ho_designation'][$key],
                            'data_modification' => $request[$prefix . 'ho_data-modification'][$key],
                            'employee_from' => null,
                        ]);
                    }

                    foreach ($request[$prefix . 'bo_level'] as $key => $levItem) {
                        $conf = ApprovalConfig::create([
                            'event_id' => $request['event'],
                            'permission_for' => "bo",
                            'designation_for_id' => $request['designation_for'],
                            'department_for_id' => $depId,
                            'level' => $levItem,
                            'department_id' =>  $request[$prefix . 'bo_department'][$key],
                            'designation_id' => $request[$prefix . 'bo_designation'][$key],
                            'data_modification' => $request[$prefix . 'bo_data-modification'][$key],
                            'employee_from' => $request[$prefix . 'bo_from'][$key],
                        ]);
                    }
                }

                DB::commit();
                return response()->json(
                    [
                        'message'    => "Data inserted successfully!!",
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ],
                    200
                );
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(
                    [
                        'message'    => "Internal Server Error. Try Again!!",
                        'status' => 'error',
                        'statusCode' => 500,
                        'result_data' => '',
                        'error'  => $e->getMessage(),
                    ],
                    500
                );
            }
        }
    }

    public function get_events($moduleId)
    {
        $module = DB::table('gnl_sys_modules')->where('id', $moduleId)->first();
        $moduleName = !empty($module->module_name) ? $module->module_name : '';
        $events = DB::table('hr_reporting_boss_event')
            ->where([['is_delete', 0]])
            ->where("module_id", $moduleId)
            ->get();

        return view('HR.Configuration.ApprovalConfig.events', compact('events', 'moduleId', 'moduleName'));
    }

    public function delete($con)
    {
        $conArr = explode('-', $con);

        if ($this->hasPendingApplication($conArr)) {
            return response()->json([
                'statusCode' => 400,
                'status' => 'error',
                'message'    => 'This event has pending application!!',
                'result_data' => ''
            ], 400);
        }

        // $delete = ApprovalConfig::where('event_id', $conArr[0])
        //     ->where('designation_for_id', $conArr[1])
        //     ->delete();

        $delete = ApprovalConfig::where('event_id', $conArr[0])
            ->where('designation_for_id', $conArr[1])
            ->update(['is_delete' => 1]);

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

    public function hasPendingApplication($con)
    {

        $model = '\\App\\Model\\HR\\' . (new ApplicationProcessController)->get_application_type(null, $con[0])[1];
        $pendAppl = $model::where('is_active', 3)->get();

        foreach ($pendAppl as $p) {

            if ($p->employee['designation_id'] == $con[1]) {

                return true;
            }
        }

        return false;
    }
}
