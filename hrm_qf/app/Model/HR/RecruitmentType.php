<?php

namespace App\Model\HR;

use App\BaseModel;

class RecruitmentType extends BaseModel
{

    protected $table = 'hr_recruitment_types';

    protected $fillable = [
        'title',
        'salary_method',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
