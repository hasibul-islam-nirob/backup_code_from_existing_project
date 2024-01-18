<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeDemotion extends BaseModel
{
    protected $table = 'hr_app_demotions';
    protected $fillable = [
        'demotion_code',
        'branch_id',
        'emp_id',
        'description',
        'status',
        'demotion_date',
        'exp_effective_date',
        'effective_date',
        'current_stage',
        'company_id',
        'current_designation_id',
        'designation_to_demote_id',

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

    public function designation_to_demote()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_to_demote_id', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_demotions');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
