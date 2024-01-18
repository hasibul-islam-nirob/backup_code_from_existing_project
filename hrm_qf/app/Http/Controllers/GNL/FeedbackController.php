<?php

namespace App\Http\Controllers\GNL;

use DateTime;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use App\Model\GNL\Feedback;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\GNL\Others\CommonController;
use Illuminate\Support\Facades\Date;

class FeedbackController extends Controller
{

    public function getPassport($requestData, $operationType)
    {
        $errorMsg = null;

        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                 'f_title' => 'required',
                //  'f_description' => 'required'
            );

            $attributes = array(
                'date'     => 'date',
                // 'branch_id' => 'Branch'
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);


            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();

            if ($requestData->is_active == 1) { // only view
                $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit', 'send', 'btnHide' => true];
            }

            $errorMsg = $IgnoreArray;
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
            //  dd($request);

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');
            $branchId          = (empty($request->branch_id)) ? null : $request->branch_id;

            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            $totalRecords = Feedback::select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = Feedback::select('count(*) as allcount')->where('is_delete', 0)->where('f_title', 'like', '%' . $searchValue . '%')->count();

            $selBranchArr = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId
            ]);
            $userInfo = Auth::user();

            $allData  = Feedback::from('gnl_feedback as gfeed')
                ->where('gfeed.is_delete', 0)
                ->select('gfeed.*')
                ->when(true, function ($query) use ($columnName, $columnSortOrder, $request) { //Ordering
                    if ($columnName == "f_title") {
                        $query->orderBy("gfeed." . $columnName, $columnSortOrder);
                    }  elseif ($columnName == "f_code") {

                        $query->orderBy("gfeed." . $columnName, $columnSortOrder);
                    } elseif($columnName == "date"){
                        $query->orderBy("gfeed." . $columnName, $columnSortOrder);

                    }

                    if ($columnName == "branch") {
                        $query->join('gnl_branchs as b', function ($join) {
                            $join->on('gfeed.branch_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);
                    }
                     elseif ($columnName == "status") {
                        $query->orderBy('gfeed.is_active', $columnSortOrder);
                    }
                })
                ->when(true, function ($query) use ($request, $userInfo, $searchValue, $selBranchArr) { //Searching
                    if (Common::isSuperUser() == false) {

                        $query->Where('gfeed.created_by', $userInfo['id']);

                        if (!empty($userInfo['id'])) {
                            $query->where('gfeed.created_by', $userInfo['id']);
                        }
                    }
                    if (!empty($searchValue)) {
                        $query->where('gfeed.f_title', 'like', '%' . $searchValue . '%');
                    } if (!empty($searchValue)) {
                        $query->where('gfeed.f_description', 'like', '%' . $searchValue . '%');
                    }
                    if (!empty($selBranchArr)) {
                        $query->whereIn('gfeed.branch_id', $selBranchArr);
                    }

                    if (!empty($request->appl_code)) {
                        $query->where('gfeed.f_code', 'LIKE', "%{$request->appl_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('gfeed.is_active', $request->appl_status);
                    }

                    if (Common::isSuperUser() == false && Common::isDeveloperUser() == false) {

                        ## for all data action
                        if (Common::isActionPermitedForThisUser($actionAllData = 21) == false) {

                            $query->orWhere('gfeed.created_by', $userInfo['id']);

                            if (!empty($userInfo['id'])) {
                                $query->where('gfeed.created_by', $userInfo['id']);
                            }

                            ## for all without ho == 22
                            if (Common::isActionPermitedForThisUser($actionAllData = 22) == true && Common::isHeadOffice() == true) {
                                $query->orWhere('gfeed.branch_id', '<>', $this->hoId);
                            }
                            ## for only ho == 23
                            elseif (Common::isActionPermitedForThisUser($actionAllData = 23) == true && Common::isHeadOffice() == true) {
                                $query->orWhere('gfeed.branch_id', $this->hoId);
                            }
                        }
                    }
                })
                ->skip($start)->take($rowperpage)->select('gfeed.*')->get();

                // dd($allData);
            $data      = array();
            $sno = $start + 1;

            $date=  new DateTime('today')  ;
            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['f_title']            = $row->f_title;
                $data[$key]['f_code']             = $row->f_code;
                $data[$key]['f_description']      = $row->f_description;
                $data[$key]['date']               = (new DateTime($row->date))->format('d-m-Y');
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";


                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0"><i class="fa fa-paper-plane mr-2"></i>Sending</span>';
                }

                $button = '';
                if($row->is_active == 3) {
                    $button .= '<button class="btn btn-sm approve-reject-btn btn-success mr-1" data-btn="approve" data-id="'.encrypt($row->id).'">Approve</button>';
                    $button .= '<button class="btn btn-sm approve-reject-btn btn-danger" data-btn="reject" data-id="'.encrypt($row->id).'">Reject</button>';
                }

                if($row->is_active == 1 && $row->status == 0) {
                    $button .= '<button class="btn btn-sm complete-btn btn-success mr-1" data-btn="approve" data-id="'.encrypt($row->id).'">Complete</button>';
                }
                elseif($row->is_active == 1 && $row->status == 1) {
                    $button .= '<button class="btn btn-sm complete-btn btn-success mr-1" hidden data-btn="approve" data-id="'.encrypt($row->id).'">Completed</button>';
                }


                $data[$key]['status'] = $statusFlag;
                if(Auth::user()-> branch_id == 1) {
                    $data[$key]['button'] = $button;
                }
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

                $sno++;
            }
            // dd($row);

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            // dd($data);
            return response()->json($json_data);
        }
    }



    public function insert(Request $request, $status)

    {
        //  dd($request->all());

        $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                     = new Feedback();
                $appl->f_title            = $request['f_title'];
                $appl->f_code             = Common::generateFeedbackCode(Auth::user()->branch_id);
                $appl->f_description      = $request['f_description'];
                $appl->date               = (new DateTime('now'))->format('Y-m-d');
                $appl->branch_id          = Auth::user()->branch_id;
                $appl->created_by         = Auth::user()->id;
                $appl->is_active          = ($status == 'send') ? 3 : 0 ;
                $appl->attachment         = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'),              'feedback_panel', '') : null;
          // return response()->json($status);
                if ($status == 'send' || $status == 'draft') {

                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode'=> 400,
                            'result_data' => ''
                        ], 400);
                    }

                    $appl->save();
                    DB::commit();
                }

                // dd($appl);
                // $appl->save();

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
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

    public function update(Request $request, $status)
    {
        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                $appl                       = Feedback::findOrFail(decrypt($request->id));
                $appl->f_title              = $request['f_title'];
                $appl->f_description        = $request['f_description'];
                $appl->date                 = (new DateTime('now'))->format('Y-m-d');
                $appl->created_by           = Auth::user()->id;
                $appl->is_active            = ($status == 'send') ? 3 : 0;
                $appl->attachment           = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'), 'feedback_panel', '') : null;

                if ($status == 'send') {

                    $appl->is_active = 3;

                    $appl->update();
                    DB::commit();

                    return response()->json([
                         'message'    => "Application Sent and Approved!!",
                         'status' => 'success',
                         'statusCode' => 200,
                         'result_data' => '',
                        ], 200);
                }
                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => true,
                    'error'  => $e->getMessage(),
                ], 500);
            }
        }
        else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }


    public function get($id)
     {
        $id = decrypt($id);

        $queryData = Feedback::with('branch')->find($id);

        if ($queryData) {
            $responseData = [
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => $queryData
            ];
            return response()->json($responseData, 200);
        } else {
            $responseData = [
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => ''
            ];
            return response()->json($responseData, 500);
        }
    }

    public function updateStatus(Request $request)
    {
        // dd($request->all());
        $validator = $request->validate([
            'status' => 'required',
            'id'     => 'required',
        ]);

        $feedback = Feedback::findOrFail(decrypt($request->id));

        $feedback->is_active = ($request->status == 1) ? 1 : 2;

        $feedback->update();

        return response()->json([
            'message'    => 'Stauts Updated Successfully',
            'status' => 'success',
            'statusCode'=> 200,
            'result_data' => '',
        ], 200);
    }

    public function updateAction(Request $request)
    {
        $validator = $request->validate([
            'id'     => 'required',
        ]);

        $feedback = Feedback::findOrFail(decrypt($request->id));

        $feedback->status = 1;

        $feedback->update();

        return response()->json([
            'message'    => 'Stauts Updated Successfully',
            'status' => 'success',
            'statusCode'=> 200,
            'result_data' => '',
        ], 200);
    }

    public function send($id)
    {
        $appl    = Feedback::find(decrypt($id));

        $permission_for = ($appl->branch_id == 1) ? "ho" : "bo";

         $appl->is_active = 3;
        // $appl->current_stage = null;
        $send=$appl->save();

        // $this->finish($appl, $appl->date);

        if ($send) {
            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Successfully send',
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'statusCode' => 500,
                'message' => 'Error to send application',
                'result_data' => ''
            ], 500);
        }
    }

    public function delete($id)
    {
        $targetRow            = Feedback::where('id', decrypt($id))->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();

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


}

