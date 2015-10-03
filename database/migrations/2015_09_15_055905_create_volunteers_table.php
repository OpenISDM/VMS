<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersTable extends Migration
{
    /**
     * Run the migrations.
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
            $table->string('address')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->unique();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->string('introduction')->nullable();
            $table->boolean('is_actived')->default(false);
            $table->boolean('is_locked')->default(false);
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
