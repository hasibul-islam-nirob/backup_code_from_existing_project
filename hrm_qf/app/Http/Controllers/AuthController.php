<?php

namespace App\Http\Controllers;

use App\User;
use DateTime;
use App\Model\GNL\SysUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Model\GNL\SysUserRole;
use App\Model\GNL\SysUserDevice;
use App\Model\GNL\SysUserHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Model\GNL\SysUserFailedLogin;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use App\Services\RoleService as Role;

class AuthController extends Controller
{

    public function index()
    {
        if (Auth::check()) {

            //////////////////////////////////////////////////////

            // $routes = [];
            // foreach (\Route::getRoutes()->getIterator() as $route){
            //     if ($route->uri !== "/"
            //         && strpos($route->uri, 'api') === false
            //         && strpos($route->uri, 'ajax') === false
            //         && strpos($route->uri, '_debugbar') === false
            //         && strpos($route->uri, '_ignition') === false
            //         && strpos($route->uri, 'password') === false
            //         && strpos($route->uri, '/') !== false
            //         && strpos($route->uri, '/add') === false
            //         && strpos($route->uri, '/edit') === false
            //         && strpos($route->uri, '/view') === false
            //         && strpos($route->uri, '/delete') === false
            //         && strpos($route->uri, '/publish') === false
            //         && strpos($route->uri, '/approve') === false
            //         && strpos($route->uri, '/destroy') === false
            //         && strpos($route->uri, '/change_pass') === false
            //         && strpos($route->uri, '/sys_permission') === false
            //         && strpos($route->uri, '/passign') === false
            //         && strpos($route->uri, '/execute') === false
            //         && strpos($route->uri, '/execute') === false
            //         && strpos($route->uri, '/CheckDayEnd') === false
            //         && strpos($route->uri, '/routes') === false
            //         && strpos($route->uri, '/refreshRoutes') === false
            //         && strpos($route->uri, '/day_end') === false
            //         && strpos($route->uri, '/month_end') === false
            //         && strpos($route->uri, '/update_sales_script') === false
            //         && strpos($route->uri, '/product') === false
            //         && strpos($route->uri, '/getData') === false
            //         && strpos($route->uri, '/invoice') === false
            //         && strpos($route->uri, '/load') === false
            //         && strpos($route->uri, '/popUp') === false
            //         && strpos($route->uri, '/year_end') === false
            //         && strpos($route->uri, '/auth') === false
            //         && strpos($route->uri, '/ajE') === false
            //         && strpos($route->uri, '/aj') === false
            //         ){

            //         $routes[] = $route->uri;
            //     }
            // }
            // $queryData = DB::table('gnl_sys_menus')->where('is_delete', 0)->pluck('route_link')->toArray();

            // $result = array_diff($routes, $queryData);

            // dd($result);

            /////////////////////////////////

            if (!empty(Session::get('LoginBy.user_role.role_module'))) {
                $SysModules = Session::get('LoginBy.user_role.role_module');
            } else {
                $SysModules = array();
            }
            return view('module_dashboard', compact('SysModules'));
        } else {
            return view('login');
        }
    }

