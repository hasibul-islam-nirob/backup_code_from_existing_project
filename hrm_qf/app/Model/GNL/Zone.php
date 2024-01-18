<?php

namespace App\Model\GNL;

use App\BaseModel;

class Zone extends BaseModel
{

    protected $table = 'gnl_zones';
    
    protected $fillable = [
        'zone_name',
        'zone_code',
        'company_id',
        'region_arr',

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

    //     return $this->belongsTo(Area::class);
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
