<?php

namespace App\Services;

use DateTime;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Exception;

class BackupCommonService
{
    public static function stringShort($string, $separator)
    {

        if (strlen($string) < 1) {
            return false;
        }

        $stringArr = explode($separator, $string);

        if (count($stringArr) > 1) {
            $shortString = $stringArr[0] . "." . last($stringArr);
        } elseif (isset($stringArr[0])) {
            $shortString = $stringArr[0];
        } else {
            $shortString = $string;
        }

        return $shortString;
    }

    public static function unauthorisedUserAccess($request, $operationType)
    {
        // $message = "You are not authorise this data"
        $accessLogData = array();

        $accessLogData['branch_id']    = Auth::user()->branch_id;
        $accessLogData['execution_by'] = Auth::user()->id;

        $accessLogData['table_name']     = $request->getTable();
        $accessLogData['operation_type'] = "unauthorised-" . $operationType;
        $accessLogData['query_sql']      = "id = " . $request->id;

        // if(!isset($accessLogData['operation_type'])){
        //     $accessLogData['operation_type'] = "unauthorised";
        // }

        DB::table('access_unauthorised_log')->insert($accessLogData);

        return true;

        // $notification = array(
        //     'message'    => $message,
        //     'alert-type' => 'error',
        // );

        // // return redirect('/access_denied');
        // // return redirect()->back()->with($notification);

        // Auth::logout();
        // return Redirect::to('/')->with($notification);
        // Session::flush();

    }

    public static function unauthorisedUserLogout($message = "You are not authorised to modification this data.", $method = "get")
    {

        $notification = array(
            'message'    => $message,
            'alert-type' => 'error',
        );

        // // return redirect('/access_denied');
        // // return redirect()->back()->with($notification);

        Session::flush();
        Auth::logout();

        if ($method == "post") {
            return response()->json($notification);
        } else {
            return Redirect::to('/')->with($notification);
        }
    }

    public static function getDBConnection()
    {
        // dd(DB::getDefaultConnection());
        // return DB::connection()->getConfig()['driver'];
        return DB::getDefaultConnection();
    }

    public static function networkConnection()
    {
        $connected = @fsockopen("www.example.com", 80);

        if ($connected) {
            $isConn = true;

            fclose($connected);
        } else {
            $isConn = false;
        }

        return $isConn;
    }

    public static function isSuperUser($userId = null)
    {
        $userInfo = Auth::user();
        $roleId   = $userInfo->sys_user_role_id;

        if (!empty($userId)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userId)->first();
            $roleId   = ($userInfo) ? $userInfo->sys_user_role_id : null;
        }

        if ($roleId == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function isActionPermitedForThisUser($actionStatus, $userId = null)
    {
        $current_route_name = Route::getCurrentRoute()->action['prefix'];
        if(empty($current_route_name)) {
            $current_route_name = Route::getCurrentRoute()->uri();
        }

        $rolePermissionAll = (!empty(session()->get('LoginBy.user_role.role_permission'))) ? session()->get('LoginBy.user_role.role_permission') : array();

        if (!empty($userId)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userId)->first();
            $roleId   = ($userInfo) ? $userInfo->sys_user_role_id : null;

            $roleData = DB::table('gnl_sys_user_roles')->where('id', $roleId)->first();
            $rolePermissionAll   = ($roleData) ? unserialize(base64_decode($roleData->serialize_permission)) : array();
        }

        
        $roleMenus = (isset($rolePermissionAll[$current_route_name])) ? $rolePermissionAll[$current_route_name] : array();
        $searchAction = array_search($actionStatus, array_column($roleMenus, 'set_status'));

        if ($searchAction == false) {
            return false;
        } else {
            return true;
        }
    }

    public static function isDeveloperUser($userID = null)
    {
        $userInfo = Auth::user();
        $roleID   = $userInfo->sys_user_role_id;

        if (!empty($userID)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userID)->first();
            $roleID   = ($userInfo) ? $userInfo->sys_user_role_id : null;
        }

