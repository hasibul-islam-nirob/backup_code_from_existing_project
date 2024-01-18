<?php

namespace App\Model\HR;

use App\BaseModel;

class EmployeeOrganizationDetails extends BaseModel
{

    protected $table = 'hr_emp_organization_details';
    protected $fillable = [
        'emp_id',
        'project_id',
        'project_type_id',
        'company_id',

        'rec_type_id',
        'level',
        'grade',
        'step',
        'payscal_id',
        'salary_structure_id',

        'phone_no',
        'fax_no',
        'fiscal_year_id',
        'last_inc_date',
        'security_amount',
        'adv_security_amount',
        'installment_amount',
        'edps_start_month',
        'status',
        'location',
        'room_no',
        'device_id',
        'tot_salary',
        'salary_inc_year',
        'security_amount_location',
        'edps_amount',
        'edps_lifetime',
        'no_of_installment',

        'has_house_allowance',
        'has_travel_allowance',
        'has_daily_allowance',
        'has_medical_allowance',
        'has_utility_allowance',
        'has_mobile_allowance',
        'has_welfare_fund',
        ];

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }

}
