<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessHyprlinksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('process_hyperlinks', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('process_id')->unsigned();
            $table->bigInteger('hyperlink_id')->unsigned();

            $table->foreign('process_id')->references('id')
                ->on('processes')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('hyperlink_id')->references('id')
                ->on('hyperlinks')->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('process_hyperlinks');
    }
}
