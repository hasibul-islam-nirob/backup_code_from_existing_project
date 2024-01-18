<?php

use Illuminate\Database\Seeder;

class GNLSeeder extends Seeder
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
                ## GNL

                GnlBranchsSeeder::class,
                GnlCompaniesSeeder::class,
                GnlCompanyConfigSeeder::class,
                GnlCompanyTypeSeeder::class,
                GnlCountrySeeder::class,
                GnlDistrictsSeeder::class,
                GnlDivisionsSeeder::class,
                GnlDynamicFormSeeder::class,
                GnlDynamicFormTypeSeeder::class,
                GnlDynamicFormValueSeeder::class,
                GnlGroupsSeeder::class,
                GnlInstallmentTypeSeeder::class,
                GnlPaymentSystemSeeder::class,
                GnlProjectsSeeder::class,
                GnlProjectTypeSeeder::class,
                GnlSysMenusSeeder::class,
                GnlSysModulesSeeder::class,
                GnlSysUsersSeeder::class,
                GnlSysUserRolesSeeder::class,
                GnlUnionsSeeder::class,
                GnlUpazilasSeeder::class,
                GnlUserPermissionSeeder::class,
                GnlVillageSeeder::class,
            ]
        );
    }
}
