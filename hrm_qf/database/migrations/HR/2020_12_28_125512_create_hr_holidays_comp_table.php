<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrHolidaysCompTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_holidays_comp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('company_id')->nullable()->comment('Master id of company table');
            $table->string('ch_title',200);
            $table->string('ch_day',20);
            $table->text('ch_description')->nullable();
            $table->date('ch_eff_date')->comment('From which date holiday will be effective	');

            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('record create date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_holidays_comp');
    }
}
