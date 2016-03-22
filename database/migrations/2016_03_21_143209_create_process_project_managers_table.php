<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessProjectManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_project_manager', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_id')->unsigned();
            $table->integer('project_manager_id')->unsigned();

            $table->foreign('process_id')->references('id')
                ->on('processes')->onDelete('cascade')
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
        Schema::drop('process_project_manager');
    }
}
