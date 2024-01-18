<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeAccountDetails extends BaseModel
{

    protected $table = 'hr_emp_account_details';
    protected $fillable = [
        'emp_id',
        'bank_id',
        'bank_branch_id',
        'bank_acc_type',
        'bank_acc_number',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
