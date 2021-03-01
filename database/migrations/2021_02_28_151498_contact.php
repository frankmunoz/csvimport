<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Contact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('csv_data_id')->unsigned();
            $table->integer('status');
            $table->string('name');
            $table->string('birthdate');
            $table->string('phone');
            $table->string('address');
            $table->string('credit_card');
            $table->string('franchise');
            $table->string('email');
            $table->string('error');
            $table->timestamps();
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('csv_data_id')->references('id')->on('csv_data');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
