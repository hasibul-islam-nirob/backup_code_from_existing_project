<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\Zone;
use App\Model\GNL\Company;
use Illuminate\Http\Request;
use App\Model\GNL\MapZoneArea;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class ZoneController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $columns = array(
    //             0 => 'zn.id',
    //             1 => 'zn.zone_name',
    //             2 => 'zn.zone_code',
    //             3 => 'ar.area_name',
    //         );

    //         $limit = $request->input('length');
    //         $start = $request->input('start');
    //         $order = $columns[$request->input('order.0.column')];
    //         $dir = $request->input('order.0.dir');
    //         // Searching variable
    //         $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

    //         // Query
    //         $ZoneData = DB::table('gnl_zones as zn')
    //             ->where([['zn.is_delete', 0], ['zn.is_active', 1]])
    //             ->select('zn.id as id', 'zn.zone_name', 'zn.zone_code', 'zn.area_arr', 'zn.branch_arr')
    //             ->where(function ($ZoneData) use ($search) {
    //                 if (!empty($search)) {
    //                     $ZoneData->where('zn.zone_name', 'LIKE', "%{$search}%")
    //                         ->orWhere('zn.zone_code', 'LIKE', "%{$search}%");
    //                 }
    //             })
    //             ->orderBy($order, $dir);

    //         $tempQueryData = clone $ZoneData;
    //         $ZoneData = $ZoneData->offset($start)->limit($limit)->get();

    //         $totalData = DB::table('gnl_zones')->where([['is_delete', 0], ['is_active', 1]])->count();

    //         $totalFiltered = $totalData;
    //         if (!empty($search)) {
    //             $totalFiltered = $tempQueryData->count();
    //         }

    //         $DataSet = array();
    //         $i = $start;

    //         foreach ($ZoneData as $Row) {

    //             $zoneWiseAreahName = DB::table('gnl_areas')
    //                 ->where([['is_delete', 0], ['is_active', 1]])
    //                 ->whereIn('id', explode(',', $Row->area_arr))
    //                 ->select(DB::raw('GROUP_CONCAT(area_name, "(", area_code, ")") as area_name'))
    //                 ->first();

    //             $TempSet = array();
    //             $TempSet = [
    //                 'id' => ++$i,
    //                 'zone_name' => $Row->zone_name,
    //                 'zone_code' => $Row->zone_code,
    //                 'area_name' => ($zoneWiseAreahName) ? $zoneWiseAreahName->area_name : $Row->area_arr,
    //                 'action' => Role::roleWiseArray($this->GlobalRole, $Row->id, [])
    //             ];

    //             $DataSet[] = $TempSet;
    //         }
    //         $json_data = array(
    //             "draw" => intval($request->input('draw')),
    //             "recordsTotal" => intval($totalData),
    //             "recordsFiltered" => intval($totalFiltered),
    //             "data" => $DataSet,
    //         );

    //         echo json_encode($json_data);

    //     } else {
    //         return view('GNL.ZoneList.index');
    //     }

    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = array(
                0 => 'zn.id',
                1 => 'zn.zone_name',
                2 => 'zn.zone_code',
                3 => 'ar.region_name',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            // Searching variable
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            // Query
            $ZoneData = DB::table('gnl_zones as zn')
                ->where([['zn.is_delete', 0], ['zn.is_active', 1]])
                ->select('zn.id as id', 'zn.zone_name', 'zn.zone_code', 'zn.region_arr')
                ->where(function ($ZoneData) use ($search) {
                    if (!empty($search)) {
                        $ZoneData->where('zn.zone_name', 'LIKE', "%{$search}%")
                            ->orWhere('zn.zone_code', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy($order, $dir);

            $tempQueryData = clone $ZoneData;
            $ZoneData = $ZoneData->offset($start)->limit($limit)->get();

            $totalData = DB::table('gnl_zones')->where([['is_delete', 0], ['is_active', 1]])->count();

            $totalFiltered = $totalData;
            if (!empty($search)) {
                $totalFiltered = $tempQueryData->count();
            }

            $DataSet = array();
            $i = $start;

            foreach ($ZoneData as $Row) {

                $zoneWiseRegionName = DB::table('gnl_regions')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', explode(',', $Row->region_arr))
                    ->select(DB::raw('GROUP_CONCAT(" ",region_name, " [", region_code, "] ") as region_name'))
                    ->first();

                $TempSet = array();
                $TempSet = [
                    'id' => ++$i,
                    'zone_name' => $Row->zone_name,
                    'zone_code' => $Row->zone_code,
                    'region_name' => ($zoneWiseRegionName) ? $zoneWiseRegionName->region_name : $Row->region_arr,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id, [])
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
            return view('GNL.ZoneList.index');
        }

    }

    // public function add(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         $validateData = $request->validate([
    //             'zone_name' => 'required',
    //             'zone_code' => 'required',
    //         ]);

    //         $RequestData = $request->all();
    //         $AreaArr = (isset($RequestData['area_arr']) ? $RequestData['area_arr'] : array());

    //         $BranchArr = "";
    //         if (count($AreaArr) > 0) {

    //             $areaWiseBranch = DB::table('gnl_areas')
    //                 ->where([['is_delete', 0], ['is_active', 1]])
    //                 ->whereIn('id', $AreaArr)
    //                 ->select(DB::raw('GROUP_CONCAT(branch_arr) as branch_arr'))
    //                 ->first();

    //             if ($areaWiseBranch) {
    //                 $BranchArr = $areaWiseBranch->branch_arr;
    //             }

    //             $RequestData['area_arr'] = implode(',', $AreaArr);
    //             $RequestData['branch_arr'] = $BranchArr;
    //         }

    //         $isInsert = Zone::create($RequestData);

    //         if ($isInsert) {
    //             $notification = array(
    //                 'message' => 'Successfully Inserted New Area List',
    //                 'alert-type' => 'success',
    //             );

    //             return Redirect::to('gnl/zone')->with($notification);

    //         } else {
    //             $notification = array(
    //                 'message' => 'Unsuccessful to insert data in Zone List',
    //                 'alert-type' => 'error',
    //             );
    //             return redirect()->back()->with($notification);
    //         }
    //     } else {
    //         $Companies = Company::where('is_delete', 0)
    //             ->select(['id', 'comp_name'])
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         return view('GNL.ZoneList.add', compact('Companies'));
    //     }
    // }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'zone_name' => 'required',
                'zone_code' => 'required',
            ]);

            $RequestData = $request->all();
            $RegionArr = (isset($RequestData['region_arr']) ? $RequestData['region_arr'] : array());
            // $BranchArr = "";
            if (count($RegionArr) > 0) {

                // $regionWiseBranch = DB::table('gnl_areas')
                //     ->where([['is_delete', 0], ['is_active', 1]])
                //     ->whereIn('id', $AreaArr)
                //     ->select(DB::raw('GROUP_CONCAT(branch_arr) as branch_arr'))
                //     ->first();

                // if ($areaWiseBranch) {
                //     $BranchArr = $areaWiseBranch->branch_arr;
                // }

                $RequestData['region_arr'] = implode(',', $RegionArr);
                // $RequestData['branch_arr'] = $BranchArr;
            }

            $isInsert = Zone::create($RequestData);
            $lastInsertQuery = Zone::latest()->first();
            $zoneId = $lastInsertQuery->id;
            if (count($RegionArr) > 0) {
                $area = DB::table('gnl_regions')
                        ->where([
                            ['is_active', 1],
                            ['is_delete', 0]
                        ])
                        ->whereIn('id', $RegionArr)
                        ->select('area_arr')
                        ->get();
                if (count($area) > 0) {
                    $branchIds = array();
                    foreach($area as $Row)
                    {
                        $branchId = DB::table('gnl_areas')
                                    ->where([
                                        ['is_active', 1],
                                        ['is_delete', 0]
                                    ])
                                    ->whereIn('id', explode(',', $Row->area_arr))
                                    ->select('branch_arr')
                                    ->get()
                                    ->toArray();
                        $branchIds = array_merge($branchIds, $branchId);
                    }

                    if (count($branchIds) > 0) {
                        foreach($branchIds as $Row)
                        {
                            $branchIds = DB::table('gnl_branchs')
                                        ->where([
                                            ['is_active', 1],
                                            ['is_delete', 0]
                                        ])
                                        ->whereIn('id', explode(',', $Row->branch_arr))
                                        ->update(['zone_id' => $zoneId]);
                        }
                    }
                }
            }

            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Zone in List',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/zone')->with($notification);

            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Zone List',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $Companies = Company::where('is_delete', 0)
                ->select(['id', 'comp_name'])
                ->orderBy('id', 'DESC')
                ->get();
            return view('GNL.ZoneList.add', compact('Companies'));
        }
    }

    public function edit(Request $request, $id = null)
    {

        $ZoneData = Zone::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'zone_name' => 'required',
                'zone_code' => 'required',
            ]);

            $RequestData = $request->all();
            $RegionArr = (isset($RequestData['region_arr']) ? $RequestData['region_arr'] : array());
            if (count($RegionArr) > 0) {

                $RequestData['region_arr'] = implode(',', $RegionArr);
            }
            else {
                $RequestData['region_arr'] = array();
            }

            $isUpdate = $ZoneData->update($RequestData);
            DB::table('gnl_branchs')
                ->where([
                    ['is_active', 1],
                    ['is_delete', 0],
                    ['zone_id', $ZoneData->id]
                ])
                ->update(['zone_id' => null]);
            if (count($RegionArr) > 0) {
                $area = DB::table('gnl_regions')
                        ->where([
                            ['is_active', 1],
                            ['is_delete', 0]
                        ])
                        ->whereIn('id', $RegionArr)
                        ->select('area_arr')
                        ->get();
                    // dd($area);

                if (count($area) > 0) {
                    $branchIds = array();
                    foreach($area as $Row)
                    {
                        $branchId = DB::table('gnl_areas')
                                    ->where([
                                        ['is_active', 1],
                                        ['is_delete', 0]
                                    ])
                                    ->whereIn('id', explode(',', $Row->area_arr))
                                    ->select('branch_arr')
                                    ->get()
                                    ->toArray();
                        $branchIds = array_merge($branchIds, $branchId);
                    }
                    if (count($branchIds) > 0) {
                        foreach($branchIds as $Row)
                        {
                            $branchIds = DB::table('gnl_branchs')
                                        ->where([
                                            ['is_active', 1],
                                            ['is_delete', 0]
                                        ])
                                        ->whereIn('id', explode(',', $Row->branch_arr))
                                        ->update(['zone_id' => $ZoneData->id]);
                        }
                    }
                }
            }
            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Updated Zone',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/zone')->with($notification);

            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Zone list',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $Companies = Company::where('is_delete', 0)
                ->select(['id', 'comp_name'])
                ->orderBy('id', 'DESC')
                ->get();
            $ZoneData = Zone::where('id', $id)->first();
            return view('GNL.ZoneList.edit', compact('ZoneData', 'Companies'));
        }
    }

    // public function edit(Request $request, $id = null)
    // {

    //     $ZoneData = Zone::where('id', $id)->first();

    //     if ($request->isMethod('post')) {

    //         $validateData = $request->validate([
    //             'zone_name' => 'required',
    //             'zone_code' => 'required',
    //         ]);

    //         $RequestData = $request->all();
    //         $AreaArr = (isset($RequestData['area_arr']) ? $RequestData['area_arr'] : array());

    //         $BranchArr = "";
    //         if (count($AreaArr) > 0) {

    //             $areaWiseBranch = DB::table('gnl_areas')
    //                 ->where([['is_delete', 0], ['is_active', 1]])
    //                 ->whereIn('id', $AreaArr)
    //                 ->select(DB::raw('GROUP_CONCAT(branch_arr) as branch_arr'))
    //                 ->first();

    //             if ($areaWiseBranch) {
    //                 $BranchArr = $areaWiseBranch->branch_arr;
    //             }

    //             $RequestData['area_arr'] = implode(',', $AreaArr);
    //             $RequestData['branch_arr'] = $BranchArr;
    //         }
    //         else {
    //             $RequestData['area_arr'] = array();
    //             $RequestData['branch_arr'] = array();
    //         }

    //         $isUpdate = $ZoneData->update($RequestData);

    //         if ($isUpdate) {
    //             $notification = array(
    //                 'message' => 'Successfully Updated Zone',
    //                 'alert-type' => 'success',
    //             );

    //             return Redirect::to('gnl/zone')->with($notification);

    //         } else {
    //             $notification = array(
    //                 'message' => 'Unsuccessful to Update data in Zone list',
    //                 'alert-type' => 'error',
    //             );
    //             return redirect()->back()->with($notification);
    //         }
    //     } else {
    //         $Companies = Company::where('is_delete', 0)
    //             ->select(['id', 'comp_name'])
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         $ZoneData = Zone::where('id', $id)->first();
    //         return view('GNL.ZoneList.edit', compact('ZoneData', 'Companies'));
    //     }
    // }

    public function view($id = null)
    {
        $Companies = Company::where('is_delete', 0)
            ->select(['id', 'comp_name'])
            ->orderBy('id', 'DESC')
            ->get();
        $ZoneData = Zone::where('id', $id)->first();
        return view('GNL.ZoneList.view', compact('ZoneData', 'Companies'));
    }

    public function delete($id = null)
    {
        $ZoneData = Zone::where('id', $id)->first();
        $ZoneData->is_delete = 1;
        $delete = $ZoneData->save();
        DB::table('gnl_branchs')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['zone_id', $ZoneData->id]
            ])
            ->update(['zone_id' => null]);
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

    public function isActive($id = null)
    {
        $ZoneData = Zone::where('id', $id)->first();
        if ($ZoneData->is_active == 1) {
            $ZoneData->is_active = 0;
        } else {
            $ZoneData->is_active = 1;
        }

        $Status = $ZoneData->save();

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

    public function ajaxZoneListLoad(Request $request)
    {

        if ($request->ajax()) {

            if ($request->ajax()) {

                $Edit = false;
                $RegionArray = array(0);
                $ExceptRegionArray = array(0);

                $ZoneID = (isset($request->ZoneID)) ? $request->ZoneID : false;

                $zoneWiseRegion = DB::table('gnl_zones')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->select(DB::raw('GROUP_CONCAT(region_arr) as region_arr'))
                    ->where(function ($zoneWiseRegion) use ($ZoneID) {
                        if (!empty($ZoneID)) {
                            $zoneWiseRegion->where('id', '<>', $ZoneID);
                        }
                    })
                    ->first();

                if ($zoneWiseRegion) {
                    $ExceptRegionArray = explode(',', $zoneWiseRegion->region_arr);
                }

                if ($ZoneID) {
                    $Edit = true;

                    $selectRegion = DB::table('gnl_zones')
                        ->where([['is_active', 1], ['is_delete', 0], ['id', $ZoneID]])
                        ->select(DB::raw('GROUP_CONCAT(region_arr) as region_arr'))
                        ->first();

                    if ($selectRegion) {
                        $RegionArray = explode(',', $selectRegion->region_arr);
                    }
                }

                $RegionData = DB::table('gnl_regions')
                    ->select(DB::raw('id, region_name'))
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereNotIn('id', $ExceptRegionArray)
                    ->get();

                $output = '<div class="row">';

                if (count($RegionData) > 0) {
                    $i = 0;
                    foreach ($RegionData as $Region) {

                        if ($Edit && in_array($Region->id, $RegionArray)) {
                            $CheckText = 'checked';
                        } else {
                            $CheckText = '';
                        }

                        $output .= '<div class="col-lg-4">';
                        $output .= '<div class="checkbox-custom checkbox-primary">';
                        $output .= '<input type="checkbox" ' . $CheckText . ' class="checkboxs" name="region_arr[]" id="region_arr_' . $i . '" value="' . $Region->id . '" />';
                        $output .= '<label for="region_arr_' . $i . '" style="color:#000;">' . $Region->region_name . '</label>';
                        $output .= '</div>';
                        $output .= '</div>';

                        $i++;

                        if (($i % 3) == 0) {
                            $output .= '</div>';
                            $output .= '<div class="row">';
                        }
                    }
                } else {
                    $output .= '<div class="col-lg-12 text-center">';
                    $output .= '<p style="color:red;">No new region for selecting.</p>';
                    $output .= '</div>';
                }

                $output .= '</div>';

                echo $output;
            }
        }
    }

    // public function ajaxZoneListLoad(Request $request)
    // {

    //     if ($request->ajax()) {

    //         if ($request->ajax()) {

    //             $Edit = false;
    //             $AreaArray = array(0);
    //             $ExceptAreaArray = array(0);

    //             $ZoneID = (isset($request->ZoneID)) ? $request->ZoneID : false;

    //             $zoneWiseArea = DB::table('gnl_zones')
    //                 ->where([['is_active', 1], ['is_delete', 0]])
    //                 ->select(DB::raw('GROUP_CONCAT(area_arr) as area_arr'))
    //                 ->where(function ($zoneWiseArea) use ($ZoneID) {
    //                     if (!empty($ZoneID)) {
    //                         $zoneWiseArea->where('id', '<>', $ZoneID);
    //                     }
    //                 })
    //                 ->first();

    //             if ($zoneWiseArea) {
    //                 $ExceptAreaArray = explode(',', $zoneWiseArea->area_arr);
    //             }

    //             if ($ZoneID) {
    //                 $Edit = true;

    //                 $selectArea = DB::table('gnl_zones')
    //                     ->where([['is_active', 1], ['is_delete', 0], ['id', $ZoneID]])
    //                     ->select(DB::raw('GROUP_CONCAT(area_arr) as area_arr'))
    //                     ->first();

    //                 if ($selectArea) {
    //                     $AreaArray = explode(',', $selectArea->area_arr);
    //                 }
    //             }

    //             $AreaData = DB::table('gnl_areas')
    //                 ->select(DB::raw('id, area_name'))
    //                 ->where([['is_delete', 0], ['is_active', 1]])
    //                 ->whereNotIn('id', $ExceptAreaArray)
    //                 ->get();

    //             $output = '<div class="row">';

    //             if (count($AreaData) > 0) {
    //                 $i = 0;
    //                 foreach ($AreaData as $Area) {

    //                     if ($Edit && in_array($Area->id, $AreaArray)) {
    //                         $CheckText = 'checked';
    //                     } else {
    //                         $CheckText = '';
    //                     }

    //                     $output .= '<div class="col-lg-4">';
    //                     $output .= '<div class="checkbox-custom checkbox-primary">';
    //                     $output .= '<input type="checkbox" ' . $CheckText . ' class="checkboxs" name="area_arr[]" id="area_arr_' . $i . '" value="' . $Area->id . '" />';
    //                     $output .= '<label for="area_arr_' . $i . '" style="color:#000;">' . $Area->area_name . '</label>';
    //                     $output .= '</div>';
    //                     $output .= '</div>';

    //                     $i++;

    //                     if (($i % 3) == 0) {
    //                         $output .= '</div>';
    //                         $output .= '<div class="row">';
    //                     }
    //                 }
    //             } else {
    //                 $output .= '<div class="col-lg-12 text-center">';
    //                 $output .= '<p style="color:red;">No new area for selecting.</p>';
    //                 $output .= '</div>';
    //             }

    //             $output .= '</div>';

    //             echo $output;
    //         }
    //     }
    // }


}
