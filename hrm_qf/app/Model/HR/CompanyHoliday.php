<?php

namespace App\Model\HR;

use App\BaseModel;

class CompanyHoliday extends BaseModel
{
    protected $table = 'hr_holidays_comp';
    protected $fillable = [
        'company_id',
        'branch_arr',
        'ch_title',
        'ch_date',
        'ch_day',
        'ch_description',
        'ch_eff_date',

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

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
