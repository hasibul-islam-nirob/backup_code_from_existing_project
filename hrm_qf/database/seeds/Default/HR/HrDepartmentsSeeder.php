<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = DB::table('hr_departments')->get();
        $sql = "INSERT INTO `hr_departments` (`id`, `company_id`, `dept_name`, `short_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, '1', 'General', NULL, 1, 0, '2020-07-25 20:31:28', 1, '2020-07-25 14:31:28', NULL)";
        if (count($departments) == 0) {
            DB::insert($sql);
        }
    }
}
