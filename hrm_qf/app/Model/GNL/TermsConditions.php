<?php

namespace App\Model\GNL;

use App\BaseModel;

class TermsConditions extends BaseModel
{

    protected $table = 'gnl_terms_conditions';
    protected $fillable = [
        'company_id',
        'type_id',
        'tc_name',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
