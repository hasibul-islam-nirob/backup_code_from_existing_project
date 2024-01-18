<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeLeaveAdjustment extends BaseModel
{
    protected $table = 'hr_app_leaves_adjustment';
    protected $fillable = [
        'emp_id',
        'fiscal_year_id',
        'adjustment_for',
        'adjustment_month',
        'adjustment_value',
        'description',
        'application_date',
        'effective_date',
        'branch_id',

        'is_active',
        'is_delete',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo('App\Model\GNL\FiscalYear', 'fiscal_year_id', 'id');
    }

    public function month()
    {
        return $this->belongsTo('App\Model\HR\HrMonths', 'adjustment_month', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'emp_id', 'id');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'created_by', 'id');
    }
}
