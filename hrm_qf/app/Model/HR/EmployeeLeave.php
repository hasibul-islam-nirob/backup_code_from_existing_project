<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeLeave extends BaseModel
{

    protected $table = 'hr_app_leaves';
    protected $fillable = [
        'leave_code',
        'leave_cat_id',
        'emp_id',
        'resp_emp_id',
        'branch_id',
        'date_from',
        'date_to',
        'description',
        'leave_date',
        'effective_date',
        'attachment',
        'current_stage',
        'company_id',

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

    public function reasons()
    {
        return $this->belongsTo('App\Model\HR\ApplicationReasons', 'reason', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'emp_id', 'id');
    }

    public function resp_employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'resp_emp_id', 'id');
    }

    public function leave_category()
    {
        return $this->hasOne('App\Model\HR\EmployeeLeaveCategory', 'id', 'leave_cat_id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_leaves');
    }
    public function created_by()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'created_by', 'id');
    }

}
