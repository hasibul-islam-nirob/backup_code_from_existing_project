<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\Company;
use Illuminate\Http\Request;
use App\Model\GNL\TermsConditions;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class TermsConditionsController extends Controller
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

            $masterQuery = TermsConditions::where([['is_delete', '=', 0], ['is_active', '=', 1]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('tc_name', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('id', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = TermsConditions::where([['is_delete', '=', 0], ['is_active', '=', 1]])
                ->count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $DataSet[] = [
                    'id' => ++$i,
                    'tc_name' => $Row->tc_name,
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
            return view('GNL.TermsConditions.index');
        }
    }

    public function add(Request $request)
    {

      if ($request->isMethod('post')) {
          $validateData = $request->validate([
                'type_id' => 'required',
                'tc_name' => 'required',
          ]);
          $RequestData = $request->all();

          $isInsert = TermsConditions::create($RequestData);

          if ($isInsert) {
              $notification = array(
                  'message' => 'Successfully Inserted Data',
                  'alert-type' => 'success',
              );
              return Redirect::to('gnl/terms_conditions')->with($notification);
          } else {
              $notification = array(
                  'message' => 'Unsuccessful to insert data',
                  'alert-type' => 'error',
              );
              return redirect()->back()->with($notification);
          }
      } else {
            $termTypes = DB::table('gnl_terms_type')->get();
            return view('GNL.TermsConditions.add',compact('termTypes'));
        }
    }

    public function edit(Request $request, $id = null)
    {

                $TCData = TermsConditions::where('id', $id)->first();
                if ($request->isMethod('post')) {
                $validateData = $request->validate([
                    'type_id' => 'required',
                    'tc_name' => 'required',
                ]);

                $Data = $request->all();

                $isUpdate = $TCData->update($Data);

                if ($isUpdate) {
                    $notification = array(
                        'message' => 'Successfully Updated  Data',
                        'alert-type' => 'success',
                    );
                    return Redirect::to('gnl/terms_conditions')->with($notification);
                } else {
                    $notification = array(
                        'message' => 'Unsuccessful to update data',
                        'alert-type' => 'error',
                    );
                    return redirect()->back()->with($notification);
                }
            } else {
                
                $termTypes = DB::table('gnl_terms_type')->get();
                return view('GNL.TermsConditions.edit', compact('TCData','termTypes'));
        }
    }

    public function view($id = null)
    {
        $TCData = TermsConditions::where('id', $id)->first();
        $termType = DB::table('gnl_terms_type')->where('id',$TCData->type_id)->first();
        return view('GNL.TermsConditions.view', compact('TCData','termType'));
    }
    public function delete(Request $request)
    {

        $TCData = TermsConditions::where('id', $request->RowID)->first();
        $TCData->is_delete = 1;

        $delete = $TCData->save();

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

    //Parmanent Delete Product UOM
    public function destroy($id = null)
    {
        $TCData = TermsConditions::where('id', $id)->first();
        $delete = $TCData->delete();

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

    // Publish/ Unpublish Product UOM
    public function isActive($id = null)
    {
        $TCData = TermsConditions::where('id', $id)->first();
        if ($TCData->is_active == 1) {
            $TCData->is_active = 0;
        } else {
            $TCData->is_active = 1;
        }

        $Status = $TCData->save();

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
