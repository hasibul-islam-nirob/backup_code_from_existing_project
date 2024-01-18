<?php

namespace App\Model\GNL;

// use Illuminate\Database\Eloquent\Model;

use App\BaseModel;

class CompanyType extends BaseModel
{
    protected $table = 'gnl_company_type';

    protected $fillable = [
        'company_id', 'name', 
        
        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function company()
    {
        return $this->belongsTo('App\Model\GNL\Company', 'company_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
