<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlCompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company_type = DB::table('gnl_company_type')->get();
        $sql = "INSERT INTO `gnl_company_type` (`id`, `company_id`, `name`, `is_active`, `is_delete`, `updated_at`, `created_at`) VALUES
        (2, '1', 'NGO', 1, 0, '2020-10-11 09:21:03', '2020-10-11 15:21:03'),
        (3, '1', 'Fashion', 1, 0, '2020-10-11 09:21:36', '2020-10-11 15:21:36'),
        (4, '1', 'Supper Shop', 1, 0, '2020-10-11 09:21:50', '2020-10-11 15:21:50')";
        if (count($company_type) == 0) {
            DB::insert($sql);
        }
    }
}
