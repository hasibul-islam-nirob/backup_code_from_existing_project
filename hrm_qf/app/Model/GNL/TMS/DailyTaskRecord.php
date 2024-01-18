<?php

namespace App\Model\GNL\TMS;

use App\BaseModel;

class DailyTaskRecord extends BaseModel
{

    protected $table = 'tms_emp_daily_task';

    protected $fillable = [
        'emp_id',
        'module_id',
        'task_type_id',
        'task_id',
        'task_title',
        'task_date',
        'assigned_by',
        'description',
        'attachment',
        'company_id',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
