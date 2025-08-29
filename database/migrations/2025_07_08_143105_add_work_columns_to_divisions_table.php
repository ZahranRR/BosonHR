<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_work_columns_to_divisions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('divisions', function (Blueprint $table) {
            $table->string('work_days')->nullable();
            $table->boolean('has_overtime')->default(false)->after('work_days');
        });
    }

    public function down(): void {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn(['work_days', 'has_overtime']);
        });
    }
};

