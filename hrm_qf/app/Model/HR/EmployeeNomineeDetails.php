<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeNomineeDetails extends BaseModel
{

    protected $table = 'hr_emp_nominee_details';
    protected $fillable = [
        'emp_id',
        'name',
        'relation',
        'percentage',
        'nid',
        'address',
        'mobile',
        'photo',
        'signature',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
