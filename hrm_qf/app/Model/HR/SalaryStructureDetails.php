<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class SalaryStructureDetails extends BaseModel
{

    protected $table = 'hr_payroll_salary_structure_details';
    protected $fillable = [
        'salary_structure_id',
        'inc_percentage',
        'amount',
        'no_of_inc',
        'allowance_type_id',
        'calculation_type',
        'data_type'
    ];
    
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
