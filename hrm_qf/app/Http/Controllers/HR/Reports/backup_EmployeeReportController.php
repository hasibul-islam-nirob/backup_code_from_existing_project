<?php
namespace App\Http\Controllers\HR\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Applications\EmployeeLeaveController;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;

class Backup_EmployeeReportController extends Controller{

    public function getEmployeeReport(){
        return view('HR.Reports.StuffReports.EmployeeReports.employee');
    }

    public function loadEmployeeReport(Request $request){

        /* dd($request->all()); */

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;

        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId);

        $allData = Employee::from('hr_employees as emp')

                ->where('emp.is_delete', 0)

                ->when(true, function ($query) use ($request, $selBranchArr) {

                    /* if (!empty($searchValue)) {

                        $query->where('emp_code', 'like', '%' . $searchValue . '%');

                    } */

                    if (!empty($selBranchArr)) {

                        $query->whereIn('emp.branch_id', $selBranchArr);

                    }

                    if (!empty($request->designation_id)) {

                        $query->where('emp.designation_id', $request->designation_id);

                    }
                    if (!empty($request->emp_marital_status) || !empty($request->emp_religion)) {

                        $query->join('hr_emp_personal_details as pd', function($join){
                            $join->on('emp.id', '=', 'pd.emp_id');
                        });

                    }
                    
                    if (!empty($request->gender)) {

                        $query->where('emp.gender', $request->gender);

                    }
                    if (!empty($request->emp_marital_status)) {

                        $query->where('pd.marital_status', $request->emp_marital_status);

                    }
                    if (!empty($request->emp_religion)) {

                        $query->where('pd.religion', $request->emp_religion);

                    }

                    if (!empty($request->department_id)) {

                        $query->where('emp.department_id', $request->department_id);

                    }

                    if (!empty($request->emp_code)) {

                        $query->where('emp.emp_code', 'LIKE', "%{$request->emp_code}%");

                    }

                    if ($request->emp_status == "0" || !empty($request->emp_status)) {

                        $query->where('emp.status', $request->emp_status);

                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {

                        $query->whereBetween('emp.join_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);

                    } elseif (!empty($request->start_date)) {

                        $query->where('emp.join_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));

                    } elseif (!empty($request->end_date)) {

                        $query->where('emp.join_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));

                    }
                })

                ->select('emp.*')->get();

            $data = array();
            $sno  = 1;

            foreach ($allData as $key => $row) {

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

                // dd($row);

                $data[$key]['id']           = $sno;
                $data[$key]['username']     = $row->sys_user["username"];
                $data[$key]['emp_name']     = $row->emp_name . ' [' . $row->emp_code . ']';
                $data[$key]['gender']       = $row->gender;
                $data[$key]['marital_status']     = $row->personalData['marital_status'];
                $data[$key]['blood_group']     = $row->personalData['blood_group'];
                $data[$key]['religion']     = $row->personalData['religion'];
                
                $data[$key]['phone_number'] = $row->personalData['mobile_no'];
                $data[$key]['branch']       = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['designation']  = $row->designation['name'];
                $data[$key]['department']   = $row->department['dept_name'];
                $data[$key]['join_date']    = $row->join_date;
                $data[$key]['status']       = $status;

                $sno++;

            }
        return view('HR.Reports.StuffReports.EmployeeReports.employee_body', compact('data'));
    }
}