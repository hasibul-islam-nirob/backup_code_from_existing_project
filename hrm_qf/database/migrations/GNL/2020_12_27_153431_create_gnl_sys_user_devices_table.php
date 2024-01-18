<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSysUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_sys_user_devices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
             $table->string('sys_username',100)->nullable();
             $table->integer('sys_user_id')->nullable();
             $table->integer('sys_user_role_id')->nullable();
             $table->string('device_name',250)->nullable();
             $table->text('access_token')->nullable();
             $table->string('ip_address',30)->nullable();
             $table->string('browser_address',150)->nullable();
             $table->text('http_user_agent')->nullable();
             $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
             $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
             $table->dateTime('created_at')->nullable()->comment('Record create date time');
             $table->dateTime('updated_at')->nullable()->comment('record modified date time');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_sys_user_devices');
    }
}
