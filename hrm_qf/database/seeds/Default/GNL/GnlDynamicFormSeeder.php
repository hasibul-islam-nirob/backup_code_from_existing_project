<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlDynamicFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dynamic_form = DB::table('gnl_dynamic_form')->get();
        $sql = "INSERT INTO `gnl_dynamic_form` (`id`, `type_id`, `name`, `input_type`, `order_by`, `note`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 'Print Type', 'select', 1, NULL, 1, 0, '2020-10-11 17:45:01', 1, '2021-03-09 11:50:47', 1),
        (2, 1, 'Company Type', 'select', 0, NULL, 1, 0, '2020-10-11 18:13:29', 1, '2021-03-09 12:05:22', 1),
        (6, 1, 'Max bill print', 'text', 3, NULL, 1, 0, '2021-03-09 17:52:11', 1, '2021-03-09 11:52:11', NULL),
        (7, 1, 'Return policy', 'text', 4, NULL, 1, 0, '2021-03-09 17:52:43', 1, '2021-03-09 11:52:43', NULL),
        (8, 1, 'Vat', 'text', 5, NULL, 1, 0, '2021-03-09 17:53:09', 1, '2021-03-09 11:53:09', NULL),
        (9, 1, 'Fiscal year start', 'select', 6, NULL, 1, 0, '2021-03-09 17:54:37', 1, '2021-03-09 11:54:37', NULL),
        (10, 1, 'Fiscal year end', 'select', 7, NULL, 1, 0, '2021-03-09 17:55:00', 1, '2021-03-09 11:55:00', NULL),
        (11, 1, 'Processing fee editable', 'radio', 8, NULL, 1, 0, '2021-03-09 17:55:43', 1, '2021-03-21 05:58:36', 1)";
        if (count($dynamic_form) == 0) {
            DB::insert($sql);
        }
    }
}
