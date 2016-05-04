<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectCustomFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_custom_field', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description');
            $table->boolean('required');
            $table->tinyInteger('type');
            $table->boolean('is_published')->default(true);
            $table->binary('metadata')->nullable();
            $table->integer('order');
            $table->bigInteger('project_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['order', 'project_id']);

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
        Schema::drop('project_custom_field');
    }
}
