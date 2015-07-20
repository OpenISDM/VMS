<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsAndProcessesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name')->default('');
			$table->text('description')->default('');
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->boolean('is_ongoing')->default(false);
            $table->timestamps();
        });

		Schema::create('processes', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('fk_project_id')->unsigned()->default(0);
			$table->foreign('fk_project_id')->references('id')->on('projects')->onDelete('cascade');
			$table->string('name')->default('');
			$table->text('description')->default('');
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->boolean('is_ongoing')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('processes');
        Schema::drop('projects');
    }
}
