<?php

namespace App\Model\GNL;

use App\BaseModel;

class Area extends BaseModel
{
    protected $table = 'gnl_areas';

    protected $fillable = [
        'area_name',
        'area_code',
        'company_id',
        'branch_arr',

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
    //     return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    // }

    // public function MapAreaBranch()
    // {

    //     return $this->belongsTo('App\Model\GNL\MapAreaBranch');
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
