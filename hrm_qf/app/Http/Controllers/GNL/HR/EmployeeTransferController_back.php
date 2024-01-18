<?php

namespace App\Http\Controllers\GNL\HR;

use App\Http\Controllers\Controller;

use App\Model\GNL\SysUser;
use App\Model\GNL\HR\Employee;
use App\Model\GNL\HR\EmployeeTransfer;

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
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;

class back_EmployeeTransferController extends Controller
{

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

            return view('GNL.HR.EmployeeTransfer.index', $data);
        }

        $columns = [
            'emp_code',
            'employeeName',
            'branchFrom',
            'branchTo',
            'transferDate',
            'status',
            'action',
        ];

        $limit            = $req->length;
        $orderColumnIndex = (int) $req->input('order.0.column') <= 1 ? 0 : (int) $req->input('order.0.column') - 1;
        $order            = $columns[$orderColumnIndex];
        $dir              = $req->input('order.0.dir');

        // Searching variable
        $search = (empty($req->input('search.value'))) ? null : $req->input('search.value');

        $employeeTransfers = DB::table('hr_employee_transfer as het')
            ->where('het.is_delete', 0)
            ->select('het.id', 'het.emp_id as employeeNo', 'he.emp_name as employeeName', 'he.emp_code as employeeCode', 'gbfrom.branch_name as branchFrom', 'gbfrom.branch_code as branchFromCode', 'gbto.branch_name as branchTo', 'gbto.branch_code as branchToCode', 'het.transfer_date', 'het.is_approved')
            ->leftjoin('hr_employees as he', 'het.emp_id', 'he.id')
            ->leftjoin('gnl_branchs as gbfrom', 'het.branch_from', 'gbfrom.id')
            ->leftjoin('gnl_branchs as gbto', 'het.branch_to', 'gbto.id')
            ->where(function ($query) use ($search) {
                $query
                    // ->where('het.employee_no', 'LIKE', "%{$search}%")
                    ->where('gbfrom.branch_name', 'LIKE', "%{$search}%")
                    ->orWhere('gbfrom.branch_code', 'LIKE', "%{$search}%")
                    ->orWhere('he.emp_name', 'LIKE', "%{$search}%")
                    ->orWhere('he.emp_code', 'LIKE', "%{$search}%")
                    ->orWhere('gbto.branch_name', 'LIKE', "%{$search}%")
                    ->orWhere('gbto.branch_code', 'LIKE', "%{$search}%");
            })
            ->orderBy('het.id', 'DESC')
            ->orderBy($order, $dir)
            ->limit($limit)
            ->offset($req->start)
            ->get();
        // dd($employeeTransfers);
        $totalData = $employeeTransfers->count();
        $sl        = (int) $req->start + 1;

        foreach ($employeeTransfers as $key => $row) {

            $status = '<span class="text-primary">Approved</span>';
            $IgnoreArray = array();

            if ($row->is_approved == 0) {
                $status = '<a type="button" class="btn btn-danger btn-sm" href="javascript:void(0)" onClick="fnApprove(' . $row->id . ')"><i class="fad fa-info-circle"></i> Approve</a>';
            }
            else if ($row->is_approved == 1) {
                // dd($row->is_approved);
                $IgnoreArray = ['delete'];
            }

            $employeeTransfers[$key]->sl           = $sl++;
            $employeeTransfers[$key]->transferDate = (new DateTime($row->transfer_date))->format('d-m-Y');
            $employeeTransfers[$key]->employeeName = $row->employeeName . " (" . $row->employeeCode . ")";
            $employeeTransfers[$key]->branchFrom = $row->branchFrom . " (" . $row->branchFromCode . ")";
            $employeeTransfers[$key]->branchTo = $row->branchTo . " (" . $row->branchToCode . ")";

            $employeeTransfers[$key]->status       = $status;
            // $employeeTransfers[$key]->action       = encrypt($row->id);
            $employeeTransfers[$key]->action = Role::roleWiseArray($this->GlobalRole, $row->id, $IgnoreArray);
        }

        $data = array(
            "draw"            => intval($req->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalData,
            'data'            => $employeeTransfers,
        );

        return response()->json($data);
    }

    public function add(Request $req)
    {
        if ($req->isMethod('post')) {
            return $this->store($req);
        }

        $sysDate   = Common::systemCurrentDate(Common::getBranchId());
        $companyId = Common::getCompanyId();

        $employees = DB::table('hr_employees')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->select('id','employee_no', DB::raw('CONCAT(emp_name, " (", emp_code, ")") AS employee'))
            ->get();

        $data = array(
            'employees' => $employees,
            'sysDate'   => $sysDate,
            'companyId' => $companyId,
        );

        return view('GNL.HR.EmployeeTransfer.add', $data);
    }

    public function store(Request $req)
    {
        $passport = $this->getValidationPass($req, $operationType = 'store');
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        $req['transfer_date'] = (new DateTime($req->transfer_date))->format('Y-m-d');
        $req['created_at']    = now();
        $req['created_by']    = Auth::user()->id;

        $checkTx = false;

        $isCreate = EmployeeTransfer::create($req->all());

        if ($isCreate) {
            $notification = array(
                'message'    => 'Successfully Inserted',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
            );
        }

        return response()->json($notification);
    }

    public function edit(Request $req)
    {
        if ($req->isMethod('post')) {
            return $this->update($req);
        }

        $sysDate   = Common::systemCurrentDate(Common::getBranchId());
        $companyId = Common::getCompanyId();

        $employees = DB::table('hr_employees')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->select('id','employee_no', DB::raw('CONCAT(emp_name, " (", emp_code, ")") AS employee'))
            ->get();

        $employeeTransfers = EmployeeTransfer::find($req->id);

        $data = array(
            'employees'     => $employees,
            'sysDate'       => $sysDate,
            'companyId'     => $companyId,
            'emp_id'        => $employeeTransfers->emp_id,
            'branch_from'   => $employeeTransfers->branch_from,
            'branch_to'     => $employeeTransfers->branch_to,
            'transfer_date' => $employeeTransfers->transfer_date,
        );

        return view('GNL.HR.EmployeeTransfer.edit', $data);
    }

    public function update(Request $req)
    {
        $employeeTransfers = EmployeeTransfer::find($req->id);
        $passport          = $this->getValidationPass($req, $operationType = 'update', $employeeTransfers);
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        $req['transfer_date'] = (new DateTime($req->transfer_date))->format('Y-m-d');
        $req['is_approved'] = 0;
        $req['updated_at']    = now();
        $req['updated_by']    = Auth::user()->id;
        $isUpdated            = $employeeTransfers->update($req->all());

        if ($isUpdated) {
            $notification = array(
                'message'    => 'Successfully Updated.',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
            );
        }

        return response()->json($notification);
    }

    public function delete(Request $req)
    {
        $employeeTransfers = EmployeeTransfer::find($req->id);
        $passport          = $this->getValidationPass($req, $operationType = 'delete', $employeeTransfers);
        
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
        
        $isDeleted = $employeeTransfers->update(['is_delete' => 1]);

        if ($isDeleted) {
            $notification = array(
                'message'    => 'Successfully Deleted.',
                'alert-type' => 'success',
            );
        } else {
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                // 'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );
        }

        return redirect()->back()->with($notification);
    }

    public function view($id)
    {
        $employeeTransfers = DB::table('hr_employee_transfer as het')
            ->where('het.id', $id)
            ->select('het.id', 'het.emp_id', 'he.emp_name as employeeName', 'he.emp_code as employeeCode', 'gbfrom.branch_name as branchFrom', 'gbto.branch_name as branchTo', 'het.transfer_date', 'het.is_approved', 'gsuCr.full_name as created_by')
            ->leftjoin('hr_employees as he', 'het.emp_id', 'he.id')
            ->leftjoin('gnl_branchs as gbfrom', 'het.branch_from', 'gbfrom.id')
            ->leftjoin('gnl_branchs as gbto', 'het.branch_to', 'gbto.id')
            ->leftjoin('gnl_sys_users as gsuCr', 'het.created_by', 'gsuCr.id')
            ->first();

        $data = array(
            'empCode'      => $employeeTransfers->employeeCode,
            'employeeName' => $employeeTransfers->employeeName . " (" . $employeeTransfers->employeeCode . ")",
            'branchFrom'   => $employeeTransfers->branchFrom,
            'branchTo'     => $employeeTransfers->branchTo,
            'transferDate' => (new DateTime($employeeTransfers->transfer_date))->format('d-m-Y'),
            'isApproved'   => $employeeTransfers->is_approved == 1 ? 'Approved' : 'Pending',
            'approvedBy'   => $employeeTransfers->is_approved != 1 ? "Not Approved" : '',
            'createdBy'    => $employeeTransfers->created_by,
        );

        return view('GNL.HR.EmployeeTransfer.view', $data);
    }

    public function approve(Request $req)
    {
        DB::beginTransaction();
        try {
            $employeeTransfers = EmployeeTransfer::find($req->id);
            $employeeTransfers->update([
                'is_approved' => 1,
                'approved_by' => Auth::user()->id,
            ]);

            ## Update Employee Table
            $employee = Employee::where('id', $employeeTransfers->emp_id)
                                ->where([['is_delete', 0], ['is_active', 1]])
                                ->first();
            if($employee){
                $employee->update(['branch_id' => $employeeTransfers->branch_to]);
            }
            
            #Update System User Table
            $systemUser = SysUser::where('emp_id', $employeeTransfers->emp_id)
                                ->where([['is_delete', 0], ['is_active', 1]])
                                ->first();
            
            if($systemUser){
                $systemUser->update(['branch_id' => $employeeTransfers->branch_to]);
            }

            
        } catch (\Exception $e) {
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );
        }

        DB::commit();
        $notification = array(
            'message'    => 'Successfully Approved.',
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

    public function getValidationPass($req, $operationType, $transfer = null)
    {
        $errorMsg = null;

        if ($operationType == 'store') {

            $validator = Validator::make($req->all(), array(
                'emp_id'   => 'required',
                'branch_from'   => 'required',
                'branch_to'     => 'required',
                'transfer_date' => 'required',
            ));

            $attributes = array(
                'emp_id'   => 'Employee No',
                'branch_from'   => 'Branch From',
                'branch_to'     => 'Branch To',
                'transfer_date' => 'Transfer Date',
            );

            $validator->setAttributeNames($attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType != 'delete') {
            if ($errorMsg == null) {
                if ($req->branch_from == $req->branch_to) {
                    $errorMsg = "Branch From & Branch To can't be same";
                }
            }

            if ($errorMsg == null) {

                $employee = DB::table('hr_employees')
                    ->where('id', $req->emp_id)
                    ->select('id', 'branch_id')
                    ->first();
                
                if(empty($employee)){
                    $errorMsg = "Employee Not Found";
                }
                else {
                    $employeeBranch = DB::table('gnl_branchs')
                        ->where('id', $employee->branch_id)
                        ->selectRaw('CONCAT(branch_code, " - ", branch_name) AS branch')
                        ->value('branch');

                    if ($req->branch_from != $employee->branch_id) {
                        $errorMsg = "Employee branch from must be " . $employeeBranch;
                    }

                    else{
                        $checkTx = '';

                        $checkTx = ACCS::checkTransactionForEmployee($req->emp_id,'transferring');
                        if(empty($checkTx)){
                            $checkTx = BILLS::checkTransactionForEmployee($req->emp_id,'transferring');
                        }
                        if(empty($checkTx)){
                            $checkTx = FAMS::checkTransactionForEmployee($req->emp_id,'transferring');
                        }
                        if(empty($checkTx)){
                            $checkTx = INVS::checkTransactionForEmployee($req->emp_id,'transferring');
                        }
                        if(empty($checkTx)){
                            $checkTx = MFNS::checkTransactionForEmployee($req->emp_id,'transferring');
                        }
                        if(empty($checkTx)){
                            $checkTx = POSS::checkTransactionForEmployee($req->emp_id,'transferring');
                        }
                        
                        if (!empty($checkTx)) {
                            $errorMsg = $checkTx;
                        }
                    }
                }

                
            }
        }

        if ($operationType == 'delete') {
            if ($transfer->is_approved == 1) {
                $errorMsg = "Already approved. So, you can't delete this.";
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'errorMsg' => $errorMsg,
        );

        return $passport;
    }
}
