<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerIdToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable(); // Tambahkan kolom manager_id
            $table->foreign('manager_id') // Tambahkan foreign key ke user_id di tabel users
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropForeign(['manager_id']); // Hapus foreign key
            $table->dropColumn('manager_id'); // Hapus kolom manager_id
        });
    }
}
