<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('month')->default(now()->format('F'))->change();
            $table->integer('total_days_worked')->default(0); 
            $table->integer('total_days_off')->nullable();
            $table->integer('total_late_check_in')->default(0); 
            $table->integer('total_early_check_out')->default(0); 
            $table->integer('effective_work_days')->default(0); 
            $table->integer('current_salary')->default(0); 
            $table->integer('overtime_pay')->default(0); 
            $table->integer('total_salary')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Foreign Key relation dengan tabel employees

            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
        
        
        
        
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
