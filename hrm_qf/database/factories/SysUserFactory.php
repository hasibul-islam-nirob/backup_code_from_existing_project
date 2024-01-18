<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\GNL\SysUser;
use Faker\Generator as Faker;

$factory->define(SysUser::class, function (Faker $faker) {
    return [
        'sys_user_role_id' => 1,
        'company_id' => 1,
        'full_name' => $faker->name,
        'username' => 'unit_'.$faker->name,
        'password' => '123456',
        'email' => $faker->unique()->safeEmail,
        'contact_no' => null,
        'designation' => null,
        'department' => null,
        'user_image' => null,
        'signature_image' => null,
        'last_login_ip' => null,
        'last_login_time' => null,
        'ip_address' => null,
        'branch_id' => 1,
        'emp_id' => null,

        'is_active' => 1,
        'is_delete' => 0,
        'created_at' => now(),
        'created_by' => 1,
        'updated_at' => now(),
        'updated_by' => 1,
    ];
});
