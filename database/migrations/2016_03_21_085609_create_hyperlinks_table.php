<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHyperlinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hyperlinks', function (BluePrint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('link', 2083);
            $table->bigInteger('project_id')->index()->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            // foreign constraint
            $table->foreign('project_id')->references('id')
                  ->on('projects')->onDelete('cascade')
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
        Schema::drop('hyperlinks');
    }
}
