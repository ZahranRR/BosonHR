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
        Schema::table('offrequests', function (Blueprint $table) {
            $table->dropUnique('offrequests_email_unique'); // Ganti dengan nama constraint unik yang tepat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offrequests_email', function (Blueprint $table) {
            //
        });
    }
};
