<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class PensionSchemeDetails extends BaseModel
{

    protected $table = 'hr_payroll_settings_pension_details';
    protected $fillable = [
        'pension_id',
        'benefit_y',
        'calculation_type',
        'rate',
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
