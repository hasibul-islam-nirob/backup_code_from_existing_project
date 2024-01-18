<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class EmployeeTransfer extends BaseModel
{

    protected $table = 'hr_app_transfers';

    protected $fillable = [
        'transfer_code',
        'branch_id',
        'branch_to_id',
        'emp_id',
        'description',
        'status',
        'transfer_date',
        'exp_effective_date',
        'effective_date',
        'attachment',
        'current_stage',
        'company_id',

        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_id', 'id');
    }

    public function branch_to()
    {
        return $this->belongsTo('App\Model\GNL\Branch', 'branch_to_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\GNL\HR\Employee', 'emp_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
