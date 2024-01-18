<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlDynamicFormTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dynamic_form_type = DB::table('gnl_dynamic_form_type')->get();
        $sql = "INSERT INTO `gnl_dynamic_form_type` (`id`, `name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 'Company Configuration', 1, 0, '2020-10-11 17:02:45', 1, '2020-10-11 11:17:09', 1),
        (2, 'General Configuration', 1, 0, '2020-10-11 17:17:42', 1, '2020-10-11 11:17:42', NULL)";
        if (count($dynamic_form_type) == 0) {
            DB::insert($sql);
        }
    }
}
