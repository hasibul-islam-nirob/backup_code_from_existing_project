<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryLogTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_log_trans', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('company_id')->default(1);
            $table->integer('branch_id')->default(1);
            $table->integer('branch_to')->nullable();
            $table->string('table_name',100)->nullable(); 
            $table->text('fillable')->nullable(); 
            $table->text('attributes')->nullable(); 
            $table->longtext('attr_values')->nullable(); 
            $table->string('operation_type',100)->nullable(); 
            $table->longtext('query_sql')->nullable(); 
            $table->dateTime('execution_time')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('query_log_trans');
    }
}
