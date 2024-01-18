<?php

namespace App\Model\HR;

use App\BaseModel;

class WelfareFundDetails extends BaseModel
{

    protected $table = 'hr_payroll_settings_wf_details';
    protected $fillable = [
        'wf_id',
        'type',
        'grade',
        'level',
        'calculation_type',
        'amount',
        'don_sector',
        'data_type',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
