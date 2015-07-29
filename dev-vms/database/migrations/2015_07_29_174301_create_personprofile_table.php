<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonprofileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personprofile', function (Blueprint $table) {
            $table->string('id')->primary('id');
			$table->string('username');
			$table->string('sex');
			$table->string('birthdate');
			$table->string('email');
			$table->string('cellphone');
			$table->string('city');
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
        Schema::drop('personprofile');
    }
}
