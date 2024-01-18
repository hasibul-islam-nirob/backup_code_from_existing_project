<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class EmployeeTerminate extends BaseModel
{

    protected $table = 'hr_employee_terminate';

    protected $fillable = [
        'emp_id',
        'company_id',
        'branch_id',
        // 'branch_to',
        'terminate_date',
        
        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',

        'is_approved',
        'approved_by'
    ];

    // /* Here Insert Created By & Update By */
    // public static function boot()
    // {
    //     parent::boot();
    // }
}
