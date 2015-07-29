<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmGroupsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pm_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->text('description')->default('');
            $table->timestamps();
        });

        Schema::create('pm_group_membership', function (Blueprint $table) {
            $table->integer('fk_pm_group_id')->unsigned()->default(0);
            $table->foreign('fk_pm_group_id')->references('id')->on('pm_groups')->onDelete('cascade');
            $table->integer('fk_user_id')->unsigned()->default(0);
            $table->foreign('fk_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('pm_project_ownership', function (Blueprint $table) {
            $table->integer('fk_pm_group_id')->unsigned()->default(0);
            $table->foreign('fk_pm_group_id')->references('id')->on('pm_groups')->onDelete('cascade');
            $table->integer('fk_project_id')->unsigned()->default(0);
            $table->foreign('fk_project_id')->references('id')->on('projects')->onDelete('cascade');;
            $table->integer('fk_current_pm_id')->unsigned()->default(0);
            $table->foreign('fk_current_pm_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('pm_project_ownership');
        Schema::drop('pm_group_membership');
        Schema::drop('pm_groups');
    }
}
