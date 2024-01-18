<?php

namespace App\Model\GNL;
use App\BaseModel;

class HOIG extends BaseModel
{

    protected $table = 'query_db_ho_ig';

    protected $fillable = [
        'table_name',
    ];

    public $timestamps = false;

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
