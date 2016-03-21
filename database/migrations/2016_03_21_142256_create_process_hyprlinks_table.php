<?php

use Illuminate\Database\Migrations\Migration;

class CreateProcessHyprlinksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('process_hyperlinks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_id')->unsigned();
            $table->integer('hyperlink_id')->unsigned();

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
