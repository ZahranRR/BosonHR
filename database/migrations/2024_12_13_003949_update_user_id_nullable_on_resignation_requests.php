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
            $table->unsignedBigInteger('user_id')->nullable()->change();  // Memungkinkan NULL pada user_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();  // Mengubah kembali menjadi tidak nullable
        });
    }
};
