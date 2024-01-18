<?php

namespace App\Model\GNL\TMS;

use App\BaseModel;

class Task extends BaseModel
{

    protected $table = 'tms_tasks';

    protected $fillable = [
        'task_code',
        'task_title',
        'task_type_id',
        'company_id',
        'task_date',
        'module_id',
        'assigned_by',
        'assigned_to',
        'attachment',
        'description',
        'instruction',
        'status',

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
