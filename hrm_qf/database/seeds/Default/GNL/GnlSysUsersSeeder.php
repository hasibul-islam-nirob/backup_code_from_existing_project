<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GnlSysUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sys_users = DB::table('gnl_sys_users')->get();
        if (count($sys_users) == 0) {
            DB::table('gnl_sys_users')->insert([
                'company_id' => 1,
                'branch_id' => 1,
                'emp_id' => 1,
                'sys_user_role_id' => 1,
                'full_name' => 'Super Admin',
                'username' => 'sadmin',
                'password' => Hash::make('123456'),
                'email' => 'superadmin@info.com',
                'contact_no' => '',
                'user_image' => '',
                'user_image_url' => '',
                'signature_image' => '',
                'signature_image_url' => '',
                'ip_address' => '',
                'browser_address' => '',
                'last_login_ip' => '103.55.146.171',
                'last_login_time' => Carbon\Carbon::now(),
                'is_active'=>1,
                'is_delete'=>0,
                'created_at'=> Carbon\Carbon::now(),
                'created_by'=>1,
                'updated_at'=>Carbon\Carbon::now(),
                'updated_by'=>1,
            ]);
        }
    }
}
