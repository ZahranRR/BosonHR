<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('offrequests', function (Blueprint $table) {
            $table->string('image')->nullable(); // Menambahkan kolom image
        });
    }
    
    public function down()
    {
        Schema::table('offrequests', function (Blueprint $table) {
            $table->dropColumn('image'); // Hapus kolom image jika rollback
        });
    }
    
    
};
