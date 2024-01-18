<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeLeaveCategoryDetails extends BaseModel
{

    protected $table = 'hr_leave_category_details';

    protected $fillable = [
        'leave_cat_id',
        'rec_type_id',
        'consume_policy',
        'remaining_leave_policy',
        'app_submit_policy',
        'capable_of_provision',
        'allocated_leave',
        'eligibility_counting_from',
        'max_leave_entitle',
        'consume_after',
        'leave_withdrawal_policy',
        'times_of_leave',
        'effective_date_from',
        'effective_date_to',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

    public function rec_type()
    {
        return $this->hasOne('App\Model\HR\RecruitmentType', 'id', 'rec_type_id');
    }
}
