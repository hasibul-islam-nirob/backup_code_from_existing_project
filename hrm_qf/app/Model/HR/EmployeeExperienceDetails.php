<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeExperienceDetails extends BaseModel
{

    protected $table = 'hr_emp_experience_details';
    protected $fillable = [
        'emp_id',
        'org_name',
        'org_type',
        'org_location',
        'designation',
        'department',
        'job_responsibility',
        'area_of_experience',
        'duration',
        'start_date',
        'end_date',
        'address',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
