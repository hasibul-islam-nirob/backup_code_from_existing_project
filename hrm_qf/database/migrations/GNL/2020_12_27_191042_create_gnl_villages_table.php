<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlVillagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_villages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('division_id')->index('FK_divisions_for_villages')->nullable()->comment('Master id of devisions table');
            $table->integer('district_id')->index('Fk_districts_for_villages')->nullable()->comment('Master id of districts table');
            $table->integer('upazila_id')->index('Fk_upazilas_for_villages')->nullable()->comment('Master id of upazilas table');
            $table->integer('union_id')->index('FK_unions_for_villages')->nullable()->comment('Master id of unions table');
            $table->string('village_name',100)->nullable();
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
        Schema::dropIfExists('gnl_villages');
    }
}
