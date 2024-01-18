<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

use App\Model\HR\BankBranch;

class BanksBranchController extends Controller
{


    public function getPassport($request, $operationType, $data = null){
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'bank_id'                       => 'required',
                'name'                          => 'required',
                // 'address'                       => 'required',
                // 'email'                         => 'required',
                // 'phone'                         => 'required',
                // 'contact_person'                => 'required',
                // 'contact_person_designation'    => 'required',
                // 'contact_person_phone'          => 'required',
                // 'contact_person_email'          => 'required',
                
            );

            $attributes = array(
                'bank_id'                       => 'Bank Name',
                'name'                          => 'Branch Name',
                // 'address'                       => 'Branch Address',
                // 'email'                         => 'Branch Email Address',
                // 'phone'                         => 'Branch Phone Number',
                // 'contact_person'                => 'Contact Person',
                // 'contact_person_phone'          => 'Contact Person Phone',
                // 'contact_person_email'          => 'Contact Person Email',
                // 'contact_person_designation'    => 'Designation',
                
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }


        if ($errorMsg == null &&  ($operationType == 'store' || $operationType == 'update')) {
            
            $duplicateQuery = BankBranch::where('bank_id', $request->bank_id)->where('name', $request->name)->count();
            
            if ($duplicateQuery > 0) {
                $errorMsg = "Branch name already exist.";
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }


    public function index(Request $request){

        if ($request->isMethod('post')) {
            
            $columns = [
                0 => 'bank_name',
                1 => 'name',
                2 => 'address',
                3 => 'phone',
                4 => 'contact_person',
                5 => 'contact_person_designation',
                6 => 'contact_person_phone',
                7 => 'contact_person_email',
            ];


            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = DB::table('hr_banks')
                ->where('is_delete', 0)
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('name', 'LIKE', "%{$search}%");
                        $query->orWhere('address', 'LIKE', "%{$search}%");
                        $query->orWhere('phone', 'LIKE', "%{$search}%");
                        $query->orWhere('email', 'LIKE', "%{$search}%");
                        $query->orWhere('contact_person', 'LIKE', "%{$search}%");
                        $query->orWhere('contact_person_designation', 'LIKE', "%{$search}%");
                        $query->orWhere('contact_person_phone', 'LIKE', "%{$search}%");
                        $query->orWhere('contact_person_email', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);


            $masterQuery = DB::table('hr_bank_branches')
                        ->join('hr_banks', 'hr_banks.id', 'hr_bank_branches.bank_id')
                        ->select('hr_banks.name AS bank_name','hr_bank_branches.*')
                        ->where('hr_bank_branches.is_delete', 0)
                        ->where(function ($query) use ($search) {
                            if (!empty($search)) {
                                $query->where('hr_bank_branches.name', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_banks.name', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.address', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.phone', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.email', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.contact_person', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.contact_person_designation', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.contact_person_phone', 'LIKE', "%{$search}%");
                                $query->orWhere('hr_bank_branches.contact_person_email', 'LIKE', "%{$search}%");
                            }
                        })
                        ->orderBy('id', 'DESC')
                        ->orderBy($order, $dir);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_bank_branches')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }


            $sl = (int)$request->start + 1;
            $data      = array();
            foreach ($masterQuery as $key => $row) {
                $IgnoreArray = [''];

                $data[$key]['id']                          = $sl++;
                $data[$key]['bank_name']                   = $row->bank_name;
                $data[$key]['name']                        = $row->name;
                $data[$key]['email']                       = $row->email;
                $data[$key]['address']                     = $row->address;
                $data[$key]['phone']                       = $row->phone;
                $data[$key]['contact_person']              = $row->contact_person;
                $data[$key]['contact_person_designation']  = $row->contact_person_designation;
                $data[$key]['contact_person_phone']        = $row->contact_person_phone;
                $data[$key]['contact_person_email']        = $row->contact_person_email;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);
            }

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                'data'              => $data,
            );

            return response()->json($json_data);

        }
    }


    public function insert(Request $request){

        if ($request->isMethod('post')) {

            $passport = $this->getPassport($request, $operationType = 'store');

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['message'],
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400,
                );
                return response()->json($notification, $notification['statusCode']);
            }

