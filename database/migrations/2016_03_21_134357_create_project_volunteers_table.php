<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_volunteers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('volunteer_id')->unsigned();
            $table->smallInteger('status');
            $table->boolean('is_full_profile_permit');
            $table->smallInteger('permission');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['project_id', 'volunteer_id']);

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
