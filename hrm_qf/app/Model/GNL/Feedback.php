<?php

// namespace App;
namespace App\Model\GNL;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;

class Feedback extends Model
{
    protected $table='gnl_feedback';
    protected $fillable = [
        'f_title',
        'f_code',
        'f_description',
        'date',
        'branch_id',
        'status',
        'attachment',

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

  
    // public static function boot()
    // {
    //     parent::boot();
    // }

}

