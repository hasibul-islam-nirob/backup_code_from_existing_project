<?php

namespace App\Model\GNL;

use App\BaseModel;

class PaymentSystem extends BaseModel
{

    protected $table = 'gnl_payment_system';
    protected $fillable = [
        'payment_system_name',
        'short_name',
        'status',
        'order_by',
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
