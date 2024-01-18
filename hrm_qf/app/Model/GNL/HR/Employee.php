<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class Employee extends BaseModel
{

    protected $table = 'hr_employees';
    protected $fillable = [
        'employee_no',
        'emp_code',
        'emp_name',
        'user_id',
        'branch_id',
        'designation_id',
        'department_id',
        'gender',
        'join_date',
        'basic_salary',
        'org_mobile',
        'org_email',
        'status',

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
    public function User()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Model\GNL\Company', 'company_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo('App\Model\GNL\HR\EmployeeDesignation', 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Model\GNL\HR\EmpDepartment', 'department_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
