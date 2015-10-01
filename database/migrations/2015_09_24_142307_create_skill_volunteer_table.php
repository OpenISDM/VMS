<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillVolunteerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_volunteer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('volunteer_id')->unsigned();
            $table->integer('skill_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            // foreign constraints
            $table->foreign('volunteer_id')->references('id')
                  ->on('volunteers')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')
                  ->on('skills')->onDelete('cascade')
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
        Schema::drop('skill_volunteer');
    }
}
