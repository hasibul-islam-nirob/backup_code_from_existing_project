<?php

namespace App\Http\Controllers\HR\Holiday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\GNL\Branch;
use App\Model\GNL\Company;
use App\Model\HR\RescheduleHoliday;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;

class RescheduleHolidayController extends Controller
{
    
    public function getPassport($request, $operationType, $data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'company_id' => 'required',
                // 'branch_id' => 'required',
                // 'somity_id' => 'required',
                'title' => 'required',
                'working_date' => 'required',
                'reschedule_date' => 'required'
                

            );

            $attributes = array(
                // 'company_id' => 'Conpany  is required',
                // 'branch_id' => 'Branch  is required',
                // 'somity_id' => 'somity  is required',
                'title' => 'Conpany  is required',
                'working_date' => 'Holiday Working Date From  is required',
                'reschedule_date' => 'Holiday Reschedule Date To  is required'

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
                0 => 'hr_holiday_reschedule.id',
                1 => 'hr_holiday_reschedule.title',
                2 => 'hr_holiday_reschedule.app_for',
                3 => 'hr_holiday_reschedule.working_date',
                4 => 'hr_holiday_reschedule.reschedule_date',
                5 => 'hr_holiday_reschedule.description',
                6 => 'gnl_companies.comp_name',
            );

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = RescheduleHoliday::where('hr_holiday_reschedule.is_delete', ' =', 0)
                ->select('hr_holiday_reschedule.*',
                    'gnl_companies.comp_name')
                // ->whereIn('hr_holiday_reschedule.branch_id', HRS::getUserAccesableBranchIds())
                ->leftJoin('gnl_companies', 'hr_holiday_reschedule.company_id', '=', 'gnl_companies.id')
                ->where(function ($SpecialHolidayData) use ($search) {
                    if (!empty($search)) {
                        $SpecialHolidayData->where('hr_holiday_reschedule.title', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holiday_reschedule.app_for', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holiday_reschedule.working_date', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holiday_reschedule.reschedule_date', 'LIKE', "%{$search}%")
                            ->orWhere('hr_holiday_reschedule.description', 'LIKE', "%{$search}%")
                            ->orWhere('gnl_companies.comp_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC')
                ->orderBy($order, $dir);

            $tempQueryData = clone $masterQuery;
            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();

            $totalRecords = DB::table('hr_holiday_reschedule')->where('is_delete', 0)->count();
            $totalRecordswithFilter = $totalRecords;

            if (!empty($search)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }
            

            $sl = (int)$request->start + 1;
            $data      = array();

            $current_date = date('Y-m-d', strtotime(Common::systemCurrentDate()));

            foreach ($masterQuery as $key => $Row) {

                $IgnoreArray = [];
                $workingDate = $Row->working_date;
                $rescheduleDate = $Row->reschedule_date;

                // if( $workingDate <= $current_date ){
                //     $IgnoreArray = [ 'delete'];
                // }

                $data[$key]['id']                  = $sl++;
                $data[$key]['title']               = $Row->title;
                $data[$key]['app_for']             = $Row->app_for;
                // $data[$key]['working_date']        = Common::viewDateFormat($Row->working_date);
                $data[$key]['working_date']        = date('d-m-Y', strtotime($Row->working_date));
                // $data[$key]['reschedule_date']     = Common::viewDateFormat($Row->reschedule_date);
                $data[$key]['reschedule_date']     = date('d-m-Y', strtotime($Row->reschedule_date));
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
                // dd($RequestData);

                $requestData['working_date'] = new DateTime($requestData['working_date']);
                $requestData['working_date'] = $requestData['working_date']->format('Y-m-d');

                $requestData['reschedule_date'] = new DateTime($requestData['reschedule_date']);
                $requestData['reschedule_date'] = $requestData['reschedule_date']->format('Y-m-d');

                $isInsert = RescheduleHoliday::create($requestData);

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
        $getData = RescheduleHoliday::where('id', decrypt($request->id))->where('is_delete', 0)->first();
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


    public function update(Request $request){

        //================================================

        if ($request->isMethod('post')) {

            $updateData = RescheduleHoliday::where('id', decrypt($request->edit_id) )->first();
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

                $requestData['working_date'] = new DateTime($requestData['working_date']);
                $requestData['working_date'] = $requestData['working_date']->format('Y-m-d');

                $requestData['reschedule_date'] = new DateTime($requestData['reschedule_date']);
                $requestData['reschedule_date'] = $requestData['reschedule_date']->format('Y-m-d');

                
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



    public function delete($id)
    {
        $deletedData = RescheduleHoliday::where('id', decrypt($id))->first();

        $passport = $this->getPassport(null, $operationType = 'delete', $deletedData);

        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'alert-type' => 'error',
                'statusCode' => 400
            );
            return response()->json($notification, $notification['statusCode']);
        }

        $delete = RescheduleHoliday::where('id', decrypt($id))->update(['is_delete' => 1]);

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

   

}
