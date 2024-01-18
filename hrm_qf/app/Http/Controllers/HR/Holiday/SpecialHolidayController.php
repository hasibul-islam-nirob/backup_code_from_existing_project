<?php

namespace App\Http\Controllers\HR\Holiday;

use App\Http\Controllers\Controller;
use App\Model\GNL\Branch;
use App\Model\GNL\Company;
use App\Model\HR\SpecialHoliday;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;

class SpecialHolidayController extends Controller
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
                // 'company_id' => 'required',
                // 'branch_id' => 'required',
                // 'somity_id' => 'required',
                'sh_title' => 'required',
                'sh_date_from' => 'required',
                'sh_date_to' => 'required'


            );

            $attributes = array(
                // 'company_id' => 'Conpany  is required',
                // 'branch_id' => 'Branch  is required',
                // 'somity_id' => 'somity  is required',
                'sh_title' => 'Conpany  is required',
                'sh_date_from' => 'Holiday Date From  is required',
                'sh_date_to' => 'Holiday Date To  is required'

            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
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
                0 => 'hr_holidays_special.id',
                1 => 'hr_holidays_special.sh_title',
                2 => 'hr_holidays_special.sh_app_for',
                3 => 'hr_holidays_special.sh_date_from',
                4 => 'hr_holidays_special.sh_date_to',
                5 => 'hr_holidays_special.sh_description',
                6 => 'gnl_companies.comp_name',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = SpecialHoliday::where('hr_holidays_special.is_delete', ' =', 0)
                ->select('hr_holidays_special.*',
                    'gnl_companies.comp_name')
                // ->whereIn('hr_holidays_special.branch_id', HRS::getUserAccesableBranchIds())
                ->leftJoin('gnl_companies', 'hr_holidays_special.company_id', '=', 'gnl_companies.id')
                ->where(function ($SpecialHolidayData) use ($search) {
                    if (!empty($search)) {
                        $SpecialHolidayData->where('hr_holidays_special.sh_title', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_app_for', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_date_from', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_date_to', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_description', 'LIKE', "%{$search}%")
                            ->orWhere('gnl_companies.comp_name', 'LIKE', "%{$search}%");
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

            foreach ($masterQuery as $key => $Row) {

                $IgnoreArray = [];
                $start_d = $Row->sh_date_from;
                $end_d = $Row->sh_date_to;

                if( $start_d <= $current_date ){
                    $IgnoreArray = ['edit', 'delete'];
                }

                $data[$key]['id']                  = $sl++;
                $data[$key]['sh_title']            = $Row->sh_title;
                $data[$key]['sh_app_for']          = $Row->sh_app_for;
                $data[$key]['sh_date_from']        = Common::viewDateFormat($Row->sh_date_from);
                $data[$key]['sh_date_to']          = Common::viewDateFormat($Row->sh_date_to);
                $data[$key]['comp_name']           = $Row->comp_name;
                $data[$key]['action']              = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($Row->id), $IgnoreArray);

            }

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                "data"              => $data,
            );

            return response()->json($json_data);

        }
    }



    public function getData(Request $request)
    {
        $data = array();

        if ($request->context == 'CompanyDataBranchData') {

            $CompanyData = DB::table('gnl_companies')
                ->where([['is_delete', 0], ['is_active', 1] ])
                ->selectRaw('CONCAT(comp_name, " [", comp_code, "]") AS comp_name, id')
                ->pluck('comp_name', 'id')
                ->toArray();

            $branchData = Common::getAllBranch();

            $data = array(
                'companyData'   => $CompanyData,
                'branchData'    => $branchData
            );
        }

        return response()->json($data);

    }


    public function get(Request $request)
    {
        $getData = SpecialHoliday::where('id', decrypt($request->id))->where('is_delete', 0)->first();
        $CompanyData = DB::table('gnl_companies')
                ->where([['is_delete', 0], ['is_active', 1] ])
                ->selectRaw('CONCAT(comp_name, " [", comp_code, "]") AS comp_name, id')
                ->pluck('comp_name', 'id')
                ->toArray();
        $branchData = Common::getAllBranch();

        $data = array(
            'getData'       => $getData,
            'companyData'   => $CompanyData,
            'branchData'    => $branchData
        );

        return response()->json($data);
    }


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
                $requestData = $request->all();

                if($requestData['sh_app_for'] == 'branch'){
                    $requestData['company_id'] = Common::getCompanyId();
                }

                $requestData['sh_date_from'] = new DateTime($requestData['sh_date_from']);
                $requestData['sh_date_from'] = $requestData['sh_date_from']->format('Y-m-d');

                $requestData['sh_date_to'] = new DateTime($requestData['sh_date_to']);
                $requestData['sh_date_to'] = $requestData['sh_date_to']->format('Y-m-d');

                $isInsert = SpecialHoliday::create($requestData);

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

        //================================================

        if ($request->isMethod('post')) {

            $updateData = SpecialHoliday::where('id', decrypt($request->edit_id) )->first();
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

                if($requestData['sh_app_for'] == 'branch'){
                    $requestData['company_id'] = Common::getCompanyId();
                }

                $requestData['sh_date_from'] = new DateTime($requestData['sh_date_from']);
                $requestData['sh_date_from'] = $requestData['sh_date_from']->format('Y-m-d');

                $requestData['sh_date_to'] = new DateTime($requestData['sh_date_to']);
                $requestData['sh_date_to'] = $requestData['sh_date_to']->format('Y-m-d');

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
        $deletedData = SpecialHoliday::where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = SpecialHoliday::where('id', decrypt($id))->update(['is_delete' => 1]);

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

            $startDateFrom = new DateTime($request->get('startDateFrom'));
            $startDateTo   = new DateTime($request->get('startDateTo'));
            $tergateId  = empty($request->get('tergateId')) ? null : $request->get('tergateId') ;
            $branchId  = empty($request->get('branchId')) ? null : $request->get('branchId') ;

            if (DB::getSchemaBuilder()->hasTable('mfn_day_end')) {
                $queryData1 = DB::table('mfn_day_end')
                    ->where('is_delete', 0)
                    ->whereBetween('date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->where(function($query) use ($branchId){
                        if(!empty($branchId)){
                            $query->where('branchId', $branchId);
                        }
                    })
                    ->count();

                if ($queryData1 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            if (DB::getSchemaBuilder()->hasTable('pos_day_end')) {
                $queryData1 = DB::table('pos_day_end')
                    ->where('is_delete', 0)
                    ->where(function($query) use ($branchId){
                        if(!empty($branchId)){
                            $query->where('branch_id', $branchId);
                        }
                    })
                    ->whereBetween('branch_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->count();

                if ($queryData1 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            if (DB::getSchemaBuilder()->hasTable('acc_day_end')) {
                $queryData2 = DB::table('acc_day_end')
                    ->where('is_delete', 0)
                    ->where(function($query) use ($branchId){
                        if(!empty($branchId)){
                            $query->where('branch_id', $branchId);
                        }
                    })
                    ->whereBetween('branch_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->count();

                if ($queryData2 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            $queryData3 = null;
            $queryData5 = null;
            // // // MFN Day End, HR Day End, INV er day end
            // ->whereBetween('gh_date', [$startDateFrom->format('d-m'), $startDateTo->format('d-m')])
            if (DB::getSchemaBuilder()->hasTable('hr_holidays_govt')) {
                $queryData3 = DB::table('hr_holidays_govt')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->get();
            }

            // if (DB::getSchemaBuilder()->hasTable('hr_holidays_comp')) {
            //     $queryData5 = DB::table('hr_holidays_comp')
            //         ->where([['is_active', 1], ['is_delete', 0]])
            //         ->where('ch_eff_date', '=', $startDateFrom->format('Y-m-d'))
            //     //->whereBetween('ch_eff_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
            //         ->orderBy('ch_eff_date', 'desc')
            //         ->first();
            // }

            $holidaybar = array();
            if (!empty($queryData5)) {
                $holidaybar = explode(',', $queryData5->ch_day);
            }

            $tempFromDate = clone $startDateFrom;
            $tempToDate   = clone $startDateTo;

            while ($tempFromDate <= $tempToDate) {

                $tempdate     = date_format($tempFromDate, 'l');
                $tempDayMonth = date_format($tempFromDate, 'd-m');

                // dd($tempdate, $tempDayMonth, $tempFromDate, $tempToDate);

                if (!empty($holidaybar) && in_array($tempdate, $holidaybar)) {
                    // company holiday day check if exist return
                    return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                    break;
                }

                if (!empty($queryData3) && $queryData3->where('gh_date', $tempDayMonth)->count() > 0) {
                    // govt holiday check if exist return
                    return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                    break;
                }

                if (DB::getSchemaBuilder()->hasTable('hr_holidays_special')) {
                    $queryData4 = DB::table('hr_holidays_special')
                        ->where([['is_active', 1], ['is_delete', 0]])
                        ->where(function($query) use ($tergateId){
                            if(!empty($tergateId)){
                                $query->where('id', '<>', $tergateId);
                            }
                        })
                        ->where([
                            ['sh_date_from', '<=', $tempFromDate->format('Y-m-d')],
                            ['sh_date_to', '>=', $tempFromDate->format('Y-m-d')] ])
                        ->count();

                    if ($queryData4 > 0) {
                        return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                        break;

                    }else{
                        return response()->json(array("exists" => 0, "Table" => 'DayNot'));
                    }
                }

                $tempFromDate->modify('+1 day');

            }
        }
    }

    /*
    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $columns = array(
                0 => 'hr_holidays_special.id',
                1 => 'hr_holidays_special.sh_title',
                2 => 'hr_holidays_special.sh_app_for',
                3 => 'hr_holidays_special.sh_date_from',
                4 => 'hr_holidays_special.sh_date_to',
                5 => 'hr_holidays_special.sh_description',
                6 => 'gnl_companies.comp_name',
                7 => 'action',
            );
            // Datatable Pagination Variable
            $totalData     = SpecialHoliday::where('hr_holidays_special.is_delete', '=', 0)->count();
            $totalFiltered = $totalData;
            $limit         = $request->input('length');
            $start         = $request->input('start');
            $order         = $columns[$request->input('order.0.column')];
            $dir           = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            // Query
            $SpecialHolidayData = SpecialHoliday::where('hr_holidays_special.is_delete', ' =', 0)
                ->select('hr_holidays_special.*',
                    'gnl_companies.comp_name')
            // ->whereIn('hr_holidays_special.branch_id', HRS::getUserAccesableBranchIds())
                ->leftJoin('gnl_companies', 'hr_holidays_special.company_id', '=', 'gnl_companies.id')
                ->where(function ($SpecialHolidayData) use ($search) {
                    if (!empty($search)) {
                        $SpecialHolidayData->where('hr_holidays_special.sh_title', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_app_for', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_date_from', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_date_to', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holidays_special.sh_description', 'LIKE', "%{$search}%")
                            ->orWhere('gnl_companies.comp_name', 'LIKE', "%{$search}%");
                    }
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy('hr_holidays_special.id', 'DESC')
                ->orderBy($order, $dir)
                ->get();

            if (!empty($search)) {
                $totalFiltered = count($SpecialHolidayData);
            }

            $DataSet = array();
            $i       = 0;

            foreach ($SpecialHolidayData as $Row) {
                $TempSet = array();
                $TempSet = [
                    // 'id' => $Row->id,
                    'sid'            => ++$i,
                    'sh_title'       => $Row->sh_title,
                    'sh_app_for'     => $Row->sh_app_for,
                    'sh_date_from'   => date('d-m-Y', strtotime($Row->sh_date_from)),
                    'sh_date_to'     => date('d-m-Y', strtotime($Row->sh_date_to)),
                    'sh_description' => $Row->sh_description,
                    'comp_name'      => $Row->comp_name,
                    'action'         => Role::roleWiseArray($this->GlobalRole, $Row->id),
                ];

                $DataSet[] = $TempSet;
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $DataSet,
            );

            return response()->json($json_data);
        }
        return view('HR.Holiday.SpecialHoliday.index');
    }

    // Add and Store Special Holiday
    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                // 'company_id' => 'required',
                // 'branch_id' => 'required',
                'sh_app_for'   => 'required',
                'sh_date_from' => 'required',
                'sh_date_to'   => 'required ',
                'sh_title'     => 'required',
            ]);

            $RequestData = $request->all();
            // dd($RequestData);

            $RequestData['sh_date_from'] = new DateTime($RequestData['sh_date_from']);
            $RequestData['sh_date_from'] = $RequestData['sh_date_from']->format('Y-m-d');

            $RequestData['sh_date_to'] = new DateTime($RequestData['sh_date_to']);
            $RequestData['sh_date_to'] = $RequestData['sh_date_to']->format('Y-m-d');

            $isInsert = SpecialHoliday::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message'    => 'Successfully Inserted New Special Holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/specialholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to insert data in Special Holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/specialholiday')->with($notification);
            }
        } else {

            $CompanyData = Company::where('is_delete', 0)->orderBy('id', 'DESC')->get();

            $BranchData = Branch::where(['is_delete' => 0, 'is_approve' => 1])
                ->orderBy('branch_code', 'ASC')
                ->get();

            return view('HR.Holiday.SpecialHoliday.add', compact('CompanyData', 'BranchData'));
        }
    }

    // Edit Special Holiday
    public function edit(Request $request, $id = null)
    {

        $SpecialHolidayData = SpecialHoliday::where('id', $id)->first();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'sh_app_for'   => 'required',
                'sh_date_from' => 'required',
                'sh_date_to'   => 'required ',
            ]);

            $Data = $request->all();

            $Data['sh_date_from'] = new DateTime($Data['sh_date_from']);
            $Data['sh_date_from'] = $Data['sh_date_from']->format('Y-m-d');

            $Data['sh_date_to'] = new DateTime($Data['sh_date_to']);
            $Data['sh_date_to'] = $Data['sh_date_to']->format('Y-m-d');

            $isUpdate = $SpecialHolidayData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated Special Holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('hr/specialholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update data in Special Holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('hr/specialholiday')->with($notification);
            }
        } else {

            $CompanyData = Company::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            $BranchData  = Branch::where(['is_delete' => 0, 'is_approve' => 1])
                ->orderBy('branch_code', 'ASC')
                ->get();
            return view('HR.Holiday.SpecialHoliday.edit', compact('SpecialHolidayData', 'CompanyData', 'BranchData'));
        }
    }

    //View Special Holiday
    public function view($id = null)
    {

        $SpecialHolidayData = SpecialHoliday::where('id', $id)->first();
        $CompanyData        = Company::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        $BranchData         = Branch::where(['is_delete' => 0, 'is_approve' => 1])
            ->orderBy('branch_code', 'ASC')
            ->get();
        return view('HR.Holiday.SpecialHoliday.view', compact('SpecialHolidayData', 'CompanyData', 'BranchData'));
    }

    // Soft Delete Special Holiday
    public function delete($id = null)
    {

        $SpecialHolidayData            = SpecialHoliday::where('id', $id)->first();
        $SpecialHolidayData->is_delete = 1;

        $delete = $SpecialHolidayData->save();

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

    // Parmanent Delete Special Holiday
    public function destroy($id = null)
    {
        $SpecialHolidayData = SpecialHoliday::where('id', $id)->first();
        $delete             = $SpecialHolidayData->delete();

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

    // Publish/Unpublish Special Holiday
    public function isactive($id = null)
    {
        $SpecialHolidayData = SpecialHoliday::where('id', $id)->first();
        if ($SpecialHolidayData->is_active == 1) {
            $SpecialHolidayData->is_active = 0;
        } else {
            $SpecialHolidayData->is_active = 1;
        }
        $Status = $SpecialHolidayData->save();

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

            $startDateFrom = new DateTime($request->get('startDateFrom'));
            $startDateTo   = new DateTime($request->get('startDateTo'));

            if (DB::getSchemaBuilder()->hasTable('mfn_day_end')) {
                $queryData1 = DB::table('mfn_day_end')
                    ->where('is_delete', 0)
                    ->whereBetween('date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->count();

                if ($queryData1 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            if (DB::getSchemaBuilder()->hasTable('pos_day_end')) {
                $queryData1 = DB::table('pos_day_end')
                    ->where([['is_active', 0], ['is_delete', 0]])
                    ->whereBetween('branch_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->count();

                if ($queryData1 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            if (DB::getSchemaBuilder()->hasTable('acc_day_end')) {
                $queryData2 = DB::table('acc_day_end')
                    ->where([['is_active', 0], ['is_delete', 0]])
                    ->whereBetween('branch_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->count();

                if ($queryData2 > 0) {
                    return response()->json(array("exists" => 1, "Table" => 'DayEnd'));
                }
            }

            $queryData3 = null;
            $queryData5 = null;
            // // // MFN Day End, HR Day End, INV er day end
            // ->whereBetween('gh_date', [$startDateFrom->format('d-m'), $startDateTo->format('d-m')])
            if (DB::getSchemaBuilder()->hasTable('hr_holidays_govt')) {
                $queryData3 = DB::table('hr_holidays_govt')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->get();
            }

            if (DB::getSchemaBuilder()->hasTable('hr_holidays_comp')) {
                $queryData5 = DB::table('hr_holidays_comp')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('ch_eff_date', '<=', $startDateFrom->format('Y-m-d'))
                //->whereBetween('ch_eff_date', [$startDateFrom->format('Y-m-d'), $startDateTo->format('Y-m-d')])
                    ->orderBy('ch_eff_date', 'desc')
                    ->first();
            }

            $holidaybar = array();
            if (!empty($queryData5)) {
                $holidaybar = explode(',', $queryData5->ch_day);
            }

            $tempFromDate = clone $startDateFrom;
            $tempToDate   = clone $startDateTo;

            while ($tempFromDate <= $tempToDate) {

                $tempdate     = date_format($tempFromDate, 'l');
                $tempDayMonth = date_format($tempFromDate, 'd-m');

                // dd($tempdate, $tempDayMonth, $tempFromDate, $tempToDate);

                if (!empty($holidaybar) && in_array($tempdate, $holidaybar)) {
                    // company holiday day check if exist return
                    return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                    break;
                }

                if (!empty($queryData3) && $queryData3->where('gh_date', $tempDayMonth)->count() > 0) {
                    // govt holiday check if exist return
                    return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                    break;
                }

                if (DB::getSchemaBuilder()->hasTable('hr_holidays_special')) {
                    $queryData4 = DB::table('hr_holidays_special')
                        ->where([['is_active', 1], ['is_delete', 0]])
                        ->where([['sh_date_from', '<=', $tempFromDate->format('Y-m-d')], ['sh_date_to', '>=', $tempFromDate->format('Y-m-d')]])
                        ->count();

                    if ($queryData4 > 0) {
                        return response()->json(array("exists" => 1, "Table" => 'Holiday'));
                        break;
                    }
                }

                $tempFromDate->modify('+1 day');

            }
        }
    }
    */

}
