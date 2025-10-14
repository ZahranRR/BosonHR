<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id('cash_advance_id');
            $table->unsignedBigInteger('employee_id');
            $table->integer('total_amount');
            $table->integer('installments')->default(1); // jumlah cicilan
            $table->integer('installment_amount');
            $table->integer('remaining_installments')->default(1);
            $table->string('start_month',7);
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
            $table->timestamps();
        
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_advances');
    }
};
