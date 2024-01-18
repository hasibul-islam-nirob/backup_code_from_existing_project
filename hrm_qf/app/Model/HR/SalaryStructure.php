<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class SalaryStructure extends BaseModel
{

    protected $table = 'hr_payroll_salary_structure';
    protected $fillable = [
        'pay_scale_id',
        'company_id',
        'grade',
        'level',
        'designations',
        'basic',
        'project_id',
        'recruitment_type_id',
        'acting_benefit_amount',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function salary_structure_details(){
        return $this->hasMany('App\Model\HR\SalaryStructureDetails', 'salary_structure_id', 'id');
    }

    public function pay_scale()
    {
        return DB::table('hr_payroll_payscale')->find($this->pay_scale_id);
    }

    public function company(){
        return DB::table('gnl_companies')->find($this->company_id);
    }

    public function designations(){
        return implode(', ', (array)DB::table('hr_designations')->whereIn('id', explode(',', $this->designations))->get()->pluck('name')->toArray());
    }

    public function project(){
        return DB::table('gnl_projects')->find($this->project_id);
    }


    public function recruitmentType(){
        return DB::table('hr_recruitment_types')->find($this->recruitment_type_id);
    }

    public function recruitment_type(){
        return implode(', ', (array)DB::table('hr_recruitment_types')->whereIn('id', explode(',', $this->recruitment_type_id))->get()->pluck('title')->toArray());
    }
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
