<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        ## create a event to happen on saving
        static::saving(function ($model) {

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

            $ignoreArr = [
                'access_query_log',
                'query_log_branch',
                'query_log_fixed',
                'query_log_trans',
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
                // dd($logData);

                $logData['operation_type'] = "insert";
                $logData['execution_time'] = $model->freshTimestamp();

                session()->put('logData', $logData);
            }

        });

        ## create a event to happen on updating
        static::updating(function ($model) {

            if (in_array('updated_at', $model->fillable)) {
                $model->updated_at = $model->freshTimestamp();
            }

            if (in_array('updated_by', $model->fillable)) {
                $model->updated_by = Auth::id();
            }

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

                $logData['operation_type'] = "hard_delete";
                $logData['execution_time'] = $model->freshTimestamp();

                session()->put('logData', $logData);
            }
        });

    }
}
