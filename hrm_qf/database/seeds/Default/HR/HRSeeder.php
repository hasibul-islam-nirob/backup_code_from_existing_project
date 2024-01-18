<?php

use Illuminate\Database\Seeder;

class HRSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                ## HR

                HrDepartmentsSeeder::class,
                HrGovtHolidaySeeder::class,
                HrGovtHolidaySeeder::class,
            ]
        );
    }
}
