<?php

namespace App\Http\Controllers\GNL;
use Response;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\GNL\SignatureSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SignatureSettingsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     parent::__construct();
    // }
    
    public function index(Request $req) {

        $dataSignature =SignatureSettings::where([['gnl_signature_setting.is_delete', 0]])
                   
                    ->select('gnl_signature_setting.*')
                    ->orderby('gnl_signature_setting.module_id')
                    ->get();
       
        foreach ($dataSignature as $key => $value){
           
            $modules = '';
            $moduleIDs = explode(',', $value->module_id);
           
            $modulearray = DB::table('gnl_sys_modules')
                ->whereIn('id', $moduleIDs)->pluck('module_name')->toArray();
            $modules = implode(',', $modulearray);
            $dataSignature[$key]['module_name'] = $modules;
           
        }            
        
            $data = array(
                // 'module_data' => $module_data,
                'dataSignature' => $dataSignature,
                // 'signatureImageFilename' => $signatureImageFilename,
            );
            
        // dd($dataSignature);
        return view('GNL.SignatureSettings.index',$data);
    }

    public function add(Request $req) {
        
        if ($req->isMethod('post')) {
           
            $RequestData = $req->all();
            // dd($RequestData);
            DB::beginTransaction();

            try {

                $head_title = (isset($RequestData['head_title']) ? $RequestData['head_title'] : array());
                $head_signatorDesignationId = (isset($RequestData['head_signatorDesignationId']) ? $RequestData['head_signatorDesignationId'] : array());
                $head_signatorEmployeeId = (isset($RequestData['head_signatorEmployeeId']) ? $RequestData['head_signatorEmployeeId'] : array());
                $head_positionOrder = (isset($RequestData['head_positionOrder']) ? $RequestData['head_positionOrder'] : array());
                $head_signatorApplicable = (isset($RequestData['head_signatorApplicable']) ? $RequestData['head_signatorApplicable'] : array());
                $modules = isset($RequestData['module_id']) ? $RequestData['module_id'] : array();

                if (empty($modules)) {
                    $notification = array(
                        'message' => 'Module is not selected',
                        'alert-type' => 'error',
                    );
                    return redirect()->back()->with($notification);
                }
                // dd(implode(",",$modules));

                $RequestData2['module_id'] = implode(",",$modules);
                    foreach ($head_title as $key => $headtitle) {
                        if (!empty($headtitle) && !empty($head_signatorDesignationId[$key])) {
                            $RequestData2['applicableFor'] = $head_signatorApplicable[$key];
                            $RequestData2['title'] = $headtitle;
                            $RequestData2['signatorDesignationId'] = $head_signatorDesignationId[$key];
                            $RequestData2['signatorEmployeeId'] = $head_signatorEmployeeId[$key];                            
                            $RequestData2['positionOrder'] = $head_positionOrder[$key];
                            $isInsertDetails = SignatureSettings::create($RequestData2);
                            // dd($isInsertDetails);
                        }
                        else{
                            // dd($head_signatorDesignationId[1], $key);
                        }
                    }
                
                    // $RequestData2[]='';
                    // $module_ids = $RequestData['module_id']; // Assuming $RequestData['module_id'] is an array of module_id values

                    // foreach ($module_ids as $module_id) {
                    //     $RequestData2['module_id'] = $module_id;
                    
                    //     foreach ($head_title as $key => $headtitle) {
                    //         if (!empty($headtitle) && !empty($head_signatorDesignationId[$key])) {
                    //             $RequestData2['applicableFor'] = $head_signatorApplicable[$key];
                    //             $RequestData2['title'] = $headtitle;
                    //             $RequestData2['signatorDesignationId'] = $head_signatorDesignationId[$key];
                    //             $RequestData2['signatorEmployeeId'] = $head_signatorEmployeeId[$key];
                    //             $RequestData2['positionOrder'] = $head_positionOrder[$key];
                    
                    //             // Insert the record into the SignatureSettings table
                    //             $isInsertDetails = SignatureSettings::create($RequestData2);
                    //         }
                    //     }
                    // }
                    

                // $branch_title = (isset($RequestData['branch_title']) ? $RequestData['branch_title'] : array());
                // $branch_signatorDesignationId = (isset($RequestData['branch_signatorDesignationId']) ? $RequestData['branch_signatorDesignationId'] : array());
                // // $branch_signatorEmployeeId = (isset($RequestData['branch_signatorEmployeeId']) ? $RequestData['branch_signatorEmployeeId'] : array());
                // $branch_positionOrder = (isset($RequestData['branch_positionOrder']) ? $RequestData['branch_positionOrder'] : array());
    

                //     foreach ($branch_title as $key => $branchtitle) {
                //         if (!empty($branchtitle) && !empty($branch_signatorDesignationId[$key])) {
                //             $RequestData2['applicableFor'] = 'Branch';
                //             $RequestData2['title'] = $branchtitle;
                //             $RequestData2['signatorDesignationId'] = $branch_signatorDesignationId[$key];
                //             // $RequestData2['signatorEmployeeId'] = $branch_signatorEmployeeId[$key];                            
                //             $RequestData2['positionOrder'] = $branch_positionOrder[$key];
                //             $isInsertDetails = SignatureSettings::create($RequestData2);
                //         }
                //     }

               
                // Your Code here
                DB::commit();
                // return
                $notification = array(
                    'message' => 'Successfully inserted Signature Settings',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/signature_set')->with($notification);
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to inserted Issue List',
                    'alert-type' => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );
                return redirect()->back()->with($notification);
                //return $e;
            }

           
        } 
        $EmpDesignations = DB::table('hr_designations')
                        ->where([['is_delete', 0]])
                        ->get();


        $dataSignature =SignatureSettings::where([['gnl_signature_setting.is_delete', 0]])
                        ->select('module_id')
                        ->distinct('module_id')
                        ->get();

        $alreadyHaveIds = array();

        if(count($dataSignature->pluck('module_id')->toArray()) > 0){
            $alreadyHaveIds = explode(",", implode(",", $dataSignature->pluck('module_id')->toArray()));
        }
                       
        $module_data =DB::table('gnl_sys_modules')->whereNotIn('id', $alreadyHaveIds)
        ->where([['is_delete', 0]])
        ->get();

        $data = array(
            'module_data' => $module_data,
            'EmpDesignations' => $EmpDesignations,
            // 'signatureImageFilename' => $signatureImageFilename,
        );
        return view('GNL.SignatureSettings.add',$data);
    }


    public function edit(Request $req,$id = null) {
        $module_id = SignatureSettings::where([['is_delete', 0]])
                        ->where('id',$id)
                        ->first()->module_id;
        // dd($module_id); 

        $moduleIDs = explode(',', $module_id);
        $modulearray = DB::table('gnl_sys_modules')
            ->whereIn('id', $moduleIDs)->pluck('module_name')->toArray();
        $modules = implode(',', $modulearray);
        $module_names = $modules;
       
      
        // dd($module_names);
        if ($req->isMethod('post')) {
            
            $RequestData = $req->all();
            $moduleIds = implode(',', $RequestData['module_id']);
            // dd($moduleIds);

            if(!empty($module_id)){
                SignatureSettings::where('module_id',$moduleIds)->delete();
            }
            
            // dd($RequestData);

            DB::beginTransaction();

            try {
                // $RequestData2['module_id'] = $RequestData['module_id'];
                $RequestData2['module_id'] = $moduleIds;

                $head_title = (isset($RequestData['head_title']) ? $RequestData['head_title'] : array());
                $head_signatorDesignationId = (isset($RequestData['head_signatorDesignationId']) ? $RequestData['head_signatorDesignationId'] : array());
                $head_signatorEmployeeId = (isset($RequestData['head_signatorEmployeeId']) ? $RequestData['head_signatorEmployeeId'] : array());
                $head_positionOrder = (isset($RequestData['head_positionOrder']) ? $RequestData['head_positionOrder'] : array());
                $head_signatorApplicable = (isset($RequestData['head_signatorApplicable']) ? $RequestData['head_signatorApplicable'] : array());
                   
                foreach ($head_title as $key => $headtitle) {
                    if (!empty($headtitle) && !empty($head_signatorDesignationId[$key])) {
                        $RequestData2['applicableFor'] = $head_signatorApplicable[$key];
                        $RequestData2['title'] = $headtitle;
                        $RequestData2['signatorDesignationId'] = $head_signatorDesignationId[$key];
                        $RequestData2['signatorEmployeeId'] = $head_signatorEmployeeId[$key];                            
                        $RequestData2['positionOrder'] = $head_positionOrder[$key];
                        $isInsertDetails = SignatureSettings::create($RequestData2);
                        // dd($isInsertDetails);
                    }
                    else{
                        // dd($head_signatorDesignationId[1], $key);
                    }
                }
                
              
                DB::commit();
                // return
                $notification = array(
                    'message' => 'Successfully inserted Signature Settings',
                    'alert-type' => 'success',
                );

                return Redirect::to('gnl/signature_set')->with($notification);
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to inserted Issue List',
                    'alert-type' => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );
                return redirect()->back()->with($notification);
                //return $e;
            }

        } 
        $employeeData = DB::table('hr_employees')
        ->where([['is_delete', 0],['is_active', 1]])
        ->get();
        
        
        // $headData = SignatureSettings::where([['is_delete', 0]])
        //                 ->where('applicableFor','HeadOffice')
        //                 ->where('module_id',$module_id)
        //                 ->get();
        // $branchData = SignatureSettings::where([['is_delete', 0]])
        //             ->where('applicableFor','Branch')
        //             ->where('module_id',$module_id)
        //             ->get();
        // $EmpDesignations = DB::table('hr_designations')
        //             ->where([['is_delete', 0]])
        //             ->get();

        // $module_data =DB::table('gnl_sys_modules')->where('id', $module_id)
        // ->where([['is_delete', 0]])
        // ->get();
       

        $module_ids = explode(',', $module_id);
        $modules = [];
        $branchs = [];
       
        $designations = []; 
        foreach ($module_ids as $key=> $single_module_id) {
            $headData = SignatureSettings::where([['is_delete', 0]])
                ->where('module_id', $module_id)
                ->get();
           
            $EmpDesignations = DB::table('hr_designations')
                ->where([['is_delete', 0]])
                ->get();

            $module_data = DB::table('gnl_sys_modules')->where('id', $single_module_id)
                ->where([['is_delete', 0]])
                ->get();
            
            $modules[$key] = $module_data;
            $designations[$key] = $EmpDesignations;
          
        }
                    
        $data = array(
            'module_data' => $modules,
            'module_id' => $module_id,
            'branchData' => $branchs,
            'headData' => $headData,
            'employeeData' => $employeeData,
            'EmpDesignations' => $designations,
            'module_name'     => $module_names,
        );
        // dd($data);
        return view('GNL.SignatureSettings.edit',$data);
    }
    public function delete(Request $req)
    {
        $wareaData = SignatureSettings::where('id', $req->id)->first();
        $wareaData->is_delete = 1;
        $delete = $wareaData->save();

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

    public function isActive(Request $req)
    {
        $wareaData = SignatureSettings::where('id', $req->id)->first();


        if( $wareaData->status== 1){
            $wareaData->status = 0;
        }else{
            $wareaData->status = 1;
        }
        

        $upate = $wareaData->save();

        if ($upate) {
            $notification = array(
                'message' => 'Successfully Status Changed',
                'alert-type' => 'success',
            );
           return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful changing Status',
                'alert-type' => 'error',
            );
           return redirect()->back()->with($notification);
        }
    }
}
