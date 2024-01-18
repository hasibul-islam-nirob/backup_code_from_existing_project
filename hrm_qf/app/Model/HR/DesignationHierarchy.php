<?php

namespace App\Model\HR;

use App\BaseModel;

class DesignationHierarchy extends BaseModel
{

    protected $table = 'hr_designation_hierarchy';
    protected $fillable = [
        'path',
        'designation_id',
        'no_of_child',
    ];

}
