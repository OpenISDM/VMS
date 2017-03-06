<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_id')->unsigned();
            $table->bigInteger('project_volunteer_id')->unsigned();
            $table->string('status');
            $table->double('longtitude', 15, 12);
            $table->double('latitude', 15, 12);
            $table->boolean('car');
            $table->boolean('motobike');
            $table->boolean('bycicle');
            $table->boolean('smart_phone');
            $table->boolean('tablet');
            $table->boolean('notebook');
            $table->timestamps();
            $table->foreign('project_volunteer_id')->references('id')->on('project_volunteers');
            $table->foreign('process_id')->references('id')->on('processes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('process_candidates');
    }
}
