<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlDivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = DB::table('gnl_divisions')->get();
        $sql = "INSERT INTO `gnl_divisions` (`id`, `division_name`, `bn_name`, `short_name`, `url`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 'Chattagram', 'চট্টগ্রাম', 'CTG', 'www.chittagongdiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (2, 'Rajshahi', 'রাজশাহী', 'RAJ', 'www.rajshahidiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (3, 'Khulna', 'খুলনা', 'KHUL', 'www.khulnadiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (4, 'Barisal', 'বরিশাল', 'BAR', 'www.barisaldiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (5, 'Sylhet', 'সিলেট', 'SYL', 'www.sylhetdiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (6, 'Dhaka', 'ঢাকা', 'DHK', 'www.dhakadiv.gov.bd', 1, 0, NULL, 99, '2020-07-05 20:38:06', 99),
        (7, 'Rangpur', 'রংপুর', 'RANG', 'www.rangpurdiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (8, 'Mymensingh', 'ময়মনসিংহ', 'MYMAN', 'www.mymensinghdiv.gov.bd', 1, 0, NULL, NULL, NULL, NULL)";
        if (count($divisions) == 0) {
            DB::insert($sql);
        }
    }
}
