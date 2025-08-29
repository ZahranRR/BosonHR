<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            $table->string('name')->nullable()->change(); // Atau gunakan default value
            // Contoh dengan default value:
            // $table->string('name')->default('Unknown')->change();
        });
    }

    public function down(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change(); // Mengembalikan ke kondisi awal
        });
    }
};
