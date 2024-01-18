<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class EmployeeLeaveCategory extends BaseModel
{

    protected $table = 'hr_leave_category';

    protected $fillable = [
        'name',
        'short_form',
        'leave_type_uid',
        
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

    public function leave_type(){
        return DB::table('gnl_dynamic_form_value')->where([['type_id', 3],['form_id', 1],['uid', $this->leave_type_uid]])->first();
    }

    public function leave_details()
    {
        return $this->hasMany('App\Model\HR\EmployeeLeaveCategoryDetails', 'leave_cat_id', 'id');
    }
}
