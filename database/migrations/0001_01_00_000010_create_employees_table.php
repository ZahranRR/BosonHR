<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('employee_id');
            $table->unsignedBigInteger('recruitment_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('place_birth')->nullable();
            $table->date('date_birth')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('address')->nullable();
            $table->string('current_address')->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('blood_rhesus')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('hp_number')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Widow', 'Widower'])->nullable();
            $table->string('cv_file')->nullable();
            $table->string('update_cv')->nullable();
            $table->enum('last_education', ['Elementary School', 'Junior High School', 'Senior High School', 'Vocational High School', 'Associate Degree 1', 'Associate Degree 2', 'Associate Degree 3', 'Bachelor’s Degree', 'Master’s Degree', 'Doctoral Degree'])->nullable();
            $table->string('degree')->nullable();
            $table->date('starting_date')->nullable();
            $table->string('interview_by')->nullable();
            $table->integer('current_salary')->nullable();;
            $table->boolean('insurance')->nullable();
            $table->string('serious_illness')->nullable();
            $table->string('hereditary_disease')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('relations', ['Parent', 'Guardian', 'Husband', 'Wife', 'Sibling'])->nullable();
            $table->string('emergency_number')->nullable();
            $table->enum('status', ['Active', 'Inactive','Pending','Suspend'])->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
