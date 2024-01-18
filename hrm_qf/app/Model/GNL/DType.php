<?php

namespace App\Model\GNL;

use App\BaseModel;

class DType extends BaseModel
{

    protected $table = 'gnl_dynamic_form_type';

    protected $fillable = [
        'name',

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
