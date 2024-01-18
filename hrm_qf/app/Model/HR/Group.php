<?php

namespace App\Model\HR;

use App\BaseModel;

class Group extends BaseModel
{

    protected $table = 'gnl_groups';
    protected $fillable = [
        'group_name',
        'group_email',
        'group_phone',
        'group_addr',

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
