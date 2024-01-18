<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class EmployeePromotion extends BaseModel
{
    protected $table = 'hr_app_promotions';
    protected $fillable = [
        'promotion_code',
        'branch_id',
        'emp_id',
        'description',
        'status',
        'promotion_date',
        'exp_effective_date',
        'effective_date',
        'current_stage',
        'company_id',
        'current_department_id',
        'department_to_promote_id',
        'current_designation_id',
        'designation_to_promote_id',

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
        return $this->belongsTo('App\Model\GNL\HR\Employee', 'emp_id', 'id');
    }

    public function current_department()
    {
        return $this->belongsTo('App\Model\GNL\HR\EmpDepartment', 'current_department_id', 'id');
    }

    public function department_to_promote()
    {
        return $this->belongsTo('App\Model\GNL\HR\EmpDepartment', 'department_to_promote_id', 'id');
    }

    public function current_designation()
    {
        return $this->belongsTo('App\Model\GNL\EmpDesignation', 'current_designation_id', 'id');
    }

    public function designation_to_promote()
    {
        return $this->belongsTo('App\Model\GNL\EmpDesignation', 'designation_to_promote_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
