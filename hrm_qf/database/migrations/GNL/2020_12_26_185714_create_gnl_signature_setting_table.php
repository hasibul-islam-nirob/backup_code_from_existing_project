<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSignatureSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_signature_setting', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('module_id');
            $table->string('title',250)->collation('utf8mb4_general_ci');
            $table->integer('signatorDesignationId');
            $table->integer('signatorEmployeeId')->nullable();
            $table->enum('applicableFor', ['HeadOffice', 'Branch'])->collation('utf8mb4_general_ci');
            $table->tinyInteger('positionOrder')->nullable();
            $table->boolean('status')->default(1);
            $table->dateTime('created_at')->comment('Record create date time');
            $table->dateTime('updated_at')->comment('record modified date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_signature_setting');
    }
}
