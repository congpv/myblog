<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doctor_user_id')->unique();            
            $table->timestamps();
        });
        
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_user_id')->unsigned()->unique();
            $table->string('patient_first_name')->nullable();
            $table->string('patient_last_name')->nullable();
            $table->string('patient_full_name')->nullable();            
            $table->string('patient_link')->nullable();
            $table->string('patient_picture')->nullable();
            $table->integer('patient_gender')->nullable()->default(1)->comment('1: male, 2: female, Null: undefined');
            $table->timestamps();
            
            $table->foreign('patient_user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });
        
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reservation_patient_id')->unsigned();
            $table->date('reservation_date')->nullable();
            $table->integer('reservation_month')->nullable();
            $table->integer('reservation_year')->nullable();
            $table->string('reservation_time_begin')->nullable();
            $table->string('reservation_time_end')->nullable();
            $table->string('reservation_time_frame')->nullable();
            $table->integer('reservation_is_deleted')->default('0');
            $table->timestamps();
            
            $table->foreign('reservation_patient_id')
            ->references('id')
            ->on('patients')
            ->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('reservations');
    }
}
