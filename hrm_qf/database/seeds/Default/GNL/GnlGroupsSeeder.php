<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = DB::table('gnl_groups')->get();
        $sql = "INSERT INTO `gnl_groups` (`id`, `group_name`, `group_email`, `group_phone`, `group_addr`, `group_web_add`, `group_logo`, `short_form`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 'USHA Foundation', 'edusha501@gmail.com', '01932299501', 'Dighibarabo, Tarabo, Rupganj, Narayanganj', 'www.ushafoundation.org', NULL, NULL, 1, 0, '2020-02-15 15:59:45', 72, '2020-02-27 10:23:08', 72)";
        if (count($groups) == 0) {
            DB::insert($sql);
        }
    }
}
