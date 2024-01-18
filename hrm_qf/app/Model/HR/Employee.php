<?php

namespace App\Model\HR;

use App\BaseModel;
use App\Model\GNL\SysUser;

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
        'permanent_date',
        'prov_period',
        'basic_salary',
        'org_mobile',
        'org_email',
        'status',

        'status',
        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function sys_user()
    {
        return $this->belongsTo('App\Model\GNL\SysUser', 'user_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo('App\Model\HR\EmployeeDesignation', 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Model\HR\EmpDepartment', 'department_id', 'id');
    }

    public function personalData()
    {
        return $this->belongsTo('App\Model\HR\EmployeePersonalDetails', 'id', 'emp_id');
    }

    public function organizationData()
    {
        return $this->belongsTo('App\Model\HR\EmployeeOrganizationDetails', 'id', 'emp_id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
