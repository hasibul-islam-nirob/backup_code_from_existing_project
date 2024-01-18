<?php

namespace App\Model\HR;

use App\BaseModel;

class AllApproval extends BaseModel
{
    protected $table = 'hr_approval_all';
    protected $fillable = [
        'event_id',
        'master_id',
        'step_no',
        'inspect_by',
        'inspection_status',
        'comment',
        'comment_date',
        'related_data',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Model\HR\Employee', 'inspect_by', 'user_id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
