<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeMovement extends BaseModel
{

    protected $table = 'hr_app_movements';

    protected $fillable = [
        'movement_code',
        'branch_id',
        'department_id',
        'emp_id',
        'description',
        'status',
        'movement_date',
        'appl_date',
        'start_time',
        'end_time',
        'reason',
        'effective_date',
        'attachment',
        'current_stage',
        'company_id',
        'location_to',
        'location_to_branch',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'approved_by',
        'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }
    public function branch_to()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'location_to_branch', 'id');
    }
    
    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'emp_id', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_movements');
    }

    public function reasons()
    {
        return $this->belongsTo('App\Model\HR\ApplicationReasons', 'reason', 'id');
    }
    public function created_by()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'created_by', 'id');
    }
    public function approve_by()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'approved_by', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
