<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeLevelsController extends Controller
{
    public function index(Request $request){
        if($request->isMethod('post')){
            if(!is_null($request->grade) && !is_null($request->level)){
                DB::beginTransaction();
                try{
                        DB::table('hr_config')->where('title', 'grade')
                        ->update([
                            'content'     => $request->grade,
                        ]);
                        DB::table('hr_config')->where('title', 'level')
                        ->update([
                            'content'     => $request->level,
                        ]);
                        DB::commit();
                        $notification = array(
                            'message'    => 'Grade and Level has been added Successfully',
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
            elseif(!is_null($request->grade) && is_null($request->level)){
                DB::beginTransaction();
                try{
                        DB::table('hr_config')->where('title', 'grade')
                        ->update([
                            'content'     => $request->grade,
                        ]);
                        DB::commit();
                        $notification = array(
                            'message'    => 'Grade has been added Successfully',
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
            elseif(is_null($request->grade) && !is_null($request->level)){
                DB::beginTransaction();
                try{
                        DB::table('hr_config')->where('title', 'level')
                        ->update([
                            'content'     => $request->level,
                        ]);
                        DB::commit();
                        $notification = array(
                            'message'    => 'Level has been added Successfully',
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
            }else {
                $notification = array(
                            'alert-type' => 'error',
                            'message'    => 'No data provided!',
                        );
                    return response()->json($notification);
            }

                
        }
        $grades = DB::table('hr_config')->where('title', 'grade')->first()->content;
        // dd($grades);
        $levels = DB::table('hr_config')->where('title', 'level')->first()->content;
        // dd($levels);
        $data = array(
            'grades'          => $grades,
            'levels'          => $levels,
        );
        // dd($data);
        return view('HR.Configuration.GradeAndLevels.index',$data);
    }

    public function duplicateCheck(Request $request){
        $grades = collect(json_decode(DB::table('hr_config')->where('title', 'grade')->first()->content))->toArray();
        $levels = collect(json_decode(DB::table('hr_config')->where('title', 'level')->first()->content))->toArray();
        if($grades)
        if(in_array(($request->grade),$grades) && in_array(($request->level),$levels))
        {
            return response()->json([
                'success' => false,
                'message' => "This grade and level already exist!",
            ]);
        }elseif((!in_array(($request->grade),$grades)) && in_array(($request->level),$levels))
        {
            return response()->json([
                'success' => false,
                'message' => "This level already exist!",
            ]);
        }elseif(in_array(($request->grade),$grades) && (!in_array(($request->level),$levels)))
        {
            return response()->json([
                'success' => false,
                'message' => "This grade already exist!",
            ]);
        }
    }
}
