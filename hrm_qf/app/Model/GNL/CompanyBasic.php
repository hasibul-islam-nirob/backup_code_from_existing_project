<?php

namespace App\Model\GNL;
use App\BaseModel;

class CompanyBasic extends BaseModel
{

    protected $table = 'gnl_company_basic';
    protected $fillable = [
        'group_id',
        'comp_name',
        'comp_code',
        'comp_email',
        'comp_phone',
        'comp_addr',
        'comp_web_add',
        'comp_logo',
        'module_arr',

        'db_name',
        'host',
        'username',
        'password',
        'port',
        
        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'


    ];

    public function group()
    {

        return $this->belongsTo('App\Model\GNL\Group', 'group_id', 'id')->where('is_delete', 0);

    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
