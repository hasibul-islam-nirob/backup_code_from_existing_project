<?php

namespace App\Model\GNL\HR;

use App\BaseModel;

class Room extends BaseModel
{

    protected $table = 'hr_rooms';
    protected $fillable = [
        'company_id',
        'dept_id',
        'room_name',
        'room_code',

        'is_active',
        'is_delete',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function department()
    {
        return $this->belongsTo('App\Model\GNL\HR\EmpDepartment', 'dept_id', 'id');
    }
    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
