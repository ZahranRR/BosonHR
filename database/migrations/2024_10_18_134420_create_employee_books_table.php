<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employee_books', function (Blueprint $table) {
            $table->bigIncrements('employeebook_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('incident_date');
            $table->text('incident_detail');
            $table->text('remarks');
            $table->enum('category', ['violation', 'warning', 'reprimand']);
            $table->enum('type_of', ['SOP', 'Administrative', 'Behavior']);
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_books');
    }
};
