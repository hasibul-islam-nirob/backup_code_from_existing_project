<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class Bonus extends BaseModel
{

    protected $table = 'hr_payroll_settings_bonus';
    protected $fillable = [
        'name',
        'group_id',
        'company_id',
        'project_id',
        'rec_type_ids',
        'religion_id',

        'calculation_percentage',
        'based_on',
        'min_job_m',
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

    public function religion(){
        return DB::table('mfn_member_religions')->find($this->religion_id);
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