        if ($roleID == 19) {
            return true;
        } else {
            return false;
        }
    }

    public static function isHeadOffice($userId = null)
    {
        $userInfo = Auth::user();
        
        if (!empty($userId)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userId)->first();
        }

        if ($userInfo['branch_id'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function isHeadOfficeUser($userID = null)
    {
        $userInfo = Auth::user();
        $roleID   = $userInfo->sys_user_role_id;

        if (!empty($userID)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userID)->first();
            $roleID   = ($userInfo) ? $userInfo->sys_user_role_id : null;
        }

        if ($roleID == 21) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUserId()
    {
        $userInfo = Auth::user();
        $userID   = $userInfo->id;

        return $userID;
    }

    public static function getRoleId($userID = null)
    {
        $userInfo = Auth::user();
        $roleID   = $userInfo->sys_user_role_id;

        if (!empty($userID)) {
            $userInfo = DB::table('gnl_sys_users')->where('id', $userID)->first();
            $roleID   = ($userInfo) ? $userInfo->sys_user_role_id : null;
        }

        return $roleID;
    }

    public static function getModuleId()
    {
        $CurrentRouteURI   = Route::getCurrentRoute()->uri();
        $currentRouteURIAr = explode('/', $CurrentRouteURI);
        $moduleName        = $currentRouteURIAr[0];

        $module_id = DB::table('gnl_sys_modules')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('route_link', $moduleName)
            ->first()->id;

        if ($module_id) {
            return $module_id;
        } else {
            return false;
        }
    }

    public static function checkActivatedModule($moduleName)
    {
        $roleModules    = Session::get('LoginBy.user_role.role_module');
        $isActiveModule = array_search($moduleName, array_column($roleModules, 'module_link'));

        if ($isActiveModule) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Company ID from user login session
     */
    public static function getCompanyId()
    {
        $companyId = Session::get('LoginBy.user_config.company_id');

        if ($companyId == '' || $companyId == null || empty($companyId)) {
            $companyId = 1;
        }

        return $companyId;
    }

    public static function getCompanyInfo()
    {
        $companyId = Session::get('LoginBy.user_config.company_id');

        if ($companyId == '' || $companyId == null || empty($companyId)) {
            $companyId = 1;
        }

        return DB::table('gnl_companies')->find($companyId);
    }

    public static function getCompanyType()
    {
        ## formID 2 = company type
        $companyInfo = DB::table('gnl_company_config')->where([['company_id', self::getCompanyId()], ['form_id', 2]])->first();

        $companyType = "";
        if ($companyInfo) {
            $companyType = $companyInfo->form_value;
        }

        return $companyType;
    }

    public static function getCounterNo()
    {
        // $counter_no = Session::get('LoginBy.user_config.counter_no');

        // if ($counter_no == '' || $counter_no == null || empty($counter_no)) {
        //     $counter_no = '00';
        // }

        $counter_no = '00';

        return $counter_no;
    }
    /**
     * Get Branch ID from user login session
     */
    public static function getBranchId()
    {
        $branchId = Session::get('LoginBy.user_config.branch_id');

        if ($branchId == '' || $branchId == null || empty($branchId)) {
            $branchId = 1;
        }

        return $branchId;
    }

    public static function getZoneId($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = self::getBranchId();
        }

        $zoneQuery = DB::table('gnl_zones')
            ->where([['is_active', 1], ['is_delete', 0]])
            ->where(function ($zoneQuery) use ($branchId) {
                if (!empty($branchId)) {
                    $zoneQuery->where('branch_arr', 'LIKE', "{$branchId}")
                        ->orWhere('branch_arr', 'LIKE', "{$branchId},%")
                        ->orWhere('branch_arr', 'LIKE', "%,{$branchId},%")
                        ->orWhere('branch_arr', 'LIKE', "%,{$branchId}");
                }
            })
            ->pluck('id')
            ->first();

        if ($zoneQuery) {
            return $zoneQuery;
        } else {
            return null;
        }
    }

    public static function getAreaId($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = self::getBranchId();
        }

        $areaQuery = DB::table('gnl_areas')
            ->where([['is_active', 1], ['is_delete', 0]])
            ->where(function ($areaQuery) use ($branchId) {
                if (!empty($branchId)) {
                    $areaQuery->where('branch_arr', 'LIKE', "{$branchId}")
                        ->orWhere('branch_arr', 'LIKE', "{$branchId},%")
                        ->orWhere('branch_arr', 'LIKE', "%,{$branchId},%")
                        ->orWhere('branch_arr', 'LIKE', "%,{$branchId}");
                }
            })
            ->pluck('id')
            ->first();

        if ($areaQuery) {
            return $areaQuery;
        } else {
            return null;
        }
    }

    public static function getProjectId($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = self::getBranchId();
        }

        $projectId = DB::table('gnl_branchs')->where('id', $branchId)->pluck('project_id')->first();

        if ($projectId) {
            return $projectId;
        } else {
            return null;
        }
    }

    public static function getProjectTypeId($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = self::getBranchId();
        }

        $projectTypeId = DB::table('gnl_branchs')->where('id', $branchId)->pluck('project_type_id')->first();

        if ($projectTypeId) {
            return $projectTypeId;
        } else {
            return null;
        }
    }

    // public static function getBranches($parameters = []){ ## get all branches via different parameter

    //     // $groupId = (isset($parameters['groupId'])) ? $parameters['groupId'] : null;
    //     $companyId = (isset($parameters['companyId'])) ? $parameters['companyId'] : null;
    //     $projectId = (isset($parameters['projectId'])) ? $parameters['projectId'] : null;
    //     $projectTypeId = (isset($parameters['projectTypeId'])) ? $parameters['projectTypeId'] : null;
    //     // $regionId = (isset($parameters['regionId'])) ? $parameters['regionId'] : null;
    //     // $zoneId = (isset($parameters['zoneId'])) ? $parameters['zoneId'] : null;
    //     // $areaId = (isset($parameters['areaId'])) ? $parameters['areaId'] : null;
    //     $branchId = (isset($parameters['branchId'])) ? $parameters['branchId'] : null;

    //     $queryData = DB::table('gnl_branchs')
    //                 ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
    //                 ->where(function($queryData) use ($companyId, $projectId, $projectTypeId){
    //                     if(!empty($companyId)){
    //                         $queryData->where('company_id', $companyId);
    //                     }

    //                     if(!empty($projectId)){
    //                         $queryData->where('project_id', $projectId);
    //                     }

    //                     if(!empty($projectTypeId)){
    //                         $queryData->where('project_type_id', $projectTypeId);
    //                     }
    //                 })
    //                 ->get();

    //     return 0;
    // }

    /**
     * if findDate dewa hoy tahole branchID & selModule dite hobe na
     */
    public static function systemFiscalYear($findDate = null, $companyId = null, $branchId = null, $selModule = 'pos')
    {
        if ($companyId == null) {
            $companyId = self::getCompanyId();
        }

        if ($branchId == null) {
            $branchId = self::getBranchId();
        }

        if ($findDate == null) {
            $findDate = self::systemCurrentDate($branchId, $selModule);
        }

        $findDate = new DateTime($findDate);

        $fiscalQuery = DB::table('gnl_fiscal_year')
            ->where([['is_active', 1], ['is_delete', 0],
                ['company_id', $companyId],
                ['fy_start_date', '<=', $findDate->format('Y-m-d')],
                ['fy_end_date', '>=', $findDate->format('Y-m-d')],
            ])
            ->select('id', 'fy_name', 'fy_start_date', 'fy_end_date')
            ->first();

        $fiscalData = array();

        if ($fiscalQuery) {
            $fiscalData = [
                'id'            => $fiscalQuery->id,
                'fy_name'       => $fiscalQuery->fy_name,
                'fy_start_date' => $fiscalQuery->fy_start_date,
                'fy_end_date'   => $fiscalQuery->fy_end_date,
            ];
        } else {
            $fiscalData = [
                'id'            => 0,
                'fy_name'       => "Jan-Dec",
                'fy_start_date' => $findDate->format('Y') . "-01-01",
                'fy_end_date'   => $findDate->format('Y') . "-12-31",
            ];
        }
        return $fiscalData;
    }

    public static function getModuleByRoute()
    {
        $CurrentRouteURI   = Route::getCurrentRoute()->uri();
        $currentRouteURIAr = explode('/', $CurrentRouteURI);
        // echo $CurrentRouteURI."<br>";

        $moduleName = $currentRouteURIAr[0];

        return $moduleName;
    }
    
    public static function fnIsOpening($selBranch = null , $selModule = null){
        
        $currentDate = self::systemCurrentDate($selBranch,$selModule);
        $openingDate = self::getBranchSoftwareStartDate($selBranch,$selModule);

        $currentDate = date('Y-m-d',strtotime($currentDate));
        $openingDate = date('Y-m-d',strtotime($openingDate));

       
        if($currentDate == $openingDate){
            return true;
        }else{
            return false;
        }

    }
    /**
     * Get Branch Software Opening date
     */
    public static function getBranchSoftwareStartDate($selBranch = null, $selModule = null)
    {
        if ($selBranch == '' || $selBranch == null || empty($selBranch)) {
            $branchId = self::getBranchId();
        } else {
            $branchId = $selBranch;
        }

        $CurrentRouteURI   = Route::getCurrentRoute()->uri();
        $currentRouteURIAr = explode('/', $CurrentRouteURI);
        // echo $CurrentRouteURI."<br>";

        if ($selModule == '' || $selModule == null || empty($selModule)) {
            $moduleName = $currentRouteURIAr[0];
        } else {
            $moduleName = $selModule;
        }

        // if ($moduleName == 'gnl') {
        //     $fieldName = false;
        // } elseif ($moduleName == 'pos') {
        //     $fieldName = "soft_start_date";
        // } elseif ($moduleName == 'acc') {
        //     $fieldName = "acc_start_date";
        // } elseif ($moduleName == 'hr') {
        //     $fieldName = "hr_start_date";
        // } elseif ($moduleName == 'mfn') {
        //     $fieldName = "mfn_start_date";
        // } elseif ($moduleName == 'inv') {
        //     $fieldName = "inv_start_date";
        // } elseif ($moduleName == 'bill') {
        //     $fieldName = "bill_start_date";
        // } elseif ($moduleName == 'fam') {
        //     $fieldName = "fam_start_date";
        // } elseif ($moduleName == 'proc') {
        //     $fieldName = "proc_start_date";
        // } else {
        //     $fieldName = false;
        // }

        if ($moduleName == 'pos') {
            $fieldName = "soft_start_date";
        } elseif ($moduleName == 'acc') {
            $fieldName = "acc_start_date";
        } elseif ($moduleName == 'mfn') {
            $fieldName = "mfn_start_date";
        } else {
            // $fieldName = false;
            $fieldName = "branch_opening_date";
        }

        if ($fieldName) {
            $BranchData = DB::table("gnl_branchs")->where('id', $branchId)->first();
            return $BranchData->$fieldName;
        } else {
            return null;
        }
    }

    /**
     * Get System Date Depending on Day End
     */
    public static function systemCurrentDate($selBranch = null, $selModule = null)
    {
        if ($selBranch == '' || $selBranch == null || empty($selBranch)) {
            $branchId = self::getBranchId();
        } else {
            $branchId = $selBranch;
        }

        $CurrentRouteURI   = Route::getCurrentRoute()->uri();
        $currentRouteURIAr = explode('/', $CurrentRouteURI);
        // echo $CurrentRouteURI."<br>";

        if ($selModule == '' || $selModule == null || empty($selModule)) {
            $moduleName = $currentRouteURIAr[0];
        } else {
            $moduleName = $selModule;
        }

        $CurrentDate = new DateTime();
        $QueryFlag   = true;

        // Table Name & Field

        if ($moduleName == 'gnl') {
            $QueryFlag = false;
            $tableName = false;
            $fieldName = false;
        } elseif ($moduleName == 'pos') {
            $tableName = "pos_day_end";
            $fieldName = "soft_start_date";
        } elseif ($moduleName == 'acc') {
            $tableName = "acc_day_end";
            $fieldName = "acc_start_date";
        } elseif ($moduleName == 'hr') {
            // $tableName = "hr_day_end";
            $tableName = false;
            $fieldName = "hr_start_date";
        } elseif ($moduleName == 'mfn') {
            $tableName = "mfn_day_end";
            $fieldName = "mfn_start_date";
        } elseif ($moduleName == 'inv') {
            $tableName = "inv_day_end";
            $fieldName = "inv_start_date";
        } elseif ($moduleName == 'bill') {
            $tableName = false;
            $fieldName = "bill_start_date";
        } elseif ($moduleName == 'fam') {
            // $tableName = false;
            // $fieldName = "fam_start_date";

            $tableName = false;
            $fieldName = false;
            $QueryFlag = false;

        } elseif ($moduleName == 'proc') {
            $tableName = false;
            $fieldName = "proc_start_date";
        } else {
            $tableName = false;
            $fieldName = false;
            $QueryFlag = false;
        }

        if ($QueryFlag == true) {

            $day_end_empty = true;

            if ($tableName) {

                if ($moduleName == 'mfn') {
                    $DayEndData = DB::table($tableName)
                        ->where(['branchId' => $branchId, 'isActive' => 1])
                        ->first();
                } else {
                    $DayEndData = DB::table($tableName)
                        ->where([['is_active', 1], ['is_delete', 0], ['branch_id', $branchId]])
                    // ->where(['branch_id' => $branchId, 'is_active' => 1])
                        ->first();
                }

                if ($DayEndData) {
                    $day_end_empty = false;
                }
            }

            if ($day_end_empty) {
                $BranchData = DB::table('gnl_branchs')
                    ->where(['id' => $branchId, 'is_approve' => 1])
                    ->first();

                if ($BranchData) {
                    if (!empty($BranchData->$fieldName)) {
                        $CurrentDate = new DateTime($BranchData->$fieldName);
                    }
                }
            } else {
                if ($moduleName == 'mfn') {
                    $CurrentDate = new DateTime($DayEndData->date);
                } else {
                    $CurrentDate = new DateTime($DayEndData->branch_date);
                }
            }
        }

        $CurrentDate = $CurrentDate->format('d-m-Y');

        return $CurrentDate;
    }

    /**
     * Get Next Working Date in System
     */
    public static function systemNextWorkingDay($currentDate, $selBranch = null, $selCompany = null)
    {
        if ($selBranch == '' || $selBranch == null || empty($selBranch)) {
            $branchId = self::getBranchId();
        } else {
            $branchId = $selBranch;
        }

        if ($selCompany == '' || $selCompany == null || empty($selCompany)) {
            $companyId = self::getCompanyId();
        } else {
            $companyId = $selCompany;
        }

        // $branchId = self::getBranchId();
        // $companyId = self::getCompanyId();

        $TempCurrentDate = new DateTime($currentDate);
        $TempNextDate    = $TempCurrentDate->modify('+1 day');

        $HolidayFlag = true;

        while ($HolidayFlag == true) {
            $HolidayFlag = false;

            $TempNext = $TempNextDate->format('d-m');
            // This is for Half Day Name
            // $DayName = strtolower($TempNextDate->format('D'));

            // This is Full day name
            $DayName = $TempNextDate->format('l');

            $TempNextD = $TempNextDate->format('Y-m-d');

            $GovtHoliday = DB::table('hr_holidays_govt')->where(['gh_date' => $TempNext, 'is_delete' => 0])->count();

            if ($GovtHoliday > 0) {
                $HolidayFlag = true;
            } else {
                $CompanyArr = (!empty($companyId)) ? ['company_id', '=', $companyId] : ['company_id', '<>', ''];

                $CompanyHolidayID = DB::table('hr_holidays_comp')->where(['is_delete' => 0])
                    ->where('ch_eff_date', '<=', $TempNextD)
                    ->where([$CompanyArr])
                    ->orderBy('ch_eff_date', 'DESC')
                    ->first();
                $CompanyHoliday = null;
                if(!empty($CompanyHolidayID)){
                    $CompanyHoliday = DB::table('hr_holidays_comp')->where('id', $CompanyHolidayID->id)
                                        ->where(function ($CompanyHoliday) use ($DayName) {
                                            $CompanyHoliday->where('ch_day', 'LIKE', "{$DayName}")
                                                ->orWhere('ch_day', 'LIKE', "%,{$DayName},%")
                                                ->orWhere('ch_day', 'LIKE', "%,{$DayName}%")
                                                ->orWhere('ch_day', 'LIKE', "%{$DayName},%");
                                        })
                                ->first();

                }
                    ## previous code heeree backup for  for doubt why gose in above query  
                    // $CompanyHoliday = $ComapnyHolidayModel::where(['is_delete' => 0])
                    // ->where('ch_eff_date', '<=', $TempNextD)
                    // ->where([$CompanyArr])
                    // ->where(function ($CompanyHoliday) use ($DayName) {
                    //     $CompanyHoliday->where('ch_day', 'LIKE', "{$DayName}")
                    //         ->orWhere('ch_day', 'LIKE', "%,{$DayName},%")
                    //         ->orWhere('ch_day', 'LIKE', "%,{$DayName}%")
                    //         ->orWhere('ch_day', 'LIKE', "%{$DayName},%");
                    // })
                    // ->count();
                    // if ($CompanyHoliday > 0) {
                

                if (!empty($CompanyHoliday)) {
                    $HolidayFlag = true;
                } else {
                    $SpecialHolidayORG = DB::table('hr_holidays_special')->where(['sh_app_for' => 'org', 'is_delete' => 0])
                        ->where('sh_date_from', '<=', $TempNextD)
                        ->where('sh_date_to', '>=', $TempNextD)
                        ->count();

                    if ($SpecialHolidayORG > 0) {
                        $HolidayFlag = true;
                    } else {
                        $SpecialHolidayBranch = DB::table('hr_holidays_special')->where(['sh_app_for' => 'branch', 'is_delete' => 0])
                            ->where('branch_id', '=', $branchId)
                            ->where('sh_date_from', '<=', $TempNextD)
                            ->where('sh_date_to', '>=', $TempNextD)
                            ->count();

                        if ($SpecialHolidayBranch > 0) {
                            $HolidayFlag = true;
                        }
                    }
                }
            }

            if ($HolidayFlag == true) {
                $TempNextDate = $TempNextDate->modify('+1 day');
            }
        }

        $currentDate = $TempNextDate->format('Y-m-d');

        return $currentDate;
    }

    /**
     * Get Next Working Date in System
     */
    public static function systemPreWorkingDay($currentDate, $selBranch = null, $selCompany = null)
    {
        if ($selBranch == '' || $selBranch == null || empty($selBranch)) {
            $branchId = self::getBranchId();
        } else {
            $branchId = $selBranch;
        }

        if ($selCompany == '' || $selCompany == null || empty($selCompany)) {
            $companyId = self::getCompanyId();
        } else {
            $companyId = $selCompany;
        }

        // $branchId = self::getBranchId();
        // $companyId = self::getCompanyId();

        $TempCurrentDate = new DateTime($currentDate);
        $TempNextDate    = $TempCurrentDate->modify('-1 day');

        $HolidayFlag = true;

        while ($HolidayFlag == true) {
            $HolidayFlag = false;

            $TempNext = $TempNextDate->format('d-m');
            // This is for Half Day Name
            // $DayName = strtolower($TempNextDate->format('D'));

            // This is Full day name
            $DayName = $TempNextDate->format('l');

            $TempNextD = $TempNextDate->format('Y-m-d');

            $GovtHoliday = DB::table('hr_holidays_govt')->where(['gh_date' => $TempNext, 'is_delete' => 0])->count();

            if ($GovtHoliday > 0) {
                $HolidayFlag = true;
            } else {
                $CompanyArr = (!empty($companyId)) ? ['company_id', '=', $companyId] : ['company_id', '<>', ''];

                $CompanyHoliday = DB::table('hr_holidays_comp')->where(['is_delete' => 0])
                    ->where('ch_eff_date', '<=', $TempNextD)
                    ->where([$CompanyArr])
                    ->where(function ($CompanyHoliday) use ($DayName) {
                        $CompanyHoliday->where('ch_day', 'LIKE', "{$DayName}")
                            ->orWhere('ch_day', 'LIKE', "%,{$DayName},%")
                            ->orWhere('ch_day', 'LIKE', "%,{$DayName}%")
                            ->orWhere('ch_day', 'LIKE', "%{$DayName},%");
                    })
                    ->count();

                if ($CompanyHoliday > 0) {
                    $HolidayFlag = true;
                } else {
                    $SpecialHolidayORG = DB::table('hr_holidays_special')->where(['sh_app_for' => 'org', 'is_delete' => 0])
                        ->where('sh_date_from', '<=', $TempNextD)
                        ->where('sh_date_to', '>=', $TempNextD)
                        ->count();

                    if ($SpecialHolidayORG > 0) {
                        $HolidayFlag = true;
                    } else {
                        $SpecialHolidayBranch = DB::table('hr_holidays_special')->where(['sh_app_for' => 'branch', 'is_delete' => 0])
                            ->where('branch_id', '=', $branchId)
                            ->where('sh_date_from', '<=', $TempNextD)
                            ->where('sh_date_to', '>=', $TempNextD)
                            ->count();

                        if ($SpecialHolidayBranch > 0) {
                            $HolidayFlag = true;
                        }
                    }
                }
            }

            if ($HolidayFlag == true) {
                $TempNextDate = $TempNextDate->modify('-1 day');
            }
        }

        $currentDate = $TempNextDate->format('Y-m-d');

        return $currentDate;
    }

    /**
     * Get system Month Working Days
     * @param companyID @type int
     * @param branchID @type int
     * @param somityID @type int
     * @param startDate @type string '02-02-2020' or '2020-02-02'
     * @param endDate @type string '02-02-2020' or '2020-02-02'
     * @param period @type string '2 day' or '2 month' or '2 year'
     *
     * @Condition
     * startDate != null && endDate != null && period == null
     * startDate != null && endDate == null && period == null (Auto Calculate last day of month)
     * startDate != null && endDate == null && period != null  (Auto calculate last day depend on period(+))
     * startDate == null && endDate != null && period == null  (Auto calculate first day of month is 01)
     * startDate == null && endDate != null && period != null (Auto calculate first day depend on period(-))
     * startDate == null && endDate == null && period != null (Get System Current date as first day & last day calculate depend on period(+))
     * startDate == null && endDate == null && period == null (Get System Current date as first day & last day calculate depend on month)
     *
     * Calling: Common::systemMonthWorkingDay(companyID,branchID,somityID,startDate,endDate, period)
     */

    public static function systemMonthWorkingDay($companyId = null, $branchId = null, $somityId = null, $startDate = null, $endDate = null, $period = null)
    {
        $companyId = (!empty($companyId)) ? $companyId : self::getCompanyId();
        $branchId  = (!empty($branchId)) ? $branchId : self::getBranchId();
        $somityId  = (!empty($somityId)) ? $somityId : 1;

        $companyId = (!empty($companyId)) ? $companyId : 1;

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if ($period == '') {
            $period = null;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $fromDate = new DateTime($startDate);
            $toDate   = new DateTime($endDate);
        } elseif (!empty($startDate) && empty($endDate) && empty($period)) {
            $fromDate = new DateTime($startDate);
            // Count day of curent month
            $lastday = cal_days_in_month(CAL_GREGORIAN, $fromDate->format('m'), $fromDate->format('Y'));
            $toDate  = new DateTime($lastday . "-" . $fromDate->format('m') . "-" . $fromDate->format('Y'));
        } elseif (!empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime($startDate);
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && !empty($endDate) && empty($period)) {
            $fromDate = new DateTime("01-" . $endDate->format('m') . "-" . $endDate->format('Y'));
            $toDate   = new DateTime($endDate);
        } elseif (empty($startDate) && !empty($endDate) && !empty($period)) {
            $toDate   = new DateTime($endDate);
            $tempDate = clone $toDate;
            $fromDate = $tempDate->modify('-' . $period);
        } elseif (empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime(self::systemCurrentDate());
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && empty($endDate) && empty($period)) {
            $fromDate = new DateTime(self::systemCurrentDate());
            // Count day of curent month
            $lastday = cal_days_in_month(CAL_GREGORIAN, $fromDate->format('m'), $fromDate->format('Y'));
            $toDate  = new DateTime($lastday . "-" . $fromDate->format('m') . "-" . $fromDate->format('Y'));
        }

        $workingDays = array();

        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $govtHolidays = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            $companyArr          = (!empty($companyId)) ? ['company_id', '=', $companyId] : ['company_id', '<>', ''];
            $companyHolidayQuery = DB::table('hr_holidays_comp')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where([$companyArr])
                ->get();
            $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Special Holiday for Organization Query
            $specialHolidayORGQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchId)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $workdayFlag = true;

                // Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    $RowFG = (array) $RowFG;

                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $workdayFlag = false;
                    }
                }

                ## Company Holiday Check
                // if ($workdayFlag == true) {
                //     foreach ($companyHolidays as $RowC) {
                //         $RowC = (array) $RowC;

                //         $ch_day = $RowC['ch_day'];

                //         $ch_day_arr  = explode(',', $RowC['ch_day']);
                //         $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                //         // This is Full day name
                //         $dayName = $tempLoopDate->format('l');

                //         if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                //             $workdayFlag = false;
                //         }
                //     }
                // }

                ## Company Holiday Check
                if ($workdayFlag == true) {
                    foreach ($companyHolidays as $RowC) {

                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        $ch_eff_date_end = $RowC['ch_eff_date_end'];
                        if(!empty($ch_eff_date_end)){
                            $ch_eff_date_end = new DateTime($ch_eff_date_end);
                        }
                        

                        ## This is Full day name
                        $dayName = $tempLoopDate->format('l');
                        
                        if (!empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) && 
                            ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) && 
                            ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))) {

                            $workdayFlag = false;
                        }
                        else if ($ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) && 
                            ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))) {
                            $workdayFlag = false;
                        }

                        
                    }
                }

                // Special Holiday Org check
                if ($workdayFlag == true) {
                    foreach ($sHolidaysORG as $RowO) {
                        $RowO = (array) $RowO;

                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                // Special Holiday Branch check
                if ($workdayFlag == true) {
                    foreach ($sHolidaysBr as $RowB) {
                        $RowB = (array) $RowB;

                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                if ($workdayFlag == true) {
                    array_push($workingDays, $tempLoopDate->format('Y-m-d'));

                }
                $tempLoopDate = $tempLoopDate->modify('+1 day');
            }
        }
        return $workingDays;
    }

    /**
     * Get system Holidays
     * @param companyID @type int
     * @param branchID @type int
     * @param somityID @type int
     * @param startDate @type string '02-02-2020' or '2020-02-02'
     * @param endDate @type string '02-02-2020' or '2020-02-02'
     * @param period @type string '2 day' or '2 month' or '2 year'
     *
     * @condition
     * startDate != null && endDate != null && period == null
     * startDate != null && endDate == null && period != null  (Auto calculate last day depend on period(+))
     * startDate == null && endDate != null && period != null (Auto calculate first day depend on period(-))
     * startDate == null && endDate == null && period != null (Get System Current date as first day & last day calculate depend on period(+))
     * Calling: Common::systemHolidays(companyID,branchID,somityID,startDate,endDate, period)
     */

    public static function systemHolidays($companyId = null, $branchId = null, $somityId = null, $startDate = null, $endDate = null, $period = null)
    {
        $companyId = (!empty($companyId)) ? $companyId : self::getBranchId();
        $branchId  = (!empty($branchId)) ? $branchId : self::getBranchId();
        $somityId  = (!empty($somityId)) ? $somityId : 1;

        $companyId = (!empty($companyId)) ? $companyId : 1;

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if ($period == '') {
            $period = null;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $fromDate = new DateTime($startDate);
            $toDate   = new DateTime($endDate);
        } elseif (!empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime($startDate);
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && !empty($endDate) && !empty($period)) {
            $toDate   = new DateTime($endDate);
            $tempDate = clone $toDate;
            $fromDate = $tempDate->modify('-' . $period);
        } elseif (empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime(self::systemCurrentDate());
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        }

        $holiDays = array();

        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $govtHolidays = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            $companyArr          = (!empty($companyId)) ? ['company_id', '=', $companyId] : ['company_id', '<>', ''];
            $companyHolidayQuery = DB::table('hr_holidays_comp')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where([$companyArr])
                ->orderBy('ch_eff_date','DESC')
                ->get();
            $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Special Holiday for Organization Query
            $specialHolidayORGQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchId)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            //  // Special Holiday for Somity Query
            //  $specialHolidaySQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
            //  ->where('branch_id', '=', $branchId)
            //  ->select('id','company_id','branch_id','sh_title','sh_app_for','sh_date_from','sh_date_to')
            //  ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $holiDayFlag = false;

                ## Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    $RowFG = (array) $RowFG;
                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $holiDayFlag = true;
                    }
                }

                ## Company Holiday Check
                if ($holiDayFlag == false) {
                    foreach ($companyHolidays as $RowC) {
                        $RowC = (array) $RowC;

                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        $ch_eff_date_end = $RowC['ch_eff_date_end'];
                        if(!empty($ch_eff_date_end)){
                            $ch_eff_date_end = new DateTime($ch_eff_date_end);
                        }

                        ## This is Full day name
                        $dayName = $tempLoopDate->format('l');
                        if (!empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) && 
                            ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) && 
                            ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))) {

                            $holiDayFlag = true;
                        }
                        else if ($ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) && 
                            ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))) {
                            $holiDayFlag = true;
                        }

                        // if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                        //     $holiDayFlag = true;
                        // }
                    }
                }

                ## Special Holiday Org check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysORG as $RowO) {
                        $RowO = (array) $RowO;

                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                ## Special Holiday Branch check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysBr as $RowB) {
                        $RowB = (array) $RowB;

                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                if ($holiDayFlag == true) {
                    array_push($holiDays, $tempLoopDate->format('Y-m-d'));
                }
                $tempLoopDate = $tempLoopDate->modify('+1 day');
            }
        }

        return $holiDays;
    }
    /**
     *
     * @param {string} field_id -> field id
     * @param {string} accept_file_type  -> file type approve, value accept 'image' or 'other'
     * @param {string} accept_file_size  -> file size in mb
     */
    public static function upload_validation($myFile, $accept_file_size = 1, $accept_file_type = 'other')
    {

        if (count($myFile) < 1) {
            return array();
        }

        $filetype = $myFile['type'];
        $filesize = $myFile['size'];

        $filesize = $filesize / (1024 * 1024); // in mb

        $errorFileSize = false;
        $errorFileType = false;

        if ($filesize > $accept_file_size) {
            $errorFileSize = true;
        }

        if ($accept_file_type == 'image') {

            if ($filetype == 'image/jpeg' ||
                $filetype == 'image/png' ||
                $filetype == 'image/bmp' ||
                $filetype == 'image/gif' ||
                $filetype == 'image/webp') {

                $errorFileType = false;
            } else {
                $errorFileType = true;
            }
        } else {
            if ($filetype == 'image/jpeg' ||
                $filetype == 'image/png' ||
                $filetype == 'image/bmp' ||
                $filetype == 'image/gif' ||
                $filetype == 'image/webp' ||
                $filetype == 'application/pdf' ||
                $filetype == 'application/msword' ||
                $filetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                $filetype == 'application/vnd.ms-excel' ||
                $filetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                $filetype == 'text/csv' ||
                $filetype == 'application/vnd.ms-powerpoint' ||
                $filetype == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {

                $errorFileType = false;
            } else {
                $errorFileType = true;
            }
        }

        if ($errorFileSize === true) {

            $notification = array(
                'message'    => 'File size must be equal or less than ' . $accept_file_size . ' mb !!',
                'alert-type' => 'error',
            );

            return Redirect()->back()->with($notification);

        } else if ($errorFileType === true) {
            $notification = array(
                'message'    => 'File format do not accept !!',
                'alert-type' => 'error',
            );

            return Redirect()->back()->with($notification);
        } else {
            return array(
                'filetype' => $filetype,
                'filesize' => $filesize,
            );
        }

    }

    public static function fileUpload($image, $tableName, $uploadId, $fileName = null, $barcode = null, $barcode_name = null)
    {
        $img_path = "uploads/" . $tableName . "/" . $uploadId;
        if ($barcode) {
            $image_name = $barcode_name;
            $ext_img    = 'png';
            $img_path .= "/barcode";
        } else {
            if (!empty($fileName)) {
                $image_name = $fileName;
            } else {
                $image_name = hexdec(uniqid());
            }
            $ext_img = strtolower($image->getClientOriginalExtension());
        }
        $image_full_name = $image_name . '.' . $ext_img;

        if (!is_dir($img_path)) {
            mkdir($img_path, 0777, true);
        }

        $image_url = $img_path . "/" . $image_full_name;

        if ($barcode) {
            $isUpload = file_put_contents($image_url, $image);
        } else {
            $isUpload = $image->move($img_path, $image_full_name);

        }
        if ($isUpload) {
            return $image_url;
        } else {
            $notification = array(
                'message'    => 'Unsuccessful to Insert',
                'alert-type' => 'error',
            );
            return Redirect()->back()->with($notification);
        }
    }

    public static function getWeekdayName()
    {
        $days = array(
            'Saturday'  => 'Saturday',
            'Sunday'    => 'Sunday',
            'Monday'    => 'Monday',
            'Tuesday'   => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday'  => 'Thursday',
            'Friday'    => 'Friday',
        );

        return $days;
    }

/* Query Builder */

    public static function ViewTableOrder($table = null, $option = [], $select = [], $order = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->orderBy($order[0], $order[1])
            ->get();

        return $data;
    }

    public static function ViewTableOrderShop($table = null, $option = [], $select = [], $order = [], $order1 = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->orderBy($order[0], $order[1])
            ->orderBy($order1[0], $order1[1])
            ->get();

        return $data;
    }

    public static function ViewTableOrderIn($table = null, $option = [], $optionIn = [], $select = [], $order = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->whereIn($optionIn[0], $optionIn[1])
            ->orderBy($order[0], $order[1])
            ->get();

        return $data;
    }

    public static function ViewTableOrderNotIn($table = null, $option = [], $optionIn = [], $select = [], $order = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->whereNotIn($optionIn[0], $optionIn[1])
            ->orderBy($order[0], $order[1])
            ->get();

        return $data;
    }

    public static function ViewTableJoinAll($table = null, $table2 = null, $option = [], $select = [], $joinOption = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->leftjoin($table2, [$joinOption[0] => $joinOption[1]])
        // ['gnl_sys_menus.is_delete' => 0])
            ->where($option)
            ->get();

        return $data;
    }

    public static function ViewTableFirst($table = null, $option = [], $select = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->first();
        return $data;
    }

    public static function ViewTableFirstIn($table = null, $option = [], $optionIn = [], $select = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->whereIn($optionIn[0], $optionIn[1])
            ->first();
        return $data;
    }

    public static function ViewTableLast($table = null, $option = [], $select = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->first();
        return $data;
    }

    public static function ViewTableLastIn($table = null, $option = [], $optionIn = [], $select = [])
    { //for all view
        $data = DB::table($table)
            ->select($select)
            ->where($option)
            ->whereIn($optionIn[0], $optionIn[1])
            ->first();
        return $data;
    }

    public static function getAllBranch()
    {
        $branch_arr = self::fnForBranchZoneAreaWise();

        if (self::getDBConnection() == "sqlite") {
            $branchData = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1], ['id', "!=", 1]])
                ->whereIn('id', $branch_arr)
                ->selectRaw('(branch_name || " [" || branch_code || "]") AS branch_name, id')
                ->pluck('branch_name', 'id')
                ->toArray();
        } else {
            $branchData = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1], ['id', "!=", 1]])
                ->whereIn('id', $branch_arr)
                ->selectRaw('CONCAT(branch_name, " [", branch_code, "]") AS branch_name, id')
                ->pluck('branch_name', 'id')
                ->toArray();
        }
        return $branchData;
    }

    public static function getAllProduct()
    {
        if (self::getDBConnection() == "sqlite") {
            $productData = DB::table('pos_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->selectRaw('(product_name || " [" || prod_barcode || "]") AS product_name, id')
                ->pluck('product_name', 'id')
                ->toArray();
        } else {
            $productData = DB::table('pos_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->selectRaw('CONCAT(product_name, " [", prod_barcode, "]") AS product_name, id')
                ->pluck('product_name', 'id')
                ->toArray();
        }
        return $productData;
    }

/* End Query Builder */

/* -------------------------------------------------------------------- generate bill start */

    public static function generateCustomerNo($branchId = null)
    {
        $ModelT = "App\\Model\\POS\\Customer";

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "CUS" . $BranchCode;
        $record    = $ModelT::select(['id', 'customer_no'])
            ->where('branch_id', $branchId)
            ->where('customer_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('customer_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->customer_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateGuarantorNo($branchId = null)
    {
        $ModelT = "App\\Model\\POS\\Guarantor";

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "GUR" . $BranchCode;
        $record    = $ModelT::select(['id', 'guarantor_no'])
        // ->where('branch_id', $branchId)
            ->where('guarantor_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('guarantor_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->guarantor_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateEmployeeNo($branchId = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "EMP" . $BranchCode;
        $record    = DB::table('hr_employees')->select(['id', 'employee_no'])
        // ->where('branch_id', $branchID)
            ->where('employee_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('employee_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->employee_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

/* --------------------------------------------------------------------- generate bill End */

    public static function getSignatureSettings($branchId = null)
    {
        if (is_null($branchId)) {
            $branchId = Auth::user()->branch_id;
        }

        $CurrentRouteURI   = Route::getCurrentRoute()->uri();
        $currentRouteURIAr = explode('/', $CurrentRouteURI);
        $moduleName        = $currentRouteURIAr[0];

        $module_id = DB::table('gnl_sys_modules')
            ->where([['is_delete', 0], ['is_active', 1]])->where('route_link', $moduleName)
            ->first()->id;

        // dd($module_id);

        $Model = 'App\\Model\\GNL\\SignatureSettings';
        if ($branchId == 1) {

            $QuerryData = $Model::where('is_delete', 0)->where('status', 1)->where('module_id', $module_id)->where('applicableFor', 'HeadOffice')->orderBy('positionOrder')->get();

        } else {
            $QuerryData = $Model::where('is_delete', 0)->where('status', 1)->where('module_id', $module_id)->where('applicableFor', 'Branch')->orderBy('positionOrder')->get();

        }
        // dd($QuerryData);

        return $QuerryData;
    }

    public static function getSignatureEmployee($branchId = null, $designation = null, $employeeID = null)
    {
        if (is_null($branchId)) {
            $branchId = Auth::user()->branch_id;
        }

        if (!empty($designation)) {
            $QuerryData = DB::table('hr_employees')
                ->where('branch_id', $branchId)
                ->where('designation_id', $designation)
                ->first();

            if (!empty($QuerryData)) {
                return $QuerryData->emp_name . " (" . $QuerryData->emp_code . ")";
            } else {
                return '';
            }
        }

        if (!empty($employeeID)) {
            $QuerryData = DB::table('hr_employees as he')
                ->where('he.id', $employeeID)
                ->leftJoin('hr_designations as hd', 'hd.id', 'he.designation_id')
                ->select('hd.name')
                ->first();

            if (!empty($QuerryData)) {
                return $QuerryData->name;
            } else {
                return '';
            }
        }
    }

    public static function numberToWord($Number = null)
    {
        return self::numberToWordT($Number) . " Only";
    }

    public static function numberToWordT($Number = null)
    {
        $my_number = $Number;
        if (($Number < 0) || ($Number > 999999999)) {
            throw new Exception("Number is out of range");
        }
        $Kt = floor($Number / 10000000); /* Koti */
        $Number -= $Kt * 10000000;
        $Gn = floor($Number / 100000); /* lakh  */
        $Number -= $Gn * 100000;
        $kn = floor($Number / 1000); /* Thousands (kilo) */
        $Number -= $kn * 1000;
        $Hn = floor($Number / 100); /* Hundreds (hecto) */
        $Number -= $Hn * 100;
        $Dn  = floor($Number / 10); /* Tens (deca) */
        $n   = $Number % 10; /* Ones */
        $res = "";

        if ($Kt) {
            $res .= self::numberToWordT($Kt) . " Crore "; /* Koti */
        }
        if ($Gn) {
            $res .= self::numberToWordT($Gn) . " Lac"; /* Lakh */
        }
        if ($kn) {
            $res .= (empty($res) ? "" : " ") .
            self::numberToWordT($kn) . " Thousand";
        }
        if ($Hn) {
            $res .= (empty($res) ? "" : " ") .
            self::numberToWordT($Hn) . " Hundred";
        }

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
            "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
            "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
            "Nineteen");

        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
            "Seventy", "Eigthy", "Ninety");

        if ($Dn || $n) {
            if (!empty($res)) {
                $res .= " and ";
            }
            if ($Dn < 2) {
                $res .= $ones[$Dn * 10 + $n];
            } else {
                $res .= $tens[$Dn];
                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }
        if (empty($res)) {
            $res = "zero";
        }
        return $res;
    }

    public static function AccessDeniedReason($condition = null)
    {
        $text = 'You are not allowed to execute this action.';

        if ($condition == "date") {
            $text = "Branch and System Date did not matched.";
        }

        if ($condition == "salesReturn") {
            $text = "Sales Return Data. Access it from Sales Return.";
        }

        if ($condition == "hasSalesReturn") {
            $text = "This data has SalesReturn.";
        }
        if ($condition == "dayEndForward") {
            $text = "Branch Date gone forward. Have to delete those Date first.";
        }
        if ($condition == "monthEndForward") {
            $text = "Branch Month gone forward. Have to delete those Month first.";
        }
        if ($condition == "hasPayment") {
            $text = "This data has Payments.";
        }
        if ($condition == "hasCollection") {
            $text = "This data has Collections.";
        }
        if ($condition == "firstCollection") {
            $text = "This is first Collection from sales.";
        }

        if ($condition == "hasSale") {
            $text = "This data has Sales.";
        }
        if ($condition == "approved") {
            $text = "Approved Data.";
        }

        if ($condition == "authorized") {
            $text = "Authorized data can not be updated or removed.";
        }
        if ($condition == "completed") {
            $text = "Completed data can not be updated or removed.";
        }
        if ($condition == "fixedID") {
            $text = "This data can not be deleted.";
        }
        if ($condition == "FundTransferFromOtherBranch") {
            $text = "Fund Transfer from another branch.";
        }
        if ($condition == "AutoVoucher") {
            $text = "Auto Voucher can not be updated or deleted.";
        }
        if ($condition == "holiday") {
            $text = "Can not execute this action.It Has Active Day/Day End Ahead";
        }

        if ($condition == "transaction") {
            $text = "This data cant be deleted because it already has transaction";
        }
        if ($condition == "Rebate") {
            $text = "Rebate data cant be updated/deleted From here.";
        }

        return $text;
    }

    public static function fnForBranchZoneAreaWise($branchId = null, $zoneId = null, $areaId = null, $companyId = null)
    {
        $selBranchArr = array();
        if (empty($branchId) || $branchId == -1 || $branchId == -2) {
            if (!empty($zoneId) && !empty($areaId)) {
                $zoneQuery = DB::table('gnl_zones')
                    ->where([['is_active', 1], ['is_delete', 0], ['id', $zoneId]])
                    ->where(function ($zoneQuery) use ($companyId) {
                        if (!empty($companyId)) {
                            $zoneQuery->where('company_id', $companyId);
                        }
                    })
                    ->select('branch_arr')
                    ->first();

                if ($zoneQuery) {
                    $selBranchArrZ = explode(',', $zoneQuery->branch_arr);
                } else {
                    $selBranchArrZ = HRS::getUserAccesableBranchIds();
                }

                $areaQuery = DB::table('gnl_areas')
                    ->where([['is_active', 1], ['is_delete', 0], ['id', $areaId]])
                    ->where(function ($areaQuery) use ($companyId) {
                        if (!empty($companyId)) {
                            $areaQuery->where('company_id', $companyId);
                        }
                    })
                    ->select('branch_arr')
                    ->first();

                if ($areaQuery) {
                    $selBranchArr = explode(',', $areaQuery->branch_arr);
                } else {
                    $selBranchArr = HRS::getUserAccesableBranchIds();
                }

                // $selBranchArr = array_unique(array_merge($selBranchArrZ, $selBranchArrA));

                // dd(count($selBranchArr));
            } elseif (!empty($zoneId) && empty($areaId)) {
                $zoneQuery = DB::table('gnl_zones')
                    ->where([['is_active', 1], ['is_delete', 0], ['id', $zoneId]])
                    ->where(function ($zoneQuery) use ($companyId) {
                        if (!empty($companyId)) {
                            $zoneQuery->where('company_id', $companyId);
                        }
                    })
                    ->select('branch_arr')
                    ->first();

                if ($zoneQuery) {
                    $selBranchArr = explode(',', $zoneQuery->branch_arr);
                } else {
                    $selBranchArr = HRS::getUserAccesableBranchIds();
                }

            } elseif (!empty($areaId) && empty($zoneId)) {
                $areaQuery = DB::table('gnl_areas')
                    ->where([['is_active', 1], ['is_delete', 0], ['id', $areaId]])
                    ->where(function ($areaQuery) use ($companyId) {
                        if (!empty($companyId)) {
                            $areaQuery->where('company_id', $companyId);
                        }
                    })
                    ->select('branch_arr')
                    ->first();

                if ($areaQuery) {
                    $selBranchArr = explode(',', $areaQuery->branch_arr);
                } else {
                    $selBranchArr = HRS::getUserAccesableBranchIds();
                }

            } else {
                $selBranchArr = HRS::getUserAccesableBranchIds();
            }

            if ($branchId == -2) {
                $positionA = array_search(1, $selBranchArr);

                if ($positionA !== false) {
                    unset($selBranchArr[$positionA]);
                    $selBranchArr = array_values($selBranchArr);
                }
            }
        } else {
            $selBranchArr = [$branchId];
        }

        return $selBranchArr;
    }

    public static function fnForBranchData($branchArr)
    {
        $branchData = array();
        if (count($branchArr) > 0) {

            if (self::getDBConnection() == "sqlite") {
                $branchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $branchArr)
                    ->selectRaw('(branch_name || " [" || branch_code || "]" ) AS branch_name, id')
                    ->pluck('branch_name', 'id')
                    ->toArray();
            } else {
                $branchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $branchArr)
                    ->selectRaw('CONCAT(branch_name, " [", branch_code, "]") AS branch_name, id')
                    ->pluck('branch_name', 'id')
                    ->toArray();
            }
        }
        return $branchData;
    }


    /**
     * 
     */

    public static function fnForEmployeeData($employeeArr, $posModule = false, $userFlag = false)
    {
        $employeeData = array();
        if (count($employeeArr) > 0) {

            if (self::getDBConnection() == "sqlite") {
                $employeeData = DB::table('hr_employees')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('employee_no', $employeeArr)
                    ->selectRaw('(emp_name || " [" || emp_code || "]") AS emp_name, employee_no')
                    ->pluck('emp_name', 'employee_no')
                    ->toArray();
            } else {
                if ($posModule) {
                    $employeeData = DB::table('hr_employees')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('employee_no', $employeeArr)
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, employee_no')
                        ->pluck('emp_name', 'employee_no')
                        ->toArray();
                } else {

                    if($userFlag == false) {
                        ## employee data by employee id
                        $employeeData = DB::table('hr_employees')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('id', $employeeArr)
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
                        ->pluck('emp_name', 'id')
                        ->toArray();
                    }
                    else {
                        ## employee data by user_id 
                        $employeeData = DB::table('hr_employees')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('user_id', $employeeArr)
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, user_id')
                        ->pluck('emp_name', 'user_id')
                        ->toArray();
                    }
                   
                }
            }

        }

        return $employeeData;
    }



    /**
     * get decimal value
     */
    public static function getDecimalValue($amount)
    {
        $decimal = Session::get('decimalConfig');
        if ($decimal == 0) {
            return number_format(round($amount));
        } else {
            return number_format(round($amount), $decimal);
        }

        //cases to be implemented
        /*
    1. full round => 100
    2. show decimal value => 100.25 & != 100.00
    3. show all decimal => 100.25 || 100.00
     */
    }

    public static function fnForRoleInfo($roleArr = [])
    {
        $roleData = array();
        if (count($roleArr) > 0) {

            if (self::getDBConnection() == "sqlite") {
                $roleData = DB::table('gnl_sys_user_roles')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $roleArr)
                    ->selectRaw('role_name, id')
                    ->pluck('role_name', 'id')
                    ->toArray();
            } else {
                $roleData = DB::table('gnl_sys_user_roles')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $roleArr)
                    ->selectRaw('role_name, id')
                    ->pluck('role_name', 'id')
                    ->toArray();
            }
        }

        return $roleData;
    }

    public static function fnForSendSms($receiver, $bodyTxt, $text_type = "text", $sender = "8809612441636")
    {
        // sender id 8809612441636 for usha only
        // $text_type == text for normal SMS/unicode for Bangla SMS
        // api_key == C200100760f51b411780e4.77150263
        $api_key = "C200100760f51b411780e4.77150263";

        $url  = "http://portal.metrotel.com.bd/smsapi";
        $data = [
            //   "api_key" => "your_api-key",
            //   "type" => "{content type}",
            //   "contacts" => "88017xxxxxxxx+88018xxxxxxxx",
            //   "senderid" => "{sender id}",
            //   "msg" => "{your message}",

            "api_key"  => $api_key,
            "type"     => $text_type,
            "contacts" => $receiver,
            "senderid" => $sender,
            "msg"      => $bodyTxt,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function numToOrdinalWord($num)
    {
        $first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
        $second_word =array('','','Twenty','Thirty','Forty','Fifty');

        if($num <= 20)
            return $first_word[$num];

        $first_num = substr($num,-1,1);
        $second_num = substr($num,-2,1);

        return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
    }

    public static function designationWiseReportFormatAndSearchFeild()
    {
        $dataField   = array();
        $SearchField = array();

        $userInfo = Auth::user();
        $designation_value = 0;

        if ($userInfo->branch_id > 1) {
            $roleId             = $userInfo->sys_user_role_id;
            $designationRoleMap = DB::table('hr_designation_role_mapping')
                                    ->where(function ($query) use ($roleId) {
                                        if (!empty($roleId)) {
                                            $query->where('role_id', 'LIKE', "{$roleId}")
                                                ->orWhere('role_id', 'LIKE', "{$roleId},%")
                                                ->orWhere('role_id', 'LIKE', "%,{$roleId},%")
                                                ->orWhere('role_id', 'LIKE', "%,{$roleId}");
                                        }
                                    })
                                    ->first();

            if($designationRoleMap) {
                $designation_value  = (int) $designationRoleMap->position_id;
            }
            else {
                $designation_value = 1;
            }
        }

        /**
         * 0 HO
         * 1 CO
         * 2 Account
         * 3 branch
         * 4 area
         * 5 zone
         */

        switch ($designation_value) {
            case 3:
                $dataField = array_merge($dataField, ['co' => 'CO Wise']);
                $dataField = array_merge($dataField, ['samity' => 'Samity Wise']);
                $dataField = array_merge($dataField, ['member' => 'Member Wise']);

                array_push($SearchField, "samity");
                array_push($SearchField, "fieldofficerdropdown");
                break;
            case 4:
                $dataField = array_merge($dataField, ['branch' => 'Branch Wise']);
                $dataField = array_merge($dataField, ['co' => 'CO Wise']);
                $dataField = array_merge($dataField, ['samity' => 'Samity Wise']);
                $dataField = array_merge($dataField, ['member' => 'Member Wise']);

                array_push($SearchField, "samity");
                array_push($SearchField, "fieldofficerdropdown");
                array_push($SearchField, "branch");
                break;
            case 5:
                $dataField = array_merge($dataField, ['area' => 'Area Wise']);
                $dataField = array_merge($dataField, ['branch' => 'Branch Wise']);
                $dataField = array_merge($dataField, ['co' => 'CO Wise']);
                $dataField = array_merge($dataField, ['samity' => 'Samity Wise']);
                $dataField = array_merge($dataField, ['member' => 'Member Wise']);

                array_push($SearchField, "samity");
                array_push($SearchField, "fieldofficerdropdown");
                array_push($SearchField, "branch");
                array_push($SearchField, "area");
                break;
            case 0:
                $dataField = array_merge($dataField, ['zone' => 'Zone Wise']);
                $dataField = array_merge($dataField, ['area' => 'Area Wise']);
                $dataField = array_merge($dataField, ['branch' => 'Branch Wise']);
                $dataField = array_merge($dataField, ['co' => 'CO Wise']);
                $dataField = array_merge($dataField, ['samity' => 'Samity Wise']);
                $dataField = array_merge($dataField, ['member' => 'Member Wise']);

                array_push($SearchField, "samity");
                array_push($SearchField, "fieldofficerdropdown");
                array_push($SearchField, "branch");
                array_push($SearchField, "area");
                array_push($SearchField, "zone");
                break;
            default:
                $dataField = array_merge($dataField, ['samity' => 'Samity Wise']);
                $dataField = array_merge($dataField, ['member' => 'Member Wise']);

                array_push($SearchField, "samity");
                break;
        }

        $data = array(
            'SearchField' => $SearchField,
            'dataField'   => $dataField,
        );

        return $data;
    }

    //Create Function ReplaceZeroWithDash

    public static function replaceZeroWithDash($arg)
    {
        if($arg == 0)
        {
            return "-";
        }elseif($arg === 0)
        {
            return "-";
        }elseif($arg == '0')
        {
            return "-";
        }else{
            return $arg;
        }

    }

}
