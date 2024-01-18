<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeReferenceDetails extends BaseModel
{

    protected $table = 'hr_emp_reference_details';
    protected $fillable = [
        'emp_id',
        'name',
        'designation',
        'relation',
        'nid',
        'mobile',
        'phone',
        'email',
        'occupation',
        'working_address',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
