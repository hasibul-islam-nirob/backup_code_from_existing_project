<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class SmsForward extends BaseModel
{
    protected $table = 'gnl_sms_notification';
    
    protected $fillable = [
        'sms_to',
        'sms_title',
        'sms_body',

        'sms_type',
        'sms_length',
        'receiver_length',

        'receiver_numbers',
        'receiver_id',
        'receiver_status',
        'api_response',

        'branch_id',
        'samity_id',

        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
