<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrGovtHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays_govt = DB::table('hr_holidays_govt')->get();
        $sql = "INSERT INTO `hr_holidays_govt` (`id`, `company_id`, `gh_title`, `gh_date`, `efft_start_date`, `efft_end_date`, `gh_description`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (28, 1, 'International Mother Language Day', '21-02', NULL, NULL, 'International Mother Language Day', 1, 0, '2020-01-21 06:34:27', NULL, '2020-01-21 06:37:03', NULL),
        (29, 1, 'Sheikh Mujibur Rahman\'s birthday', '17-03', '2020-02-22', NULL, 'Father of Nation of Bangladesh', 1, 0, '2020-01-21 06:37:28', 1, '2020-02-22 10:55:19', 1),
        (30, 1, 'Independence Day', '26-03', NULL, NULL, 'Declaration of Independence from Pakistan in 1971', 1, 0, '2020-01-21 06:39:30', NULL, '2020-01-21 06:39:30', NULL),
        (31, 1, 'Pahela Boishakh', '14-04', NULL, NULL, 'Bengali New Year', 1, 0, '2020-01-21 06:40:00', NULL, '2020-01-21 06:40:00', NULL),
        (32, 1, 'May Day', '01-05', NULL, NULL, 'International Labour Day', 1, 0, '2020-01-21 06:40:19', NULL, '2020-01-21 06:40:19', NULL),
        (33, 1, 'National Mourning Day', '15-08', NULL, NULL, 'National Mourning Day', 1, 0, '2020-01-21 06:40:50', NULL, '2020-01-21 06:40:50', NULL),
        (34, 1, 'Victory Day', '16-12', NULL, NULL, 'Commemorates the surrender of the Pakistani army to the Mukti Bahini', 1, 0, '2020-01-21 06:42:09', NULL, '2020-01-21 06:42:09', NULL),
        (35, 1, 'Christmas Day', '25-12', NULL, NULL, 'Christmas Day', 1, 0, '2020-01-21 06:42:33', 1, '2020-02-16 08:48:23', 1),
        (37, 1, 'Shadinota Dibash', '26-03', '2020-03-26', NULL, NULL, 1, 1, '2020-02-22 15:27:33', 72, '2020-02-27 16:04:36', 72)";
        if (count($holidays_govt) == 0) {
            DB::insert($sql);
        }
    }
}
