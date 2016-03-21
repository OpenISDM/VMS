<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_volunteers', function(Blueprint $table) {
            $table->increments(id);
            $table->integer('project_id')->unsigned();
            $table->integer('volunteer_id')->unsigned();
            $table->smallInteger('status');
            $table->smallInteger('permission');

            $table->foreign('project_id')->references('id')
                ->on('projects')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('volunteer_id')->references('id')
                ->on('volunteers')->onDelete('cascade')
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
        Schema::drop('project_volunteers');
    }
}
