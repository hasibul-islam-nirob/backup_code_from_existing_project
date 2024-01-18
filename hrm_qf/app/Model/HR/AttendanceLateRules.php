<?php

namespace App\Model\HR;

use App\BaseModel;

class AttendanceLateRules extends BaseModel
{
    protected $table = 'hr_attendance_late_rules';
    protected $fillable = [
        'late_bypass',
        'lp_accepted',
        'lp_deduction',
        'ext_start_time',
        'eff_date_start',
        'eff_date_end',
        'is_delete',
        'is_active',
    ];

 
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
