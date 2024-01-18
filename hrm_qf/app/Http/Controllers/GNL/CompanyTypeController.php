<?php

namespace App\Http\Controllers\GNL;

use Illuminate\Http\Request;

use App\Model\GNL\CompanyType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;

class CompanyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $req)
    {
        if (!$req->ajax()) {

            $DataSet = CompanyType::where([['is_active', 1], ['is_delete', 0]])->get();
            return view('GNL.CompanyType.index', compact('DataSet'));
        }


        $columns = [
            'name',
            'company_id',
        ];

        $limit            = $req->length;
        $orderColumnIndex = (int) $req->input('order.0.column') <= 1 ? 0 : (int) $req->input('order.0.column') - 1;
        $order            = $columns[$orderColumnIndex];
        $dir              = $req->input('order.0.dir');

        // Searching variable

        $search              = (empty($req->input('search.value'))) ? null : $req->input('search.value');
        $DataSet = DB::table('gnl_company_type')->where([['gnl_company_type.is_active', 1], ['gnl_company_type.is_delete', 0]])
            ->leftJoin('gnl_companies', 'gnl_companies.id', 'gnl_company_type.company_id')
            ->select(
                'gnl_company_type.id AS id',
                'gnl_company_type.name AS name',
                'gnl_companies.comp_name AS comp_name'

            )
            ->orderBy($order, $dir);

        // dd($DataSet);
        if ($search != null) {
            $DataSet->where(function ($query) use ($search) {
                $query->Where('gnl_company_type.name', 'LIKE', "%{$search}%")
                    ->orWhere('gnl_companies.comp_name', 'LIKE', "%{$search}%");
            });
        }

        $totalData = (clone $DataSet)->count();
        // dd($totalData);
        $DataSet = $DataSet->limit($limit)->offset($req->start)->get();

        $sl = (int) $req->start + 1;

        foreach ($DataSet as $key => $row) {
            $DataSet[$key]->sl               = $sl++;
            $DataSet[$key]->id               = encrypt($row->id);
        }

        $data = array(
            "draw"            => intval($req->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalData,
            'data'            => $DataSet,
        );

        return response()->json($data);
    }

    public function add(Request $req)
    {
        if ($req->isMethod('post')) {
            return $this->store($req);
        } else {
            return view('GNL.CompanyType.add');
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

            $isInsert = CompanyType::create($req->all());

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
        $TargetData = CompanyType::find(decrypt($request->id));
        // dd($TargetData);

        if ($request->isMethod('post')) {
            return $this->update($request);
        } else {
            return view('GNL.CompanyType.edit', compact('TargetData'));
        }
    }


    public function update(Request $req)
    {
        $TargetData     = CompanyType::find(decrypt($req->id));
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
        $TargetData = CompanyType::find(decrypt($id));
        return view('GNL.CompanyType.view', compact('TargetData'));
    }
    public function delete(Request $req)
    {



        $TargetData     = CompanyType::find(decrypt($req->id));
        // dd($TargetData );
        $passport = $this->getPassport($req, $operationType = 'delete', $TargetData);
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['errorMsg'],
                'alert-type' => 'error',
            );
            return response()->json($notification);
        }

        if ($TargetData->is_delete == 0) {

            $TargetData->is_delete = 1;
            $isSuccess = $TargetData->update();

            if ($isSuccess) {
                $notification = array(
                    'message' => 'Successfully Deleted',
                    'alert-type' => 'success',
                );
                return response()->json($notification);
            }
        }
    }

    public function isActive(Request $req)
    {

        $TargetData     = CompanyType::find(decrypt($req->id));
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
                'name'             => 'required',
                'company_id' => 'required',
            );
        }

        $attributes = array(
            'name'                          => 'Empty Name',
            'company_id'                      =>  'Company required',
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
