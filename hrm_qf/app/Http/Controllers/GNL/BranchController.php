<?php

namespace App\Http\Controllers\GNL;

use DateTime;
use App\Model\GNL\Area;
use App\Model\GNL\Zone;
use App\Model\GNL\Group;
use App\Model\GNL\Branch;
use App\Model\GNL\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;

class BranchController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = array(
                1 => 'gb.branch_name',
                2 => 'gb.branch_code',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');


            $zoneId   = (empty($request->input('zoneId'))) ? null : $request->input('zoneId');
            $regionId = (empty($request->input('regionId'))) ? null : $request->input('regionId');
            $areaId = (empty($request->input('areaId'))) ? null : $request->input('areaId');
            $isIndependent   = (empty($request->input('isIndependent'))) ? null : $request->input('isIndependent');
            $isApproved   = (empty($request->input('isApproved'))) ? null : $request->input('isApproved');
            $isActive   = (empty($request->input('isActive'))) ? null : $request->input('isActive');

            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            // Query
            $branchData = DB::table('gnl_branchs as gb')
                ->where('gb.is_delete', 0)
                ->whereIn('gb.id', HRS::getUserAccesableBranchIds())
                ->join('gnl_companies as gc', 'gb.company_id', '=', 'gc.id')
                ->leftJoin('gnl_zones as gz', 'gb.zone_id', '=', 'gz.id')
                ->leftJoin('gnl_regions as gr', 'gb.region_id', '=', 'gr.id')
                ->leftJoin('gnl_areas as ga', 'gb.area_id', '=', 'ga.id')
                ->select('gb.*', 'gc.comp_name', 'ga.area_name', 'ga.area_code', 'gr.region_name', 'gr.region_code', 'gz.zone_name', 'gz.zone_code')
                ->where(function ($query) use ($search, $zoneId, $regionId, $areaId, $isIndependent, $isApproved, $isActive) {
                    // if (Common::getBranchId() != 1) {
                    //     $branchData->where('gnl_branchs.id', Common::getBranchId());
                    // }
                    if (!empty($search)) {
                        $query->where('gb.branch_name', 'LIKE', "%{$search}%")
                            ->orWhere('gb.branch_code', 'LIKE', "%{$search}%")
                            ->orWhere('gb.contact_person', 'LIKE', "%{$search}%")
                            ->orWhere('gb.branch_opening_date', 'LIKE', "%{$search}%")
                            ->orWhere('gb.soft_start_date', 'LIKE', "%{$search}%")
                            ->orWhere('gc.comp_name', 'LIKE', "%{$search}%");
                    }

                    if (!empty($zoneId)) {
                        $query->where('gb.zone_id', $zoneId);
                    }

                    if (!empty($regionId)) {
                        $query->where('gb.region_id', $regionId);
                    }

                    if (!empty($areaId)) {
                        $query->where('gb.area_id', $areaId);
                    }

                    if (!empty($isIndependent)) {
                        if ($isIndependent == 1) {
                            $query->where('gb.independent_branch_date', 'not like', "0000-00-00");
                        } else {
                            $query->where('gb.is_approve', 'like', "0000-00-00");
                        }
                    }

                    if (!empty($isApproved)) {
                        if ($isApproved == 1) {
                            $query->where('gb.is_approve', 1);
                        } else {
                            $query->where('gb.is_approve', '<>', 1);
                        }
                    }

                    if (!empty($isActive)) {
                        if ($isActive == 1) {
                            $query->where('gb.is_active', 1);
                        } else {
                            $query->where('gb.is_active', '<>', 1);
                        }
                    }
                })
                ->orderBy($order, $dir);


            $tempQueryData = clone $branchData;
            $branchData = $branchData->offset($start)->limit($limit)->get();

            $totalData = DB::table('gnl_branchs')->where('is_delete', 0)->whereIn('id', HRS::getUserAccesableBranchIds())->count();
            $totalFiltered = $totalData;

            if (!empty($search) || !empty($zoneId) || !empty($regionId) || !empty($areaId)
                || !empty($isIndependent) || !empty($isApproved) || !empty($isActive)
            ) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;

            foreach ($branchData as $row) {

                $IgnoreArray = array();

                if ($row->id == 1) {
                    $IgnoreArray = ['delete'];
                }

                $ApproveText = ($row->is_approve == 1) ?
                    '<span class="text-primary">Approved</span>' :
                    '<span class="text-danger">Pending</span>';

                $OpeningDateText = "<p><b>Branch:</b> " . Common::viewDateFormat($row->branch_opening_date) . "</p>";

                if (!empty($row->soft_start_date)) {
                    $OpeningDateText .= "<p><b>Software:</b> " . Common::viewDateFormat($row->soft_start_date) . "</p>";
                } elseif (!empty($row->mfn_start_date)) {
                    $OpeningDateText .= "<p><b>Software:</b> " . Common::viewDateFormat($row->mfn_start_date) . "</p>";
                } elseif (!empty($row->inv_start_date)) {
                    $OpeningDateText .= "<p><b>Software:</b> " . Common::viewDateFormat($row->inv_start_date) . "</p>";
                } elseif (!empty($row->acc_start_date)) {
                    $OpeningDateText .= "<p><b>Software:</b> " . Common::viewDateFormat($row->acc_start_date) . "</p>";
                }


                $contInfo = "<p><b>Person:</b>" . $row->contact_person . "</p>";
                $contInfo .= " <p><b>Mobile:</b>" . $row->branch_phone . "</p>";
                $TempSet = array();
                $TempSet = [
                    'id' => ++$i,
                    'branch_name'   => $row->branch_name,
                    'branch_code'   => $row->branch_code,
                    'zone_name'     => !empty($row->zone_name) ? $row->zone_name . " [". $row->zone_code. "]" : "",
                    'region_name'   => !empty($row->region_name) ? $row->region_name . " [". $row->region_code. "]" : "",
                    'area_name'     => !empty($row->area_name) ? $row->area_name . " [". $row->area_code. "]" : "",
                    'Contact Info'  => $contInfo,
                    'opening Date'  => $OpeningDateText,
                    'comp_name'     => $row->comp_name,
                    'approved'      => $ApproveText,
                    'action'        => Role::roleWiseArray($this->GlobalRole, $row->id, $IgnoreArray, null, $row->is_approve)
                ];

                $DataSet[] = $TempSet;
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            );

            echo json_encode($json_data);
        } else {
            return view('GNL.Branch.index');
        }
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'branch_name' => 'required',
            ]);

            // $SDate = (empty($request->input('sDate'))) ? null : $request->input('sDate');

            $RequestData = $request->all();
            $RequestData['branch_opening_date'] = (empty($RequestData['branch_opening_date'])) ? null : (new DateTime($RequestData['branch_opening_date']))->format('Y-m-d');
            $RequestData['soft_start_date'] = (empty($RequestData['soft_start_date'])) ? null : (new DateTime($RequestData['soft_start_date']))->format('Y-m-d');
            $RequestData['acc_start_date'] = (empty($RequestData['acc_start_date'])) ? null : (new DateTime($RequestData['acc_start_date']))->format('Y-m-d');
            $RequestData['mfn_start_date'] = (empty($RequestData['mfn_start_date'])) ? null : (new DateTime($RequestData['mfn_start_date']))->format('Y-m-d');
            $RequestData['fam_start_date'] = (empty($RequestData['fam_start_date'])) ? null : (new DateTime($RequestData['fam_start_date']))->format('Y-m-d');
            $RequestData['inv_start_date'] = (empty($RequestData['inv_start_date'])) ? null : (new DateTime($RequestData['inv_start_date']))->format('Y-m-d');
            $RequestData['proc_start_date'] = (empty($RequestData['proc_start_date'])) ? null : (new DateTime($RequestData['proc_start_date']))->format('Y-m-d');
            $RequestData['bill_start_date'] = (empty($RequestData['bill_start_date'])) ? null : (new DateTime($RequestData['bill_start_date']))->format('Y-m-d');
            $RequestData['hr_start_date'] = (empty($RequestData['hr_start_date'])) ? null : (new DateTime($RequestData['hr_start_date']))->format('Y-m-d');

            $isInsert = Branch::create($RequestData);

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted Branch Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/branch')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Branch',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.Branch.add', compact('GroupData'));
        }
    }

    public function edit(Request $request, $id = null)
    {

        $BranchData = Branch::where('id', $id)->first();

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'branch_name' => 'required',
            ]);

            $Data = $request->all();
            if ("Branch Area Change") {
                $oldAreaId = DB::table('gnl_areas')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where(function ($areaQuery) use ($id) {
                        if (!empty($id)) {
                            $areaQuery->where('branch_arr', 'LIKE', "{$id}")
                                ->orWhere('branch_arr', 'LIKE', "{$id},%")
                                ->orWhere('branch_arr', 'LIKE', "%,{$id},%")
                                ->orWhere('branch_arr', 'LIKE', "%,{$id}");
                        }
                    })
                    ->select('id')
                    ->pluck('id')
                    ->first();
                if (!empty($oldAreaId)) {
                    $BranchArray = '';
                    $selectBranch = DB::table('gnl_areas')
                        ->where([['is_active', 1], ['is_delete', 0], ['id', $oldAreaId]])
                        ->select(DB::raw('GROUP_CONCAT(branch_arr) as branch_arr'))
                        ->first();

                    if ($selectBranch) {
                        $BranchArray = explode(',', $selectBranch->branch_arr);
                        $index = array_search($id, $BranchArray);
                        array_splice($BranchArray, $index, 1);
                    }
                    $Req['branch_arr'] = implode(',', $BranchArray);
                    $oldArea = Area::where('id', $oldAreaId)->first();
                    $oldArea->update($Req);
                }
                if (!empty($Data['area_id'])) {
                    $newBranchArray = '';
                    $addBranch = DB::table('gnl_areas')
                        ->where([['is_active', 1], ['is_delete', 0], ['id', $Data['area_id']]])
                        ->select(DB::raw('GROUP_CONCAT(branch_arr) as branch_arr'))
                        ->first();

                    if ($addBranch) {
                        $newBranchArray = explode(',', $addBranch->branch_arr);
                        array_push($newBranchArray, $id);
                    }
                    $newArea['branch_arr'] = implode(',', $newBranchArray);
                    $Area = Area::where('id', $Data['area_id'])->first();
                    $Area->update($newArea);
                }
            }

            if (isset($Data['branch_opening_date'])) {
                $Data['branch_opening_date'] = (new DateTime($Data['branch_opening_date']))->format('Y-m-d');
            }

            if (isset($Data['soft_start_date'])) {
                $Data['soft_start_date'] = (new DateTime($Data['soft_start_date']))->format('Y-m-d');
            }

            if (isset($Data['acc_start_date'])) {
                $Data['acc_start_date'] = (new DateTime($Data['acc_start_date']))->format('Y-m-d');
            }

            if (isset($Data['mfn_start_date'])) {
                $Data['mfn_start_date'] = (new DateTime($Data['mfn_start_date']))->format('Y-m-d');
            }

            if (isset($Data['fam_start_date'])) {
                $Data['fam_start_date'] = (new DateTime($Data['fam_start_date']))->format('Y-m-d');
            }

            if (isset($Data['inv_start_date'])) {
                $Data['inv_start_date'] = (new DateTime($Data['inv_start_date']))->format('Y-m-d');
            }

            if (isset($Data['proc_start_date'])) {
                $Data['proc_start_date'] = (new DateTime($Data['proc_start_date']))->format('Y-m-d');
            }

            if (isset($Data['bill_start_date'])) {
                $Data['bill_start_date'] = (new DateTime($Data['bill_start_date']))->format('Y-m-d');
            }

            if (isset($Data['hr_start_date'])) {
                $Data['hr_start_date'] = (new DateTime($Data['hr_start_date']))->format('Y-m-d');
            }

            $isUpdate = $BranchData->update($Data);

            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Branch Data',
                    'alert-type' => 'success',
                );
                return Redirect::to('gnl/branch')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Branch',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $branchId = $id;
            // $areaId = DB::table('gnl_areas')
            //     ->where([['is_active', 1], ['is_delete', 0]])
            //     ->where(function ($areaQuery) use ($branchId) {
            //         if (!empty($branchId)) {
            //             $areaQuery->where('branch_arr', 'LIKE', "{$branchId}")
            //                 ->orWhere('branch_arr', 'LIKE', "{$branchId},%")
            //                 ->orWhere('branch_arr', 'LIKE', "%,{$branchId},%")
            //                 ->orWhere('branch_arr', 'LIKE', "%,{$branchId}");
            //         }
            //     })
            //     ->pluck('id')
            //     ->first();
            // $regionId = DB::table('gnl_regions')
            //     ->where([['is_active', 1], ['is_delete', 0]])
            //     ->where(function ($query) use ($areaId) {
            //         if (!empty($areaId)) {
            //             $query->where('area_arr', 'LIKE', "{$areaId}")
            //                 ->orWhere('area_arr', 'LIKE', "{$areaId},%")
            //                 ->orWhere('area_arr', 'LIKE', "%,{$areaId},%")
            //                 ->orWhere('area_arr', 'LIKE', "%,{$areaId}");
            //         }
            //     })
            //     ->pluck('id')
            //     ->first();
            // #####
            // $zoneId = DB::table('gnl_zones')
            //     ->where([['is_active', 1], ['is_delete', 0]])
            //     ->where(function ($query) use ($regionId) {
            //         if (!empty($regionId)) {
            //             $query->where('region_arr', 'LIKE', "{$regionId}")
            //                 ->orWhere('region_arr', 'LIKE', "{$regionId},%")
            //                 ->orWhere('region_arr', 'LIKE', "%,{$regionId},%")
            //                 ->orWhere('region_arr', 'LIKE', "%,{$regionId}");
            //         }
            //     })
            //     ->pluck('id')
            //     ->first();
            // $BranchData = Branch::where('id', $id)->first();
            $ZoneData = Zone::where('is_delete', 0)->orderBy('zone_code', 'ASC')->get();
            $RegionData = Region::where('is_delete', 0)->orderBy('region_code', 'ASC')->get();
            $AreaData = Area::where('is_delete', 0)->orderBy('area_code', 'ASC')->get();
            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.Branch.edit', compact('BranchData', 'GroupData', 'ZoneData', 'RegionData', 'AreaData'));
        }
    }

    public function view($id = null)
    {
        $BranchData = Branch::where('id', $id)->first();
        return view('GNL.Branch.view', compact('BranchData'));
    }

    public function delete($id = null)
    {

        $BranchData = Branch::where('id', $id)->first();
        $BranchData->is_delete = 1;
        $delete = $BranchData->save();

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

    public function isApprove($id = null)
    {

        $BranchData = Branch::where('id', $id)->first();

        $BranchData->is_approve = 1;
        //        if ($BranchData->is_approve == 0) {
        //            $BranchData->is_approve = 1;
        //        } else {
        //            $BranchData->is_approve = 0;
        //        }
        $Status = $BranchData->save();

        if ($Status) {
            $notification = array(
                'message' => 'Successfully approved',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to approve',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isActive($id = null)
    {
        $BranchData = Branch::where('id', $id)->first();
        if ($BranchData->is_active == 1) {
            $BranchData->is_active = 0;
            # code...
        } else {
            $BranchData->is_active = 1;
        }

        $Status = $BranchData->save();

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

    public function getRegion(Request $request)
    {
        if ($request->ajax()) {

            $ZoneID     = $request->zoneId;
            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';

            $zoneWiseQuery = DB::table('gnl_zones')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($zoneWiseQuery) use ($ZoneID) {
                    if (!empty($ZoneID)) {
                        $zoneWiseQuery->where('id', $ZoneID);
                    }
                })
                ->selectRaw('GROUP_CONCAT(region_arr) as region_arr')
                // ->select('area_arr')
                ->first();

            if ($zoneWiseQuery) {
                $region_arr_zone_wise = explode(',', $zoneWiseQuery->region_arr);
            } else {
                $region_arr_zone_wise = array();
            }

            $Region = Region::whereIn('id', $region_arr_zone_wise)
                ->select('id', 'region_name', 'region_code')
                ->orderBy('region_code', 'ASC')
                ->get();

            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $Region,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($Region as $Row) {
                    // $output .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->area_code) . ' - ' . $Row->area_name . '</option>';
                    $output .= '<option value="' . $Row->id . '">' . $Row->region_name . ' [' . $Row->region_code . ']</option>';
                }
                echo $output;
            }
        }
    }

    public function getArea(Request $request)
    {
        if ($request->ajax()) {

            $RegionID     = $request->regionId;
            $returnType = (isset($request->returnType)) ? $request->returnType : 'text';

            $regionWiseQuery = DB::table('gnl_regions')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($regionWiseQuery) use ($RegionID) {
                    if (!empty($RegionID)) {
                        $regionWiseQuery->where('id', $RegionID);
                    }
                })
                ->selectRaw('GROUP_CONCAT(area_arr) as area_arr')
                // ->select('area_arr')
                ->first();

            if ($regionWiseQuery) {
                $area_arr_region_wise = explode(',', $regionWiseQuery->area_arr);
            } else {
                $area_arr_region_wise = array();
            }

            $Area = Area::whereIn('id', $area_arr_region_wise)
                ->select('id', 'area_name', 'area_code')
                ->orderBy('area_code', 'ASC')
                ->get();

            if ($returnType == 'json') {
                $data = [
                    'status'      => 'success',
                    'message'     => '',
                    'result_data' => $Area,
                ];

                return response()->json($data);
            } else {
                $output = '<option value="">All</option>';
                foreach ($Area as $Row) {
                    // $output .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->area_code) . ' - ' . $Row->area_name . '</option>';
                    $output .= '<option value="' . $Row->id . '">' . $Row->area_name . ' [' . $Row->area_code . ']</option>';
                }
                echo $output;
            }
        }
    }
}
