<?php

namespace App\Model\GNL;

use App\BaseModel;

class SysMenuAction extends BaseModel
{

    protected $table = 'gnl_user_permissions';
    protected $fillable = [
        'name', 'bn_name', 'route_link', 'page_title', 'method_name', 'module_id', 'menu_id', 'order_by', 'set_status', 'notes', 'is_active', 'is_delete', 'updated_at', 'created_at',
        'created_by',
		'updated_by'
    ];

    public function SysMenu()
    {
        return $this->belongsTo(SysUsrMenus::class, 'menu_id', 'id');
    }

    public function SysModule()
    {
        return $this->belongsTo(SysModule::class, 'module_id', 'id');
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
