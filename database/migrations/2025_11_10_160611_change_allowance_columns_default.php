<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // ubah kolom agar nullable dan default null
            $table->integer('positional_allowance')->nullable()->default(null)->change();
            $table->integer('transport_allowance')->nullable()->default(null)->change();
            $table->integer('bonus_allowance')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('positional_allowance')->nullable(false)->default(0)->change();
            $table->integer('transport_allowance')->nullable(false)->default(0)->change();
            $table->integer('bonus_allowance')->nullable(false)->default(0)->change();
        });
    }
};
