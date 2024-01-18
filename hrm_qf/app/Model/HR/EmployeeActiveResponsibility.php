<?php

namespace App\Model\HR;

use App\BaseModel;
class EmployeeActiveResponsibility extends BaseModel
{
    protected $table = 'hr_app_active_responsibilities';
    protected $fillable = [
        'active_responsibility_code',
        'branch_id',
        'emp_id',
        'description',
        'status',
        'active_responsibility_date',
        'exp_effective_date',
        'effective_date',
        'current_stage',
        'company_id',
        'current_designation_id',
        'designation_to_promote_id',
        'expiry_date',
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

    public function current_designation()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'current_designation_id', 'id');
    }

    public function designation_to_promote()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_to_promote_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
