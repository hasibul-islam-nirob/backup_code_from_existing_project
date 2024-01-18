<?php

namespace App\Model\GNL;
use App\BaseModel;

class CompanyConfig extends BaseModel
{

    protected $table = 'gnl_company_config';
    protected $fillable = [
        'company_id',
        'form_id',
        'module_id',
        'form_value',
        'created_at',
        'updated_at',
    ];


    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
