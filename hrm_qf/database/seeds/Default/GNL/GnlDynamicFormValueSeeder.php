<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlDynamicFormValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dynamic_form_value = DB::table('gnl_dynamic_form_value')->get();
        $sql = "INSERT INTO `gnl_dynamic_form_value` (`id`, `type_id`, `form_id`, `name`, `value_field`, `order_by`, `note`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 1, 'POS', 'POS', 1, NULL, 1, 0, '2021-03-21 13:31:54', 1, '2021-03-21 07:31:54', NULL),
        (2, 1, 1, 'A4', 'A4', 2, NULL, 1, 0, '2021-03-21 13:32:29', 1, '2021-03-21 07:32:29', NULL),
        (3, 1, 2, 'NGO', '2', 1, NULL, 1, 0, '2021-03-21 13:33:48', 1, '2021-03-21 07:33:48', NULL),
        (4, 1, 2, 'Fashion', '3', 2, NULL, 1, 0, '2021-03-21 13:34:13', 1, '2021-03-21 07:34:13', NULL),
        (5, 1, 2, 'Supper Shop', '4', 3, NULL, 1, 0, '2021-03-21 13:34:35', 1, '2021-03-21 07:34:35', NULL),
        (6, 1, 7, '0', '0', 1, NULL, 1, 1, '2021-03-22 13:35:24', 1, '2021-03-22 07:35:41', 1),
        (7, 1, 9, '01-07', '01-07', 1, NULL, 1, 0, '2021-03-22 14:24:58', 1, '2021-03-22 08:24:58', NULL),
        (8, 1, 10, '30-06', '30-06', 1, NULL, 1, 0, '2021-03-22 14:25:14', 1, '2021-03-22 08:25:23', 1)";
        if (count($dynamic_form_value) == 0) {
            DB::insert($sql);
        }
    }
}
