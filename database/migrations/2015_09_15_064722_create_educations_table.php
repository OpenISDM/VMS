<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('school');
            $table->integer('degree');
            $table->string('field_of_study')->nullable();
            $table->integer('start_year');
            $table->integer('end_year')->nullable();
            $table->bigInteger('volunteer_id')->index()->unsigned();    // foregin key
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
        Schema::drop('educations');
    }
}
