<?php

namespace App\Http\Controllers\HR\Holiday;

use DateTime;
use Illuminate\Http\Request;
use App\Model\HR\GovtHoliday;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\CommonService as Common;

class GovtHolidayController extends Controller
{
    public function getPassport($request, $operationType, $data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'gh_title'                          => 'required',
            );

            $attributes = array(
                'gh_title'                          => 'Name',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($errorMsg == null &&  ($operationType == 'store' || $operationType == 'update')) {

            $duplicateQuery = DB::table('hr_holidays_govt')
                ->where([['gh_title', $request->gh_title], ['is_delete', 0]])
                ->where(function ($query) use ($operationType, $data) {
                    if ($operationType == 'update') {
                        $query->where('id', '<>', $data->id);
                    }
                })
                ->count();
            if ($duplicateQuery > 0) {
                $errorMsg = "This Holiday already exist.";
            }
        }


        /*

         ## condition check for bank
        if ($errorMsg == null && ($operationType == 'delete' || $operationType == 'index')) {
            $childData = DB::table('')->where([['bank_id', $data->id], ['is_delete', 0]])->count();

            if ($childData > 0) {
                $errorMsg = "Branchs of Bank Data Exist! Please delete child data first.";
            }
        }

        */

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
                0 => 'gh_date',
                1 => 'gh_title',
                2 => 'gh_description',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = DB::table('hr_holidays_govt')
                ->where('is_delete', 0)
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('gh_title', 'LIKE', "%{$search}%");
                        $query->orWhere('gh_description', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_holidays_govt')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            $sl = (int)$request->start + 1;
            $data      = array();
            $status = "";

            $current_date = date('Y-m-d', strtotime(Common::systemCurrentDate()));

            foreach ($masterQuery as $key => $row) {
                $IgnoreArray = [];

                $start_d = $row->efft_start_date;
                $end_d = $row->efft_end_date;

                if (!empty($end_d) && ($start_d <= $current_date)) {
                    $IgnoreArray = ['delete'];
                }


                if (!empty($end_d) && $end_d <= $current_date) {
                    // $status = '<span style="color: #0fdb50"><i class="fas fa-check mr-2"></i>Active</span>';
                    $status = '<span style="color: #000000"><i class="fas fa-times mr-2"></i>Close</span>';
                    $IgnoreArray = ['edit', 'delete'];
                } else if ($start_d <= $current_date) {
                    $status = '<span style="color: #0fdb50"><i class="fas fa-check mr-2"></i>Active</span>';
                    $IgnoreArray = ['delete'];
                } else if ($start_d > $current_date) {
                    $status = '<span style="color: #000000"><i class="fas fa-times mr-2"></i>In Active</span>';
                }
                // else if(($end_d) && $end_d > $current_date || ){
                //     $status = '<span style="color: #000000"><i class="fas fa-times mr-2"></i>In Active</span>';
                // }

                /* ========================================================== /
                if(  $row->efft_start_date > date('Y-m-d', strtotime(Common::systemCurrentDate())) || $row->gh_date > date('d-m', strtotime(Common::systemCurrentDate())) ){
                    $status = '<span style="color: #1a75ff"><i class="fas fa-check mr-2"></i>Upcoming</span>';

                }else if( !empty($row->efft_end_date) && $row->efft_end_date < date('Y-m-d', strtotime(Common::systemCurrentDate())) ){
                    $status = '<span style="color: #000000"><i class="fas fa-times mr-2"></i>Close</span>';

                }else if($row->gh_date == date('d-m', strtotime(Common::systemCurrentDate()))){
                    $status = '<span style="color: #0fdb50"><i class="fas fa-check mr-2"></i>Active</span>';

                }else if($row->gh_date < date('d-m', strtotime(Common::systemCurrentDate()))  ){
                    $status = '<span style="color: #1a75ff"><i class="fas fa-check mr-2"></i>Upcoming</span>';
                }
                / ========================================================*/


                $data[$key]['id']                   = $sl++;
                $data[$key]['gh_date']              = $row->gh_date;
                $data[$key]['gh_title']             = $row->gh_title;
                $data[$key]['efft_start_date']       = Common::viewDateFormat($row->efft_start_date);
                $data[$key]['efft_end_date']       = Common::viewDateFormat($row->efft_end_date);
                $data[$key]['status']       = $status;
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

                $RequestData = $request->all();

                $sDate = new DateTime($RequestData['efft_start_date']);
                $sDate = $sDate->format('Y-m-d');
                $RequestData['efft_start_date'] = $sDate;

                if (!empty($RequestData['efft_end_date'])) {
                    $eDate = new DateTime($RequestData['efft_end_date']);
                    $eDate = $eDate->format('Y-m-d');
                    $RequestData['efft_end_date'] = $eDate;
                } else {
                    $RequestData['efft_end_date'] = null;
                }

                $isInsert = GovtHoliday::create($RequestData);

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



    public function get(Request $request)
    {

        $getData = GovtHoliday::where('id', decrypt($request->id))->where('is_delete', 0)->first();
        //$getData = EmpDepartment::where('id', decrypt($request->id))->where('is_delete', 0)->first();

        return response()->json($getData);
    }


    public function update(Request $request)
    {
        if ($request->isMethod('post')) {

            $updateData = GovtHoliday::where('id', decrypt($request->edit_id))->first();
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

                $RequestData = $request->all();

                if (!empty($RequestData['efft_start_date'])) {
                    $sDate = new DateTime($RequestData['efft_start_date']);
                    $sDate = $sDate->format('Y-m-d');
                    $RequestData['efft_start_date'] = $sDate;
                }

                if (!empty($RequestData['efft_end_date'])) {
                    $eDate = new DateTime($RequestData['efft_end_date']);
                    $eDate = $eDate->format('Y-m-d');
                    $RequestData['efft_end_date'] = $eDate;
                } else {
                    $RequestData['efft_end_date'] = null;
                }

                $isUpdate = $updateData->update($RequestData);

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
        $deletedData = GovtHoliday::where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = GovtHoliday::where('id', decrypt($id))->update(['is_delete' => 1]);

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

    /*
    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Govt Holiday
    public function index()
    {
        $GovtHolidayData = GovtHoliday::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return view('HR.Holiday.GovtHoliday.index', compact('GovtHolidayData'));
    }

    // Add and Store Govt Holiday
    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'gh_title' => 'required',
                'gh_date' => 'required ',
                'efft_start_date' => 'required ',
            ]);

            $RequestData = $request->all();

            $sDate = new DateTime($RequestData['efft_start_date']);
            $sDate = $sDate->format('Y-m-d');

            $RequestData['efft_start_date'] = $sDate;

            $eDate = new DateTime($RequestData['efft_end_date']);
            $eDate = $eDate->format('Y-m-d');

            $RequestData['efft_end_date'] = $eDate;

            $isInsert = GovtHoliday::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/govtholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/govtholiday')->with($notification);
            }
        } else {

            return view('HR.Holiday.GovtHoliday.add');
        }
    }

    // Edit GovtHoliday
    public function edit(Request $request, $id = null)
    {

        $GovtHolidayData = GovtHoliday::where('id', $id)->first();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'gh_title' => 'required',
                'gh_date' => 'required ',
                'efft_start_date' => 'required ',
            ]);

            $Data = $request->all();

            $sDate = new DateTime($Data['efft_start_date']);
            $sDate = $sDate->format('Y-m-d');

            $Data['efft_start_date'] = $sDate;

            $eDate = new DateTime($Data['efft_end_date']);
            $eDate = $eDate->format('Y-m-d');

            $Data['efft_end_date'] = $eDate;

            $isUpdate = $GovtHolidayData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated GovtHoliday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/govtholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in GovtHoliday',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/govtholiday')->with($notification);
            }
        } else {

            return view('HR.Holiday.GovtHoliday.edit', compact('GovtHolidayData'));
        }
    }

    //View GovtHoliday
    public function view($id = null)
    {

        $GovtHolidayData = GovtHoliday::where('id', $id)->first();

        return view('HR.Holiday.GovtHoliday.view', compact('GovtHolidayData'));
    }

    // Soft Delete GovtHoliday
    public function delete($id = null)
    {

        $GovtHolidayData = GovtHoliday::where('id', $id)->first();
        $GovtHolidayData->is_delete = 1;

        $delete = $GovtHolidayData->save();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // Parmanent Delete GovtHoliday
    public function destroy($id = null)
    {
        $GovtHolidayData = GovtHoliday::where('id', $id)->first();
        $delete = $GovtHolidayData->delete();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // Publish/Unpublish GovtHoliday
    public function isactive($id = null)
    {
        $GovtHolidayData = GovtHoliday::where('id', $id)->first();
        if ($GovtHolidayData->is_active == 1) {
            $GovtHolidayData->is_active = 0;
        } else {
            $GovtHolidayData->is_active = 1;
        }
        $Status = $GovtHolidayData->save();

        if ($Status) {
            $notification = array(
                'message' => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }
    */
}
