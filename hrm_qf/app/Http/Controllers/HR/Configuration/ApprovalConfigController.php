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

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");


            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            $allData = DB::table('hr_reporting_boss_config as conf')
                ->where([['conf.event_id', $eventId],['conf.is_active', 1], ['conf.is_delete', 0]])
                ->join('hr_departments', 'conf.department_for_id', '=', 'hr_departments.id')
                ->join('hr_designations', 'conf.designation_for_id', '=', 'hr_designations.id')
                ->select('conf.*', 'hr_designations.name as des_name', 'hr_departments.dept_name as dept_name')
                ->where(function($query) use ($searchValue){
                    if(!empty($searchValue)){
                        $query->where('hr_designations.name', 'like', '%' . $searchValue . '%');
                        $query->orWhere('hr_departments.dept_name', 'like', '%' . $searchValue . '%');
                    }
                })
                // ->groupBy('conf.event_id')
                ->groupBy('conf.designation_for_id')
                ->groupBy('conf.department_for_id');


            $allData = $allData->skip($start)->take($rowperpage)->get();
            // dd($allData);

            $totalRecords = ApprovalConfig::where([['event_id', $eventId],['is_active', 1], ['is_delete', 0]])      ->groupBy('designation_for_id')
            ->groupBy('department_for_id')->get();

            $totalRecords = count($totalRecords);
            $totalRecordswithFilter = $totalRecords;
            if(!empty($searchValue)){
                $totalRecordswithFilter = $allData->count();
            }


            $data      = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {

                // dd($row);

                $IgnoreArray = array();
                
                $data[$key]['sl']                 = $sno;
                $data[$key]['designation_for']    = $row->des_name;
                $data[$key]['department_for']    = $row->dept_name;
                $data[$key]['permission_for']    = $row->permission_for;
                $data[$key]['created_at']    = (new DateTime($row->created_at))->format('d-m-Y h:i:s A');

                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, $row->event_id . '-' . $row->designation_for_id . '-' . $row->permission_for, $IgnoreArray, $row->is_active);
                $sno++;
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
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

    public function get(Request $request, $con, $arr)
    {
        $conArr = explode('-', $con);

        $elementDataArr = explode(',', $arr);

        $desc_name = $elementDataArr[1];
        $dept_name = $elementDataArr[2];

        $getDescId = DB::table('hr_designations')->where([['is_active',1], ['is_delete', 0],['name', $desc_name]])->first();
        $getDescId = !empty($getDescId) ? $getDescId->id : null;
        $getDeptId = DB::table('hr_departments')->where([['is_active',1], ['is_delete', 0],['dept_name', $dept_name]])->first();
        $getDeptId = !empty($getDeptId) ? $getDeptId->id : null;

        try {
            $rBossCon = ApprovalConfig::where([
                ['event_id', $conArr[0]],
                ['department_for_id', $getDeptId],
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

        $departmentQuery = DB::table('hr_departments')->where([['is_active',1], ['is_delete', 0]])->pluck('id');
        $designationsQuery = DB::table('hr_designations')->where([['is_active',1], ['is_delete', 0]])->pluck('id');


        if( isset($request['department_for'][0]) && $request['department_for'][0] == 0){
            $departmentArr = $departmentQuery;
        }else{
            $deptData = isset($request['department_for']) ? $request['department_for'] : [];
            
            $result = array_filter($deptData, function ($value) {
                return $value !== 0;
            });
            $departmentArr = $result;
        }



        if($request['designation_for'] == 0){
            $designationArr = $designationsQuery;
        }else{
            $designationArr = explode(',', $request['designation_for']);
        }

        // ss($request->all(), $designationArr, $departmentArr);

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
            // DB::beginTransaction();
            try {

                $test = DB::table('hr_reporting_boss_config')->where('event_id', $request['event'])
                    ->where('designation_for_id', $request['designation_for'])
                    ->whereIn('department_for_id', $request['department_for'])
                    ->pluck('id');

                try {
                    DB::table('hr_reporting_boss_config')->whereIn('id', $test)->delete();
                } catch (Exception $e) {
                    // Log or print the error message for debugging
                    ss($e->getMessage());
                }
                // ApprovalConfig::whereIn('id', $test)->delete();

                foreach($departmentArr as $deptKey => $deptData){
                    foreach($designationArr as $desKey => $desData){

        
                        $prefix = $request['designation_for'] . '_' . $request['department_for'][0] . '_';
        
                        foreach ($request[$prefix . 'ho_level'] as $key => $levItem) {
                               
                                $conf = ApprovalConfig::create([
                                    'event_id' => $request['event'],
                                    'permission_for' => "ho",
                                    'designation_for_id' => $desData,
                                    'department_for_id' => $deptData,
                                    'level' => $levItem,
                                    'department_id' =>  $request[$prefix . 'ho_department'][$key],
                                    'designation_id' => $request[$prefix . 'ho_designation'][$key],
                                    'data_modification' => $request[$prefix . 'ho_data-modification'][$key],
                                    'employee_from' => null,
                                ]);
                                

                            
                        }
        
                        foreach ($request[$prefix . 'bo_level'] as $key => $levItem) {

                            if($request[$prefix . 'bo_from'][$key] != null){
                                $conf = ApprovalConfig::create([
                                    'event_id' => $request['event'],
                                    'permission_for' => "bo",
                                    'designation_for_id' => $desData,
                                    'department_for_id' => $deptData,
                                    'level' => $levItem,
                                    'department_id' =>  $request[$prefix . 'bo_department'][$key],
                                    'designation_id' => $request[$prefix . 'bo_designation'][$key],
                                    'data_modification' => $request[$prefix . 'bo_data-modification'][$key],
                                    'employee_from' => $request[$prefix . 'bo_from'][$key],
                                ]);
                                
                            }
                            
                        }
                    }
                }
                
                /*
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
                */

                // DB::commit();
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
                // DB::rollBack();
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

        // ss($request->all());

        $departmentQuery = DB::table('hr_departments')->where([['is_active',1], ['is_delete', 0]])->pluck('id');
        $designationsQuery = DB::table('hr_designations')->where([['is_active',1], ['is_delete', 0]])->pluck('id');


        if( isset($request['department_for'][0]) && $request['department_for'][0] == 0){
            $departmentArr = $departmentQuery;
        }else{
            $deptData = isset($request['department_for'][0]) ? $request['department_for'][0] : [];
            if(!empty($deptData)){
                $departmentArr = explode(',', $deptData);
            }
        }

        if($request['designation_for'] == 0){
            $designationArr = $designationsQuery;
        }else{
            $designationArr = explode(',', $request['designation_for']);
        }

        /*
        $dataArr = [];
        foreach($departmentArr as $deptKey => $deptData){
            foreach($designationArr as $desKey => $desData){

                $prefix = $request['designation_for'] . '_' . $request['department_for'][0] . '_';

                foreach ($request[$prefix . 'ho_level'] as $key => $levItem) {
                    $conf = [
                        'event_id' => $request['event'],
                        'permission_for' => "ho",
                        'designation_for_id' => $desData,
                        'department_for_id' => $deptData,
                        'level' => $levItem,
                        'department_id' =>  $request[$prefix . 'ho_department'][$key],
                        'designation_id' => $request[$prefix . 'ho_designation'][$key],
                        'data_modification' => $request[$prefix . 'ho_data-modification'][$key],
                        'employee_from' => null,
                    ];

                    array_push($dataArr, $conf);
                }

                foreach ($request[$prefix . 'bo_level'] as $key => $levItem) {

                    if($request[$prefix . 'bo_from'][$key] != null){
                        $conf = [
                            'event_id' => $request['event'],
                            'permission_for' => "bo",
                            'designation_for_id' => $desData,
                            'department_for_id' => $deptData,
                            'level' => $levItem,
                            'department_id' =>  $request[$prefix . 'bo_department'][$key],
                            'designation_id' => $request[$prefix . 'bo_designation'][$key],
                            'data_modification' => $request[$prefix . 'bo_data-modification'][$key],
                            'employee_from' => $request[$prefix . 'bo_from'][$key],
                        ];
                        array_push($dataArr, $conf);
                    }
                    
                }
            }
        }*/
        

        if ($request->ajax()) {
            
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

                foreach($departmentArr as $deptKey => $deptData){
                    foreach($designationArr as $desKey => $desData){

        
                        $prefix = $request['designation_for'] . '_' . $request['department_for'][0] . '_';
        
                        foreach ($request[$prefix . 'ho_level'] as $key => $levItem) {
                                $ch_department_id = $request[$prefix . 'ho_department'][$key];
                                $ch_designation_id = $request[$prefix . 'ho_designation'][$key];

                                $duplicatecheck = DB::table('hr_reporting_boss_config')
                                    ->where([
                                        ['is_active', 1], 
                                        ['is_delete', 0], 
                                        ['event_id', $request['event']], 
                                        ['department_for_id', $deptData], 
                                        ['designation_for_id', $desData],
                                        ['permission_for','ho'],
                                        ['level', $levItem], 
                                        ['department_id', $ch_department_id],
                                        ['designation_id', $ch_designation_id]
                                        
                                    ])->count();
                                if($duplicatecheck > 0){
                                    continue;
                                }

                                $conf = ApprovalConfig::create([
                                    'event_id' => $request['event'],
                                    'permission_for' => "ho",
                                    'designation_for_id' => $desData,
                                    'department_for_id' => $deptData,
                                    'level' => $levItem,
                                    'department_id' =>  $request[$prefix . 'ho_department'][$key],
                                    'designation_id' => $request[$prefix . 'ho_designation'][$key],
                                    'data_modification' => $request[$prefix . 'ho_data-modification'][$key],
                                    'employee_from' => null,
                                ]);
                                

                            
                        }
        
                        foreach ($request[$prefix . 'bo_level'] as $key => $levItem) {

                            $ch_department_id = $request[$prefix . 'bo_department'][$key];
                            $ch_designation_id = $request[$prefix . 'bo_designation'][$key];

                            $duplicatecheck = DB::table('hr_reporting_boss_config')
                                ->where([
                                    ['is_active', 1], 
                                    ['is_delete', 0], 
                                    ['event_id', $request['event']], 
                                    ['department_for_id', $deptData], 
                                    ['designation_for_id', $desData],
                                    ['permission_for','bo'],
                                    ['level', $levItem], 
                                    ['department_id', $ch_department_id],
                                    ['designation_id', $ch_designation_id]
                                    
                                ])->count();
                            if($duplicatecheck > 0){
                                continue;
                            }
        
                            if($request[$prefix . 'bo_from'][$key] != null){
                                $conf = ApprovalConfig::create([
                                    'event_id' => $request['event'],
                                    'permission_for' => "bo",
                                    'designation_for_id' => $desData,
                                    'department_for_id' => $deptData,
                                    'level' => $levItem,
                                    'department_id' =>  $request[$prefix . 'bo_department'][$key],
                                    'designation_id' => $request[$prefix . 'bo_designation'][$key],
                                    'data_modification' => $request[$prefix . 'bo_data-modification'][$key],
                                    'employee_from' => $request[$prefix . 'bo_from'][$key],
                                ]);
                                
                            }
                            
                        }
                    }
                }

                /*
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
                }*/

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
