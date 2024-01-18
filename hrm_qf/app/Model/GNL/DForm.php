<?php

namespace App\Model\GNL;

use App\BaseModel;
use App\Model\GNL\DType;

class DForm extends BaseModel
{

    protected $table = 'gnl_dynamic_form';
    protected $fillable = [
        'type_id',
        'uid',
        'name',
        'input_type',
        'order_by',
        'module_id',
        'note',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function type()
    {
      return $this->belongsTo('App\Model\GNL\DType', 'type_id', 'id');
    }

    public function module()
    {
      return $this->belongsTo('App\Model\GNL\SysModule', 'module_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
