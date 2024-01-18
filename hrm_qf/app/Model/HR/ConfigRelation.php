<?php

namespace App\Model\HR;

use App\BaseModel;

class ConfigRelation extends BaseModel
{
    protected $table = 'hr_relationships';
    protected $fillable = [
        'name',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'status'
    ];
}
