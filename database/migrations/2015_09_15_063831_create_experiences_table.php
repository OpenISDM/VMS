<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company');
            $table->string('job_title', 100);
            $table->integer('start_year');
            $table->integer('end_year')->nullable();
            $table->bigInteger('volunteer_id')->index()->unsigned();    // foreign key
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            // foreign constraint
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
        Schema::drop('experiences');
    }
}
