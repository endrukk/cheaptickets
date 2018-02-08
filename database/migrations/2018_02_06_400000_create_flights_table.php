<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_departure');
            $table->integer('id_departure_country');
            $table->integer('id_destination');
            $table->integer('id_destination_country');
            $table->dateTime('date');
            $table->integer('length');
            $table->integer('price');
            $table->integer('id_company');
            $table->timestamps();

//            $table->foreign('id_departure')->references('id')->on('cities');
//            $table->foreign('id_departure_country')->references('id')->on('countries');
//
//            $table->foreign('id_destination')->references('id')->on('cities');
//            $table->foreign('id_destination_country')->references('id')->on('countries');
//
//            $table->foreign('id_company')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
