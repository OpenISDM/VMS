<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('volunteer_id')->unsigned();
            $table->integer('equipment_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

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
        Schema::drop('equipment_volunteers');
    }
}
