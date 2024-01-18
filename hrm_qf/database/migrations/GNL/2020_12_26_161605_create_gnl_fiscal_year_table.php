<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlFiscalYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_fiscal_year', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('company_id')->nullable()->index('company_id')->comment('Master id of Company Table');
            $table->string('fy_name',200)->nullable();
            $table->date('fy_start_date')->nullable();
            $table->date('fy_end_date')->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');
            // $table->unique(['company_id','fy_start_date','fy_end_date']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_fiscal_year');
    }
}
