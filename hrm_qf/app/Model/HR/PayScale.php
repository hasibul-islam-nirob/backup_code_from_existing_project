<?php

namespace App\Model\HR;

use App\BaseModel;

class PayScale extends BaseModel
{

    protected $table = 'hr_payroll_payscale';
    protected $fillable = [
        'name',
        'eff_date_start',
        'eff_date_end',
        // 'active_status',
        'is_delete',
        'is_active',
    ];


    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
