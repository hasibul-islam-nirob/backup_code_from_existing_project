<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessQueryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_query_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->string('company_id',20)->nullable()->comment('Master id of company table');
            $table->string('branch_id',20)->nullable()->comment('Branch Id sometime brnach_from also');
            $table->string('branch_to',20)->nullable();
            $table->integer('branch_from')->nullable();
            $table->string('table_name',100)->nullable();
            $table->string('operation_type',100)->nullable();
            $table->longtext('remarks')->nullable();
            $table->dateTime('execution_time')->nullable();
            $table->string('execution_by',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_query_log');
    }
}
