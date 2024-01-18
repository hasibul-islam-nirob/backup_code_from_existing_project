<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use Illuminate\Http\Request;
use App\Model\GNL\CompanyHoliday;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
class CompHolidayController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Company Holiday
    public function index(Request $request)
    {
        if ($request->ajax()){

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
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
            $totalData = CompanyHoliday::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            $IgnoreArray = [];
            foreach ($masterQuery as $key => $Row){
                
                $passport = $this->fnForCheckingActiveOrDayEnd($Row->ch_eff_date);


                if (isset($passport['Table']) && $passport['Table'] == 'DayEnd')
                {

                    $IgnoreArray = [
                        'delete', 'message' => Common::AccessDeniedReason('holiday'),
                        'edit', 'message' => Common::AccessDeniedReason('holiday'),
                    ];
                        
                }

                $DataSet[] = [
                    'id' => ++$i,
                    'ch_title' => $Row->ch_title,
                    'ch_day' => $Row->ch_day,
                    'ch_description' => $Row->ch_description,
                    'ch_eff_date' => (new DateTime($Row->ch_eff_date))->format('d-m-y'),
                    'comp_name' => (!empty($Row->company['comp_name']))? $Row->company['comp_name'] : '' ,

                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id, isset($IgnoreArray) ? $IgnoreArray : array()),
                ];
            }
            echo json_encode([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            ]);
        }
        else{
            return view('GNL.CompanyHoliday.index');
        }
    }

    // Add and Store Company Holiday
    public function add(Request $request)
    {

        $days = Common::getWeekdayName();
        // $days = implode(',', $days);

        if ($request->isMethod('post')) {

            
            $validateData = $request->validate([
                'company_id' => 'required',
                'ch_title' => 'required',
                'ch_eff_date' => 'required',
            ]);

            $RequestData = $request->all();
            $RequestData['ch_eff_date'] = new DateTime($RequestData['ch_eff_date']);
            $RequestData['ch_eff_date'] = $RequestData['ch_eff_date']->format('Y-m-d');

            if (!isset($RequestData['ch_day'])) {
                $notification = [
                    'alert-type' => 'error',
                    'message' => 'Day Field is Required.'
                ];

                return redirect()->back()->with($notification);
            }

            $RequestData['ch_day'] = implode(",", $RequestData['ch_day']);

            $isInsert = CompanyHoliday::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted Company Holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Company Holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            }
        } else {

            return view('GNL.CompanyHoliday.add', compact('days'));
        }
    }

    // Edit Company Holiday
    public function edit(Request $request, $id = null)
    {

        $days = Common::getWeekdayName();
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'company_id' => 'required',
                'ch_title' => 'required',
                // 'ch_day' => 'required',
                'ch_eff_date' => 'required',
            ]);

            $Data = $request->all();
            $Data['ch_eff_date'] = new DateTime($Data['ch_eff_date']);
            $Data['ch_eff_date'] = $Data['ch_eff_date']->format('Y-m-d');

            if (!isset($Data['ch_day'])) {
                $notification = [
                    'alert-type' => 'error',
                    'message' => 'Day Field is Required.'
                ];

                return redirect()->back()->with($notification);
            }

            $Data['ch_day'] = implode(",", $Data['ch_day']);

            $isUpdate = $CompHolidayData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Company holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Company holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            }
        } else {

            return view('GNL.CompanyHoliday.edit', compact('CompHolidayData', 'days'));
        }
    }

    //View Company Holiday
    public function view($id = null)
    {

        $days = Common::getWeekdayName();
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        return view('GNL.CompanyHoliday.view', compact('CompHolidayData', 'days'));
    }

    // Soft Delete Company Holiday
    public function delete($id = null)
    {

        $CompHolidayData = CompanyHoliday::where('id', $id)->first();
        $CompHolidayData->is_delete = 1;

        $delete = $CompHolidayData->save();

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

    // Parmanent Delete Company Holiday
    public function destroy($id = null)
    {
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();
        $delete = $CompHolidayData->delete();

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

    ## Publish/Unpublish Company Holiday
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
    public function CheckDayEnd(Request $request)
    {
        if ($request->ajax()) {

            $selectedDate = (!empty($request->get('startDateFrom'))) ? $request->get('startDateFrom') : null;

            if(empty($selectedDate)){
                return response()->json(array("exists" => 1, "Table" => 'emptydata'));
            }

            $selectedDate = (new DateTime($selectedDate))->format('Y-m-d');

            $passport = $this->fnForCheckingActiveOrDayEnd($selectedDate);

            return response()->json($passport);

        }
    }

    ## This function is used to check Acive Day/ Day End 
    public function fnForCheckingActiveOrDayEnd($effectiveDate)
    {
        if(DB::getSchemaBuilder()->hasTable('pos_day_end')){
            $queryData1 = DB::table('pos_day_end')
            ->where('is_delete', 0)
            ->where([['branch_date', '>=', $effectiveDate]])
            ->count();


            if ($queryData1 > 0) {
                $passport = array('exists'  => 1,
                    'Table' => 'DayEnd',
                );
                return $passport;
            }

        }

        if(DB::getSchemaBuilder()->hasTable('acc_day_end')){

            $queryData2 = DB::table('acc_day_end')
            ->where('is_delete', 0)
            ->where('branch_date','>=',$effectiveDate)
            ->count();

            if ($queryData2 > 0) {
                $passport = array('exists'  => 1,
                    'Table' => 'DayEnd',
                );
                return $passport;
            }
        }

        if(DB::getSchemaBuilder()->hasTable('mfn_day_end')){

            $queryData3 = DB::table('mfn_day_end')
            ->where('is_delete', 0)
            ->where('branch_date','>=',$effectiveDate)
            ->count();

            if ($queryData3 > 0) {
                $passport = array('exists'  => 1,
                    'Table' => 'DayEnd',
                );
                return $passport;
            }
        }

        if(DB::getSchemaBuilder()->hasTable('inv_day_end')){

            $queryData4 = DB::table('inv_day_end')
            ->where('is_delete', 0)
            ->where('branch_date','>=',$effectiveDate)
            ->count();

            if ($queryData4 > 0) {
                $passport = array('exists'  => 1,
                    'Table' => 'DayEnd',
                );
                return $passport;
            }
        }

        if (DB::getSchemaBuilder()->hasTable('hr_holidays_comp')) {
            $queryData5 = DB::table('hr_holidays_comp')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('ch_eff_date', '=', $effectiveDate)
                ->count();

            if ($queryData5 > 0) {
                $passport = array('exists'  => 1,
                    'Table' => 'Holiday',
                );
                return $passport;
            }
        }
    }
}
