<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveysTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('fk_pm_group_id')->unsigned()->default(0);
			$table->foreign('fk_pm_group_id')->references('id')->on('pm_groups')->onDelete('cascade');
			$table->string('name')->default('');
			$table->text('description')->default('');
            $table->timestamps();
        });
		
		Schema::create('survey_questions', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('fk_survey_id')->unsigned()->default(0);
			$table->foreign('fk_survey_id')->references('id')->on('surveys')->onDelete('cascade');
			$table->string('question')->default('');
			$table->string('answer_type')->default('');
			$table->timestamps();
		});
		
		Schema::create('survey_question_options', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('fk_survey_question_id')->unsigned()->default(0);
			$table->foreign('fk_survey_question_id')->references('id')->on('survey_questions')->onDelete('cascade');
			$table->string('option')->default('');
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
		Schema::drop('survey_question_options');
		Schema::drop('survey_questions');
        Schema::drop('surveys');
    }
}
