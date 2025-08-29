<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('offrequests', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('user_id');
        });
    
        // Menambahkan foreign key untuk relasi dengan tabel employees
        Schema::table('offrequests', function (Blueprint $table) {
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    
        // Update data yang sudah ada agar employee_id terisi
        DB::table('offrequests')
            ->whereNull('employee_id')
            ->update([
                'employee_id' => DB::raw('user_id')
            ]);
    }
    

    public function down()
    {
        Schema::table('offrequests', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);  // Menghapus foreign key constraint
            $table->dropColumn('employee_id');     // Menghapus kolom employee_id
        });
    }
};
