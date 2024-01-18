<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlTermsConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_terms_conditions', function (Blueprint $table) {
             $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
             $table->integer('company_id')->nullable()->comment('Master id of company table');
             $table->string('tc_name',500)->nullable();
             $table->text('tc_remarks')->nullable();
             $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
             $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
             $table->dateTime('created_at')->nullable();
             $table->integer('created_by')->nullable();
             $table->dateTime('updated_at')->nullable();
             $table->integer('updated_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_terms_conditions');
    }
}
