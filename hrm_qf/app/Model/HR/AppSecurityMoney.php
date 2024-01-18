<?php

namespace App\Model\HR;

use App\BaseModel;

class AppSecurityMoney extends BaseModel
{
    protected $table = 'hr_app_security_money';
    protected $fillable = [
        'security_money_code',
        'branch_id',
        'company_id',
        'project_id',
        'emp_id',
        'employee_no',

        'collection_amount',
        'collection_month',
        'collection_type',
        'first_repay_month',
        
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

    public function collectionBy()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'collection_by', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_movements');
    }

    public function designation()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Model\HR\EmpDepartment', 'department_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
    
}
