<?php

namespace App\Http\Controllers\GNL;

use Illuminate\Http\Request;

use App\Model\GNL\PaymentSystem;
// use App\Model\GNL\PaymentAccount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Services\RoleService as Role;

use Illuminate\Support\Facades\Validator;

class PaymentSystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'payment_system_name',
                2 => 'short_name',


            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable

            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');


            // dd($PurchaseBill,$EmployeeID, $supplierID);

            // // Query
            $masterQuery = PaymentSystem::from('gnl_payment_system')
                ->where([['gnl_payment_system.is_delete', 0]])


                ->select('gnl_payment_system.*')

                ->where(function ($masterQuery) use ($search) {

                    if (!empty($search)) {
                        $masterQuery->where('gnl_payment_system.payment_system_name', 'LIKE', "%{$search}%")
                            ->orWhere('gnl_payment_system.short_name', 'LIKE', "%{$search}%");
                    }
                })

                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $tempQueryData = clone $masterQuery;
            // $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalData = PaymentSystem::where([['is_delete', '=', 0]])
                // ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
                ->count();
            $totalFiltered = $totalData;


            if (!empty($search)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $Row) {

                $status = '';

                if ($Row->status == 0) {
                    $status = 'ALL (Supplier/Sales)';
                } else if ($Row->status == 1) {
                    $status = 'Supplier Payment';
                } else if ($Row->status == 2) {
                    $status = 'Sales & SalesReturn';
                }




                $TempSet = [
                    'id' => ++$i,
                    'payment_system_name' => $Row->payment_system_name,
                    'short_name' => $Row->short_name,
                    'status' =>  $status,
                    'order'  =>  $Row->order_by,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id, [], $Row->is_active),
                ];

                $DataSet[] = $TempSet;
                // }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            );

            echo json_encode($json_data);
        } else {

            return view('GNL.PaymentSystem.index');
        }
    }

    public function add(Request $req)
    {

        if ($req->isMethod('post')) {

            // dd($req->all());
            return $this->store($req);
        } else {
            return view('GNL.PaymentSystem.add');
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

            $isInsert = PaymentSystem::create($req->all());

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
        $TargetData = PaymentSystem::find($request->id);
        if ($request->isMethod('post')) {


            // $RequestData = $request->all();
            $request['branch_id'] = $TargetData->branch_id;

            // dd($request->all());
            return $this->update($request);
        } else {
            return view('GNL.PaymentSystem.edit', compact('TargetData'));
        }
    }


    public function update(Request $req)
    {
        $TargetData     = PaymentSystem::find($req->id);
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
        $TargetData = PaymentSystem::find($id);
        return view('GNL.PaymentSystem.view', compact('TargetData'));
    }
    public function delete($id = null)
    {



        $TargetData     = PaymentSystem::find($id);
        // dd($TargetData );

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

        $TargetData     = PaymentSystem::find($req->id);
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
                'payment_system_name'     => 'required',
                'short_name' => 'required',
            );
        }

        $attributes = array(
            'payment_system_name'     => 'Empty Payment System Name',
            'short_name' => 'Empty Short Name',

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
        // not used this funtion yet
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
