<?php

namespace App\Model\HR;

use App\BaseModel;

class Loan extends BaseModel
{

    protected $table = 'hr_payroll_settings_loan';
    protected $fillable = [
        'vehicle_type',
        'max_installment',
        'max_amount',
        'settlement_fee',
        'effective_date',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
