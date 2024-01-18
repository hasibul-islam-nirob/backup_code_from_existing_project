<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

use App\Model\HR\Bank;

class BankController extends Controller
{

    public function getPassport($request, $operationType, $data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'name'                          => 'required',
                //'address'                       => 'required',
                // 'email'                         => 'required',
                // 'phone'                         => 'required',
                // 'contact_person'                => 'required',
                // 'contact_person_designation'    => 'required',
                // 'contact_person_phone'          => 'required',
                // 'contact_person_email'          => 'required',

            );

            $attributes = array(
                'name'                          => 'BANK Name',
                // 'address'                       => 'BANK Address',
                // 'email'                         => 'BANK Email Address',
                // 'phone'                         => 'BANK Phone Number',
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

            $duplicateQuery = DB::table('hr_banks')
                ->where([['name', $request->name], ['is_delete', 0]])
                ->where(function ($query) use ($operationType, $data) {
                    if ($operationType == 'update') {
                        $query->where('id', '<>', $data->id);
                    }
                })
                ->count();
            if ($duplicateQuery > 0) {
                $errorMsg = "Bank name already exist.";
            }
        }

        ## condition check for bank
        if ($errorMsg == null && ($operationType == 'delete' || $operationType == 'index')) {
            $childData = DB::table('hr_bank_branches')->where([['bank_id', $data->id], ['is_delete', 0]])->count();

            if ($childData > 0) {
                $errorMsg = "Branchs of Bank Data Exist! Please delete child data first.";
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {

            $columns = array(
                0 => 'name',
                1 => 'address',
                2 => 'email',
                3 => 'phone',
                4 => 'contact_person',
                5 => 'contact_person_designation',
                6 => 'contact_person_phone',
                7 => 'contact_person_email',
            );

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

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_banks')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            $sl = (int)$request->start + 1;
            $data      = array();

            foreach ($masterQuery as $key => $row) {

                $IgnoreArray = [];

                $passport = $this->getPassport(null, $operationType = 'index', $row);
                if ($passport['isValid'] == false) {
                    $IgnoreArray = ['delete'];
                }

                $data[$key]['id']                          = $sl++;
                $data[$key]['name']                        = $row->name;
                $data[$key]['address']                     = $row->address;
                $data[$key]['email']                       = $row->email;
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

    public function insert(Request $request)
    {
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
                $isInsert = Bank::create($requestData);

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

    public function update(Request $request)
    {
        if ($request->isMethod('post')) {

            $updateData = Bank::where('id', decrypt($request->edit_id))->first();
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

    public function delete($id)
    {
        $deletedData = DB::table('hr_banks')->where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = DB::table('hr_banks')->where('id', decrypt($id))->update(['is_delete' => 1]);

        if ($delete) {
            $notification = array(
                'message'    => "Successfully deleted",
                'alert-type' => 'success',
                'status' => 'success',
                'statusCode' => 200
            );
        } else {
            $notification = array(
                'message'    => "Failed to delete",
                'alert-type' => 'error',
                'status' => 'error',
                'statusCode' => 400
            );
        }

        return response()->json($notification, $notification['statusCode']);
    }

    public function get(Request $request)
    {
        $bankData = DB::table('hr_banks')->where('id', decrypt($request->id))->where('is_delete', 0)->first();

        return response()->json($bankData);
    }
    
}
