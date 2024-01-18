<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_companies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('group_id')->nullable()->index('group_id')->comment('Master id of Group Table');
            $table->string('comp_name',200)->nullable();
            $table->string('comp_code',50)->nullable();
            $table->string('module_arr',250)->nullable()->comment('Selected Module ID');
            $table->string('comp_email',50)->nullable();
            $table->string('comp_phone',20)->nullable();
            $table->text('comp_addr')->nullable();
            $table->string('comp_web_add',50)->nullable();
            $table->text('comp_logo')->nullable();
            $table->string('db_name', 100)->nullable();
            $table->string('host', 100)->nullable();
            $table->string('username', 50)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('port', 50)->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');
            // $table->unique(['comp_code','is_delete']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_companies');
    }
}
