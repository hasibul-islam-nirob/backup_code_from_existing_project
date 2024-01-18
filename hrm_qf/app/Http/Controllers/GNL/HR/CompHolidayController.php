<?php

namespace App\Http\Controllers\GNL\HR;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\GNL\HR\CompanyHoliday;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use Illuminate\Support\Facades\Validator;


class CompHolidayController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        // if ($operationType == 'store' || $operationType == 'update') {

        //     $rules = array(
        //         'branch_from_id' => 'required',
        //         'branch_to_id' => 'required',
        //         'employee_id' => 'required',
        //         'transfer_date'        => 'required',
        //         'exp_effective_date'     => 'required',

        //     );

        //     $attributes = array(
        //         'exp_effective_date'     => 'Expected effective date',
        //         'transfer_date'        => 'Transfer Date',
        //         'branch_from_id' => 'Branch From',
        //         'branch_to_id' => 'Branch To',
        //         'employee_id' => 'Employee',

        //     );

        //     $validator = Validator::make($requestData->all(), $rules, [], $attributes);

        //     if ($validator->fails()) {
        //         $errorMsg = implode(' || ', $validator->errors()->all());
        //     }
        // }

        if ($operationType == 'index') {

            $ignoreArray = array();

            if ($requestData->ch_eff_date < date('Y-m-d')) {
                $ignoreArray = ['delete', 'edit', 'message' => "Today is greater than effective date."];
            }

            $errorMsg = $ignoreArray;
        }

        if ($operationType == 'delete') {
            if ($requestData->ch_eff_date < date('Y-m-d')) {
                $errorMsg = "Today is greater than effective date.";
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    ## List of Company Holiday
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
            return view('GNL.HR.CompanyHoliday.index');
        }

    }

    ## Add and Store Company Holiday
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
            $RequestData['ch_eff_date'] = new DateTime($RequestData['ch_eff_date']);
            $RequestData['ch_eff_date'] = $RequestData['ch_eff_date']->format('Y-m-d');

            $RequestData['ch_day'] = implode(",", $RequestData['ch_day']);

            $prevHolidayRow = CompanyHoliday::where([['is_delete', 0], ['is_active', 1]])->orderBy('ch_eff_date','DESC')->first();
            $preEffectiveDateEnd = $RequestData['ch_eff_date'];
            $preEffectiveDateEnd = (new DateTime($preEffectiveDateEnd))->modify('-1 day');
            $preEffectiveDateEnd = $preEffectiveDateEnd->format('Y-m-d');

            if($prevHolidayRow){
                $prevHolidayRow->ch_eff_date_end = $preEffectiveDateEnd;
                $prevHolidayRow->save();
            }

            $isInsert = CompanyHoliday::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message'    => 'Successfully Inserted Company Holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to insert data in Company Holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            }
        } else {

            return view('GNL.HR.CompanyHoliday.add', compact('days'));
        }
    }

    ## Edit Company Holiday
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
            $Data['ch_eff_date'] = new DateTime($Data['ch_eff_date']);
            $Data['ch_eff_date'] = $Data['ch_eff_date']->format('Y-m-d');

            $Data['ch_day'] = implode(",", $Data['ch_day']);


            $prevHolidayRow = CompanyHoliday::where([['is_delete', 0], ['is_active', 1], ['id', '!=', $CompHolidayData->id]])
                                            ->orderBy('ch_eff_date','DESC')
                                            ->first();

            $preEffectiveDateEnd = $Data['ch_eff_date'];
            $preEffectiveDateEnd = (new DateTime($preEffectiveDateEnd))->modify('-1 day');
            $preEffectiveDateEnd = $preEffectiveDateEnd->format('Y-m-d');

            if($prevHolidayRow){
                $prevHolidayRow->ch_eff_date_end = $preEffectiveDateEnd;
                $prevHolidayRow->save();
            }


            $isUpdate = $CompHolidayData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message'    => 'Successfully Updated Company holiday Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            } else {
                $notification = array(
                    'message'    => 'Unsuccessful to Update data in Company holiday',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/compholiday')->with($notification);
            }
        } else {

            return view('GNL.HR.CompanyHoliday.edit', compact('CompHolidayData', 'days'));
        }
    }

    ## View Company Holiday
    public function view($id = null)
    {

        $days            = Common::getWeekdayName();
        $CompHolidayData = CompanyHoliday::where('id', $id)->first();

        return view('GNL.HR.CompanyHoliday.view', compact('CompHolidayData', 'days'));
    }

    ## Delete Company Holiday
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

        ## Set Effective End date = null for for previous holiday
        $prevHolidayRow = CompanyHoliday::where([['is_delete', 0], ['is_active', 1], ['id', '!=', $CompHolidayData->id]])
                                            ->where('ch_eff_date', '<', $CompHolidayData->ch_eff_date)
                                            ->orderBy('ch_eff_date','DESC')
                                            ->first();
        if($prevHolidayRow){
            $prevHolidayRow->ch_eff_date_end = null;
            $prevHolidayRow->save();
        }
        ## ----

        $CompHolidayData->is_delete = 1;
        $delete                     = $CompHolidayData->save();

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

    ## Parmanent Delete Company Holiday
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

}