    public function postLogin(Request $request)
    {
        request()->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $Today         = new DateTime();
        $TodayDateTime = $Today->format('Y-m-d H:i:s');

        $RequestData = $request->all();

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'is_active' => 1, 'is_delete' => 0])) {
        // if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {

            // Get the currently authenticated user...
            $UserInfo = Auth::user();
            $UserID   = Auth::id();

            // $UserInfo['is_active']
            if ($UserInfo->is_active == 1) {

                ## remember me set cookie

                if (empty($RequestData['remember'])) {
                    setcookie('login_username', $RequestData['username'], 100);
                    setcookie('login_password', base64_encode($RequestData['password']), 100);

                } else {
                    setcookie('login_username', $RequestData['username'], time() + 60 * 60 * 24 * 1);
                    setcookie('login_password', base64_encode($RequestData['password']), time() + 60 * 60 * 24 * 1);
                }

                // dd($RequestData);

                /// ## this code for offline pos
                // if(Common::isSuperUser() != true && Common::isDeveloperUser() != true){
                //     if (Common::getDBConnection() == "sqlite") {
                //         if($UserInfo->branch_id == 1){
                //             $notification = array(
                //                 'message' => 'You are not authorised this location. Please go to online URL',
                //                 'alert-type' => 'error',
                //             );
                //             Session::flush();
                //             Auth::logout();
                //             return Redirect::to('/')->with($notification);
                //         }
                //     }
                // }

                // Login TRUE
                // $UserInfo['sys_user_role_id']
                $RoleID = $UserInfo->sys_user_role_id;

                // Fetch User Role Data
                $UserRoleData = SysUserRole::where('id', $RoleID)
                    ->select(['id', 'parent_id', 'role_name', 'serialize_module', 'serialize_menu', 'serialize_permission',
                        'modules', 'menus', 'permissions'])
                    ->first();

                // Data Insert in System History Table
                $RequestData['sys_username']     = $RequestData['username'];
                $RequestData['sys_user_id']      = $UserID;
                $RequestData['sys_user_role_id'] = $RoleID;
                $RequestData['login_time']       = $TodayDateTime;
                $RequestData['ip_address']       = $_SERVER['REMOTE_ADDR'];
                $RequestData['http_user_agent']  = $_SERVER['HTTP_USER_AGENT'];

                $InsertHistory = SysUserHistory::create($RequestData);

                if ($InsertHistory) {
                    // dd($InsertHistory);
                    $lastInsertQuery = SysUserHistory::latest()->first();
                    $historys_id     = $lastInsertQuery->id;

                    // $historys_id = $InsertHistory->id;

                    // Check User Device Table & Insert
                    $UserDeviceData = SysUserDevice::where(['sys_user_id' => $UserID,
                        'ip_address'                                          => $_SERVER['REMOTE_ADDR'],
                        'http_user_agent'                                     => $_SERVER['HTTP_USER_AGENT'],
                    ])
                        ->select(['id'])
                        ->orderBy('id', 'DESC')
                        ->first();

                    //dd($UserDeviceData);

                    // Last Login time insert in User device or Update
                    if ($UserDeviceData) {
                        $logout_time    = $TodayDateTime;
                        $UpdateDeviceTB = SysUserDevice::where('id', $UserDeviceData->id)->update(['updated_at' => $logout_time]);
                    } else {
                        $HTTP_USER_AGENT            = $_SERVER['HTTP_USER_AGENT'];
                        $HTTP_USER_AGENT_Array      = explode('(', $HTTP_USER_AGENT);
                        $HTTP_USER_AGENT_Array2     = explode(')', $HTTP_USER_AGENT_Array[1]);
                        $RequestData['device_name'] = $HTTP_USER_AGENT_Array2[0];
                        $InsertDeviceTB             = SysUserDevice::create($RequestData);
                    }

                    // 'user_config' => [
                    //     'company_id' => $UserInfo['company_id'],
                    //     'branch_id' => $UserInfo['branch_id'],
                    //     'company_logo' => '',
                    //     'employee_id' => $UserInfo['employee_id']
                    // ]

                    // // ## Company Data
                    $company_type = "";
                    if (!empty($UserInfo->company_id)) { ## formID 2 = company type

                        $company_type = Common::getCompanyType();

                        // Common::getCompanyType();
                        // $companyType = DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 2]])->first();


                        // if ($companyType) {
                        //     $company_type = $companyType->form_value;
                        // }
                    }

                    if(Common::isSuperUser() == true) {
                        $role_module = unserialize(base64_decode(Role::prepareModuleArray()));
                        $role_menu = unserialize(base64_decode(Role::prepareMenuArray()));
                        $role_permission = unserialize(base64_decode(Role::preparePermissionArray()));
                    }
                    else {
                        $role_module = unserialize(base64_decode(Role::prepareModuleArray(explode(',', $UserRoleData->modules))));
                        $role_menu = unserialize(base64_decode(Role::prepareMenuArray(explode(',', $UserRoleData->menus))));
                        $role_permission = unserialize(base64_decode(Role::preparePermissionArray(explode(',', $UserRoleData->permissions))));
                    }

                    // dd($role_module, $role_menu, $role_permission);

                    // Store data in Session
                    $LoginData = [
                        'user_config'     => [
                            'company_id'   => $UserInfo->company_id,
                            'branch_id'    => $UserInfo->branch_id,
                            'company_type' => $company_type,
                            // 'counter_no' => '00',
                            'company_logo' => '',
                            'employee_no'  => $UserInfo->employee_no,
                            'emp_id'       => $UserInfo->emp_id,
                        ],
                        // 'user_role'       => [
                        //     'role_module'     => unserialize(base64_decode($UserRoleData->serialize_module)),
                        //     'role_menu'       => unserialize(base64_decode($UserRoleData->serialize_menu)),
                        //     'role_permission' => unserialize(base64_decode($UserRoleData->serialize_permission)),
                        // ],
                        'user_role'       => [
                            'role_module'     => $role_module,
                            'role_menu'       => $role_menu,
                            'role_permission' => $role_permission,
                        ],
                        'login_role_name' => $UserRoleData->role_name,
                        'historys_id'     => $historys_id,
                        // 'last_login_ip' => $_SERVER['REMOTE_ADDR'],
                        // 'last_login_time' => $TodayDateTime,
                    ];

                    // dd($LoginData);

                    // Write in Session
                    $Session = $request->session();
                    $Session->put('LoginBy', $LoginData);
                    $lastInsertQuery->update(['session_key' => $Session->getId()]);
                    // dd($Session->getId());

                    // $UserInfo['full_name']
                    $notification = array(
                        'message'    => '!!!! Welcome ' . $UserInfo->full_name . ' !!!!',
                        'alert-type' => 'success',
                    );

                    return redirect()->intended('/')->with($notification);
                    // return Redirect::to('login')->with($notification);
                }
            } else {
                /* Logout auth if user inactive */
                Session::flush();
                Auth::logout();
                $notification = array(
                    'message'    => 'Inactive user. Please contact to administration.',
                    'alert-type' => 'error',
                );
                return Redirect::to('/')->with($notification);
            }
        } else {

            /* Failed Login */

            $RequestData['attempt_time']    = $TodayDateTime;
            $RequestData['ip_address']      = $_SERVER['REMOTE_ADDR'];
            $RequestData['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            if (SysUserFailedLogin::create($RequestData)) {
                $notification = array(
                    'message'    => 'Oppes! Username or password is incorrect',
                    'alert-type' => 'error',
                );
                return Redirect::to('/')->with($notification);
            }
        }
    }

    public function logout()
    {
        $UserID      = Auth::id();
        $LoginByData = Session::get('LoginBy');

        // Update User Table Data
        // if ($UserID) {
        //     $UpdateUserTB = SysUser::where('id', $UserID)->update([
        //         'last_login_ip' => $LoginByData['last_login_ip'],
        //         'last_login_time' => $LoginByData['last_login_time'],
        //     ]);
        // }

        // Update History Table Data
        $HistoryID = $LoginByData['historys_id'];
        if ($HistoryID) {

            $Today         = new DateTime();
            $TodayDateTime = $Today->format('Y-m-d H:i:s');

            $UpdateHistoryTB = SysUserHistory::where('id', $HistoryID)->update(['logout_time' => $TodayDateTime]);

            if ($UpdateHistoryTB) {
                Session::flush();
                Auth::logout();
                // return Redirect('login');
                // return Redirect::to('/')->with($notification);
                return Redirect::to('/');
            }
        }

        Session::flush();
        Auth::logout();
        return Redirect::to('login');
    }

    public function moduleDashboard()
    {
        if (Auth::check()) {
            if (!empty(Session::get('LoginBy.user_role.role_module'))) {
                $SysModules = Session::get('LoginBy.user_role.role_module');
            } else {
                $SysModules = array();
            }

            return view('module_dashboard', compact('SysModules'));
        }
        return Redirect::to("login")->withSuccess('Opps! You do not have access');
    }

    public function ajaxSetModuleID(Request $request)
    {

        if ($request->ajax()) {

            // $ModuleID = $request->ModuleID;
            $ModuleLink = $request->ModuleLink;
            // Store in Seesion
            // $request->session()->put('ModuleID', $ModuleID);
            $request->session()->put('ModuleID', $ModuleLink);

            // $request->session()->forget('ModuleID');

            // Retrive session Data
            $TestData = ($request->session()->get('ModuleID') !== null) ? 1 : 0;

            echo json_encode($TestData);
        }
    }

    public function resetmobile(Request $request)
    {

        if ($request->isMethod('post')) {
            request()->validate([
                'mobile' => 'required',
            ]);

            $Today         = new DateTime();
            $TodayDateTime = $Today->format('Y-m-d H:i:s');

            $RequestData = $request->all();
            $userCheck   = SysUser::where(['contact_no' => $request->mobile])->first();
            if (!empty($userCheck)) {
                #make otp

                $otp         = rand(100000, 999999);
                $user_mobile = $userCheck->contact_no;

                ## set in cookie the otp and mobile number for 3 minute

                ## function call to send massage = ();
                ## fnSendMassage();

                ##//
                // setcookie('bptomo',base64_encode($user_mobile),time()+60*3);
                // setcookie('ptomo',base64_encode($otp) ,time()+60*3);
                setcookie('bptomo', $user_mobile, time() + 60 * 3);
                setcookie('ptomo', $otp, time() + 60 * 3);

                ##set an api to send a otp code function_implementation
                return view('auth.passwords.otp');

            } else {

                /* Failed Login */

                $RequestData['attempt_time']    = $TodayDateTime;
                $RequestData['ip_address']      = $_SERVER['REMOTE_ADDR'];
                $RequestData['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                if (SysUserFailedLogin::create($RequestData)) {
                    $notification = array(
                        'message'    => 'Oppes! mobile number is incorrect',
                        'alert-type' => 'error',
                    );
                    // return Redirect::to('/')->with($notification);
                    return redirect()->back()->with($notification);
                }

            }

        } else {
            return view('auth.passwords.mobile');
        }

    }

    public function varifyotp(Request $request)
    {

        if ($request->isMethod('post')) {
            request()->validate([
                'otp' => 'required',
            ]);

            if (isset($_COOKIE['ptomo']) && isset($_COOKIE['bptomo'])) {
                $ptomo  = base64_decode($_COOKIE['ptomo']);
                $mobile = base64_decode($_COOKIE['bptomo']);
                //##
                $ptomo  = $_COOKIE['ptomo'];
                $mobile = $_COOKIE['bptomo'];
                $token  = Str::random(60);

                // dd($ptomo ,$request->otp);

                if ($ptomo == $request->otp) {

                    $userCheck                 = SysUser::where(['contact_no' => $mobile])->first();
                    $userCheck->remember_token = $token;
                    $userCheck->update();
                    // return Redirect::to('/password/reset/'.$token.'?email='.$mobile);
                    // // $url = "http://localhost/main.php?email=$email_address&event_id=$event_id";
                    return view('auth.passwords.resetmobile', compact("mobile", "token"));
                } else {

                    setcookie('ptomo', '', 100);
                    setcookie('bptomo', '', 100);

                    $notification = array(
                        'message'    => 'Oppes! OTP is incorrect',
                        'alert-type' => 'error',
                    );
                    // return Redirect::to('/')->with($notification);
                    return redirect()->back()->with($notification);

                }

            } else {
                return view('auth.passwords.mobile');
            }

            $Today         = new DateTime();
            $TodayDateTime = $Today->format('Y-m-d H:i:s');

            $RequestData = $request->all();
            $userCheck   = SysUser::where(['contact_no' => $request->mobile])->first();
            if (!empty($userCheck)) {
                #make otp

                $otp         = rand(100000, 999999);
                $user_mobile = $userCheck->contact_no;

                ## set in cookie the otp and mobile number for 3 minute

                ## function call to send massage = ();
                ## fnSendMassage();

                setcookie('ptomo', base64_encode($user_mobile), time() + 60 * 3);
                setcookie('bptomo', base64_encode($otp), time() + 60 * 3);

                ##set an api to send a otp code function_implementation
                return view('auth.passwords.otp');

            } else {

                /* Failed Login */

                $RequestData['attempt_time']    = $TodayDateTime;
                $RequestData['ip_address']      = $_SERVER['REMOTE_ADDR'];
                $RequestData['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                if (SysUserFailedLogin::create($RequestData)) {
                    $notification = array(
                        'message'    => 'Oppes! mobile number is incorrect',
                        'alert-type' => 'error',
                    );
                    // return Redirect::to('/')->with($notification);
                    return redirect()->back()->with($notification);
                }

            }

        }

    }
    public function passupdate(Request $request)
    {

        if ($request->isMethod('post')) {
            $request->validate([
                'password'              => 'required | same:password_confirmation',
                'password_confirmation' => 'required',
            ]);

            $userCheck = SysUser::where(['contact_no' => $request->contact_no])->where(['remember_token' => $request->token])->first();

            $data     = Hash::make($request->password);
            $isUpdate = $userCheck->update(['password' => $data]);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('/')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Something went wrong, Please try again.',
                    'alert-type' => 'error',
                );
                return redirect('/')->with($notification);
            }
        }

        //    / dd($request->all());
    }

    // public function registration()
    // {
    //     if (Auth::check()) {
    //         return view('registration');
    //     }

    //     return Redirect::to("login")->withSuccess('Opps! You do not have access');

    // }

    // public function postRegistration(Request $request)
    // {
    //     request()->validate([
    //         'full_name' => 'required',
    //         'username' => 'required|unique:gnl_sys_users',
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //     ]);

    //     $data = $request->all();
    //     $check = $this->create($data);

    //     return Redirect::to("module_dashboard")->withSuccess('Great! You have Successfully loggedin');
    // }

    // public function create(array $data)
    // {
    //     return User::create([
    //         'full_name' => $data['full_name'],
    //         'email' => $data['email'],
    //         'username' => $data['username'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    public function pageNotFound()
    {
        dd(1);
        if (Auth::check()) {
            return view('errors.page_not_found');
        } else {
            return view('login');
        }
    }

    public function accessDenied()
    {
        if (Auth::check()) {
            return view('errors.access_denied');
        } else {
            return view('login');
        }
    }

    public function underConstruction()
    {
        if (Auth::check()) {
            return view('errors.under_construction');
        } else {
            return view('login');
        }
    }

}
