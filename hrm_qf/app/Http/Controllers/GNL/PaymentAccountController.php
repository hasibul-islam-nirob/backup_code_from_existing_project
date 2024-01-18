<?php

namespace App\Http\Controllers\GNL;

use Illuminate\Http\Request;

use App\Model\GNL\PaymentAccount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;

use Illuminate\Support\Facades\Validator;

class PaymentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $req)
    {
        if ($req->ajax()) {

            $columns = [
                'payment_system_id',
                'provider_name',
                'acc_holder_name',
                'account_no',
                'ledger_id',
                'mobile',
            ];

            $limit            = $req->length;
            $orderColumnIndex = (int) $req->input('order.0.column') <= 1 ? 0 : (int) $req->input('order.0.column') - 1;
            $order            = $columns[$orderColumnIndex];
            $dir              = $req->input('order.0.dir');

            // Searching variable
            $search = (empty($req->input('search.value'))) ? null : $req->input('search.value');

            $DataSet = PaymentAccount::where([['gnl_payment_acc.is_delete', 0]])
                ->select(
                    'gnl_payment_acc.id AS id',
                    'gnl_payment_acc.status AS status',
                    'gnl_payment_acc.provider_name AS provider_name',
                    'gnl_payment_acc.acc_holder_name AS acc_holder_name',
                    'gnl_payment_acc.account_no AS account_no',
                    'gnl_payment_acc.ledger_id AS ledger_id',

                    'gnl_payment_acc.is_active AS is_active',
                    'gnl_payment_acc.payment_system_id AS payment_system_id',

                    'gnl_payment_acc.mobile AS mobile'
                )
                ->orderBy($order, $dir);
            //

            if ($search != null) {
                $DataSet->where(function ($query) use ($search) {
                    $query->Where('gnl_payment_acc.provider_name', 'LIKE', "%{$search}%")
                        ->orWhere('gnl_payment_acc.acc_holder_name', 'LIKE', "%{$search}%")
                        ->orWhere('gnl_payment_acc.account_no', 'LIKE', "%{$search}%");
                });
            }

            $totalData = (clone $DataSet)->count();
            $DataSet = $DataSet->limit($limit)->offset($req->start)->get();
            $LedgerData = DB::table('acc_account_ledger')->where('is_delete', 0)->where('is_group_head', 0)->orderBy('id', 'DESC')->get();

            $sl = (int) $req->start + 1;

            foreach ($DataSet as $key => $row) {

                $status = '';

                if ($row->status == 0) {
                    $status = 'ALL (Supplier/Sales)';
                } else if ($row->status == 1) {
                    $status = 'Supplier Payment';
                } else if ($row->status == 2) {
                    $status = 'Sales & SalesReturn';
                }

                $DataSet[$key]->sl                  = $sl++;
                $DataSet[$key]->payment_system_id   = $row->paymentSystem['payment_system_name'];
                $DataSet[$key]->ledger              = !empty($LedgerData->where('id', $row->ledger_id)->first()) ? $LedgerData->where('id', $row->ledger_id)->first()->name . " [" . $LedgerData->where('id', $row->ledger_id)->first()->code . "]" : '';
                $DataSet[$key]->action              = Role::roleWiseArray($this->GlobalRole, $row->id, [], $row->is_active);
                $DataSet[$key]->status              = $status;
            }

            $data = array(
                "draw"            => intval($req->input('draw')),
                "recordsTotal"    => $totalData,
                "recordsFiltered" => $totalData,
                'data'            => $DataSet,
            );

            return response()->json($data);
        }

        return view('GNL.PaymentAccount.index');
    }

    public function add(Request $req)
    {

        if ($req->isMethod('post')) {

            return $this->store($req);
        } else {
            return view('GNL.PaymentAccount.add');
        }
    }

    public function store(Request $req)
    {
        $passport = $this->getPassport($req, $operationType = 'store');
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        // store data
        DB::beginTransaction();

        try {

            $isInsert = PaymentAccount::create($req->all());

            if ($isInsert) {
                DB::commit();
                $notification = array(
                    'message'    => 'Successfully Inserted',
                    'alert-type' => 'success',
                );

                return response()->json($notification);
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );

            return response()->json($notification);
        }
    }

    public function edit(Request $request)
    {
        $TargetData = PaymentAccount::find($request->id);

        if ($request->isMethod('post')) {

            $request['branch_id'] = $TargetData->branch_id;

            return $this->update($request);
        } else {
            return view('GNL.PaymentAccount.edit', compact('TargetData'));
        }
    }

    public function update(Request $req)
    {
        $TargetData     = PaymentAccount::find($req->id);
        $passport = $this->getPassport($req, $operationType = 'update', $TargetData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        // store data
        DB::beginTransaction();

        try {
            $isUpdate = $TargetData->update($req->all());

            if ($isUpdate) {
                DB::commit();
                $notification = array(
                    'message'    => 'Successfully Updated',
                    'alert-type' => 'success',
                );

                return response()->json($notification);
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );

            return response()->json($notification);
        }
    }

    public function view($id = null)
    {
        $TargetData = PaymentAccount::find($id);
        return view('GNL.PaymentAccount.view', compact('TargetData'));
    }

    public function delete(Request $req)
    {
        $TargetData     = PaymentAccount::find($req->id);

        if ($TargetData->is_delete == 0) {

            $TargetData->is_delete = 1;
            $isSuccess = $TargetData->update();

            if ($isSuccess) {
                $notification = array(
                    'message' => 'Successfully Deleted',
                    'alert-type' => 'success',
                );

                return redirect()->back()->with($notification);
            }
        }
    }

    public function isActive(Request $req)
    {
        $TargetData     = PaymentAccount::find($req->id);
        $passport = $this->getPassport($req, $operationType = 'delete', $TargetData);
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        if ($TargetData->is_active == 1) {
            $TargetData->is_active = 0;
        } else {
            $TargetData->is_active = 1;
        }

        $TargetData->update();
        $notification = array(
            'message' => 'Activation is changed',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function getPassport($req, $operationType, $Data = null)
    {
        $errorMsg      = null;
        $rules = array();

        if ($operationType != 'delete') {

            $rules = array(
                'provider_name'     => 'required',
                'acc_holder_name' => 'required',
                'account_no'     => 'required',
                'payment_system_id' => 'required',
            );
        }

        $attributes = array(
            'provider_name'     => 'Empty Bank/Provider Name',
            'acc_holder_name' => 'Empty Account Holder Name',
            'account_no'     => 'Empty Account',
            'payment_system_id' => 'Empty Pyment System',
        );

        $validator = Validator::make($req->all(), $rules, [], $attributes);

        if ($validator->fails()) {
            $errorMsg = implode(' || ', $validator->errors()->all());
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'errorMsg' => $errorMsg,
        );

        return $passport;
    }

    public function getData(Request $req)
    {
        if ($req->context == 'member') {

            $member = DB::table('mfn_members')
                ->where('id', $req->memberId)
                ->select('id', 'primaryProductId', 'branchId')
                ->first();

            $data = array(
                'member'   => $member,
            );
        }

        return response()->json($data);
    }
}
