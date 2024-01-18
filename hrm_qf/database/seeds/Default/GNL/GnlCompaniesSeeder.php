<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = DB::table('gnl_companies')->get();
        $sql = "INSERT INTO `gnl_companies` (`id`, `group_id`, `comp_name`, `comp_code`, `module_arr`, `comp_email`, `comp_phone`, `comp_addr`, `comp_web_add`, `comp_logo`, `db_name`, `host`, `username`, `password`, `port`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 'Usha Foundation', '01', '1,2,3', 'info@ushafoundation.org', '01700000003', 'Tarabo', 'http://ushafoundation.org/', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2020-02-15 16:00:32', 1, '2021-03-23 06:07:23', 1)";
        if (count($companies) == 0) {
            DB::insert($sql);
        }
    }
}
