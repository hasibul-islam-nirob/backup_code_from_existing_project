<?php

namespace App\Model\HR;

use App\BaseModel;

class Attachment extends BaseModel
{

    protected $table = 'hr_attachments';
    protected $fillable = [
        'foreign_key',
        'path',
        'ref_table_name',
        'created_at', 
        'updated_at', 
    ];

 
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
