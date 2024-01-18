<?php

namespace App\Model\HR;

use App\BaseModel;

class AppAdvanceSalary extends BaseModel
{
    protected $table = 'hr_app_advance_salary';
    protected $fillable = [
        'branch_id',
        'company_id',
        'project_id',
        'emp_id',

        'advanced_amount',
        'installment_amount',
        'no_of_installment',
        'first_repay_month',

        'application_date',
        'payment_complete',
        'payment_by',
        'payment_date',
        'collection_complete',
        'current_stage',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'emp_id', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\Model\HR\Attachment', 'foreign_key', 'id')->where('ref_table_name', 'hr_app_movements');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

    

}
