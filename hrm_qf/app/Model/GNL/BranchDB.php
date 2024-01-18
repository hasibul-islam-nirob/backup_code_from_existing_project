<?php

namespace App\Model\GNL;
use App\BaseModel;

class BranchDB extends BaseModel
{

    protected $table = 'query_db_branch';
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
