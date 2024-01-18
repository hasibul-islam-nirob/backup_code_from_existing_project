<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlInstallmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $installment_type = DB::table('gnl_installment_type')->get();
        $sql = "INSERT INTO `gnl_installment_type` (`id`, `name`, `is_active`) VALUES
        (1, 'Monthly', 1),
        (2, 'Weekly', 1),
        (3, 'Daily', 0),
        (4, 'Quarterly', 0),
        (5, 'Half-yearly', 0),
        (6, 'Yearly', 0)";
        if (count($installment_type) == 0) {
            DB::insert($sql);
        }
    }
}
