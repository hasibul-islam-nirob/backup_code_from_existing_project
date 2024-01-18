<?php

namespace App\Model\GNL;

use App\BaseModel;
use App\Model\GNL\Division;

class District extends BaseModel
{

    protected $table = 'gnl_districts';
    protected $fillable = [
        'division_id',
        'district_name',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function division()
    {

      return $this->belongsTo('App\Model\GNL\Division', 'division_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
