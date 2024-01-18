<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeGuarantorDetails extends BaseModel
{

    protected $table = 'hr_emp_guarantor_details';
    protected $fillable = [
        'emp_id',
        'guarantor_type',
        'name',
        'designation',
        'occupation',
        'email',
        'working_address',
        'par_address',
        'nid',
        'relation',
        'mobile',
        'phone',
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
