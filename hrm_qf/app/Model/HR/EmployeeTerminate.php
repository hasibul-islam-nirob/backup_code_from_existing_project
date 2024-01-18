<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeTerminate extends BaseModel
{
    protected $table = 'hr_app_terminates';
    protected $fillable = [
        'terminate_code',
        'branch_id',
        'emp_id',
        'reason',
        'status',
        'terminate_date',
        'exp_effective_date',
        'effective_date',
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

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_leaves');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
