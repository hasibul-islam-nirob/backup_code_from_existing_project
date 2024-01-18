<?php

namespace App\Model\HR;

use App\BaseModel;

class BankBranch extends BaseModel
{

    protected $table = 'hr_bank_branches';
    protected $fillable = [
        'bank_id',
        'name',
        'address',
        'email',
        'phone',
        'contact_person',
        'contact_person_designation',
        'contact_person_phone',
        'contact_person_email',
        'address',
        'created_at', 
        'updated_at', 
        'created_by', 
        'updated_by', 
        'is_delete'
    ];

 
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
