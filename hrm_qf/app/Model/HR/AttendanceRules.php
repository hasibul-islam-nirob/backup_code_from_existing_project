<?php

namespace App\Model\HR;

use App\BaseModel;

class AttendanceRules extends BaseModel
{

    protected $table = 'hr_attendance_rules';
    protected $fillable = [
        'start_time',
        'end_time',
        'ext_start_time',
        'eff_date_start',
        'late_accept_minute',
        'early_accept_minute',
        'ot_cycle_minute',
        'attendance_bypass',
        'late_bypass',
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
