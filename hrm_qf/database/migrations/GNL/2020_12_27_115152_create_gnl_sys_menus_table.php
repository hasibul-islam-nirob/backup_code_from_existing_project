<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSysMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_sys_menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('module_id')->comment('Master id of Module Table')->nullable();
            $table->integer('parent_menu_id')->comment('Here, 0 means root menu')->default(0);
            $table->string('menu_name',80)->nullable()->index('m_name');
            $table->string('route_link',250)->nullable();
            $table->string('page_title',250)->nullable();
            $table->string('controller',70)->nullable();
            $table->string('action',70)->nullable();
            $table->string('menu_link',150)->nullable();
            $table->string('menu_sort_name',20)->nullable();
            $table->string('menu_icon',255)->nullable()->default('fa-bars');
            $table->string('order_by',30)->nullable();
            $table->text('remarks')->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which user');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which user');
            // $table->unique(['route_link','is_delete']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_sys_menus');
    }
}
