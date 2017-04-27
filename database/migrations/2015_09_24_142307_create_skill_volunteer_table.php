<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->bigIncrements('id');
            $table->bigInteger('volunteer_id')->unsigned();
            $table->bigInteger('skill_id')->unsigned();

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
