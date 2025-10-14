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
        Schema::table('company_names', function (Blueprint $table) {
            $table->string('company_address')->after('name_company');


        });
    }
    
    public function down()
    {
        Schema::table('company_names', function (Blueprint $table) {
            $table->dropColumn('company_address');
        });
    }
};
