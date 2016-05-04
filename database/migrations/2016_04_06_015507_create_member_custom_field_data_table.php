<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberCustomFieldDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_custom_field_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_custom_field_id')->unsigned();
            $table->bigInteger('member_id')->unsigned();
            $table->binary('data');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['project_custom_field_id', 'member_id'], 'project_member_unique');

            $table->foreign('project_custom_field_id')->references('id')
                ->on('project_custom_field')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('member_id')->references('id')
                ->on('project_volunteers')->onDelete('cascade')
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
        Schema::drop('member_custom_field_data');
    }
}
