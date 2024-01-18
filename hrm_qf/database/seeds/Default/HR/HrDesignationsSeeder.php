<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrDesignationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = DB::table('hr_designations')->get();
        $sql = "INSERT INTO `hr_designations` (`id`, `name`, `short_name`, `is_active`, `is_delete`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
        (1, 'Program Director-General', NULL, 1, 0, NULL, '2020-07-25 14:24:34', 1, 1),
        (2, 'Cook', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (3, 'Station Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (4, 'Manager - Marketing', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (5, 'Trainee- Credit Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (6, 'Trainee- Asst. A/C & MIS Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (7, 'RB- SS ', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (8, 'Executive Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (9, 'Deputy Executive Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (10, 'Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (11, 'Additional Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (12, 'Deputy Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (13, 'Assistant Director', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (14, 'Senior Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (15, 'Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (16, 'Deputy Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (17, 'Assistant Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (18, 'Senior Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (19, 'Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (20, 'Associate Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (21, 'Assistant Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (22, 'Senior Office Executive', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (23, 'Office Executive', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (24, 'Executive Assistant', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (25, 'Senior Driver', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (26, 'Credit Officer ', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (27, 'Senior Credit Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (28, 'Senior Branch Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (29, 'Branch Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (30, 'Associate A/C & MIS Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (31, 'Assistant A/C & MIS Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (32, 'Senior Program Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (33, 'Program Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (34, 'Zonal Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (35, 'Area Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (36, 'Driver', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (37, 'Enterprise Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (38, 'Trainee-Head Office', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (39, 'Associate Officer 01', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (40, 'Front Desk Execuitive', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (41, 'Support Staff', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (42, 'Management Trinee', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (43, 'Senior Support Staff ', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (44, 'Assistant Station Manager', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (45, 'Trainee-Enterprise Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (46, 'Project Coordinator', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (47, 'Officer-FO', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (48, 'Program Officer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (49, 'Program Officer-1', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (50, 'Volunteer', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (51, 'Field Supervisor ', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (52, 'Assistant Director-MFP', '', 1, 0, NULL, '2020-02-19 05:30:19', 0, 0),
        (53, 'test', 'yy', 1, 1, '2020-07-25 20:26:24', '2020-07-25 14:26:32', 1, 1),
        (54, 'test 2', 'dd', 1, 1, '2020-07-25 20:26:41', '2020-07-25 14:26:48', 1, 1)";
        if (count($designations) == 0) {
            DB::insert($sql);
        }
    }
}
