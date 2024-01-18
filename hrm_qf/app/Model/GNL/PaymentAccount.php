<?php

namespace App\Model\GNL;

use App\BaseModel;

class PaymentAccount extends BaseModel
{

    protected $table = 'gnl_payment_acc';
    protected $fillable = [
        'payment_system_id',
        'provider_name',
        'acc_holder_name',
        'account_no',
        'status',
        'ledger_id',
        'address',
        'mobile',
        'email',
        'routing_no',


        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];


    public function paymentSystem()
    {
        //    return $this->('App\Phone');
        return $this->belongsTo('App\Model\GNL\PaymentSystem', 'payment_system_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
