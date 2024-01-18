<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlUpazilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_upazilas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('division_id')->nullable()->comment('Master id of devisions table');
            $table->integer('district_id')->nullable()->index('district_id')->comment('Master id of districts table');
            $table->string('upazila_name',200)->nullable();
            $table->string('bn_name',200)->nullable();
            $table->string('url',50)->nullable();
            $table->tinyInteger('is_active')->nullable()->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->nullable()->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
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
        Schema::dropIfExists('gnl_upazilas');
    }
}
