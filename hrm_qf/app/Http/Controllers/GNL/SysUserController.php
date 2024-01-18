<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\SysUser;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;

class SysUserController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function getPassport($req, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules = array();

        // if ($operationType == 'store_inst' || $operationType == 'update_inst') {

        //     $rules = array(
        //         'sales_bill_no' => 'required',
        //         'sales_date' => 'required',
        //         'sales_type' => 'required',
        //         'payment_system_id' => 'required',
        //     );
        //     $rules = array_merge($rules, array(
        //         'installment_month' => 'required',
        //         'installment_type' => 'required',
        //     ));

        //      $attributes = array(
        //             'sales_bill_no' => 'Sales Bill',
        //             'sales_date' => 'Sales Date',
        //             'sales_type' => 'Sales Type',
        //             'payment_system_id' => 'Payment System',
        //             'installment_month' => 'instalment Month',
        //             'installment_type' => 'instalment Type',
        //         );
        //      $validator = Validator::make($req->all(), $rules, [], $attributes);

        //     if ($validator->fails()) {
        //         $errorMsg = implode(' || ', $validator->errors()->all());
        //     }

        //     if ($errorMsg == null && $operationType == 'update_inst') {

        //         $sales_date = (new DateTime($req->sales_date))->format('Y-m-d');

        //         if ($Data->sales_date != $sales_date) {
        //             $errorMsg = "Sorry Sales Date did not matched.";
        //         }

        //         if ($Data->sales_bill_no != $req->sales_bill_no) {
        //             $errorMsg = "Sorry Sales Bill number did not matched.";
        //         }

        //         if ($Data->employee_id != $req->employee_id) {
        //             $errorMsg = "Sorry Employee ID did not matched.";
        //         }

        //         // dd($errorMsg);
        //         // dd($Data->employee_id, $req->employee_id);

        //     }

        //     if ($errorMsg == null && ($operationType == 'update_inst' || $operationType == 'store_inst')) {

        //         $dividor = 0;
        //         $total_amt = $req->total_amount - $req->discount_amount;

        //         $installmentPacgake = DB::table('pos_inst_packages as pk')
        //             ->where([['pk.is_delete', 0], ['pk.is_active', 1]])
        //             ->where('pk.id', $req->inst_package_id)
        //             ->first();

        //         if ($req->installment_type == 1) {
        //             $dividor = $installmentPacgake->prod_inst_month;
        //         } else {
        //             if (!empty($installmentPacgake->prod_inst_week) && $installmentPacgake->prod_inst_week > 0) {
        //                 $dividor = $installmentPacgake->prod_inst_week;
        //             } else {

        //                 $month = $installmentPacgake->prod_inst_month;

        //                 $date1 = new DateTime($req->sales_date);
        //                 $temp = $date1->format('Y-m-d');

        //                 $temp = new DateTime($temp);
        //                 $endDate = $temp->modify('+' . $month . ' month');
        //                 $endDate = $endDate->format('Y-m-d');
        //                 $date2 = new DateTime($endDate);

        //                 $interval = $date1->diff($date2);
        //                 $days = $interval->format('%a');
        //                 $days = (int) $days;

        //                 $dividor = floor($days / 7);

        //             }

        //         }

        //         $cal_inst_amt = round($total_amt / $dividor);

        //         if ($req->total_quantity <= 0) {
        //             $errorMsg = "Total Quantity must be greater than zero !!";
        //         }

        //         // dd($req->all());

        //         $havetopayAmount = round($cal_inst_amt + $req->vat_amount + $req->service_charge);

        //         if ($req->paid_amount < $havetopayAmount) {
        //             $errorMsg = 'Paid amount must be greater than or equal ' . $havetopayAmount;
        //         }
        //         // if($req->paid_amount < $req->total_payable_amount ){
        //         //     $errorMsg = 'Paid amount must be smaller than or equal '.$req->total_payable_amount;
        //         // }

        //         if ($req->due_amount < 0) {
        //             $errorMsg = "Due amount cannot be less than 0!!";
        //         }

        //     }

        // }

        // if ($operationType == 'store' || $operationType == 'update') {

        //     $rules = array(
        //         'sales_bill_no' => 'required',
        //         'sales_date' => 'required',
        //         'sales_type' => 'required',
        //         'payment_system_id' => 'required',
        //         'employee_id' => 'required',

        //     );

        //     $attributes = array(
        //         'sales_bill_no' => 'Sales Bill',
        //         'sales_date' => 'Sales Date',
        //         'sales_type' => 'Sales Type',
        //         'payment_system_id' => 'Payment System',
        //         'employee_id' => 'Employee',

        //     );

        //     $validator = Validator::make($req->all(), $rules, [], $attributes);

        //     if ($validator->fails()) {
        //         $errorMsg = implode(' || ', $validator->errors()->all());
        //     }

        //     if ($errorMsg == null && $operationType == 'update') {

        //         $sales_date = (new DateTime($req->sales_date))->format('Y-m-d');

        //         if ($Data->sales_date != $sales_date) {
        //             $errorMsg = "Sorry Sales Date did not matched.";
        //         }

        //         if ($Data->sales_bill_no != $req->sales_bill_no) {
        //             $errorMsg = "Sorry Sales Bill number did not matched.";
        //         }

        //         if ($Data->employee_id != $req->employee_id) {
        //             $errorMsg = "Sorry Employee ID did not matched.";
        //         }

        //     }

        //     if ($errorMsg == null && ($operationType == 'update' || $operationType == 'store')) {

        //         if ($req->total_quantity <= 0) {
        //             $errorMsg = "Total Quantity must be greater than zero !!";
        //         }

        //         // dd($req->all());

        //         if ($req->given_amount < $req->total_payable_amount) {
        //             $errorMsg = 'Paid amount must be greater than or equal ' . $req->total_payable_amount;
        //         }

        //     }

        // }

        // if ($errorMsg == null && ($operationType == 'store' || $operationType == 'update' || $operationType == 'store_inst' || $operationType == 'update_inst')) { ## add edit stock check for HO / branch From

        //     $errorMsg = POSS::CheckProductStockinTransaction($req, $req->branch_id, $DataDetails);

        // }

        if ($operationType == 'index') {
            $IgnoreArray = array();

            // if (date('d-m-Y', strtotime($req->sales_date)) != Common::systemCurrentDate($req->branch_id, 'pos')) {
            //     $IgnoreArray = [
            //         'delete', 'edit', 'message' => Common::AccessDeniedReason('date'),
            //     ];

            //     if (in_array($req->sales_bill_no, $Data) == true) {
            //         $IgnoreArray = ['delete', 'edit', 'message' => Common::AccessDeniedReason('hasSalesReturn')];
            //     }
            // }

            $errorMsg = $IgnoreArray;
        }



        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'errorMsg' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request)
    {
        // Common Filter a role load er jonno nicher variable pathano hoche.
        $permitRoleIdArr = Role::childRolesIdsWithParent();

        if ($request->ajax()) {

            $columns = array(
                0 => 'id',
                1 => 'username',
                // 2 => 'pim.bill_no',
                // 3 => 'pim.bill_no',
                // 5 => 'pim.total_quantity',
                // 6 => 'pim.total_amount',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $zoneId = (empty($request->input('zoneId'))) ? null : $request->input('zoneId');
            $regionId = (empty($request->input('regionId'))) ? null : $request->input('regionId');
            $areaId = (empty($request->input('areaId'))) ? null : $request->input('areaId');
            $branchId = (empty($request->input('branchId'))) ? null : $request->input('branchId');

            $employeeId = (empty($request->input('employeeId'))) ? null : $request->input('employeeId');
            $userRoleId = (empty($request->input('user_role_id'))) ? null : $request->input('user_role_id');

            // $userStatus = (empty($request->input('userStatus'))) ? 'all' : $request->input('userStatus');
            $userStatus = $request->input('userStatus');

            $roleId = (empty($request->input('roleId'))) ? null : $request->input('roleId');

            if (!empty($roleId)) {
                $permitRoleIdArr = [$roleId];
            }

            // dd($userStatus);

            ## Permission & Zone Wise Branch
            $selBranchArr = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $masterQuery = DB::table('gnl_sys_users as gsu')
                ->where('is_delete', 0)
                // ->whereIn('sys_user_role_id', $permitRoleIdArr)
                // ->where('id', Auth::id())
                ->whereIn('branch_id', $selBranchArr)
                ->where(function ($masterQuery) use ($permitRoleIdArr) {
                    ## ai serial ei where condition bosate hobe noyto data vul asbe.
                    $masterQuery->whereIn('id', [Auth::id()])
                        ->orWhereIn('sys_user_role_id', $permitRoleIdArr)
                        ->whereNotIn('sys_user_role_id', [Auth::user()->sys_user_role_id]);
                })
                ->where(function ($masterQuery) use ($search, $employeeId, $userStatus, $userRoleId) {
                    if (!empty($search)) {
                        $masterQuery->where('full_name', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('contact_no', 'LIKE', "%{$search}%");
                    }

                    if (!empty($employeeId)) {
                        if (Common::getDBConnection() == "sqlite") {
                            $masterQuery->where('employee_no', $employeeId);
                        } else {
                            $masterQuery->where('emp_id', $employeeId);
                        }
                    }

                    if ($userStatus == '1' || $userStatus == '0') {
                        $masterQuery->where('is_active', $userStatus);
                    }

                    if (!empty($userRoleId)) {
                        $masterQuery->where('sys_user_role_id', $userRoleId);
                    }
                })
                ->orderBy($order, $dir)
                ->orderBy('branch_id', 'ASC')
                ->orderBy('sys_user_role_id', 'ASC')
                ->orderBy('id', 'DESC');


            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalData = DB::table('gnl_sys_users')
                ->where('is_delete', 0)
                ->whereIn('sys_user_role_id', $permitRoleIdArr)
                // ->whereIn('branch_id',$selBranchArr)
                ->count();

            $totalFiltered = $totalData;

            ## Branch Data After Query Result
            $branchArr = (!empty($masterQuery)) ? array_values($masterQuery->pluck('branch_id')->unique()->all()) : array();
            $branchData = Common::fnForBranchData($branchArr);

            if (Common::getDBConnection() == "sqlite") {
                $employeeArr = (!empty($masterQuery)) ? array_values($masterQuery->pluck('employee_no')->unique()->all()) : array();
            } else {
                $employeeArr = (!empty($masterQuery)) ? array_values($masterQuery->pluck('emp_id')->unique()->all()) : array();
            }

            $employeeData = Common::fnForEmployeeData($employeeArr);

            $roleInfoData = Common::fnForRoleInfo($permitRoleIdArr);

            ## $search, $SDate, $EDate,$branchId, $Type
            if (
                !empty($search)
                || !empty($zoneId)  || !empty($regionId) || !empty($areaId) || !empty($branchId)
                || !empty($employeeId) || !empty($userRoleId) || $userStatus == 1 || $userStatus == 0
            ) {
                $totalFiltered = $tempQueryData->count();
            }

            $dataSet = array();
            $i = $start;

            $authorized = '<span style="color: Dodgerblue;"><i class="fas fa-check"></i> &nbsp Active</span>';
            $unauth     = '<span style="color: red;"><i class="far fa-times-circle"></i> &nbsp In-Active</span>';

            foreach ($masterQuery as $row) {

                $TempSet = array();
                $IgnoreArray = array();

                $passport = $this->getPassport($row, $operationType = 'index');
                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['errorMsg'];
                }


                $user_role = (isset($roleInfoData[$row->sys_user_role_id])) ? $roleInfoData[$row->sys_user_role_id] : "";

                if (Common::getDBConnection() == "sqlite") {
                    $employee_info = (isset($employeeData[$row->employee_no])) ? $employeeData[$row->employee_no] : "";
                } else {
                    $employee_info = (isset($employeeData[$row->emp_id])) ? $employeeData[$row->emp_id] : "";
                }

                $branch_info = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
                $other_info = "<strong>Name:</strong> " . $row->full_name;
                $other_info .= "<br><strong>Mobile:</strong> " . $row->contact_no;
                $other_info .= "<br><strong>Email:</strong> " . $row->email;

                $TempSet = [
                    'id' => ++$i,
                    'username' => $row->username,
                    'user_role' => "<b>" . $user_role . "</b>",
                    'employee_info' => $employee_info,
                    'branch_info' => $branch_info,
                    'other_info' => $other_info,
                    'user_status' => (($row->is_active == 0) ? $unauth : $authorized),
                    'action' => Role::roleWiseArray($this->GlobalRole, $row->id, $IgnoreArray, $row->is_active),
                ];

                $dataSet[] = $TempSet;
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $dataSet,
            );

            echo json_encode($json_data);
        } else {
            return view('GNL.SysUser.index', compact('permitRoleIdArr'));
        }
    }

    public function back_role_index()
    {
        $userInfo = Auth::user();
        $userID   = $userInfo->id;
        $roleID   = $userInfo->sys_user_role_id;

        $UserParent = DB::table("gnl_sys_user_roles")->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $roleID)
            ->select('parent_id')
            ->first();

        $ParentId = $UserParent->parent_id;

        /////// Same Parent Data Role Wise
        $user_role = DB::table("gnl_sys_user_roles")->where([['is_delete', 0], ['is_active', 1]])
            ->where(function ($user_role) use ($roleID, $ParentId) {
                $user_role->where('parent_id', $ParentId);

                if (!empty($ParentId)) {
                    $user_role->where('id', $roleID);
                }
            })
            ->orderBy('id', 'ASC')
            ->get();

        return view('GNL.SysUser.back_role_index', compact('user_role'));
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {

            $request->validate([
                'sys_user_role_id' => 'required',
                'full_name'        => 'required',
                'username'         => 'required',
                'password'         => 'required',
                // 'user_image' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:500',
                // 'signature_image' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:500',
            ]);

            ## ## Check File validation
            $fileInfo    = Common::upload_validation($_FILES['user_image'], 1, 'image');
            // $fileInfoSig = Common::upload_validation($_FILES['signature_image'], 1, 'image');

            $data             = $request->all();
            $data['password'] = Hash::make($data['password']);
            // $data['user_image'] = null;
            // $data['signature_image'] = null;
            $isCreate = SysUser::create($data);

            $SuccessFlag = false;

            if ($isCreate) {

                $SuccessFlag     = true;
                $lastInsertQuery = SysUser::latest()->first();

                $tableName = $lastInsertQuery->getTable();
                $pid       = $lastInsertQuery->id;

                if (!empty($request->file('user_image'))) {

                    $uploadFile = $request->file('user_image');
                    // $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                    // $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                    ## ## File Upload Function
                    $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                    $lastInsertQuery->user_image = $upload;
                }

                // if (!empty($request->file('signature_image'))) {

                //     $uploadFile = $request->file('signature_image');
                //     // $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                //     // $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                //     ## ## File Upload Function
                //     $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                //     $lastInsertQuery->signature_image = $upload;
                // }

                $isSuccess = $lastInsertQuery->update();
                if ($isSuccess) {
                    $SuccessFlag = true;
                } else {
                    $SuccessFlag = false;
                }
            }

            if ($SuccessFlag) {
                $notification = array(
                    'message'    => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/sys_user')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Insert',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $roleID = Common::getRoleId();

            $user_role = DB::table('gnl_sys_user_roles')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($query) use ($roleID) {
                    if (Common::isSuperUser() == false) {
                        $query->where('id', $roleID);
                    } else {
                        $query->where('id', $roleID);

                        $UserParent = DB::table('gnl_sys_user_roles')->where([['is_delete', 0], ['is_active', 1], ['id', $roleID]])->first('parent_id');
                        if (!empty($UserParent)) {
                            $query->orWhere('parent_id', $UserParent->parent_id);
                        }
                    }
                })
                ->orderBy('order_by', 'ASC')
                ->get();

            $childIds = array();
            foreach ($user_role as $UserRole) {
                $childIds[] = $UserRole->id;
                $childIds = array_merge($childIds, Role::childRolesIds($UserRole->id));
            }

            $user_roles = DB::table('gnl_sys_user_roles')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $childIds)
                ->orderBy('order_by', 'ASC')
                ->get();

            $EmployeeData = DB::table('hr_employees')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function($query) {
                    if (Common::isSuperUser() == true || Common::isDeveloperUser() == true || Common::isHeadOffice() == true) {

                    }
                    else {
                        $selBranchArr = Common::getBranchIdsForAllSection();
                        $query->whereIn('branch_id', $selBranchArr);
                    }
                })
                ->orderBy('emp_code', 'ASC')
                ->get();

            return view('GNL.SysUser.add', compact('user_roles', 'EmployeeData', 'roleID'));
        }
    }

    public function edit(Request $request, $id = null)
    {
        $suser = SysUser::where('id', $id)->first();

        $flag = Role::checkDataPrmissionForRoleWise($suser->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $suser->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        $tableName = $suser->getTable();
        $pid       = $id;

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'sys_user_role_id' => 'required',
                'full_name'        => 'required',
                // 'user_image' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:500',
                // 'signature_image' => 'mimes:jpeg,jpg,png,JPEG,JPG,PNG | max:100',
            ]);

            ## ## Check File validation
            $fileInfo    = Common::upload_validation($_FILES['user_image'], 1, 'image');
            // $fileInfoSig = Common::upload_validation($_FILES['signature_image'], 1, 'image');

            $data = $request->all();

            if (!empty($request->file('user_image'))) {

                $uploadFile = $request->file('user_image');
                // $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                // $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                ## ## File Upload Function
                $upload             = Common::fileUpload($uploadFile, $tableName, $pid);
                $data['user_image'] = $upload;
            }

            // if (!empty($request->file('signature_image'))) {

            //     $uploadFile = $request->file('signature_image');
            //     // $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
            //     // $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

            //     ## ## File Upload Function
            //     $upload                  = Common::fileUpload($uploadFile, $tableName, $pid);
            //     $data['signature_image'] = $upload;
            // }

            $isUpdate = $suser->update($data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('gnl/sys_user')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {

            $roleID = Common::getRoleId();

            $user_role = DB::table('gnl_sys_user_roles')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($query) use ($roleID) {
                    if (Common::isSuperUser() == false) {
                        $query->where('id', $roleID);
                    } else {
                        $query->where('id', $roleID);

                        $UserParent = DB::table('gnl_sys_user_roles')->where([['is_delete', 0], ['is_active', 1], ['id', $roleID]])->first('parent_id');
                        if (!empty($UserParent)) {
                            $query->orWhere('parent_id', $UserParent->parent_id);
                        }
                    }
                })
                ->orderBy('order_by', 'ASC')
                ->get();

            $childIds = array();
            foreach ($user_role as $UserRole) {
                $childIds[] = $UserRole->id;
                $childIds = array_merge($childIds, Role::childRolesIds($UserRole->id));
            }

            $user_roles = DB::table('gnl_sys_user_roles')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $childIds)
                ->orderBy('order_by', 'ASC')
                ->get();

            $EmployeeData = DB::table('hr_employees')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function($query) {
                    if (Common::isSuperUser() == true || Common::isDeveloperUser() == true || Common::isHeadOffice() == true) {

                    }
                    else {
                        $selBranchArr = Common::getBranchIdsForAllSection();
                        $query->whereIn('branch_id', $selBranchArr);
                    }
                })
                ->orderBy('emp_code', 'ASC')
                ->get();

            return view('GNL.SysUser.edit', compact('suser', 'user_roles', 'EmployeeData', 'roleID'));
        }
    }

    public function delete($id = null)
    {
        $suser = SysUser::where('id', $id)->first();
        $flag  = Role::checkDataPrmissionForRoleWise($suser->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $suser->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        if ($suser->is_delete == 0) {

            $suser->is_delete = 1;
            $isSuccess        = $suser->update();

            if ($isSuccess) {
                $notification = array(
                    'message'    => 'Successfully Deleted',
                    'alert-type' => 'success',
                );
                return redirect()->back()->with($notification);
            }
        }
    }

    public function destroy($id = null)
    {
        $suser = SysUser::where('id', $id)->first();
        $flag  = Role::checkDataPrmissionForRoleWise($suser->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $suser->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        // $suser = SysUser::where('id', $id)->delete();
        $suser = SysUser::where('id', $id)->get()->each->delete();

        if ($suser) {
            $notification = array(
                'message'    => 'Successfully Destory',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function view($id = null)
    {
        $suser = SysUser::where('id', $id)->first();
        $flag  = Role::checkDataPrmissionForRoleWise($suser->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $suser->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        $suser = SysUser::findOrFail($id = null);

        $userRole = DB::table('gnl_sys_user_roles')->get();

        $EmployeeData = DB::table('hr_employees')->where([['is_delete', 0], ['is_active', 1]])->orderBy('emp_code', 'ASC')->get();
        return view('GNL.SysUser.view', compact('suser', 'EmployeeData', 'userRole'));
    }

    public function isActive($id = null)
    {
        $suser = SysUser::where('id', $id)->first();
        $flag  = Role::checkDataPrmissionForRoleWise($suser->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $suser->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        if ($suser->is_active == 1) {
            $suser->is_active = 0;
        } else {
            $suser->is_active = 1;
        }

        $suser->update();
        $notification = array(
            'message'    => 'user activation is changed',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    }

    public function changePassword(Request $request, $id = null)
    {
        $change = SysUser::where('id', $id)->first();

        $flag = Role::checkDataPrmissionForRoleWise($change->sys_user_role_id);

        if ($flag == false && Role::getRoleId() != $change->sys_user_role_id) {
            $notification = array(
                'message'    => 'You are not authoriised this section',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'password'      => 'required | same:conf_password',
                'conf_password' => 'required',
            ]);
            $data     = Hash::make($request->password);
            $isUpdate = $change->update(['password' => $data]);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );
                return redirect('gnl/sys_user')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        }

        return view('GNL.SysUser.change_pass', compact('id'));
    }
}
