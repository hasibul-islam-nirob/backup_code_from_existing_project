<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeAttendance extends BaseModel
{
    protected $table = 'hr_attendance';
    protected $fillable = [

        'emp_id',
        'device_id',
        'time_and_date',
        'emp_code',
        'name',
        'isFileUpload',
        // 'department_id',
        // 'branch_id',
        // 'designation_id',
        'company_id',

        // 'schedule',
        // 'date',
        // 'timetable',
        // 'on_duty',
        // 'off_duty',
        // 'clock_in',
        // 'clock_out',
        // 'late',
        // 'early',
        // 'absent',
        // 'ot_time',
        // 'work_time',

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

    // public function branch()
    // {
    //     return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    // }

    // public function designation()
    // {
    //     return $this->belongsTo('App\Model\GNL\HR\EmployeeDesignation', 'designation_id', 'id');
    // }

    // public function department()
    // {
    //     return $this->belongsTo('App\Model\GNL\HR\EmpDepartment', 'department_id', 'id');
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
