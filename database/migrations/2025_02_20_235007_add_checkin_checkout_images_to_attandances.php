<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attandances', function (Blueprint $table) {
            $table->string('image_checkin')->nullable()->after('check_in_status');
            $table->string('image_checkout')->nullable()->after('check_out_status');
        });
    }

    public function down()
    {
        Schema::table('attandances', function (Blueprint $table) {
            $table->dropColumn(['image_checkin', 'image_checkout']);
        });
    }
};
