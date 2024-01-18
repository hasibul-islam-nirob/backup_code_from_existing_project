<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeEducationDetails extends BaseModel
{

    protected $table = 'hr_emp_education_details';
    protected $fillable = [
        'emp_id',
        'exam_title',
        'department',
        'institute_name',
        'board',
        'res_type',
        'result',
        'res_out_of',
        'passing_year',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
