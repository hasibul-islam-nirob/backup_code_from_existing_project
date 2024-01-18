<?php

namespace App\Http\Controllers\HR\Holiday;

use DateTime;
use Illuminate\Http\Request;
use App\Model\HR\CompanyHoliday;
use App\Model\GNL\Company;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use App\Services\PosService as POSS;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;

use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;

class CompHolidayController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function getPassport($request, $operationType, $data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'ch_title' => 'required',
                // 'branch_from_id' => 'required',
                // 'branch_to_id' => 'required',
                // 'employee_id' => 'required',
                // 'transfer_date'        => 'required',
                // 'exp_effective_date'     => 'required',

                'company_id'  => 'required',
                'ch_title'    => 'required',
                'ch_eff_date' => 'required',
                // 'branch_arr' => 'branch_arr',

            );

            $attributes = array(
                'ch_title' => 'Name filed is required',
                'ch_eff_date'     => 'Expected effective date',
                // 'transfer_date'        => 'Transfer Date',
                // 'branch_from_id' => 'Branch From',
                // 'branch_to_id' => 'Branch To',
                // 'employee_id' => 'Employee',
                'company_id' => 'Conpany  is required'

            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }


        if ($errorMsg == null &&  ($operationType == 'store' || $operationType == 'update')) {

            $request['ch_eff_date'] = (new DateTime($request['ch_eff_date']))->format('Y-m-d');

            $duplicateQuery = DB::table('hr_holidays_comp')
                ->where([['branch_arr', $request->branch_arr], ['ch_day', $request->ch_day], ['is_delete', 0]])
                ->where(function ($query) use ($operationType, $data) {
                    if ($operationType == 'update') {
                        $query->where('id', '<>', $data->edit_id);
                        $query->orWhere([ ['ch_day', $data->ch_day], ['ch_eff_date', $data->ch_eff_date] ,['is_delete', 0]]);
                    }
                })
                ->count();

            if ($duplicateQuery > 0) {
                $errorMsg = "This Holiday already exist.";
            }
        }

        if ($operationType == 'index') {

            $ignoreArray = array();

            if ($request->ch_eff_date < date('Y-m-d')) {
                $ignoreArray = ['delete', 'edit', 'message' => "Today date is gretter effective date."];
            }

            $errorMsg = $ignoreArray;
        }

        // if ($operationType == 'delete') {
        //     if ($request->ch_eff_date < date('Y-m-d')) {
        //         $errorMsg = "Today date is gretter effective date.";
        //     }
        // }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request){

        if ($request->isMethod('post')) {
            
            $columns = array(
                0 => 'hr_holidays_comp.ch_title',
                1 => 'hr_holidays_comp.ch_day',
                2 => 'hr_holidays_comp.ch_description',
                3 => 'hr_holidays_comp.ch_eff_date',
                4 => 'hr_holidays_comp.company_id',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = DB::table('hr_holidays_comp')
                ->join('gnl_companies', 'gnl_companies.id', 'hr_holidays_comp.company_id')
                ->select('gnl_companies.comp_name AS comp_name','hr_holidays_comp.*')
                ->where('hr_holidays_comp.is_delete', 0)
                ->where(function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->where('hr_holidays_comp.ch_title', 'LIKE', "%{$search}%");
                        $query->orWhere('hr_holidays_comp.ch_description', 'LIKE', "%{$search}%");
                        $query->orWhere('hr_holidays_comp.ch_day', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_holidays_comp')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }
            

            $sl = (int)$request->start + 1;
            $data      = array();
            $current_date = date('Y-m-d', strtotime(Common::systemCurrentDate()));

            foreach ($masterQuery as $key => $row) {

                $application_for = "";
                $IgnoreArray = [];

                if( $row->ch_eff_date <= $current_date ){
                    $IgnoreArray = ['edit', 'delete'];
                }

                if( $row->branch_arr == "1"){
                    $application_for = "Head Office";
                }else if( $row->branch_arr == "0" || $row->branch_arr == null){
                    $application_for = "All Branch";
                }
                else {
                    $application_for = "Branches";
                }

                $data[$key]['id']                    = $sl++;
                $data[$key]['ch_title']              = $row->ch_title;
                $data[$key]['ch_day']                = $row->ch_day;
                $data[$key]['ch_eff_date']           = Common::viewDateFormat($row->ch_eff_date);
                $data[$key]['comp_name']             = $row->comp_name;
                $data[$key]['application_for']       = $application_for;
                $data[$key]['action']                = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

            }

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                "data"            => $data,
            );

            return response()->json($json_data);

        }
    }


    public function getData(Request $request)
    {
        $data = array();

        // dd(1, $request->all());

        if ($request->context == 'weekDayBranchData') {

            $days = Common::getWeekdayName();

            $branchData = Common::getAllBranch();

            // $branchData = DB::table('gnl_branchs')
            //     ->where([['is_delete', 0], ['is_active', 1]])
            //     ->where('id','<>',1)
            //     ->whereIn('id', HRS::getUserAccesableBranchIds())
            //     ->select('id', 'branch_name', 'branch_code')
            //     ->orderBy('id', 'ASC')
            //     ->get();
            //     // ->toArray();

            $data = array(
                'days'          => $days,
                'branchData'    => $branchData
            );
        }

        return response()->json($data);
    }


    // function addOthersInfo(){

    //     $days = Common::getWeekdayName();

    //     //$selectBranchArr = array();
    //     $branchData = DB::table('gnl_branchs')
    //         ->where([['is_delete', 0], ['is_active', 1]])
    //         ->where('id','<>',1)
    //         ->whereIn('id', HRS::getUserAccesableBranchIds())
    //         ->select('id', 'branch_name', 'branch_code')
    //         ->orderBy('id', 'ASC')
    //         ->get();

    //     $data = array(
    //         'days'          => $days,
    //         'branchData'    => $branchData
    //     );


    //     return response()->json($data);
    // }



    
    public function insert(Request $request){

        //================================================

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

                //----------------------
                $requestData                = $request->all();

                $requestData['ch_eff_date'] = (new DateTime($requestData['ch_eff_date']))->format('Y-m-d');
                $requestData['ch_day'] = implode(",", $requestData['ch_day']);

                if($requestData['branch_id'] == "-1"){
                    $requestData['branch_arr'] = implode(',', $requestData['branch_array']);
                }
                else {
                    $requestData['branch_arr'] = $requestData['branch_id'];
                }

                $isInsert = CompanyHoliday::create($requestData);

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
        $getData = CompanyHoliday::where('id', decrypt($request->id))->where('is_delete', 0)->first();
        $days = Common::getWeekdayName();
        $branchData = Common::getAllBranch();

        $data = array(
            'getData'           => $getData,
            'days'          => $days,
            'branchData'    => $branchData
        );

        return response()->json($data);
    }




    public function update(Request $request){

        //================================================

        if ($request->isMethod('post')) {

            $updateData = CompanyHoliday::where('id', decrypt($request->edit_id) )->first();
            $passport = $this->getPassport($request, $operationType = 'store', $updateData);

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

                //----------------------
                $requestData                = $request->all();

                $requestData['ch_eff_date'] = (new DateTime($requestData['ch_eff_date']))->format('Y-m-d');
                $requestData['ch_day'] = implode(",", $requestData['ch_day']);

                if($requestData['branch_id'] == "-1"){
                    $requestData['branch_arr'] = implode(',', $requestData['branch_array']);
                }
                else {
                    $requestData['branch_arr'] = $requestData['branch_id'];
                }

                $isInsert = $updateData->update($requestData);

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
    

                //----------------------



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
        $deletedData = CompanyHoliday::where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = CompanyHoliday::where('id', decrypt($id))->update(['is_delete' => 1]);

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



    public function CheckDayEnd(Request $request)
    {

        if ($request->ajax()) {

            $selectedDate = (!empty($request->get('startDateFrom'))) ? $request->get('startDateFrom') : null;

            if (empty($selectedDate)) {
                return response()->json(array("exists" => 1, "Table" => 'emptydata'));
            }

            // $selectedDate = (new DateTime($request->get('startDateFrom')))->format('Y-m-d');

            return response()->json($this->hasDayEnd($selectedDate));
        }
    }


    public function hasDayEnd($selectedDate)
    {
        $selectedDate = (new DateTime($selectedDate))->format('Y-m-d');

        if (DB::getSchemaBuilder()->hasTable('pos_day_end')) {

            $queryData1 = DB::table('pos_day_end')
                ->where([['is_active', 0], ['is_delete', 0]])
                ->where([['branch_date', '>=', $selectedDate]])
                ->count();

            if ($queryData1 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }

        }

        if (DB::getSchemaBuilder()->hasTable('acc_day_end')) {

            $queryData2 = DB::table('acc_day_end')
                ->where([['is_active', 0], ['is_delete', 0]])
                ->where('branch_date', '>=', $selectedDate)
                ->count();

            if ($queryData2 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }
        }

        if (DB::getSchemaBuilder()->hasTable('mfn_day_end')) {

            $queryData2 = DB::table('mfn_day_end')
                ->where([['isActive', 0], ['is_delete', 0]])
                ->where('date', '>=', $selectedDate)
                ->count();

            if ($queryData2 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }
        }

    }

    /*
    // List of Company Holiday
    public function index(Request $request)
    {

        if ($request->ajax()) {

            // Datatable Pagination Variable
            $limit  = $request->input('length');
            $start  = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = CompanyHoliday::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('ch_title', 'LIKE', "%{$search}%")
                            ->orWhere('ch_day', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData   = CompanyHoliday::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i       = $start;
            foreach ($masterQuery as $key => $Row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($Row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                // dd($IgnoreArray);

                $DataSet[] = [
                    'id'             => ++$i,
                    'ch_title'       => $Row->ch_title,
                    'ch_day'         => $Row->ch_day,
                    'ch_description' => $Row->ch_description,
                    'ch_eff_date'    => (new DateTime($Row->ch_eff_date))->format('d-m-y'),
                    'comp_name'      => (!empty($Row->company['comp_name'])) ? $Row->company['comp_name'] : '',
                    'action'         => Role::roleWiseArray($this->GlobalRole, $Row->id, $IgnoreArray),
                ];
            }
            echo json_encode([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $DataSet,
            ]);
        } else {
            return view('HR.Holiday.CompanyHoliday.index');
        }

        // $CompHolidayData = CompanyHoliday::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        // return view('HR.Holiday.CompanyHoliday.index', compact('CompHolidayData'));
    }

    // Add and Store Company Holiday
    public function add(Request $request)
    {
        $days = Common::getWeekdayName();
        // $days = implode(',', $days);

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'company_id'  => 'required',
                'ch_title'    => 'required',
                'ch_eff_date' => 'required',
            ]);

            $RequestData                = $request->all();

            if(isset($RequestData['branch_array'])){
                $RequestData['branch_arr'] = implode(',', $RequestData['branch_array']);
            } else {
                $RequestData['branch_arr'] = '';
                # code...
            }

            $RequestData['branch_arr'] = implode(',', $RequestData['branch_array']);

            $RequestData['ch_eff_date'] = new DateTime($RequestData['ch_eff_date']);
            $RequestData['ch_eff_date'] = $RequestData['ch_eff_date']->format('Y-m-d');

            $RequestData['ch_day'] = implode(",", $RequestData['ch_day']);

            $isInsert = CompanyHoliday::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message'    => 'Successfully Inserted Company Holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to insert data in Company Holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/compholiday')->with($notification);
            }
        } else {

            return view('HR.Holiday.CompanyHoliday.add', compact('days'));
        }
    }

    // Edit Company Holiday
    public function edit(Request $request, $id = null)
    {

        $days            = Common::getWeekdayName();
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'company_id'  => 'required',
                'ch_title'    => 'required',
                'ch_day'      => 'required',
                'ch_eff_date' => 'required',
            ]);

            $Data                = $request->all();

            if(isset($Data['branch_array'])){
                $Data['branch_arr'] = implode(',', $Data['branch_array']);
            } else {
                $Data['branch_arr'] = '';
                # code...
            }

            $Data['branch_arr'] = implode(',', $Data['branch_array']);
            $Data['ch_eff_date'] = new DateTime($Data['ch_eff_date']);
            $Data['ch_eff_date'] = $Data['ch_eff_date']->format('Y-m-d');

            $Data['ch_day'] = implode(",", $Data['ch_day']);

            $isUpdate = $CompHolidayData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated Company holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update data in Company holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/compholiday')->with($notification);
            }
        } else {

            return view('HR.Holiday.CompanyHoliday.edit', compact('CompHolidayData', 'days'));
        }
    }

    //View Company Holiday
    public function view($id = null)
    {

        $days            = Common::getWeekdayName();
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        $branchArr = explode(',', $CompHolidayData->branch_arr);
        $branchData = POSS::fnForBranchData($branchArr);
        $branchData = implode(", ", $branchData);

        return view('HR.Holiday.CompanyHoliday.view', compact('CompHolidayData', 'days', 'branchData'));
    }

    // Soft Delete Company Holiday
    public function delete($id = null)
    {
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        if (empty($CompHolidayData)) {
            $notification = array(
                'message'    => "Data not found!",
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }

        $passport = $this->getPassport($CompHolidayData, 'delete');
        if ($passport['isValid'] == false) {
            $IgnoreArray = $passport['message'];

            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }

        $CompHolidayData->is_delete = 1;
        $delete = $CompHolidayData->save();

        if ($delete) {
            $notification = array(
                'message'    => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message'    => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // Parmanent Delete Company Holiday
    public function destroy($id = null)
    {
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();
        $delete          = $CompHolidayData->delete();

        if ($delete) {
            $notification = array(
                'message'    => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message'    => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    // Publish/Unpublish Company Holiday
    public function isactive($id = null)
    {
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        if ($CompHolidayData->is_active == 1) {

            $CompHolidayData->is_active = 0;
            # code...
        } else {

            $CompHolidayData->is_active = 1;
        }

        $Status = $CompHolidayData->save();

        if ($Status) {
            $notification = array(
                'message'    => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message'    => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function CheckDayEnd(Request $request)
    {

        if ($request->ajax()) {

            $selectedDate = (!empty($request->get('startDateFrom'))) ? $request->get('startDateFrom') : null;

            if (empty($selectedDate)) {
                return response()->json(array("exists" => 1, "Table" => 'emptydata'));
            }

            // $selectedDate = (new DateTime($request->get('startDateFrom')))->format('Y-m-d');

            return response()->json($this->hasDayEnd($selectedDate));
        }
    }

    public function hasDayEnd($selectedDate)
    {
        $selectedDate = (new DateTime($selectedDate))->format('Y-m-d');

        if (DB::getSchemaBuilder()->hasTable('pos_day_end')) {

            $queryData1 = DB::table('pos_day_end')
                ->where([['is_active', 0], ['is_delete', 0]])
                ->where([['branch_date', '>=', $selectedDate]])
                ->count();

            if ($queryData1 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }

        }

        if (DB::getSchemaBuilder()->hasTable('acc_day_end')) {

            $queryData2 = DB::table('acc_day_end')
                ->where([['is_active', 0], ['is_delete', 0]])
                ->where('branch_date', '>=', $selectedDate)
                ->count();

            if ($queryData2 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }
        }

        if (DB::getSchemaBuilder()->hasTable('mfn_day_end')) {

            $queryData2 = DB::table('mfn_day_end')
                ->where([['isActive', 0], ['is_delete', 0]])
                ->where('date', '>=', $selectedDate)
                ->count();

            if ($queryData2 > 0) {
                return array("exists" => 1, "Table" => 'DayEnd');
            }
        }

    }
    */

}
