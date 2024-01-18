<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_user_permissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->string('name',60)->nullable();
            $table->string('route_link',250)->nullable();
            $table->string('page_title',255)->nullable();
            $table->string('bn_name',255)->nullable()->comment('Other language name');
            $table->string('method_name',255)->nullable();
            $table->integer('menu_id')->nullable()->index('menu_id');
            $table->tinyInteger('order_by')->nullable();
            $table->boolean('set_status')->nullable();
            $table->string('notes',255)->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which user');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which user');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_user_permissions');
    }
}
