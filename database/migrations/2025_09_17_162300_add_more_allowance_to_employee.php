<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('positional_allowance')->default(0)->after('attendance_allowance');
            $table->integer('transport_allowance')->default(0)->after('positional_allowance');
            $table->integer('bonus_allowance')->default(0)->after('transport_allowance');
        });
    }

    public function down(): void {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['positional_allowance', 'transport_allowance', 'bonus_allowance']);
        });
    }
};
