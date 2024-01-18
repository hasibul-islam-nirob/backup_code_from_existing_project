<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;
class Gratuity extends BaseModel
{

    protected $table = 'hr_payroll_settings_gratuity';
    protected $fillable = [
        'group_id',
        'company_id',
        'project_id',
        'effective_date',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function gratuity_details(){
        return $this->hasMany('App\Model\HR\GratuityDetails', 'gratuity_id', 'id');
    }

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


    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
