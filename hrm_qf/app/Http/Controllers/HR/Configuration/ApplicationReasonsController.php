<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApplicationReasonsController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'application' => 'required',
                'reason' => 'required',
            );

            $attributes = array(
                'application'     => 'Application Type',
                'reason'        => 'Reason',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
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

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $search_arr = $request->get('search');

            $searchValue = $search_arr['value'];


            $totalRecords = DB::table('hr_app_reasons')->select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = DB::table('hr_app_reasons')->select('count(*) as allcount')->where('is_delete', 0)->where('reason', 'like', '%' .$searchValue . '%')->count();

            $allData  = DB::table('hr_app_reasons as r')
                ->where('r.is_delete', 0)
                ->join('hr_reporting_boss_event as rbe', function($join){
                    $join->on('rbe.id', '=', 'r.event_id');
                })
                ->select('r.*', 'rbe.event_title')
                ->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = ['view'];

                $data[$key]['id']                 = $sno;
                $data[$key]['reason']        = $row->reason;
                $data[$key]['event']          = $row->event_title;
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;

            }

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);

        }
    }

    public function insert(Request $request)
    {

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {

                DB::table('hr_app_reasons')->insert([
                    'reason' => $request->reason,
                    'event_id' => $request->application,
                    'is_delete' => 0,
                ]);

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function update(Request $request)
    {
        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {

                DB::table('hr_app_reasons')->where('id', decrypt($request->r_id))->update([
                    'reason' => $request->reason,
                    'event_id' => $request->application,
                    'is_delete' => 0,
                ]);

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function delete($id)
    {
        
        $delete = DB::table('hr_app_reasons')->where('id', decrypt($id))->update(['is_delete' => 1]);

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


    public function getData(Request $request)
    {
        $data = array();

        // dd(1, $request->all());

        if ($request->context == 'ApplicationData') {

            $event = DB::table('hr_reporting_boss_event')->where('status', 1)->where('is_delete', 0)->get();
            
            $data = array(
                'appEvent'          => $event,
                // 'branchData'    => $branchData
            );
        }

        return response()->json($data);
    }


    public function get(Request $request)
    {
        $appReasonsData = DB::table('hr_app_reasons')->find(decrypt($request->id));
        $event = DB::table('hr_reporting_boss_event')->where('status', 1)->where('is_delete', 0)->get();

        $data = array(
            'appEvent'          => $event,
            'appData'    => $appReasonsData
        );

        return response()->json($data);
    }

}
