<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessProjectVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_project_volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_id')->unsigned();
            $table->integer('project_volunteer_id')->unsigned();
            $table->smallInteger('status');
            $table->smallInteger('permission');

            $table->foreign('process_id')->references('id')
                ->on('processes')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('project_volunteer_id')->references('id')
                ->on('project_volunteers')->onDelete('cascade')
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
        Schema::drop('process_project_volunteers');
    }
}
