<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class FiscalYear extends BaseModel
{

    protected $table = 'gnl_fiscal_year';
    protected $fillable = [
        'company_id',
        'fy_name',
        'fy_start_date',
        'fy_end_date',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function company()
    {
        //    return $this->('App\Phone');
        return $this->belongsTo('App\Model\GNL\Company', 'company_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
