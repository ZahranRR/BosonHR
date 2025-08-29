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
        Schema::table('resignation_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable();  // Relasi dengan employee

            // Menghubungkan employee_id dengan tabel employees
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            // Menghapus foreign key yang terkait dengan employee_id
            $table->dropForeign(['employee_id']);

            // Menghapus kolom employee_id
            $table->dropColumn('employee_id');
        });
    }
};
