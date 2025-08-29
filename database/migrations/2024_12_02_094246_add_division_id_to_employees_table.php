<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('employees', function (Blueprint $table) {
        $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null'); 
    });
}

public function down()
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropForeign(['division_id']);
        $table->dropColumn('division_id');
    });
}
};
