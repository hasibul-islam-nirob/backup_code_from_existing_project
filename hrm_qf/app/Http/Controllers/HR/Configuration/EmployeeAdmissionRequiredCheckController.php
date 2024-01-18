<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EmployeeAdmissionRequiredCheckController extends Controller
{
    public function index(Request $req)
    {
        if($req->isMethod('post')){
            $requiredFields = array();
            $hrConfigs = DB::table('hr_config')
                    ->where('title', 'employeeRequiredFields')
                    ->select('content')
                    ->first()
                    ->content;    
            $fields = array_keys((array)json_decode($hrConfigs));
            $requiredFields = array_keys($req->all());
            $hrConfigsRequired = array_fill_keys($requiredFields, 'required');

            $nonExistingFields = array_diff ( $fields , $requiredFields ); 
            
            $hrConfigsNotRequired = array_fill_keys($nonExistingFields,'not-required');
            //dd($hrConfigsNotRequired);
            $all_fields = array_merge($hrConfigsRequired,$hrConfigsNotRequired);
            $hrConfigsRequired = json_encode($all_fields);
            // dd($hrConfigsRequired);
            DB::beginTransaction();
                try{
                        DB::table('hr_config')->where('title', 'employeeRequiredFields')
                        ->update([
                            'content'     => $hrConfigsRequired,
                        ]);
                        DB::commit();
                        $notification = array(
                            'message'    => 'Required Data Has Been Updated Successfully',
                            'alert-type' => 'success',
                        );

                        return response()->json($notification);
                    } catch (\Throwable $e) {
                        DB::rollback();
                        $notification = array(
                            'alert-type' => 'error',
                            'message'    => 'Something went wrong',
                            'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                        );
                    return response()->json($notification);
                }
        }
        
        $requiredFields = collect(json_decode(DB::table('hr_config')->where('title', 'employeeRequiredFields')->first()->content))->toArray();
        
        $data = array(
            'requiredFields'          => $requiredFields,
        );
        return view ('HR.Configuration.EmpAdmissionRequiredCheck.index',$data);
    }
}
