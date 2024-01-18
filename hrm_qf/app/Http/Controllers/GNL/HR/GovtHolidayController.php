<?php

namespace App\Http\Controllers\GNL\HR;

use DateTime;
use Illuminate\Http\Request;
use App\Model\GNL\HR\GovtHoliday;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class GovtHolidayController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Govt Holiday
    public function index(Request $request)
    {
        if ($request->ajax()){

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = GovtHoliday::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('gh_date', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = GovtHoliday::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'gh_date' => (new DateTime($Row->gh_date.'-'.date('Y')))->format('d-m'),
                    'gh_title' => $Row->gh_title,
                    'gh_description' => $Row->gh_description,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id),
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
            return view('GNL.HR.GovtHoliday.index');
        }

        /*$GovtHolidayData = GovtHoliday::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return view('HR.GovtHoliday.index', compact('GovtHolidayData'));*/
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
                return Redirect::to('gnl/govtholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/govtholiday')->with($notification);
            }
        } else {

            return view('GNL.HR.GovtHoliday.add');
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
                return Redirect::to('gnl/govtholiday')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in GovtHoliday',
                    'alert-type' => 'error',
                );
                return Redirect::to('gnl/govtholiday')->with($notification);
            }
        } else {

            return view('GNL.HR.GovtHoliday.edit', compact('GovtHolidayData'));
        }
    }

    //View GovtHoliday
    public function view($id = null)
    {

        $GovtHolidayData = GovtHoliday::where('id', $id)->first();

        return view('GNL.HR.GovtHoliday.view', compact('GovtHolidayData'));
    }

    // Soft Delete GovtHoliday
    public function delete(Request $request)
    {

        $GovtHolidayData = GovtHoliday::where('id', $request->RowID)->first();
        $GovtHolidayData->is_delete = 1;

        $delete = $GovtHolidayData->save();

        if ($delete) {
            return [
                'message' => 'Successfully Deleted',
                'status' => 'success',
            ];
        } else {
            return [
                'message' => 'Unsuccessful to Delete',
                'status' => 'error',
            ];
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

}
