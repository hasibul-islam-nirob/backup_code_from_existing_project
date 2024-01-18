<?php

namespace App\Model\GNL;

use App\BaseModel;
use App\Model\GNL\DType;
use App\Model\GNL\DForm;


class DFormValue extends BaseModel
{

    protected $table = 'gnl_dynamic_form_value';
    protected $fillable = [
        'type_id',
        'form_id',
        'uid',
        'name',
        'value_field',
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

    // public function form()
    // {

    //     return $this->belongsTo('App\Model\GNL\DForm', 'form_id', 'uid');
    // }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
