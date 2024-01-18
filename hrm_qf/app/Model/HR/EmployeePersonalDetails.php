<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeePersonalDetails extends BaseModel
{

    protected $table = 'hr_emp_personal_details';
    protected $fillable = [
        'emp_id',
        'emp_name_bn',
        'father_name_en',
        'father_name_bn',
        'mother_name_en',
        'mother_name_bn',
        'spouse_name_en',
        'spouse_name_bn',
        // 'gender',  // this column move to hr_employees table
        'dob',
        'nid_no',
        'driving_license_no',
        'marital_status',
        'num_of_children',
        'religion',
        'blood_group',
        'birth_certificate_no',
        'passport_no',
        'tin_no',
        'phone_no',
        'mobile_no',
        'email',

        'pre_addr_division_id',
        'pre_addr_district_id',
        'pre_addr_thana_id',
        'pre_addr_union_id',
        'pre_addr_village_id',
        'pre_addr_street',

        'par_addr_division_id',
        'par_addr_district_id',
        'par_addr_thana_id',
        'par_addr_union_id',
        'par_addr_village_id',
        'par_addr_street',

        'photo',
        'nid_signature',
        'signature',
        'status'
        ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
