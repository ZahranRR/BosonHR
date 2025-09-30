<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('attendance_allowance')->default(0)->after('overtime_pay');
            $table->integer('absent_deduction')->default(0)->after('attendance_allowance');
            $table->integer('base_salary')->default(0)->after('current_salary');

        });
    }
    
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['attendance_allowance', 'absent_deduction', 'base_salary']);
        });
    }
    
};
