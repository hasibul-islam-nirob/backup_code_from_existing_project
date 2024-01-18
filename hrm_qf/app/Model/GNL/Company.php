<?php

namespace App\Model\GNL;
use App\BaseModel;

class Company extends BaseModel
{

    protected $table = 'gnl_companies';
    protected $fillable = [
        'group_id',
        'comp_name',
        'comp_code',
        'comp_email',
        'comp_phone',
        'comp_addr',
        'comp_web_add',
        'comp_logo',
        'bill_logo',
        'cover_image_lp',
        'module_arr',
        'company_type',

        'logo_view_lp',
        'logo_lp_width',
        'logo_view_report',
        'logo_report_width',
        'logo_view_bill',
        'logo_bill_width',
        'logo_bill_width_pos',
        'name_view_lp',
        'name_view_report',
        'name_view_bill',
        'br_add_view_bill',

        'schedule_flag',
        'tx_start_time',
        'tx_end_time',
        'applicable_for',

        'db_name',
        'host',
        'username',
        'password',
        'port',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'


    ];

    public function group()
    {

        return $this->belongsTo('App\Model\GNL\Group', 'group_id', 'id')->where('is_delete', 0);

    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
