<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSysUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_sys_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('company_id')->nullable()->comment('Master id of company table');
            $table->integer('branch_id')->comment('1 = Head Office Branch')->default(1);
            $table->string('emp_id',30)->comment('emp_id of Employee Table');
            $table->integer('sys_user_role_id')->nullable();
            $table->string('full_name',255)->nullable();
            $table->string('username',128)->nullable();
            $table->string('password',250)->nullable();
            $table->string('email',150)->nullable();
            $table->string('contact_no',20)->nullable();
            $table->text('user_image')->nullable();
            $table->text('user_image_url')->nullable();
            $table->text('signature_image')->nullable();
            $table->text('signature_image_url')->nullable();
            $table->string('ip_address',50)->nullable();
            $table->string('browser_address',100)->nullable();
            $table->string('last_login_ip',50)->nullable();
            $table->dateTime('last_login_time')->nullable();
            $table->string('remember_token',100)->nullable()->comment('reset password remember token');
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which user');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which user');
            // $table->unique(['username','is_delete']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_sys_users');
    }
}
