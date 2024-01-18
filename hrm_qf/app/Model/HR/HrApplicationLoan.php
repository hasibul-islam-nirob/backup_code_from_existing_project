<?php

namespace App\Model\HR;

use App\BaseModel;

class HrApplicationLoan extends BaseModel
{
    protected $table = 'hr_app_loan';
    protected $fillable = [
        'loan_code',
        'loan_type',
        'branch_id',
        'company_id',
        'project_id',
        'emp_id',
        'employee_no',

        'employment_age',
        'org_contribution_amount',
        'emp_pf_amount',
        'eligible_amount',
        'interest_amount',
        'requested_loan_amount',
        'requested_no_of_loan_installment',
        'vehicle_type',
        
        'application_code',
        'application_date',
        'effective_date',
        'exp_effective_date',
        'current_stage',
        'description',
        'attachment',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'emp_id', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_movements');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
