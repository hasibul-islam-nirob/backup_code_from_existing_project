<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlBranchsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branchs = DB::table('gnl_branchs')->get();
        $sql = "INSERT INTO `gnl_branchs` (`id`, `group_id`, `company_id`, `project_id`, `project_type_id`, `branch_name`, `branch_code`, `branch_email`, `branch_phone`, `branch_addr`, `contact_person`, `vat_registration_no`, `branch_opening_date`, `soft_start_date`, `acc_start_date`, `mfn_start_date`, `inv_start_date`, `bill_start_date`, `hr_start_date`, `fam_start_date`, `proc_start_date`, `is_approve`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 1, 1, 2, 'Head Office', '000', NULL, '453453453453', 'Tarabo', 'Mr. Tajul', NULL, '2000-01-01', '2020-07-30', '2020-07-30', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, '2020-02-15 16:05:43', 1, '2020-02-15 04:05:51', 1)";
        if (count($branchs) == 0) {
            DB::insert($sql);
        }
    }
}
