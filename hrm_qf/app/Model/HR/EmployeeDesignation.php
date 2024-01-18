<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeDesignation extends BaseModel
{

    protected $table = 'hr_designations';
    protected $fillable = [
        'name',
        'short_name',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
