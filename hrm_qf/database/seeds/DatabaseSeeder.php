<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call([
        //     LoanSeeder::class,
        // ]);
        // $this->call(UsersTableSeeder::class);
        $this->call(
            [
                ## ACC
                ACCSeeder::class,

                ## GNL
                GNLSeeder::class,

                ## HR
                HRSeeder::class,

                ## INV
                INVSeeder::class,

                ## POS
                POSSeeder::class,

                ## BILL
                BILLSeeder::class,

                ## FAM
                FAMSeeder::class,

                ## MFN
                MFNSeeder::class,
            ]
        );
    }
}
