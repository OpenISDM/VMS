<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentVolunteerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_volunteer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('volunteer_id')->unsigned();
            $table->integer('equipment_id')->unsigned();

            // foreign constraints
            $table->foreign('volunteer_id')->references('id')
                  ->on('volunteers')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')
                  ->on('equipment')->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('equipment_volunteer');
    }
}
