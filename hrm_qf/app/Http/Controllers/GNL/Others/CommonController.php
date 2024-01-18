<?php

namespace App\Http\Controllers\GNL\Others;

use App\Http\Controllers\Controller;
use App\Services\HtmlService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getDistricts(Request $req)
    {
        $districts = DB::table('gnl_districts')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->divisionId != '' ? $districts->where('division_id', $req->divisionId) : false;

        $districts = $districts->pluck('district_name', 'id')->all();

        return response()->json($districts);
    }

    public function getUpazilas(Request $req)
    {
        $upazilas = DB::table('gnl_upazilas')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->districtId != '' ? $upazilas->where('district_id', $req->districtId) : false;

        $upazilas = $upazilas->pluck('upazila_name', 'id')->all();

        return response()->json($upazilas);
    }

    public function getUnions(Request $req)
    {
        $unions = DB::table('gnl_unions')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->upazilaId != '' ? $unions->where('upazila_id', $req->upazilaId) : false;

        $unions = $unions->pluck('union_name', 'id')->all();

        return response()->json($unions);
    }

    public function getVillages(Request $req)
    {
        $villages = DB::table('gnl_villages')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
            ]);

        $req->unionId != '' ? $villages->where('union_id', $req->unionId) : false;

        $villages = $villages->pluck('village_name', 'id')->all();

        return response()->json($villages);
    }

    public function getBanks(Request $req){
        $banks = DB::table('hr_banks')
            ->where([
                ['is_delete', 0],
            ]);

        $req->id != '' ? $banks->where('id', $req->id) : false;

        $banks = $banks->pluck('name', 'id')->all();

        return response()->json($banks);
    }

    public function getBankBranches(Request $req){
        $branch = DB::table('hr_bank_branches')
            ->where([
                ['is_delete', 0],
                ['bank_id', $req->bankId]
            ]);

        //$req->bankId != '' ? $branch->where('bank_id', $req->bankId) : false;

        $branch = $branch->pluck('name', 'id')->all();

        return response()->json($branch);
    }

    public function getProjectType(Request  $req){
        $projTypes = DB::table('gnl_project_types')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
                ['project_id', $req->projectId]
            ]);

        //$req->bankId != '' ? $branch->where('bank_id', $req->bankId) : false;

        $projTypes = $projTypes->pluck('project_type_name', 'id')->all();

        return response()->json($projTypes);
    }

    public function get_employees_options_by_branch(Request $request, $branchId){
        return response()->json(HtmlService::getOptionsForEmployee($branchId));
    }

    public function search_employee_and_get_options(Request $request){
        
        $branchId = null; $departmentId = null; $designationId = null; $empCode = null;
        if(isset($request['branch_id']) && $request['branch_id'] != ''){
            $branchId = $request['branch_id'];
        }
        if(isset($request['department_id']) && $request['department_id'] != ''){
            $departmentId = $request['department_id'];
        }
        if(isset($request['designation_id']) && $request['designation_id'] != ''){
            $designationId = $request['designation_id'];
        }
        return response()->json(HtmlService::searchEmployeeAndGetOptions($branchId, $departmentId, $designationId, $empCode));
    }

}
