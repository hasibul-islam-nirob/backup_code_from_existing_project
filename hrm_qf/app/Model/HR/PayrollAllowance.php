<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class PayrollAllowance extends BaseModel
{

    protected $table = 'hr_payroll_allowance';
    protected $fillable = [
        'name',
        'short_name',
        'benifit_type_uid',

        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
    ];

    public function benifit(){
        return DB::table('gnl_dynamic_form_value')->where([['type_id', 3],['form_id', 2],['uid', $this->benifit_type_uid]])->first();
    }
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
