<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectManagerProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_manager_project', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->index()->unsigned();
            $table->integer('project_manager_id')->index()->unsigned();

            $table->foreign('project_id')->references('id')
                ->on('projects')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('project_manager_id')->references('id')
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
        Schema::drop('project_manager_project');
    }
}
