<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeTrainingDetails extends BaseModel
{

    protected $table = 'hr_emp_training_details';
    protected $fillable = [
        'emp_id',
        'title',
        'organizer',
        'country_id',
        'address',
        'topic',
        'training_year',
        'duration',
        'status',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
