<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = DB::table('gnl_projects')->get();
        $sql = "INSERT INTO `gnl_projects` (`id`, `group_id`, `company_id`, `project_name`, `project_code`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 1, 'General', '001', 1, 0, '2020-02-15 16:00:52', 1, '2020-02-15 04:00:52', NULL)";
        if (count($projects) == 0) {
            DB::insert($sql);
        }
    }
}
