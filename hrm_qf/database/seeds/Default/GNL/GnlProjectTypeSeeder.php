<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project_types = DB::table('gnl_project_types')->get();
        $sql = "INSERT INTO `gnl_project_types` (`id`, `group_id`, `company_id`, `project_id`, `project_type_name`, `project_type_code`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (2, 1, 1, 1, 'General', '001001', 1, 0, '2020-02-15 16:01:11', 1, '2020-02-15 04:01:11', NULL)";
        if (count($project_types) == 0) {
            DB::insert($sql);
        }
    }
}
