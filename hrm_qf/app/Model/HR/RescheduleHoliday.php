<?php

namespace App\Model\HR;

use App\BaseModel;

class RescheduleHoliday extends BaseModel
{
    protected $table = 'hr_holiday_reschedule';
    protected $fillable = [
        'company_id',
        'branch_id',
        'title',
        'app_for',
        'working_date',
        'reschedule_date',
        'description',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function company()
    {
        return $this->belongsTo('App\Model\GNL\Company', 'company_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
