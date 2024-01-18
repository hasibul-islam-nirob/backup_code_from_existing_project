<?php
namespace App\Http\Controllers\HR\Process;
use Illuminate\Support\Facades\DB;
use DateTime;
class ApprovalScript{
    public function __invoke()
    {
        DB::beginTransaction();
        try{
            $queries = DB::table('hr_approval_queries')->where('execution_date', '<=', (new DateTime())->format('Y-m-d'))->get()->pluck('query');
            foreach($queries as $sql){
                DB::connection()->getPdo()->exec($sql);
            }
            DB::table('hr_approval_queries')->where('execution_date', '<=', (new DateTime())->format('Y-m-d'))->delete();
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
        }
    }
}