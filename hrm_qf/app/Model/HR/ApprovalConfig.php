<?php

namespace App\Model\HR;

use App\BaseModel;

class ApprovalConfig extends BaseModel
{
    protected $table = 'hr_reporting_boss_config';
    protected $fillable = [
        "event_id",
        "permission_for",
        "designation_for_id",
        "department_for_id",
        "level",
        "department_id",
        "designation_id",
        'employee_from',
        'data_modification',

        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function designation_for()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_for_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Model\HR\EmpDepartment', 'department_id', 'id');
    }

    public function department_for()
    {
        return $this->belongsTo('App\Model\HR\EmpDepartment', 'department_for_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
