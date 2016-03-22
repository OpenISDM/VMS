<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectManagerProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_manager_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('project_manager_id')->unsigned();

            $table->foreign('project_id')->references('id')
                ->on('projects')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('project_manager_id')->references('id')
                ->on('project_managers')->onDelete('cascade')
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
        Schema::drop('project_manager_projects');
    }
}
