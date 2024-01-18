<?php

namespace App\Model\HR;

use App\BaseModel;

class ApplicationReasons extends BaseModel
{

    protected $table = 'hr_app_reasons';
    protected $fillable = [
        'event_id',
        'reason',
        'is_delete'
    ];

 
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