            try {

                $requestData = $request->all();
                $isInsert = BankBranch::create($requestData);

                if ($isInsert) {
                    $notification = array(
                        'message' => 'Successfully Inserted Data',
                        'alert-type' => 'success',
                        'status' => 'success',
                        'statusCode' => 200,
                    );
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to insert data',
                        'alert-type' => 'error',
                        'status' => 'error',
                        'statusCode' => 400,
                    );
                }
            } catch (\Exception $e) {

                $notification = array(
                    'message' => 'Internal Server Error. Try Again!!',
                    'alert-type' => 'error',
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                    'statusCode' => 500,
                );
            }

            return response()->json($notification, $notification['statusCode']);
        }
        
    }


    public function update(Request $request){

        if ($request->isMethod('post')) {

            $updateData = BankBranch::where('id', decrypt($request->edit_id))->first();
            $passport = $this->getPassport($request, 'update', $updateData);

            if ($passport['isValid'] == false) {
                $notification = array(
                    'message'    => $passport['message'],
                    'alert-type' => 'error',
                    'status' => 'error',
                    'statusCode' => 400
                );
                return response()->json($notification, $notification['statusCode']);
            }

            try {

                $requestData = $request->all();
                $isUpdate = $updateData->update($requestData);

                if ($isUpdate) {
                    $notification = array(
                        'message' => 'Successfully updated Data',
                        'alert-type' => 'success',
                        'status' => 'success',
                        'statusCode' => 200,
                    );
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to updated data',
                        'alert-type' => 'error',
                        'status' => 'error',
                        'statusCode' => 400,
                    );
                }
            } catch (\Exception $e) {

                $notification = array(
                    'message' => 'Internal Server Error. Try Again!!',
                    'alert-type' => 'error',
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                    'statusCode' => 500,
                );
            }

            return response()->json($notification, $notification['statusCode']);
        }
    }

    
    
    public function delete($id){
        
        $delete = DB::table('hr_bank_branches')->where('id', decrypt($id))->update(['is_delete' => 1]);

        if ($delete) {
            return response()->json([
                'message'    => 'Successfully deleted',
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 500,
                'status' => 'error',
                'message'    => 'Failed to delete',
                'result_data' => ''
            ], 500);
        }
    }

    public function get(Request $request){
        //$BankData = DB::table('hr_bank_branches')->where('id',decrypt($request->id))->where('is_delete', 0)->first();

        $branchData = DB::table('hr_bank_branches')
                    ->where([['hr_bank_branches.is_delete', 0],['hr_bank_branches.id',decrypt($request->id)]])
                    ->join('hr_banks', 'hr_banks.id', 'hr_bank_branches.bank_id')
                    ->select('hr_banks.name AS bank_name','hr_bank_branches.*')
                    ->first();

        //return view('HR.Configuration.Branchs.view',compact('branchData'));

        $banks = DB::table('hr_banks')->where('is_delete',0)->select('id','name')->get();

        $data = array(
            'branchData'    => $branchData,
            'banks'      => $banks
        );

        return response()->json($data);
    }


    public function getAllBank($id){

        
        $branchData = DB::table('hr_bank_branches')->where('hr_bank_branches.is_delete', 0)->where('id',decrypt($id))->first();

        $banks = DB::table('hr_banks')->where('id',$branchData->bank_id)->select('id','name')->first();

        $allBanks = DB::table('hr_banks')->where('is_delete',0)->select('id','name')->get();
        $data = array(
            'branchs'    => $branchData,
            'banks'         => $banks,
            'allBanks'      => $allBanks
        );

        return response()->json($data);
    }




    function bankData(){
        $banks = DB::table('hr_banks')->where('is_delete',0)->select('id','name')->get();
        $data = array(
            'banks'         => $banks
        );
        return response()->json($data);
    }





    /*
    public function index(Request $request){
        if (!$request->ajax()) {
            return view('HR.Configuration.Branchs.index');
        }
        $columns = [
            'bank_name',
            'name',
            'address',
            'phone',
            'contact_person',
            'contact_person_designation',
            'contact_person_phone',
            'contact_person_email',
        ];


        $totalData = DB::table('hr_bank_branches')->where('is_delete', '=', 0)->count();
        $totalFiltered = $totalData;
        $start = $request->start;
        $limit = $request->length;
        $orderColumnIndex = (int)$request->input('order.0.column') <= 1 ? 0 : (int)$request->input('order.0.column') - 1;
        $order = $columns[$orderColumnIndex];
        $dir = $request->input('order.0.dir');

        // Searching variable
        $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

        $branchData = DB::table('hr_bank_branches')
                    ->join('hr_banks', 'hr_banks.id', 'hr_bank_branches.bank_id')
                    ->select('hr_banks.name AS bank_name','hr_bank_branches.*')
                    ->where('hr_bank_branches.is_delete', 0)
                    ->where(function ($branchData) use ($search) {
                        if (!empty($search)) {
                            $branchData->where('hr_bank_branches.name', 'LIKE', "%{$search}%")
                             ->orWhere('hr_banks.name', 'LIKE', "%{$search}%");
                        }
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
      
        if (!empty($search)) {
            $totalFiltered = count($branchData);
        }



        $sl = (int)$request->start + 1;
        foreach ($branchData as $key => $row) {
            $branchData[$key]->sl = $sl++;
            $branchData[$key]->id = encrypt($row->id);
        }
        // dd( $branchData);

        $data = array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => $totalData,
            "recordsFiltered"   => $totalData,
            'data'              => $branchData,
        );


        return response()->json($data);
    }

    public function add(Request $request)
    {
        if($request->isMethod('post')){
            // dd($request->all());
            if($this->duplicateBranch($request) == true){
                $notification = array(
                    'alert-type' => 'error',
                    'message'    => 'This branch already exist!',
                );
                return response()->json($notification);
            }else {
                DB::beginTransaction();
            try{
                    DB::table('hr_bank_branches')
                        ->insert([
                        'bank_id'  => $request->bank_id,
                        'name'     => $request->name,
                        'address'  => $request->address,
                        'email'    => $request->email,
                        'phone'    => $request->phone,
                        'contact_person'=> $request->contact_person,
                        'contact_person_designation' => $request->contact_person_designation,
                        'contact_person_phone' => $request->contact_person_phone,
                        'contact_person_email' => $request->contact_person_email,
                        'created_at' => Carbon::now(),
                        'created_by' => Auth::user()->id,
                    ]);
                    DB::commit();
                    $notification = array(
                        'message'    => 'Branch has been added Successfully',
                        'alert-type' => 'success',
                    );

                    return response()->json($notification);
                } catch (\Throwable $e) {
                    DB::rollback();
                    $notification = array(
                        'alert-type' => 'error',
                        'message'    => 'Something went wrong',
                        'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                    );
                    return response()->json($notification);
                }
            }

        }
        $banks = DB::table('hr_banks')
                ->where('is_delete',0)
                ->select('id','name')
                ->get();
        return view('HR.Configuration.Branchs.add',compact('banks'));
    }

    public function edit(Request $request)
    {
        if($request->isMethod('post')){
            // dd($request->all());
            if($this->duplicateBranch($request) == true){
                $notification = array(
                    'alert-type' => 'error',
                    'message'    => 'This branch already exist!',
                );
                return response()->json($notification);
            }else {
                DB::beginTransaction();
            try{
                    DB::table('hr_bank_branches')->where('id',decrypt($request->id))
                        ->update([
                        'bank_id'  => $request->bank_id,
                        'name'     => $request->name,
                        'address'  => $request->address,
                        'email'    => $request->email,
                        'phone'    => $request->phone,
                        'contact_person'=> $request->contact_person,
                        'contact_person_designation' => $request->contact_person_designation,
                        'contact_person_phone' => $request->contact_person_phone,
                        'contact_person_email' => $request->contact_person_email,
                        'updated_at' => Carbon::now(),
                        'updated_by' => Auth::user()->id,
                    ]);
                    DB::commit();
                    $notification = array(
                        'message'    => 'Branch has been edited Successfully',
                        'alert-type' => 'success',
                    );

                    return response()->json($notification);
                } catch (\Throwable $e) {
                    DB::rollback();
                    $notification = array(
                        'alert-type' => 'error',
                        'message'    => 'Something went wrong',
                        'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                    );
                    return response()->json($notification);
                }
            }

        }
        $branchData = DB::table('hr_bank_branches')
                ->where([['hr_bank_branches.is_delete', 0],['hr_bank_branches.id',decrypt($request->id)]])
                ->first();
        $banks = DB::table('hr_banks')
                ->where('is_delete',0)
                ->select('id','name')
                ->get();
        $data = array(
            'branchData'    => $branchData,
            'banks'         => $banks,
        );
        return view('HR.Configuration.Branchs.edit',$data);
    }

    public function view($id = null)
    {
        $branchData = DB::table('hr_bank_branches')
                    ->where([['hr_bank_branches.is_delete', 0],['hr_bank_branches.id',decrypt($id)]])
                    ->join('hr_banks', 'hr_banks.id', 'hr_bank_branches.bank_id')
                    ->select('hr_banks.name AS bank_name','hr_bank_branches.*')
                    ->first();

        return view('HR.Configuration.Branchs.view',compact('branchData'));
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            DB::table('hr_bank_branches')
                ->where('id',decrypt($request->id))
                ->update(['is_delete' => 1]);
            DB::commit();
            $notification = array(
                'message'    => 'Branch data deleted successfully!',
                'alert-type' => 'success',
            );

            return response()->json($notification);
        } catch (\Exception $e) {
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            );

            return response()->json($notification);
        }
    }

    public function duplicateBranch(Request $request)
    {
        if(!is_null($request->id)){
            $branchData = DB::table('hr_bank_branches')
                    ->where('id','!=',decrypt($request->id))
                    ->where([['is_delete',0],['bank_id',$request->bank_id]])
                    ->where('name',$request->name)
                    ->first();
        }else {
            $branchData = DB::table('hr_bank_branches')
                    ->where([['is_delete',0],['bank_id',$request->bank_id]])
                    ->where('name',$request->name)
                    ->first();
        }
        if(!empty($branchData)){
            return true;
        }else {
            return false;
        }
    }

    */



}
