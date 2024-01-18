<?php

namespace App\Model\GNL;

use App\BaseModel;
use App\Model\GNL\Company;
use App\Model\GNL\Zone;

class Region extends BaseModel
{
    protected $table = 'gnl_regions';

    protected $fillable = [
        'region_name',
        'region_code',
        'company_id',
        'area_arr',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function company()
    {
        return $this->belongsTo('App\Model\GNL\Company', 'company_id', 'id');
    }

    // public function branch()
    // {
    //     return $this->belongsTo(Zone::class);
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
