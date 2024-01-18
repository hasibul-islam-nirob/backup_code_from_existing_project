<?php

namespace App\Http\Controllers\HR\Configuration;

use Redirect;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\HR\EmployeeDesignation;
use App\Model\HR\DesignationHierarchy;
use App\Services\CommonService as Common;

class DesignationHierarchyController extends Controller
{
    public function index()
    {
        $designations = DB::table('hr_designations')->where('is_delete',0)->get();
        $hierarchyData = DesignationHierarchy::all();
        return view('HR.Configuration.DesignationHierarchy.index', compact('designations', 'hierarchyData'));
    }

    public function add(Request $request)
    {
        //dd($request->all());
        DB::beginTransaction();
        // temporary comment
        // DesignationHierarchy::truncate();

        try {
            foreach ($request->all() as $key => $value){
                if ($key[0] == 'n'){
                    $date = [
                        'path' => $key,
                        'designation_id' => $value,
                        'no_of_child' => (isset($request['c-'.$key])) ? $request['c-'.$key] : 0
                    ];
                    DesignationHierarchy::create($date);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data saved successfully!!'
            ]);
        }
        catch (Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Error to save data!!'
            ]);
        }
    }

}
