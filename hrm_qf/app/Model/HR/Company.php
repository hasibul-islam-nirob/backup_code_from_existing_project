<?php

namespace App\Model\HR;

use App\BaseModel;

class Company extends BaseModel
{

    protected $table = 'gnl_companies';
    protected $fillable = [
        'group_id',
        'comp_name',
        'comp_code',
        'module_arr',

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
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
