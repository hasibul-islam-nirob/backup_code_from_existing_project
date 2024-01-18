<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlPaymentSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_system = DB::table('gnl_payment_system')->get();
        $sql = "INSERT INTO `gnl_payment_system` (`id`, `payment_system_name`, `short_name`, `status`, `order_by`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 'Cash', 'CAS', 0, NULL, 1, 0, '2020-02-14 11:45:41', NULL, '2020-02-13 23:45:41', NULL),
        (2, 'Bank', 'BAR', 1, NULL, 1, 0, NULL, NULL, '2021-02-01 22:25:14', 1),
        (3, 'Cheque', 'CHQ', 0, NULL, 0, 0, '2020-02-14 11:45:54', NULL, '2020-02-13 23:45:54', NULL),
        (4, 'Mobile Financial Service', 'MFS', 0, NULL, 0, 0, NULL, NULL, '2020-02-22 03:17:08', NULL),
        (5, 'Direct Transfer', 'DIR', 0, NULL, 0, 0, NULL, NULL, '2020-02-22 03:18:05', NULL),
        (6, 'Multiple', 'MUL', 0, NULL, 0, 0, NULL, NULL, '2020-02-22 03:18:05', NULL),
        (7, 'Other', 'OTH', 0, NULL, 0, 0, NULL, NULL, '2020-02-22 03:18:17', NULL)";
        if (count($payment_system) == 0) {
            DB::insert($sql);
        }
    }
}
