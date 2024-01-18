<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeContractConclude extends BaseModel
{
    protected $table = 'hr_app_contract_concludes';
    protected $fillable = [
        'contract_conclude_code',
        'branch_id',
        'emp_id',
        'reason',
        'description',
        'status',
        'contract_conclude_date',
        'exp_effective_date',
        'effective_date',
        'attachment',
        'current_stage',
        'company_id',

        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
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

    public function approve(){

    }

    public function update_current_stage(){
        
    }

    public function make_processing(){
        
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
