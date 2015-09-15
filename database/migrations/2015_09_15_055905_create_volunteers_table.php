<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password', 60);
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('birth_year');
            $table->string('gender');
            $table->string('city');
            $table->string('address');
            $table->string('phone_number', 20);
            $table->string('email')->unique();
            $table->string('emergency_contact');
            $table->string('emergency_phone', 20);
            $table->string('introduction');
            $table->boolean('is_actived');
            $table->boolean('is_locked');
            $table->rememberToken();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('volunteers');
    }
}
