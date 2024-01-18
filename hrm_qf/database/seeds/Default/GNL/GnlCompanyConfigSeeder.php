<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlCompanyConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company_config = DB::table('gnl_company_config')->get();
        $sql = "INSERT INTO `gnl_company_config` (`id`, `company_id`, `form_id`, `form_value`, `created_at`, `updated_at`) VALUES
                (85, 1, 2, '2', NULL, NULL),
                (86, 1, 1, 'A4', NULL, NULL),
                (87, 1, 6, '1', NULL, NULL),
                (88, 1, 7, '0', NULL, NULL),
                (89, 1, 8, NULL, NULL, NULL),
                (90, 1, 9, '01-07', NULL, NULL),
                (91, 1, 10, '30-06', NULL, NULL)";
        if (count($company_config) == 0) {
            DB::insert($sql);
        }
    }
}
