<?php

namespace App\Model\GNL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class SysUser extends Authenticatable
{
    // use Notifiable;
    use HasApiTokens, Notifiable;

    protected $table = 'gnl_sys_users';

    protected $fillable = [
        'sys_user_role_id',
        'company_id',
        'branch_id',
        'emp_id',
        'employee_no',
        'full_name',
        'username',
        'password',
        'email',
        'contact_no',
        'user_image',
        'ip_address',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_login_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        // // create a event to happen on saving
        // static::saving(function ($model) {
        //     $model->created_by = Auth::id();
        // });

        // // create a event to happen on updating
        // static::updating(function ($model) {
        //     $model->updated_by = Auth::id();
        // });

        ## create a event to happen on saving
        static::saving(function ($model) {

            # i dont know dblogData is used and where it called hasib
            if ($model->table != "db_query_log")
            {
                $dblogData = array();

                $dblogData['table_name'] = $model->table;
                $dblogData['fillable'] = implode(',', $model->fillable);
                $dblogData['attributes'] = implode(',', array_keys($model->attributes));
                $dblogData['attr_values'] = implode(',', $model->attributes);
                $dblogData['operation_type'] = "insert";

                session()->put('dblogData', $dblogData);
            }

            if ($model->exists == false) {
                
                if (in_array('is_active', $model->fillable)) {
                    // if (!in_array('is_active', $model->attributes)) {
                    //     $model->is_active = 1;
                    // }

                    if (!isset($model->attributes['is_active'])) {
                        $model->is_active = 1;
                    }
                }

                if (in_array('is_delete', $model->fillable)) {
                    $model->is_delete = 0;
                }

                if (in_array('created_at', $model->fillable)) {
                    $model->created_at = $model->freshTimestamp();
                }

                if (in_array('created_by', $model->fillable)) {
                    $model->created_by = Auth::id();
                }
            }

            // dd($model);
             if($model->branch_id != null || $model->branch_to != null || $model->branch_form != null){
                session()->forget('tempModelBranchData');
                $tempModelBranchData = array();

                if($model->branch_id != null){
                    $tempModelBranchData['branch_id'] = $model->branch_id;
                }

                if($model->branch_to != null){
                    $tempModelBranchData['branch_to'] = $model->branch_to;
                }
                
                if($model->branch_form != null){
                    $tempModelBranchData['branch_form'] = $model->branch_form;
                }
                session()->put('tempModelBranchData', $tempModelBranchData);
            }

            $branch_id_wouldbe_branch_to_for_fetch_From_branch = [
                'pos_audit_m',
                'pos_audit_d',
                'hr_employees',
                'gnl_sys_users'
            ];
            
            $ignoreArr = [
                'access_query_log',
                'query_log_branch',
                'query_log_fixed',
                'query_log_trans',
            ];

            $branch_to_table = [
                'pos_issues_m',
                'pos_issues_d'
            ];

            $branch_from_table = [
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
            ];

            $branch_multiple_table = [
                'pos_issues_m',
                'pos_issues_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_transfers_m',
                'pos_transfers_d'
            ];

            if (!in_array($model->table, $ignoreArr)) {
                $logData = array();

                $logData['table_name'] = $model->table;

                if (in_array($logData['table_name'], $branch_multiple_table)) {
                    $logData['branch_id'] = $model->branch_from;
                    $logData['branch_to'] = $model->branch_to;
                } else {
                    $logData['branch_id'] = $model->branch_id;
                }

                if (in_array($logData['table_name'], $branch_from_table)) {
                    $logData['branch_id'] = $model->branch_from;
                } elseif (in_array($logData['table_name'], $branch_to_table)) {
                    $logData['branch_to'] = $model->branch_to;
                } elseif (!empty($model->branch_id) || !is_null($model->branch_id)) {
                    $logData['branch_id'] = $model->branch_id;
                }

                if ($logData['table_name'] == "pos_transfers_m" || $logData['table_name'] == 'pos_transfers_d') {
                    $logData['branch_to'] = $model->branch_to;
                    $logData['branch_from'] = $model->branch_from;
                    unset($logData['branch_id']);
                }

                if (in_array($logData['table_name'], $branch_id_wouldbe_branch_to_for_fetch_From_branch)) {
                    $logData['branch_to'] = $model->branch_id;
                }
                

                $logData['operation_type'] = "insert";
                $logData['execution_time'] = $model->freshTimestamp();

                session()->put('logData', $logData);
            }

        });

        ## create a event to happen on updating
        static::updating(function ($model) {
            # i dont know dblogData is used and where it called hasib
            if ($model->table != "db_query_log")
            {
                $dblogData = array();

                $dblogData['table_name'] = $model->table;
                $dblogData['fillable'] = implode(',', $model->fillable);
                $dblogData['attributes'] = implode(',', array_keys($model->attributes));
                $dblogData['attr_values'] = implode(',', $model->attributes);

                if ($model->is_delete == 1) {
                    $dblogData['operation_type'] = "delete";
                }
                else{
                    $dblogData['operation_type'] = "update";
                }

                session(['dblogData' => $dblogData]);
            }



            if (in_array('updated_at', $model->fillable)) {
                $model->updated_at = $model->freshTimestamp();
            }

            if (in_array('updated_by', $model->fillable)) {
                $model->updated_by = Auth::id();
            }
             if($model->branch_id != null || $model->branch_to != null || $model->branch_form != null){
                session()->forget('tempModelBranchData');
                $tempModelBranchData = array();

                if($model->branch_id != null){
                    $tempModelBranchData['branch_id'] = $model->branch_id;
                }

                if($model->branch_to != null){
                    $tempModelBranchData['branch_to'] = $model->branch_to;
                }
                
                if($model->branch_form != null){
                    $tempModelBranchData['branch_form'] = $model->branch_form;
                }
                session()->put('tempModelBranchData', $tempModelBranchData);
            }

            $branch_id_wouldbe_branch_to_for_fetch_From_branch = [
                'pos_audit_m',
                'pos_audit_d',
                'hr_employees',
                'gnl_sys_users'
            ];



            $ignoreArr = [
                'access_query_log',
                'query_log_branch',
                'query_log_fixed',
                'query_log_trans',
            ];

            $branch_multiple_table = [
                'pos_issues_m',
                'pos_issues_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_transfers_m',
                'pos_transfers_d',
            ];

            $branch_to_table = [
                'pos_issues_m',
                'pos_issues_d',
            ];

            $branch_from_table = [
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
            ];

            if (!in_array($model->table, $ignoreArr)) {
                $logData = array();

                $logData['table_name'] = $model->table;

                if (in_array($logData['table_name'], $branch_multiple_table)) {
                    $logData['branch_id'] = $model->branch_from;
                    $logData['branch_to'] = $model->branch_to;
                } else {
                    $logData['branch_id'] = $model->branch_id;
                }

                if (in_array($logData['table_name'], $branch_from_table)) {
                    $logData['branch_id'] = $model->branch_from;
                } elseif (in_array($logData['table_name'], $branch_to_table)) {
                    $logData['branch_id'] = $model->branch_to;
                } elseif (!empty($model->branch_id) || !is_null($model->branch_id)) {
                    $logData['branch_id'] = $model->branch_id;
                }

                if ($logData['table_name'] == "pos_transfers_m" || $logData['table_name'] == 'pos_transfers_d') {
                    $logData['branch_to'] = $model->branch_to;
                    $logData['branch_from'] = $model->branch_from;
                    unset($logData['branch_id']);
                }

                if (in_array($logData['table_name'], $branch_id_wouldbe_branch_to_for_fetch_From_branch)) {
                    $logData['branch_to'] = $model->branch_id;
                }

                if ($model->is_delete == 1) {
                    $logData['operation_type'] = "delete";
                } else {
                    $logData['operation_type'] = "update";
                }

                $logData['execution_time'] = $model->freshTimestamp();

                session()->put('logData', $logData);
            }
        });

        ## create a event to happen on deleting
        static::deleting(function ($model) {
             if($model->branch_id != null || $model->branch_to != null || $model->branch_form != null){
                session()->forget('tempModelBranchData');
                $tempModelBranchData = array();

                if($model->branch_id != null){
                    $tempModelBranchData['branch_id'] = $model->branch_id;
                }

                if($model->branch_to != null){
                    $tempModelBranchData['branch_to'] = $model->branch_to;
                }
                
                if($model->branch_form != null){
                    $tempModelBranchData['branch_form'] = $model->branch_form;
                }
                session()->put('tempModelBranchData', $tempModelBranchData);
            }

            $branch_id_wouldbe_branch_to_for_fetch_From_branch = [
                'pos_audit_m',
                'pos_audit_d',
                'hr_employees',
                'gnl_sys_users'
            ];

            $ignoreArr = [
                'access_query_log',
                'query_log_branch',
                'query_log_fixed',
                'query_log_trans',
            ];

            $branch_multiple_table = [
                'pos_issues_m',
                'pos_issues_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_transfers_m',
                'pos_transfers_d',
            ];

            $branch_to_table = [
                'pos_issues_m',
                'pos_issues_d',
            ];

            $branch_from_table = [
                'pos_requisitions_m',
                'pos_requisitions_d',
                'pos_issues_r_m',
                'pos_issues_r_d',
            ];

            if (!in_array($model->table, $ignoreArr)) {
                $logData = array();

                $logData['table_name'] = $model->table;

                if (in_array($logData['table_name'], $branch_multiple_table)) {
                    $logData['branch_id'] = $model->branch_from;
                    $logData['branch_to'] = $model->branch_to;
                } else {
                    $logData['branch_id'] = $model->branch_id;
                } 

                if (in_array($logData['table_name'], $branch_from_table)) {
                    $logData['branch_id'] = $model->branch_from;
                } elseif (in_array($logData['table_name'], $branch_to_table)) {
                    $logData['branch_id'] = $model->branch_to;
                } elseif (!empty($model->branch_id) || !is_null($model->branch_id)) {
                    $logData['branch_id'] = $model->branch_id;
                }

                if ($logData['table_name'] == "pos_transfers_m" || $logData['table_name'] == 'pos_transfers_d') {
                    $logData['branch_to'] = $model->branch_to;
                    $logData['branch_from'] = $model->branch_from;
                    unset($logData['branch_id']);
                }

                if (in_array($logData['table_name'], $branch_id_wouldbe_branch_to_for_fetch_From_branch)) {
                    $logData['branch_to'] = $model->branch_id;
                }

                $logData['operation_type'] = "hard_delete";
                $logData['execution_time'] = $model->freshTimestamp();

                session()->put('logData', $logData);
            }
        });


       
    }
}
