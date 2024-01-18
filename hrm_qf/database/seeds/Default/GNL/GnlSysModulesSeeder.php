<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlSysModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sys_modules = DB::table('gnl_sys_modules')->get();
        $sql = "INSERT INTO `gnl_sys_modules` (`id`, `module_name`, `module_short_name`, `route_link`, `module_icon`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 'General Configuration', 'gconf', 'gnl', NULL, 1, 0, NULL, NULL, '2020-01-19 07:29:58', NULL),
        (2, 'Point of Sale', 'pos', 'pos', 'fa-cart-arrow-down', 1, 0, NULL, 1, '2020-02-07 00:19:49', 1),
        (3, 'Accounting', 'acc', 'acc', 'fa-usd', 1, 0, NULL, 1, '2020-02-12 10:19:01', 1),
        (5, 'Microfinance', 'mfn', 'mfn', 'fa-handshake-o', 1, 0, '2019-12-22 06:20:00', 1, '2020-07-11 10:33:37', 1),
        (9, 'Fixed Asset Management', 'fam', 'fam', NULL, 0, 0, '2019-12-23 02:09:54', 1, '2020-07-11 10:33:33', 1),
        (10, 'Inventory', 'inv', 'inv', 'fa-list-alt', 1, 0, '2019-12-23 02:10:27', 1, '2020-07-18 09:46:01', 1),
        (11, 'Procurement', 'proc', 'proc', NULL, 0, 0, '2019-12-23 02:11:25', 1, '2020-07-11 10:33:28', 1),
        (12, 'Billing System', 'bill', 'bill', 'fa-file-text-o', 1, 0, '2020-07-11 16:30:22', 1, '2020-07-11 10:33:18', 1),
        (13, 'Human Resource', 'HR', 'hr', 'fa-address-card', 1, 0, '2020-07-19 14:20:18', 1, '2020-07-19 08:20:30', 1)";
        if (count($sys_modules) == 0) {
            DB::insert($sql);
        }
    }
}
