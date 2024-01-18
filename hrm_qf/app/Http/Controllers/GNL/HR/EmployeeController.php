<?php

namespace App\Http\Controllers\GNL\HR;

use DateTime;
use Exception;
use App\Model\GNL\SysUser;
use Illuminate\Http\Request;
use App\Model\GNL\HR\Employee;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use App\Model\GNL\HR\EmployeePersonalDetails;

class EmployeeController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Employee
    public function index(Request $request)
    {

        if ($request->ajax()) {
            // Ordering Variable
            $columns = array(
                // 0 => 'emp.id',
                1 => 'emp.emp_name',
                2 => 'emp.emp_code',
                // 3 => 'emp.mobile_no',
                // 4 => 'emp.emp_email',
                4 => 'emp.department_id',
                5 => 'emp.department_id',
                6 => 'emp.branch_id',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');
            $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId   = (empty($request->region_id)) ? null : $request->region_id;
            $areaId   = (empty($request->area_id)) ? null : $request->area_id;
            $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

            $selBranchArr = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            // $BranchID = (empty($request->input('BranchID'))) ? null : $request->input('BranchID');
            // $EmpName = (empty($request->input('EmpName'))) ? null : $request->input('EmpName');
            // $Empcode = (empty($request->input('Empcode'))) ? null : $request->input('Empcode');
            // Query


            $EmployeeData = Employee::from('hr_employees as emp')
                ->where('emp.is_delete', 0)
                ->join('hr_emp_personal_details as empd', 'empd.emp_id', '=', 'emp.id')
                ->where(function ($query) use ($request, $search, $selBranchArr) {

                    if (!empty($search)) {

                        $query->where('emp_code', 'like', '%' . $search . '%');
                        $query->orWhere('emp_name', 'like', '%' . $search . '%');
                        $query->orWhere('status', 'like', '%' . $search . '%');
                        $query->orWhere('gender', 'like', '%' . $search . '%');
                        $query->orWhere('org_mobile', 'like', '%' . $search . '%');
                        $query->orWhere('org_email', 'like', '%' . $search . '%');
                    }

                    if (!empty($selBranchArr)) {

                        $query->whereIn('emp.branch_id', $selBranchArr);
                    }

                    if (!empty($request->designation_id)) {

                        $query->where('emp.designation_id', $request->designation_id);
                    }

                    if (!empty($request->department_id)) {

                        $query->where('emp.department_id', $request->department_id);
                    }

                    if (!empty($request->emp_gender)) {

                        $query->where('emp.gender', $request->emp_gender);
                    }

                    if (!empty($request->emp_code)) {

                        $query->where('emp.emp_code', 'LIKE', "%{$request->emp_code}%");
                    }

                    if ($request->emp_status == "0" || !empty($request->emp_status)) {

                        $query->where('emp.status', $request->emp_status);
                    }

                    // if (!empty($request->start_date) && !empty($request->end_date)) {

                    //     $query->whereBetween('emp.join_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    // } elseif (!empty($request->start_date)) {

                    //     $query->where('emp.join_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    // } elseif (!empty($request->end_date)) {

                    //     $query->where('emp.join_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    // }
                })
                ->select('emp.*','empd.mobile_no as personal_mobile_no')
                // ->offset($start)
                // ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('emp.emp_code', 'ASC');
            // ->get();

            $tempQueryData = clone $EmployeeData;
            $EmployeeData = $EmployeeData->offset($start)->limit($limit)->get();

            $totalData = Employee::where([['is_delete', 0]])
                ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
                ->count();
            $totalFiltered = $totalData;

            if (
                !empty($search)
                || !empty($request->branch_id)
                || !empty($request->start_date)
                || !empty($request->end_date)
                || !empty($request->zone_id)
                || !empty($request->region_id)
                || !empty($request->area_id)
                || !empty($request->designation_id)
                || !empty($request->department_id)
                || !empty($request->emp_gender)
                || !empty($request->emp_code)
                || !empty($request->emp_status)
            ) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;

            foreach ($EmployeeData as $row) {
                $TempSet = array();
                
                $status = "";

                if ($row->status == 1) {
                    $status = '<span class="text-primary">Present</span>';
                } elseif ($row->status == 2) {
                    $status = '<span class="text-danger">Resigned</span>';
                } elseif ($row->status == 3) {
                    $status = '<span class="text-danger">Dismissed</span>';
                } elseif ($row->status == 4) {
                    $status = '<span class="text-danger">Terminated</span>';
                } elseif ($row->status == 5) {
                    $status = '<span class="text-danger">Retired</span>';
                }

                $TempSet = [
                    'id' => ++$i,
                    'emp_name' => $row->emp_name,
                    'emp_code' => $row->emp_code,
                    'emp_gender' => $row->gender,
                    'org_phone_number' => $row->org_mobile,
                    'personal_mobile_no' => $row->personal_mobile_no,

                    'designation' => $row->designation['name'],
                    'department' => $row->department['dept_name'],
                    'username' => (!empty($row->User['username'])) ? $row->User['username'] : '',
                    'branch' => (!empty($row->branch['branch_name'])) ? $row->branch['branch_name'] . "(" . $row->branch['branch_code'] . ")" : "",
                    // 'comapny_name' => (!empty($row->company['comp_name'])) ? $row->company['comp_name'] : '',

                    'status' => $status,
                    'action' => Role::roleWiseArray($this->GlobalRole, $row->id, [])
                ];

                $DataSet[] = $TempSet;
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            );

            echo json_encode($json_data);
        } else {
            $EmployeeData = Employee::where('is_delete', 0)
                // ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
                ->orderBy('emp_code', 'ASC')->get();

            return view('GNL.HR.Employee.index', compact('EmployeeData'));
        }
    }

    ## Add and Store Employee
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'emp_name' => 'required',
                'username' => 'required',
                'password' => 'required',
                'mobile_no' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
            ]);

            $RequestData = $request->all();
            $RequestDetails = array();

            $RequestData['status'] = 1;

            if (!empty($RequestData['dob'])) {
                $RequestDetails['dob'] = new DateTime($RequestData['dob']);
                $RequestDetails['dob'] = $RequestDetails['dob']->format('Y-m-d');
            }

            $RequestDetails['father_name_en'] = $RequestData['father_name_en'];
            $RequestDetails['mother_name_en'] = $RequestData['mother_name_en'];
            $RequestDetails['email'] = $RequestData['email'];
            $RequestDetails['mobile_no'] = $RequestData['mobile_no'];
            $RequestDetails['nid_no'] = $RequestData['nid_no'];
            // $RequestDetails['gender'] = $RequestData['gender'];
            $RequestDetails['pre_addr_street'] = $RequestData['pre_addr_street'];
            $RequestDetails['par_addr_street'] = $RequestData['par_addr_street'];
            $RequestDetails['nid_no'] = $RequestData['nid_no'];
            $RequestDetails['passport_no'] = $RequestData['passport_no'];
            $RequestDetails['birth_certificate_no'] = $RequestData['birth_certificate_no'];
            $RequestDetails['driving_license_no'] = $RequestData['driving_license_no'];


            $RequestData['employee_no'] = Common::generateEmployeeNo($RequestData['branch_id']);


            DB::beginTransaction();
            try {

                ## Master
                $isInsertM = Employee::create($RequestData);
                $isInsertD = '';

                if ($isInsertM) {

                    $employee = Employee::where('employee_no', $RequestData['employee_no'])->orderBy('id', 'desc')->first();

                    $RequestDetails['emp_id'] = $employee->id;
                    $isInsertD = EmployeePersonalDetails::create($RequestDetails);
                }

                if ($isInsertD) {

                    $userData = array(
                        'sys_user_role_id' => 3,
                        'full_name' => $RequestData['emp_name'],
                        'username' => $RequestData['username'],
                        'password' => Hash::make($RequestData['password']),
                        'email' => $RequestData['email'],
                        'contact_no' => $RequestData['mobile_no'],
                        'branch_id' => $RequestData['branch_id'],
                        'company_id' => $RequestData['company_id'],
                        'employee_no' => $RequestData['employee_no'],
                        'emp_id' => $employee->id
                    );

                    $isInsertUser = SysUser::create($userData);
                    $successFlag = false;

                    if ($isInsertUser) {
                        $lastInsertQuery = SysUser::latest()->first();
                        $pid = $lastInsertQuery->id;

                        $isSuccess =  Employee::where('employee_no', $RequestData['employee_no'])
                            ->update(['user_id' => $pid]);

                        if ($isSuccess) {
                            $successFlag = true;
                        } else {
                            $successFlag = false;
                            $message = "Unsuccessful update in Employee.";
                        }
                    } else {
                        $successFlag = false;
                        $message = "Unsuccessful to insert data in User.";
                    }

                    DB::commit();

                    if ($successFlag) {
                        $notification = array(
                            'message' => 'Successfully Inserted New Employee Data',
                            'alert-type' => 'success',
                        );
                        return Redirect::to('gnl/employee')->with($notification);
                    } else {
                        $notification = array(
                            'message' => $message,
                            'alert-type' => 'error',
                        );
                        return redirect()->back()->with($notification);
                    }
                }
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Employee',
                    'alert-type' => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );
                return redirect()->back()->with($notification);
                //return $e;
            }
        } else {
            return view('GNL.HR.Employee.add');
        }
    }

    // Edit Employee
    public function edit(Request $request, $id = null)
    {
        $EmployeeData = Employee::where('id', $id)->first();
        $empPersonalDetails = EmployeePersonalDetails::where('emp_id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'emp_name' => 'required',
                'mobile_no' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
            ]);

            $Data = $request->all();
            $RequestDetails = array();

            if (!empty($Data['dob'])) {
                $Data['dob'] = new DateTime($Data['dob']);
                $Data['dob'] = $Data['dob']->format('Y-m-d');
            }

            $RequestDetails['father_name_en'] = $Data['father_name_en'];
            $RequestDetails['mother_name_en'] = $Data['mother_name_en'];
            $RequestDetails['email'] = $Data['email'];
            $RequestDetails['mobile_no'] = $Data['mobile_no'];
            $RequestDetails['nid_no'] = $Data['nid_no'];
            // $RequestDetails['gender'] = $Data['gender'];
            $RequestDetails['pre_addr_street'] = $Data['pre_addr_street'];
            $RequestDetails['par_addr_street'] = $Data['par_addr_street'];
            $RequestDetails['nid_no'] = $Data['nid_no'];
            $RequestDetails['passport_no'] = $Data['passport_no'];
            $RequestDetails['birth_certificate_no'] = $Data['birth_certificate_no'];
            $RequestDetails['driving_license_no'] = $Data['driving_license_no'];


            DB::beginTransaction();
            try {

                ## Master
                $isUpdateM = $EmployeeData->update($Data);
                $isUpdateD = '';

                if ($isUpdateM) {

                    // $employee = EmployeePersonalDetails::where('emp_id',$Data['id'])->first();
                    $isUpdateD = $empPersonalDetails->update($RequestDetails);
                }

                if ($isUpdateD) {

                    DB::commit();

                    $notification = array(
                        'message' => 'Successfully Updated Employee Data',
                        'alert-type' => 'success',
                    );
                    return Redirect::to('gnl/employee')->with($notification);
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to Update data in Employee',
                        'alert-type' => 'error',
                    );
                    return redirect()->back()->with($notification);
                }
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Employee',
                    'alert-type' => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );
                return redirect()->back()->with($notification);
                //return $e;
            }
        } else {
            return view('GNL.HR.Employee.edit', compact('EmployeeData', 'empPersonalDetails'));
        }
    }

    //View Employee
    public function view($id = null)
    {
        $EmployeeData = Employee::where('id', $id)->first();
        $empPersonalDetails = EmployeePersonalDetails::where('emp_id', $id)->first();
        return view('GNL.HR.Employee.view', compact('EmployeeData', 'empPersonalDetails'));
    }

    // Soft Delete Employee
    public function delete($id = null)
    {
        $EmployeeData = Employee::where('id', $id)->first();
        $EmployeeData->is_delete = 1;

        $delete = $EmployeeData->save();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // Parmanent Delete Employee
    // public function destroy($id = null)
    // {
    //     $EmployeeData = Employee::where('id', $id)->first();
    //     $delete = $EmployeeData->delete();

    //     if ($delete) {
    //         $notification = array(
    //             'message' => 'Successfully Deleted',
    //             'alert-type' => 'success',
    //         );
    //         return redirect()->back()->with($notification);
    //     } else {
    //         $notification = array(
    //             'message' => 'Unsuccessful to Delete',
    //             'alert-type' => 'error',
    //         );
    //         return redirect()->back()->with($notification);
    //     }
    // }

    // Publish/Unpublish Employee
    public function isActive($id = null)
    {
        $EmployeeData = Employee::where('id', $id)->first();

        if ($EmployeeData->is_active == 1) {
            $EmployeeData->is_active = 0;
        } else {
            $EmployeeData->is_active = 1;
        }

        $Status = $EmployeeData->save();
        if ($Status) {
            $notification = array(
                'message' => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }
}
