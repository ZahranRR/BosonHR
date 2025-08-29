<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
        });
    }
    public function down()
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
        });
    }
};
