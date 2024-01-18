<?php

namespace App\Http\Controllers\GNL\HR;

use App\Http\Controllers\Controller;
use App\Model\GNL\HR\SmsForward;
use App\Services\CommonService as Common;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SmsForwardController extends Controller
{
    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $allData = SmsForward::where('is_delete', 0)->get();

            $data          = [];
            $totalData     = SmsForward::count();
            $totalFiltered = $totalData;

            foreach ($allData as $key => $row) {
                $data[$key]['id']              = $row->id;
                $data[$key]['sl']              = $key + 1;
                $data[$key]['sms_title']       = $row->sms_title;
                $data[$key]['sms_body']        = $row->sms_body;
                $data[$key]['emp_id']     = $row->emp_id;
                $data[$key]['sender_id']       = $row->sender_id;
                $data[$key]['sms_to']          = $row->sms_to;
                $data[$key]['status']          = ($row->is_active == 0) ? "In-Active" : (($row->is_active == 1) ? "Draft" : (($row->is_active == 2) ? "Send" : ""));
                $data[$key]['receivers']       = $row->receivers;
                $data[$key]['receiver_status'] = $row->receiver_status;
                $data[$key]['action']          = Role::roleWiseArray($this->GlobalRole, encrypt($row->id));
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
            );
            return response()->json($json_data);
        } else {
            $samity = DB::table('mfn_samity')
                ->where([['is_delete', 0], ['closingDate', 'like', '%0000-00-00']])
                ->get();

            return view('GNL.SmsForward.index', compact('samity'));
        }
    }

    public function add(Request $request, $status)
    {

        //dd($status);
        //dd($request->all());
        //dd(($status == 'draft') ? 1 : (($status == 'send') ? 2 : 0));

        if ($request->isMethod('post')) {

            $passport = $this->getPassport($request);

            if ($passport['is_valid'] != true) {
                return response()->json([
                    'msg'    => $passport['msg'],
                    'status' => false,
                ], 400);
            }

            if ($passport['is_valid']) {

                DB::beginTransaction();
                try {
                    $receiverIdArr     = array();
                    $receiverNumberArr = array();
                    $receiverStatus    = array();
                    $queryData         = array();

                    $requestData = $request->all();

                    $send_type        = $requestData['send_type'];
                    $send_type_samity = (isset($requestData['send_type_samity'])) ? $requestData['send_type_samity'] : null;
                    $branch_id        = (isset($requestData['branch_id'])) ? $requestData['branch_id'] : null;
                    $samity_id        = (isset($requestData['samity_id'])) ? $requestData['samity_id'] : null;

                    if ($requestData['sms_to'] == 'employee') {

                        $queryData = DB::table('hr_employees')
                            ->where(function ($queryData) use ($send_type, $branch_id) {
                                if ($send_type == "selected") {
                                    if (!empty($branch_id)) {
                                        $queryData->where('branch_id', $branch_id);
                                    }
                                }
                            })
                            ->where([['is_active', 1], ['is_delete', 0]])
                            ->whereNotNull('org_mobile')
                            ->selectRaw('id, org_mobile as emp_phone')
                            ->pluck('id', 'emp_phone')
                            ->toArray();

                    } elseif ($requestData['sms_to'] == 'member') {

                        $queryData = DB::table('mfn_members as mb')
                            ->where(function ($queryData) use ($send_type_samity, $branch_id, $samity_id) {
                                if (!empty($branch_id)) {
                                    $queryData->where('mb.branchId', $branch_id);
                                }

                                if ($send_type_samity == "selected") {
                                    if (!empty($samity_id)) {
                                        $queryData->where('mb.samityId', $samity_id);
                                    }
                                }
                            })
                            ->where([['mb.is_delete', 0], ['mb.closingDate', 'like', '%0000-00-00']])
                            ->whereNotNull('mbd.mobileNo')
                            ->join('mfn_member_details as mbd', function ($queryData) {
                                $queryData->on('mb.id', 'mbd.memberId');
                            })
                            ->selectRaw('mbd.memberId as memberId, mbd.mobileNo as mobileNo')
                            ->pluck('memberId', 'mobileNo')
                            ->toArray();

                    } elseif ($requestData['sms_to'] == 'others') {
                        $queryData = array_flip(explode(',', $requestData['others_number']));
                    }

                    $receiverIdArr     = array_values($queryData);
                    $receiverNumberArr = array_keys($queryData);

                    foreach ($queryData as $key => $value) {
                        $receiverStatus[$key]['id'] = $value;
                        // $receiverStatus[$key]['status']    = '';
                    }

                    $requestData['receiver_numbers'] = implode(',', $receiverNumberArr);
                    $requestData['receiver_id']      = implode(',', $receiverIdArr);
                    $requestData['receiver_status']  = json_encode($receiverStatus);
                    $requestData['sms_length']       = strlen($requestData['sms_body']);
                    $requestData['receiver_length']  = count($receiverNumberArr);

                    if ($requestData['sms_to'] == 'others') {
                        $requestData['receiver_id'] = null;
                    }

                    $requestData['is_active'] = ($status == 'draft') ? 1 : (($status == 'send') ? 2 : 0);

                    //For sending message
                    if ($status == 'send') {
                        $requestData['api_response'] = Common::fnForSendSms($requestData['receiver_numbers'], $requestData['sms_body'], $requestData['sms_type']);
                    }

                    $isInsert = SmsForward::create($requestData);

                    DB::commit();

                    return response()->json([
                        'msg'    => $passport['msg'],
                        'status' => true,
                    ], 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'msg'    => "Internal Server Error. Try Again!!",
                        'status' => true,
                        'error'  => $e->getMessage(),
                    ], 500);
                }
            }
        }
    }

    public function edit(Request $request, $status)
    {
        //dd($request->all());
        if ($request->isMethod('post')) {
            $passport = $this->getPassport($request);
            if ($passport['is_valid']) {
                DB::beginTransaction();
                try {

                    $requestData = $request->all();

                    $receiverIdArr     = array();
                    $receiverNumberArr = array();
                    $receiverStatus    = array();
                    $queryData         = array();

                    $requestData = $request->all();

                    $send_type        = $requestData['send_type'];
                    $send_type_samity = (isset($requestData['send_type_samity'])) ? $requestData['send_type_samity'] : null;
                    $branch_id        = (isset($requestData['branch_id'])) ? $requestData['branch_id'] : null;
                    $samity_id        = (isset($requestData['samity_id'])) ? $requestData['samity_id'] : null;

                    if ($requestData['sms_to'] == 'employee') {

                        $queryData = DB::table('hr_employees')
                            ->where(function ($queryData) use ($send_type, $branch_id) {
                                if ($send_type == "selected") {
                                    if (!empty($branch_id)) {
                                        $queryData->where('branch_id', $branch_id);
                                    }
                                }
                            })
                            ->where([['is_active', 1], ['is_delete', 0]])
                            ->whereNotNull('org_mobile')
                            ->selectRaw('id, org_mobile as emp_phone')
                            ->toArray();

                    } elseif ($requestData['sms_to'] == 'member') {

                        $queryData = DB::table('mfn_members as mb')
                            ->where(function ($queryData) use ($send_type_samity, $branch_id, $samity_id) {
                                if (!empty($branch_id)) {
                                    $queryData->where('mb.branchId', $branch_id);
                                }

                                if ($send_type_samity == "selected") {
                                    if (!empty($samity_id)) {
                                        $queryData->where('mb.samityId', $samity_id);
                                    }
                                }
                            })
                            ->where([['mb.is_delete', 0], ['mb.closingDate', 'like', '%0000-00-00']])
                            ->whereNotNull('mbd.mobileNo')
                            ->join('mfn_member_details as mbd', function ($queryData) {
                                $queryData->on('mb.id', 'mbd.memberId');
                            })
                            ->selectRaw('mbd.memberId as memberId, mbd.mobileNo as mobileNo')
                            ->pluck('memberId', 'mobileNo')
                            ->toArray();

                    } elseif ($requestData['sms_to'] == 'others') {
                        $queryData = array_flip(explode(',', $requestData['others_number']));
                    }

                    $receiverIdArr     = array_values($queryData);
                    $receiverNumberArr = array_keys($queryData);

                    foreach ($queryData as $key => $value) {
                        $receiverStatus[$key]['id'] = $value;
                        // $receiverStatus[$key]['status']    = '';
                    }

                    $requestData['receiver_numbers'] = implode(',', $receiverNumberArr);
                    $requestData['receiver_id']      = implode(',', $receiverIdArr);
                    $requestData['receiver_status']  = json_encode($receiverStatus);
                    $requestData['sms_length']       = strlen($requestData['sms_body']);
                    $requestData['receiver_length']  = count($receiverNumberArr);

                    if ($requestData['sms_to'] == 'others') {
                        $requestData['receiver_id'] = null;
                    }

                    $requestData['is_active'] = ($status == 'draft') ? 1 : (($status == 'send') ? 2 : 0);

                    //For sending message
                    if ($status == 'send') {
                        $requestData['api_response'] = Common::fnForSendSms($requestData['receiver_numbers'], $requestData['sms_body'], $requestData['sms_type']);
                    }

                    $smsData  = SmsForward::where('id', $requestData['sms_id'])->first();
                    $isUpdate = $smsData->update($requestData);

                    DB::commit();
                    return response()->json([
                        'msg'    => $passport['msg'],
                        'status' => true,
                    ], 200);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'msg'    => "Internal Server Error. Try Again!!",
                        'status' => true,
                        'error'  => $e->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'msg'    => $passport['msg'],
                    'status' => false,
                ], 400);
            }
        }
    }

    public function get_sms($id)
    {
        $sms = SmsForward::find($id);
        return response()->json($sms);
    }

    public function send_sms($smsId)
    {
        $sms = SmsForward::find($smsId);

        $sms->api_response = Common::fnForSendSms($sms->receiver_numbers, $sms->sms_body, $sms->sms_type);
        $sms->is_active    = 2;

        $status = $sms->save();

        if ($status) { //Status change hobe

            return response()->json([
                'msg'    => 'Success!!',
                'status' => true,
            ], 200);

        } else {

            return response()->json([
                'msg'    => 'Failed to send the message!',
                'status' => false,
            ], 400);

        }
    }

    public function get_samity_by_branch($branchId)
    {
        return response()->json(DB::table('mfn_samity')->where('branchId', $branchId)->where([['is_delete', 0], ['closingDate', 'like', '%0000-00-00']])
                ->get());
    }

    public function view($id)
    {
        $empRes = SmsForward::find($id);
        $data   = [

        ];
        return view();
    }

    public function getPassport($request)
    {

        $flag = [
            'is_valid' => true,
            'msg'      => '',
        ];

        if (!isset($request['sms_title']) || $request['sms_title'] == '') {
            return [
                'is_valid' => false,
                'msg'      => 'Sms Title is Required!!',
            ];
        } else if (!isset($request['sms_body']) || $request['sms_body'] == '') {
            return [
                'is_valid' => false,
                'msg'      => 'Sms Body is Required!!',
            ];
        } else if (!isset($request['sms_to']) || $request['sms_to'] == '') {
            return [
                'is_valid' => false,
                'msg'      => 'Sms To Field is Required!!',
            ];
        }

        if ($request['sms_to'] == 'member') {

            if (!isset($request['branch_id']) || $request['branch_id'] == '') {
                return [
                    'is_valid' => false,
                    'msg'      => 'Branch Field is Required!!',
                ];
            }

            if ($request['send_type_samity'] == 'selected') {

                if (!isset($request['samity_id']) || $request['samity_id'] == '') {
                    return [
                        'is_valid' => false,
                        'msg'      => 'Samity Field is Required!!',
                    ];
                }

            }

        }

        if ($request['sms_to'] == 'employee') {

            if ($request['send_type'] == 'selected') {

                if (!isset($request['branch_id']) || $request['branch_id'] == '') {
                    return [
                        'is_valid' => false,
                        'msg'      => 'Branch Field is Required!!',
                    ];
                }

            }

        }

        if ($request['sms_to'] == 'others') {

            if (!isset($request['others_number']) || $request['others_number'] == '') {
                return [
                    'is_valid' => false,
                    'msg'      => 'Numbers Field is Required!!',
                ];
            }

        }
        return $flag;
    }

    public function delete($id)
    {
        $targetRow            = SmsForward::where('id', $id)->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();

        if ($delete) {
            return response()->json([
                'msg'    => 'Successfully deleted',
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'msg'    => 'Failed to delete',
                'status' => false,
            ], 400);
        }
    }

}
