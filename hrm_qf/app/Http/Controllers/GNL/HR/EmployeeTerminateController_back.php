<?php

namespace App\Http\Controllers\GNL\HR;

use App\Http\Controllers\Controller;

use App\Model\GNL\SysUser;
use App\Model\GNL\HR\Employee;
use App\Model\GNL\HR\EmployeeTerminate;

use App\Services\CommonService as Common;

use App\Services\AccService as ACCS;
use App\Services\BillService as BILLS;
use App\Services\FamService as FAMS;
use App\Services\GnlService as GNLS;
use App\Services\HrService as HRS;
use App\Services\InvService as INVS;
use App\Services\MfnService as MFNS;
use App\Services\PosService as POSS;


use App\Services\RoleService as Role;

use Illuminate\Support\Facades\Auth;
use Datetime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class back_EmployeeTerminateController extends Controller
{

    /**
     * Terminate & transfer HR a implement kora hocche emp_id dhore, gnl->HR a o serokom change hobe
     */

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $req)
    {
        $accessAbleBranchIds = HRS::getUserAccesableBranchIds();

        if (!$req->ajax()) {
            $branchces = DB::table('gnl_branchs')
                ->where([
                    ['is_delete', 0],
                    ['id', '>', 1],
                ])
                ->whereIn('id', $accessAbleBranchIds)
                ->orderBy('branch_code')
                ->select(DB::raw("id, CONCAT(branch_code, ' - ', branch_name) AS name"))
                ->get();

            $data = array(
                'branchces' => $branchces,
            );

            return view('GNL.HR.EmployeeTerminate.index', $data);
        }

        $columns = [
            'he.emp_code',
            'he.emp_name',
            'gb.branch_name',
            'het.terminate_date',
            'status',
            'action',
        ];

        $limit = $req->length;
        $orderColumnIndex = (int) $req->input('order.0.column') <= 1 ? 0 : (int) $req->input('order.0.column') - 1;
        $order = $columns[$orderColumnIndex];
        $dir = $req->input('order.0.dir');

        // Searching variable
        $search = (empty($req->input('search.value'))) ? null : $req->input('search.value');

        $employeeTerminates = DB::table('hr_employee_terminate as het')
            ->where('het.is_delete', 0)
            ->where('het.is_active', 1)
            ->select('het.id',
                'het.emp_id',
                'he.emp_name',
                'gb.branch_name',
                'gb.branch_code',
                'het.terminate_date',
                'het.is_approved',
                'he.emp_code')
            ->leftjoin('hr_employees as he', 'het.emp_id', 'he.id')
            ->leftjoin('gnl_branchs as gb', 'het.branch_id', 'gb.id')
            ->where(function ($query) use ($search) {
                $query
                    // ->where('het.employee_no', 'LIKE', "%{$search}%")
                // ->orWhere('gbfrom.branch_name', 'LIKE', "%{$search}%")
                    ->where('he.emp_name', 'LIKE', "%{$search}%")
                    ->orWhere('he.emp_code', 'LIKE', "%{$search}%")
                    ->orWhere('gb.branch_name', 'LIKE', "%{$search}%");
            })
            ->orderBy('het.id', 'DESC')
            ->orderBy($order, $dir)
            ->limit($limit)
            ->offset($req->start)
            ->get();

        $totalData = $employeeTerminates->count();
        $sl = (int) $req->start + 1;

        foreach ($employeeTerminates as $key => $row) {

            $status = '<span class="text-primary">Approved</span>';
            if ($row->is_approved == 0) {
                $status = '<a type="button" class="btn btn-danger btn-sm" href="javascript:void(0)" onClick="fnApprove(' . $row->id . ')"><i class="fad fa-info-circle"></i> Approve</a>';
            }

            $IgnoreArray = array();

            if ($row->is_approved == 1) {
                // dd($row->is_approved);
                $IgnoreArray = ['edit', 'delete'];
            }

            $employeeTerminates[$key]->sl = $sl++;
            $employeeTerminates[$key]->employeeName = $row->emp_name . " (" . $row->emp_code . ")";
            $employeeTerminates[$key]->employeeNo = $row->emp_id;
            $employeeTerminates[$key]->branchName = $row->branch_name . " (" . $row->branch_code . ")";
            $employeeTerminates[$key]->terminateDate = (new Datetime($row->terminate_date))->format('d-m-Y');
            $employeeTerminates[$key]->status = $status;
            // $employeeTerminates[$key]->action       = encrypt($row->id);
            $employeeTerminates[$key]->action = Role::roleWiseArray($this->GlobalRole, $row->id, $IgnoreArray);
        }

        $data = array(
            "draw" => intval($req->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            'data' => $employeeTerminates,
        );

        return response()->json($data);
    }

    public function add(Request $req)
    {
        if ($req->isMethod('post')) {
            return $this->store($req);
        }

        $sysDate = Common::systemCurrentDate(Common::getBranchId());
        $companyId = Common::getCompanyId();

        $employees = DB::table('hr_employees')
            ->where([['is_delete', 0], ['is_active', 1],['branch_id',1]])
            ->select('id','employee_no', 'emp_code', 'emp_name')
            ->get();

        $data = array(
            'employees' => $employees,
            'sysDate' => (new Datetime($sysDate))->format('d-m-Y'),
            'companyId' => $companyId,
        );

        return view('GNL.HR.EmployeeTerminate.add', $data);
    }

    public function store(Request $req)
    {
        $passport = $this->getValidationPass($req, $operationType = 'store');
        if ($passport['isValid'] == false) {
            $notification = array(
                'message' => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        if (!empty($req->terminate_date)) {
            $req['terminate_date'] = (new Datetime($req->terminate_date))->format('Y-m-d');
        }

        // $req['terminate_date'] = Carbon::parse($req->terminate_date)->format('Y-m-d');
        $req['created_at'] = now();
        $req['created_by'] = Auth::user()->id;

        

        $isCreate = EmployeeTerminate::create($req->all());

        if ($isCreate) {
            $notification = array(
                'message' => 'Successfully Inserted',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message' => 'Something went wrong',
            );
        }

        return response()->json($notification);
    }

    public function edit(Request $req)
    {
        if ($req->isMethod('post')) {
            return $this->update($req);
        }

        $sysDate = Common::systemCurrentDate(Common::getBranchId());
        $companyId = Common::getCompanyId();

        $employees = DB::table('hr_employees')
            ->where('is_delete', 0)
            ->where('is_active', 1)
        // ->select('employee_no', DB::raw('CONCAT(emp_code, " - ", emp_name) AS employee'))
            ->select('id','employee_no', 'emp_code', 'emp_name')
            ->get();

        $employeeTerminates = EmployeeTerminate::find($req->id);

        $data = array(
            'employees' => $employees,
            'sysDate' => $sysDate,
            'companyId' => $companyId,
            'emp_id' => $employeeTerminates->emp_id,
            'branch_id' => $employeeTerminates->branch_id,
            'terminate_date' => (new Datetime($employeeTerminates->terminate_date))->format('d-m-Y'),
        );

        return view('GNL.HR.EmployeeTerminate.edit', $data);
    }

    public function update(Request $req)
    {
        $employeeTerminates = EmployeeTerminate::find($req->id);
        $passport = $this->getValidationPass($req, $operationType = 'update', $employeeTerminates);
        if ($passport['isValid'] == false) {
            $notification = array(
                'message' => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        $req['terminate_date'] = (new Datetime($req->terminate_date))->format('Y-m-d');
        $req['is_approved'] = 0;
        $req['updated_at'] = now();
        $req['updated_by'] = Auth::user()->id;
        $isUpdated = $employeeTerminates->update($req->all());

        if ($isUpdated) {
            $notification = array(
                'message' => 'Successfully Updated.',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message' => 'Something went wrong',
            );
        }

        return response()->json($notification);
    }

    public function delete(Request $req)
    {
        $employeeTerminates = EmployeeTerminate::find($req->id);
        $passport = $this->getValidationPass($req, $operationType = 'delete', $employeeTerminates);
        if ($passport['isValid'] == false) {
            $notification = array(
                'message' => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }

        $isDeleted = $employeeTerminates->update(['is_delete' => 1]);

        if ($isDeleted) {
            $notification = array(
                'message' => 'Successfully Deleted.',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message' => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );
        }

        return redirect()->back()->with($notification);
    }

    public function view($id)
    {
        $employeeTerminates = DB::table('hr_employee_terminate as het')
            ->where('het.id', $id)
            ->select('het.id', 'het.emp_id', 'he.emp_name', 'he.emp_code', 'gb.branch_name', 'gb.branch_code',
                'het.terminate_date', 'het.is_approved', 'gsuCr.full_name as created_by',
                'sh.emp_name as approve_by_name', 'sh.emp_code as approve_by_code')
            ->leftjoin('hr_employees as he', 'het.emp_id', 'he.id')
            ->leftjoin('gnl_branchs as gb', 'het.branch_id', 'gb.id')
            ->leftjoin('gnl_sys_users as gsuCr', 'het.created_by', 'gsuCr.id')
            ->leftjoin('gnl_sys_users as apr', 'het.approved_by', 'apr.id')
            ->leftjoin('hr_employees as sh', 'sh.id', 'apr.emp_id')
            ->first();

        $data = array(
            'employeeNo' => $employeeTerminates->emp_code,
            'employeeName' => $employeeTerminates->emp_name . " (" . $employeeTerminates->emp_code . ")",
            'branchFrom' => $employeeTerminates->branch_name . " (" . $employeeTerminates->branch_code . ")",
            'terminateDate' => (new Datetime($employeeTerminates->terminate_date))->format('d-m-Y'),
            'isApproved' => $employeeTerminates->is_approved == 1 ? 'Approved' : 'Pending',
            'approvedBy' => $employeeTerminates->is_approved != 1 ? "Not Approved" : $employeeTerminates->approve_by_name . " (" . $employeeTerminates->approve_by_code . ")",
            'createdBy' => $employeeTerminates->created_by,
        );

        return view('GNL.HR.EmployeeTerminate.view', $data);
    }

    public function approve(Request $req)
    {
        DB::beginTransaction();
        try {
            $employeeTerminates = EmployeeTerminate::find($req->id);
            $employeeTerminates->update([
                'is_approved' => 1,
                'approved_by' => Auth::user()->id,
            ]);

            // ->where([['is_delete', 0], ['is_active', 1]])
            $employee = Employee::where('id', $employeeTerminates->emp_id)->first();
            $employee->update(['is_active' => 0, 'status' => 4]);

            $systemUser = SysUser::where('emp_id', $employeeTerminates->emp_id)->first();
            $systemUser->update(['is_active' => 0, 'sys_user_role_id' => 3]);

        } catch (\Exception $e) {
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'message' => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );
        }

        DB::commit();
        $notification = array(
            'message' => 'Successfully Approved.',
            'alert-type' => 'success',
        );

        return response()->json($notification);
    }

    public function getData(Request $req)
    {
        if ($req->context == 'branchFrom') {

            $employeeBranch = DB::table('hr_employees')
                ->where('id', $req->employeeId)
                ->value('branch_id');

            $data = array(
                'employeeBranch' => $employeeBranch,
            );
        }

        return response()->json($data);
    }

    public function getValidationPass($req, $operationType, $terminate = null)
    {
        $errorMsg = null;

        if ($operationType == 'store') {

            $validator = Validator::make($req->all(), array(
                'branch_id' => 'required',
                'emp_id' => 'required',
                'terminate_date' => 'required',
            ));

            $attributes = array(
                'branch_id' => 'Branch',
                'emp_id' => 'Employee',
                'terminate_date' => 'Terminate Date',
            );

            $validator->setAttributeNames($attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
            else {
                $checkTx = '';

                $checkTx = ACCS::checkTransactionForEmployee($req->emp_id);
                if(empty($checkTx)){
                    $checkTx = BILLS::checkTransactionForEmployee($req->emp_id);
                }
                if(empty($checkTx)){
                    $checkTx = FAMS::checkTransactionForEmployee($req->emp_id);
                }
                if(empty($checkTx)){
                    $checkTx = INVS::checkTransactionForEmployee($req->emp_id);
                }
                if(empty($checkTx)){
                    $checkTx = MFNS::checkTransactionForEmployee($req->emp_id);
                }
                if(empty($checkTx)){
                    $checkTx = POSS::checkTransactionForEmployee($req->emp_id);
                }
                
                $errorMsg = $checkTx;
            }
        }


        if ($operationType == 'delete') {
            if ($terminate->is_approved == 1) {
                $errorMsg = "Already approved. So, you can't delete this.";
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'errorMsg' => $errorMsg,
        );

        return $passport;
    }
}
