<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id')->autoIncrement();
            $table->string('employee_no',30)->nullable()->unique('employee_no')->comment('sys generate code, EMP-BranchCode-SerialNo');
            $table->integer('company_id')->nullable()->comment('Master id of company table');
            $table->integer('branch_id')->comment('Master id of Branch Table')->default(1);
            $table->string('emp_code',30)->nullable();
            $table->string('emp_name',200)->nullable();
            $table->string('emp_father_name',200)->nullable();
            $table->string('emp_mother_name',200)->nullable();
            $table->string('emp_designation',200)->nullable();
            $table->integer('designation_id')->nullable();
            $table->string('department_id',20)->nullable();
            $table->string('emp_gender',20)->nullable();
            $table->string('emp_email',50)->nullable();
            $table->string('emp_phone',30)->nullable()->unique('emp_phone');
            $table->text('emp_present_addr')->nullable();
            $table->text('emp_parmanent_addr')->nullable();
            $table->date('emp_dob')->nullable();
            $table->string('emp_id_type',100)->nullable();
            $table->string('emp_national_id',30)->nullable();
            $table->text('emp_description')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('is_active')->comment('Here, 1 = active & 0 = in active')->default(1);
            $table->tinyInteger('is_delete')->unique()->comment('Here, 0 = do not delete record, 1 = delete record')->default(0);
            $table->dateTime('created_at')->nullable()->comment('record create date time');
            $table->integer('created_by')->nullable()->comment('created by which employee');
            $table->dateTime('updated_at')->nullable()->comment('record modified date time');
            $table->integer('updated_by')->nullable()->comment('modified by which employee');
            // $table->unique(['emp_phone','is_delete'],'emp_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employees');
    }
}
