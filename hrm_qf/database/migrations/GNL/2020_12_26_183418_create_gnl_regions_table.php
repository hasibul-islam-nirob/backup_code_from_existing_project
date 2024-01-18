<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_regions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('company_id')->nullable()->comment('Master id of Company Table');
            $table->string('region_name',200)->nullable();
            $table->string('region_code',30)->nullable();
            // ->unique('region_code');
            $table->text('zone_arr')->nullable();
            $table->text('area_arr')->nullable();
            $table->longtext('branch_arr')->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
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
        Schema::dropIfExists('gnl_regions');
    }
}
