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
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->string('last_processed_month', 7)->nullable()->after('start_month');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropColumn('last_processed_month');
        });
    }
};
