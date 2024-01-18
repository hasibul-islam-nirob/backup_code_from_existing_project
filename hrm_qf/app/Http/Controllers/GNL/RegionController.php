<?php

namespace App\Http\Controllers\GNL;

use App\Model\GNL\Region;
use App\Model\GNL\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Redirect;

class RegionController extends Controller
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

            $masterQuery = Region::where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('region_name', 'LIKE', "%{$search}%")
                            ->orWhere('region_code', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('region_code', 'ASC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = Region::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row){
                $zoneWiseAreaName = DB::table('gnl_areas')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', explode(',', $Row->area_arr))
                    ->select(DB::raw('GROUP_CONCAT(" ",area_name, " [", area_code, "] ") as area_name'))
                    ->first();
                $DataSet[] = [
                    'id' => ++$i,
                    'region_name' => $Row->region_name,
                    'region_code' => $Row->region_code,
                    'area_name' =>  ($zoneWiseAreaName) ? $zoneWiseAreaName->area_name : $Row->area_arr,
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
            return view('GNL.Region.index');
        }
    }

    // public function add(Request $request)
    // {

    //     if ($request->isMethod('post')) {
    //         $validateData = $request->validate([
    //             'region_name' => 'required',
    //             'region_code' => 'required',
    //         ]);

    //         $RequestData = $request->all();
    //         $ZoneArr = (isset($RequestData['zone_arr']) ? $RequestData['zone_arr'] : array());

    //         $BranchArr = "";
    //         $AreaArr = "";

    //         if (count($ZoneArr) > 0) {

    //             $zoneWiseArea = DB::table('gnl_zones')
    //                 ->where([['is_delete', 0], ['is_active', 1]])
    //                 ->whereIn('id', $ZoneArr)
    //                 ->select(DB::raw('GROUP_CONCAT(area_arr) as area_arr, GROUP_CONCAT(branch_arr) as branch_arr'))
    //                 ->first();

    //             if ($zoneWiseArea) {
    //                 $AreaArr = $zoneWiseArea->area_arr;
    //                 $BranchArr = $zoneWiseArea->branch_arr;
    //             }

    //             $RequestData['zone_arr'] = implode(',', $ZoneArr);
    //             $RequestData['area_arr'] = $AreaArr;
    //             $RequestData['branch_arr'] = $BranchArr;
    //         }

    //         $isInsert = Region::create($RequestData);

    //         if ($isInsert) {
    //             $notification = array(
    //                 'message' => 'Successfully Inserted New Area List',
    //                 'alert-type' => 'success',
    //             );

    //             return Redirect::to('gnl/region')->with($notification);
    //         } else {
    //             $notification = array(
    //                 'message' => 'Unsuccessful to insert data in Region List',
    //                 'alert-type' => 'error',
    //             );
    //             return redirect()->back()->with($notification);
    //         }
    //     } else {

    //         $Companies = Company::where('is_delete', 0)
    //             ->select(['id', 'comp_name'])
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         return view('GNL.Region.add', compact('Companies'));
    //     }
    // }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $validateData = $request->validate([
                'region_name' => 'required',
                'region_code' => 'required',
            ]);

            $RequestData = $request->all();
            $AreaArr = (isset($RequestData['area_arr']) ? $RequestData['area_arr'] : array());
            if (count($AreaArr) > 0) {

                $RequestData['area_arr'] = implode(',', $AreaArr);
            }

            $isInsert = Region::create($RequestData);
            $lastInsertQuery = Region::latest()->first();
            $regionId = $lastInsertQuery->id;
            if (count($AreaArr) > 0) {
                $branchIds = DB::table('gnl_areas')
                        ->where([
                            ['is_active', 1],
                            ['is_delete', 0]
                        ])
                        ->whereIn('id', $AreaArr)
                        ->select('branch_arr')
                        ->get()
                        ->toArray();
                if (count($branchIds) > 0) {
                    foreach($branchIds as $Row)
                    {
                        $branchIds = DB::table('gnl_branchs')
                                    ->where([
                                        ['is_active', 1],
                                        ['is_delete', 0]
                                    ])
                                    ->whereIn('id', explode(',', $Row->branch_arr))
                                    ->update(['region_id' => $regionId]);
                    }
                }
            }
            if ($isInsert) {
                $notification = array(
                    'message' => 'Successfully Inserted New Area List',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/region')->with($notification);
            } else {
                $notification = array(
                    'message' => 'Unsuccessful to insert data in Region List',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {

            $Companies = Company::where('is_delete', 0)
                ->select(['id', 'comp_name'])
                ->orderBy('id', 'DESC')
                ->get();
            return view('GNL.Region.add', compact('Companies'));
        }
    }

    public function edit(Request $request, $id = null)
    {

        $RegionData = Region::where('id', $id)->first();

        if ($request->isMethod('post')) {

            $validateData = $request->validate([
                'region_name' => 'required',
                'region_code' => 'required',
            ]);

            $RequestData = $request->all();
            $AreaArr = (isset($RequestData['area_arr']) ? $RequestData['area_arr'] : array());

            if (count($AreaArr) > 0) {
                $RequestData['area_arr'] = implode(',', $AreaArr);
            }

            $isUpdate = $RegionData->update($RequestData);
            DB::table('gnl_branchs')
                ->where([
                    ['is_active', 1],
                    ['is_delete', 0],
                    ['region_id', $RegionData->id]
                ])
                ->update(['region_id' => null]);
            if (count($AreaArr) > 0) {
                $branchIds = DB::table('gnl_areas')
                        ->where([
                            ['is_active', 1],
                            ['is_delete', 0]
                        ])
                        ->whereIn('id', $AreaArr)
                        ->select('branch_arr')
                        ->get()
                        ->toArray();
                if (count($branchIds) > 0) {
                    foreach($branchIds as $Row)
                    {
                        DB::table('gnl_branchs')
                            ->where([
                                ['is_active', 1],
                                ['is_delete', 0]
                            ])
                            ->whereIn('id', explode(',', $Row->branch_arr))
                            ->update(['region_id' => $RegionData->id]);
                    }
                }
            }
            if ($isUpdate) {
                $notification = array(
                    'message' => 'Successfully Update New Region',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/region')->with($notification);

            } else {
                $notification = array(
                    'message' => 'Unsuccessful to Update data in Region',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
        } else {

            $RegionData = Region::where('id', $id)->first();
            $Companies = Company::where('is_delete', 0)
                ->select(['id', 'comp_name'])
                ->orderBy('id', 'DESC')
                ->get();
            return view('GNL.Region.edit', compact('Companies', 'RegionData'));
        }
    }

    public function view($id = null)
    {
        $RegionData = Region::where('id', $id)->first();
        //$ZoneData = Zone::where($ZoneData->'zone_name')->get();
        return view('GNL.Region.view', compact('RegionData'));
    }

    public function delete($id = null)
    {
        $RegionData = Region::where('id', $id)->first();
        $RegionData->is_delete = 1;
        $delete = $RegionData->save();
        DB::table('gnl_branchs')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['region_id', $RegionData->id]
            ])
            ->update(['region_id' => null]);
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
        $RegionData = Region::where('id', $id)->first();
        if ($RegionData->is_active == 1) {
            $RegionData->is_active = 0;
        } else {
            $RegionData->is_active = 1;
        }

        $Status = $RegionData->save();

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

    // public function ajaxRegionLoad(Request $request)
    // {

    //     if ($request->ajax()) {

    //         $Edit = false;
    //         $ZoneArray = array(0);
    //         $ExceptZoneArray = array(0);

    //         $RegionId = (isset($request->RegionId)) ? $request->RegionId : false;

    //         $regionWiseZone = DB::table('gnl_regions')
    //             ->where([['is_active', 1], ['is_delete', 0]])
    //             ->select(DB::raw('GROUP_CONCAT(zone_arr) as zone_arr'))
    //             ->where(function ($regionWiseZone) use ($RegionId) {
    //                 if (!empty($RegionId)) {
    //                     $regionWiseZone->where('id', '<>', $RegionId);
    //                 }
    //             })
    //             ->first();

    //         if ($regionWiseZone) {
    //             $ExceptZoneArray = explode(',', $regionWiseZone->zone_arr);
    //         }

    //         if ($RegionId) {
    //             $Edit = true;

    //             $selectZone = DB::table('gnl_regions')
    //                 ->where([['is_active', 1], ['is_delete', 0], ['id', $RegionId]])
    //                 ->select(DB::raw('GROUP_CONCAT(zone_arr) as zone_arr'))
    //                 ->first();

    //             if ($selectZone) {
    //                 $ZoneArray = explode(',', $selectZone->zone_arr);
    //             }
    //         }

    //         $ZoneData = DB::table('gnl_zones')
    //             ->select(DB::raw('id, zone_name'))
    //             ->where([['is_delete', 0], ['is_active', 1]])
    //             ->whereNotIn('id', $ExceptZoneArray)
    //             ->get();

    //         $output = '<div class="row">';

    //         if (count($ZoneData) > 0) {
    //             $i = 0;
    //             foreach ($ZoneData as $Zone) {

    //                 if ($Edit && in_array($Zone->id, $ZoneArray)) {
    //                     $CheckText = 'checked';
    //                 } else {
    //                     $CheckText = '';
    //                 }

    //                 $output .= '<div class="col-lg-4">';
    //                 $output .= '<div class="checkbox-custom checkbox-primary">';
    //                 $output .= '<input type="checkbox" ' . $CheckText . ' class="checkboxs" name="zone_arr[]" id="zone_arr_' . $i . '" value="' . $Zone->id . '" />';
    //                 $output .= '<label for="zone_arr_' . $i . '" style="color:#000;">' . $Zone->zone_name . '</label>';
    //                 $output .= '</div>';
    //                 $output .= '</div>';

    //                 $i++;

    //                 if (($i % 3) == 0) {
    //                     $output .= '</div>';
    //                     $output .= '<div class="row">';
    //                 }
    //             }
    //         } else {

    //             $output .= '<div class="col-lg-12 text-center">';
    //             $output .= '<p style="color:red;">No new zone for selecting.</p>';
    //             $output .= '</div>';
    //         }
    //         $output .= '</div>';
    //         echo $output;
    //     }
    // }

    public function ajaxAreaListLoad(Request $request)
    {
        if ($request->ajax()) {

            $Edit = false;
            $AreaArray = array(0);
            $ExceptAreaArray = array(0);

            $RegionID = (isset($request->RegionId)) ? $request->RegionId : false;
            
            $regionWiseArea = DB::table('gnl_regions')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->select(DB::raw('GROUP_CONCAT(area_arr) as area_arr'))
                ->where(function ($regionWiseArea) use ($RegionID) {
                    if (!empty($RegionID)) {
                        $regionWiseArea->where('id', '<>', $RegionID);
                    }
                })
                ->first();

            if ($regionWiseArea) {
                $ExceptAreaArray = explode(',', $regionWiseArea->area_arr);
            }

            if ($RegionID) {
                $Edit = true;

                $selectArea = DB::table('gnl_regions')
                    ->where([['is_active', 1], ['is_delete', 0], ['id', $RegionID]])
                    ->select(DB::raw('GROUP_CONCAT(area_arr) as area_arr'))
                    ->first();

                if ($selectArea) {
                    $AreaArray = explode(',', $selectArea->area_arr);
                }
            }

            $AreaData = DB::table('gnl_areas')
                ->select(DB::raw('id, area_name'))
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereNotIn('id', $ExceptAreaArray)
                ->get();

            $output = '<div class="row">';

            if (count($AreaData) > 0) {
                $i = 0;
                foreach ($AreaData as $Area) {

                    if ($Edit && in_array($Area->id, $AreaArray)) {
                        $CheckText = 'checked';
                    } else {
                        $CheckText = '';
                    }

                    $output .= '<div class="col-lg-4">';
                    $output .= '<div class="checkbox-custom checkbox-primary">';
                    $output .= '<input type="checkbox" ' . $CheckText . ' class="checkboxs" name="area_arr[]" id="area_arr_' . $i . '" value="' . $Area->id . '" />';
                    $output .= '<label for="area_arr_' . $i . '" style="color:#000;">' . $Area->area_name . '</label>';
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
                $output .= '<p style="color:red;">No new area for selecting.</p>';
                $output .= '</div>';
            }

            $output .= '</div>';

            echo $output;
        }
    }

}
