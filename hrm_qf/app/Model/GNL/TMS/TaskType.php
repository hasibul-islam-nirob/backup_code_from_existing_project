<?php

namespace App\Model\GNL\TMS;

use App\BaseModel;

class TaskType extends BaseModel
{

    protected $table = 'tms_task_types';

    protected $fillable = [
        'type_name',
        'task_type_code',
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
