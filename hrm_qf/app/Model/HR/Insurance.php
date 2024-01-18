<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;
class Insurance extends BaseModel
{

    protected $table = 'hr_payroll_settings_insurance';
    protected $fillable = [
        'group_id',
        'company_id',
        'project_id',
        'rec_type_ids',

        'calculation_type',
        'calculation_amount',
        'emp_cont_after_m',
        'org_cont_after_m',

        'first_maturity_y',
        'first_maturity_interest_rate',
        'second_maturity_y',
        'second_maturity_interest_rate',

        'method',
        'effective_date',

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

    public function recruitment_type(){
        return implode(', ', (array)DB::table('hr_recruitment_types')->whereIn('id', explode(',', $this->rec_type_ids))->get()->pluck('title')->toArray());
    }


    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
