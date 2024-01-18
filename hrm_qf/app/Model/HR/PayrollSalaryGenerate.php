<?php

namespace App\Model\HR;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class PayrollSalaryGenerate extends BaseModel
{
    protected $table = 'hr_payroll_salary';
    protected $fillable = [
        'salary_month',
        'payscale_year_id', 
        'company_id', 
        'project_id', 
        'branch_id', 
        'status', 
        'approved_by', 
        'approved_date', 
        'payment_date',
        'voucher_generate',
        'create_by', 
        'create_at', 
        'is_delete', 
        'salary_details'
    ];


    public function payScale()
    {
        return $this->belongsTo('App\Model\HR\PayScale', 'payscale_year_id', 'id');
    }

    public function company(){
        return DB::table('gnl_companies')->find($this->company_id);
    }

    public function project(){
        return DB::table('gnl_projects')->find($this->project_id);
    }

    /* Here Insert Created By & Update By */
    public static function boot()
    {
        parent::boot();
    }
}
