<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Services\CommonService as Common;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $current_route_name;
    protected $GlobalRole;
    protected $hoId;

    public function __construct()
    {
        $this->hoId = 1;

        $routearray = explode('/', Route::getCurrentRoute()->uri());

        if(in_array('hr', $routearray)){
            $this->current_route_name = Route::getCurrentRoute()->action['prefix'];
        }else{
            $this->current_route_name = Route::getCurrentRoute()->uri();
        }

        if (empty($this->current_route_name)) {
            $this->current_route_name = Route::getCurrentRoute()->uri();
        }


        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor

            $RolePermissionAll = (!empty(session()->get('LoginBy.user_role.role_permission'))) ? session()->get('LoginBy.user_role.role_permission') : array();

            $this->GlobalRole = (isset($RolePermissionAll[$this->current_route_name])) ? $RolePermissionAll[$this->current_route_name] : array();

            return $next($request);
        });

        View::share('current_route_name', $this->current_route_name);
        View::share('GlobalRole', $this->GlobalRole);
        View::share('hoId', $this->hoId);

        DB::listen(function ($query) {

            if (
                strpos($query->sql, 'query_log_branch')
                || strpos($query->sql, 'query_log_trans')
                || strpos($query->sql, 'query_log_fixed')
                || strpos($query->sql, 'query_log_transfer')
                || strpos($query->sql, 'access_query_log')
                || strpos($query->sql, 'gnl_sys_user_historys')
                || strpos($query->sql, 'gnl_sys_user_failed_login')
                || strpos($query->sql, 'gnl_sys_user_devices')
            ) {
                return false;
            }

            $insert = "insert";
            $update = "update";
            $delete = "delete from";


            if (preg_match("/{$insert}/i", $query->sql) || preg_match("/{$update}/i", $query->sql) || preg_match("/{$delete}/i", $query->sql)) {
                $q      = $query->sql;
                $needle = '?';

                // dd($query->bindings);
                foreach ($query->bindings as $replace) {
                    $pos = strpos($q, $needle);

                    if ($pos !== false) {

                        if (is_numeric($replace)) {
                            $replace = $replace;
                        } elseif (is_a($replace, 'DateTime')) {
                            // dd($replace);
                            $replace = "'" . $replace . "'";
                            // $replace = "'" . $replace->format('Y-m-d') . "'";
                        } elseif ($replace == null || $replace == null || $replace == 'NULL' || $replace == 'null' || $replace == '') {
                            $replace = "NULL";
                        } else {
                            $replace = "'" . $replace . "'";
                        }

                        $q = substr_replace($q, $replace, $pos, strlen($needle));
                    }
                }


                /*
                  ## put log data from controller hide all base model code andd on this after testing
                  $putSessionDataFromControllerFnCall = Common::queryAnalysisAndPutSession($query,$q);
                */

                $logArray      = array();
                $dataFromModel = session()->get('logData');
                session()->forget('logData');

                $logArray['company_id'] = Common::getCompanyId();
                $logArray['query_sql']  = $q;

                if ($dataFromModel != null) {

                    ## access query log insert
                    if (in_array($dataFromModel['table_name'], DB::table('access_query_table')->where('is_active', 1)->pluck('table_name')->toArray())) {
                        $accessLogData                 = $dataFromModel;
                        $accessLogData['query_sql']    = $logArray['query_sql'];
                        $accessLogData['execution_by'] = Auth::user()->id;

                        DB::table('access_query_log')->insert($accessLogData);
                    }

                    ## for offline pos
                    $transactions  = DB::table('query_db_ho')->where('is_active', 1)->pluck('table_name')->toArray();
                    $transactionsB = DB::table('query_db_branch')->where('is_active', 1)->pluck('table_name')->toArray();
                    $ignore_table  = DB::table('query_db_ho_ig')->where('is_active', 1)->pluck('table_name')->toArray();


                    if (count($transactions) > 0 || count($transactionsB) > 0) {

                        $logArray = array_merge($logArray, $dataFromModel);

                        if ($logArray['table_name'] == "pos_transfers_m" || $logArray['table_name'] == "pos_transfers_d") {
                            $pos = strpos($logArray['query_sql'], $logArray['table_name']);
                            if ($pos !== false) {
                                DB::table('query_log_transfer')->insert($logArray);
                            }
                        } else {

                            $logArray_duplicate = $logArray;
                            // unset($logArray_duplicate['branch_to']);
                            // unset($logArray_duplicate['branch_from']);

                            $logArray_duplicate['branch_id'] = Common::getBranchId();
                            // $ignore_table = DB::table('query_db_ho_ig')->pluck('table_name')->toArray();

                            if (Common::getBranchId() == 1) {

                                if (!in_array($logArray_duplicate['table_name'], $ignore_table)) {

                                    // $transactions = DB::table('query_db_ho')->pluck('table_name')->toArray();
                                    // $transactionsB = DB::table('query_db_branch')->pluck('table_name')->toArray();

                                    if (!in_array($logArray_duplicate['table_name'], $transactions) && !in_array($logArray_duplicate['table_name'], $transactionsB)) {

                                        if (
                                            preg_match("/^pos_/i", $logArray_duplicate['table_name'])
                                            || preg_match("/^gnl_/i", $logArray_duplicate['table_name'])
                                            || preg_match("/^hr_/i", $logArray_duplicate['table_name'])
                                        ) {
                                            DB::table('query_log_fixed')->insert($logArray_duplicate);
                                        }
                                    } else {
                                        if (in_array($logArray_duplicate['table_name'], $transactions)) {
                                            DB::table('query_log_trans')->insert($logArray_duplicate);
                                        }

                                        if (in_array($logArray_duplicate['table_name'], $transactionsB)) {
                                            DB::table('query_log_branch')->insert($logArray_duplicate);
                                        }
                                    }
                                }
                            } else {
                                // && !in_array($logArray_duplicate['table_name'], $ignore_table)
                                if (in_array($logArray_duplicate['table_name'], $transactionsB)) {
                                    DB::table('query_log_branch')->insert($logArray_duplicate);
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
