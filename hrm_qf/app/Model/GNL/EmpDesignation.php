<?php

namespace App\Model\GNL;

use App\BaseModel;

// use Illuminate\Database\Eloquent\Model;

class EmpDesignation extends BaseModel
{
    protected $table = 'hr_designations';

    protected $fillable = [
        'name',
        'short_name',

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
