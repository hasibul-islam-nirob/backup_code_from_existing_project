<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class PayrollPayScaleMigration extends BaseModel
{
    protected $table = 'hr_payroll_pay_scale_migration';
    protected $fillable = [
        'branch_id',
        'emp_id',
        'designation_id',
        'department_id',
        'rectuitment_type_id',

        'grade',
        'level',
        'step',
        'old_payscale_id',
        'new_payscale_id',
        'salary_structure_id',
        'effective_date',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    
    // public function oldPayScale()
    // {
    //     return $this->belongsTo('App\Model\HR\PayScale', 'old_payscale_id', 'id');
    // }

    // public function newPayScale()
    // {
    //     return $this->belongsTo('App\Model\HR\PayScale', 'new_payscale_id', 'id');
    // }

    // public function RecruitmentType()
    // {
    //     return $this->belongsTo('App\Model\HR\RecruitmentType', 'rectuitment_type_id', 'id');
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
