<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class EmpDepartment extends BaseModel
{

    protected $table = 'hr_departments';
    protected $fillable = [
        'company_id',
        'dept_name',
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
