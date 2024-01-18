<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class PayrollConfiguration extends BaseModel
{
    protected $table = 'hr_payroll_configuration';
    protected $fillable = [
        'branch_id',
        'company_id',
        'project_id',

        'rectuitment_type',
        'employee_type',

        'eff_date_start',
        'eff_date_end',
        'exp_effective_date',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function group()
    {
        return $this->belongsTo('App\Model\HR\Group', 'group_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Model\HR\Company', 'company_id', 'id');
    }

    public function project(){
        return DB::table('gnl_projects')->find($this->project_id);
    }

    // public function recruitment_type(){
    //     return implode(', ', (array)DB::table('hr_recruitment_types')->whereIn('id', explode(',', $this->rec_type_ids))->get()->pluck('title')->toArray());
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
