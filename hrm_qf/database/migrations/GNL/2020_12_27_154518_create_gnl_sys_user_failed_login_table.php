<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSysUserFailedLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_sys_user_failed_login', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->string('username',150)->nullable();
            $table->string('password',150)->nullable();
            $table->dateTime('attempt_time')->nullable();
            $table->string('ip_address',30)->nullable();
            $table->string('browser_address',150)->nullable();
            $table->text('http_user_agent')->nullable();
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
        Schema::dropIfExists('gnl_sys_user_failed_login');
    }
}
