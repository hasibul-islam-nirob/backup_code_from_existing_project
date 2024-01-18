<?php

namespace App\Model\HR;

use App\BaseModel;

class GratuityDetails extends BaseModel
{

    protected $table = 'hr_payroll_settings_gratuity_details';
    protected $fillable = [
        'gratuity_id',
        'steps',
        'year_from',
        'year_to',
        'gratuity',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
