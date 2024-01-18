<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlSysUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_sys_user_roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->unsignedInteger('id')->autoIncrement()->comment('Primary Key');
            $table->unsignedInteger('parent_id')->comment('Adjacency List Reference Id')->index('idx_usergroup_adjacency_lookup')->default(0);
            // $table->integer('left_child')->comment('Nested set left child.')->default(0);
            // $table->integer('right_child')->comment('Nested set right child.')->default(0);
            $table->string('role_name',100)->nullable()->index('role_name');
            $table->string('order_by', 30)->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which user');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which user');
            $table->longtext('modules')->nullable();
            $table->longtext('menus')->nullable();
            $table->longtext('permissions')->nullable();
            $table->longtext('serialize_module')->nullable();
            $table->longtext('serialize_menu')->nullable();
            $table->longtext('serialize_permission')->nullable();
            // $table->unique(['parent_id','role_name'],'idx_usergroup_parent_title_lookup');
            // $table->index(['left_child','right_child'],'idx_usergroup_nested_set_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_sys_user_roles');
    }
}
