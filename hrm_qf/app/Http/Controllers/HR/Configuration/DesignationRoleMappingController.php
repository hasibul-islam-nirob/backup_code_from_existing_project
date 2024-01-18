<?php

namespace App\Http\Controllers\HR\Configuration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\Validator;


class DesignationRoleMappingController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                
            );

            $attributes = array(
                
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function update(Request $request)
    {
        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            DB::beginTransaction();
            try {
                DB::table('hr_designation_role_mapping')->delete();

                    foreach($request->position_id as $key => $val){

                        if ( isset($request->designation_ids[$val]) && isset($request->role_id[$val]) ) {
                            DB::table('hr_designation_role_mapping')->insert([
                                'position_id' => $request->position_id[$key],
                                'designation_ids' => implode(',', $request->designation_ids[$request->position_id[$key]]),
                                'role_id' => implode(',', $request->role_id[$request->position_id[$key]]),
                            ]);
                        }else{
                            continue;
                        }
                        
                    }

                DB::commit();

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);

            } catch (\Exception $e) {
                
                DB::rollback();

                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

}
