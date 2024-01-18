<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGnlBranchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gnl_branchs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('group_id')->nullable()->index('group_id')->comment('Master id of Group Table');
            $table->integer('company_id')->nullable()->index('company_id')->comment('Master id of Company Table');
            $table->integer('project_id')->nullable()->index('project_id')->comment('Master id of Project Table');
            $table->integer('project_type_id')->nullable()->index('project_type_id')->comment('Master id of Project Type Table');
            $table->string('branch_name',200)->nullable();
            $table->string('branch_code',50)->nullable();
            // ->unique('branch_code');
            $table->string('branch_email',50)->nullable();
            $table->string('branch_phone',20)->nullable();
            $table->text('branch_addr')->nullable();
            $table->string('contact_person',150)->nullable();
            $table->string('vat_registration_no',50)->nullable();
            $table->date('branch_opening_date')->nullable();
            $table->date('soft_start_date')->nullable()->comment('Software starting date in this branch');
            $table->date('acc_start_date')->nullable()->comment('ACC Software starting date in this branch');
            $table->date('mfn_start_date')->nullable()->comment('Microfinance Software starting date in this branch');
            $table->date('inv_start_date')->nullable()->comment('Inventory Software starting date in this branch');
            $table->date('bill_start_date')->nullable()->comment('Billig Software starting date in this branch');
            $table->date('hr_start_date')->nullable()->comment('Human Resource Software starting date in this branch');
            $table->date('fam_start_date')->nullable()->comment('Fixed Asset Management Software starting date in this branch');
            $table->date('proc_start_date')->nullable()->comment('Procurement Software starting date in this branch');
            $table->tinyInteger('is_approve')->nullable()->comment('Approved when flag is 1, Otherwise its Waiting for Approval	')->default(0);
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('Record create date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');
            // $table->unique(['branch_code','is_delete']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnl_branchs');
    }
}
