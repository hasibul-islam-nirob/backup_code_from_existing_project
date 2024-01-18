<?php

namespace App\Http\Controllers\GNL\HR;

use DateTime;
use Illuminate\Http\Request;
use App\Model\GNL\HR\FiscalYear;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class FiscalYearController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $request)
    {

        if ($request->ajax()){

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = FiscalYear::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('fy_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = FiscalYear::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'fy_name' => $Row->fy_name,
                    'fy_start_date' => (new DateTime($Row->fy_start_date))->format('d-m-y'),
                    'fy_end_date' => (new DateTime($Row->fy_end_date))->format('d-m-y'),
                    'comp_name' => (!empty($Row->company['comp_name']))? $Row->company['comp_name'] : '',
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
            return view('GNL.HR.FiscalYear.index');
        }

        /*$FiscalYear = FiscalYear::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return view('HR.FiscalYear.index', compact('FiscalYear'));*/
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
//                'company_id' => 'required',
                'fy_name' => 'required',
                'fy_start_date' => 'required',
                //'fy_end_date' => 'required'
            ]);

            $RequestData = $request->all();
            $StartDate = new DateTime($RequestData['fy_start_date']);
            $RequestData['fy_start_date'] = $StartDate->format('Y-m-d');
            $EndDate = $StartDate;
            $EndDate = $EndDate->modify('+1 year, -1 Day');
            $RequestData['fy_end_date'] = $EndDate->format('Y-m-d');

            $isInsert = FiscalYear::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Fiscal Year',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/fiscal_year')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Fiscal Year',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {

            return view('GNL.HR.FiscalYear.add');
        }
    }

    public function edit(Request $request, $id = null)
    {

        $FiscalYear = FiscalYear::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
//                'company_id' => 'required',
                'fy_name' => 'required',
                'fy_start_date' => 'required',
            ]);

            $RequestData = $request->all();
            $StartDate = new DateTime($RequestData['fy_start_date']);
            $RequestData['fy_start_date'] = $StartDate->format('Y-m-d');
            $EndDate = $StartDate;
            $EndDate = $EndDate->modify('+1 year, -1 Day');
            $RequestData['fy_end_date'] = $EndDate->format('Y-m-d');

            $isUpdate = $FiscalYear->update($RequestData);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Fiscal Year',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/fiscal_year')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Fiscal Year',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $FiscalYear = FiscalYear::where('id', $id)->first();

            return view('GNL.HR.FiscalYear.edit', compact('FiscalYear'));
        }
    }

    public function view($id = null)
    {
        $FiscalYear = FiscalYear::where('id', $id)->first();
        return view('GNL.HR.FiscalYear.view', compact('FiscalYear'));
    }

    public function delete(Request $request)
    {
        $FiscalYear = FiscalYear::where('id', $request->RowID)->first();
        $FiscalYear->is_delete = 1;
        $delete = $FiscalYear->save();

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

    public function isactive($id = null)
    {
        $FiscalYear = FiscalYear::where('id', $id)->first();

        if ($FiscalYear->is_active == 1) {
            $FiscalYear->is_active = 0;
        } else {
            $FiscalYear->is_active = 1;
        }

        $Status = $FiscalYear->save();

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
