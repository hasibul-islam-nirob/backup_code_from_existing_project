<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TmsService
{
    /*Generate Task Code Start*/
    public static function generateTaskTypeCode($module, $task_type)
    {

        ////////// DB table use korte hbe for task data////////
        // $TaskData = DB::table('tms_tasks')->get();
        // $TaskTable = 'App\Model\GNL\TMS\Task';


        if(!empty($module || $task_type)){

            $moduleData = DB::table('gnl_sys_modules')
                // ->where([['is_delete', 0], ['is_active', 1], ['id', $module]])
                ->where([['is_delete', 0], ['id', $module]])
                ->select('module_short_name')
                ->pluck('module_short_name')
                ->first();

            // $moduleName = $moduleData->module_short_name;
            $moduleName = $moduleData;
            $typeCode = 00; //Default two digit code to show user only

            if($task_type){
                $taskTypeData = DB::table('tms_task_types')
                    ->where([['is_delete', 0], ['is_active', 1], ['id', $task_type]])
                    ->select('task_type_code')
                    ->pluck('task_type_code')
                    ->first();

                $typeCode = $taskTypeData;
            }

            $prefix = "TMS." . $moduleName . "." . sprintf("%02d", ($typeCode)) . ".";
            $prefix = strtoupper($prefix);

            $record = DB::table('tms_tasks')
                ->select(['id', 'task_code'])
                ->where('task_code', 'LIKE', "{$prefix}%")
                ->orderBy('task_code', 'DESC')
                ->first();

            if ($record) {
                $OldBillNoA = explode($prefix, $record->task_code);
                $newCode     = $prefix . sprintf("%05d", ($OldBillNoA[1] + 1));
            } else {
                $newCode = $prefix . sprintf("%05d", 1);
            }

            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Task Code End*/

    /*Get All Task Types Start*/
    public static function fnGetAllTaskType()
    {
        $typeData = DB::table('tms_task_types')->where([['is_delete', 0], ['is_active', 1]])->get();

        return $typeData;

    }
    /*Get All Task Types End*/

}
