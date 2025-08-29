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
        if (!Schema::hasColumn('roles', 'status')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->enum('status', ['enable', 'disable'])->default('enable');
            });
        }
    }
    
    public function down()
    {
        if (Schema::hasColumn('roles', 'status')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
