<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userID');
            $table->integer('stagingID');
            $table->string('fName');
            $table->string('lName');
            $table->string('addr1');
            $table->string('addr2');
            $table->string('city');
            $table->string('state');
            $table->integer('zip');
            $table->string('listName');
            $table->decimal('cashDonation')->nullable();
            $table->boolean('previousAttendee')->nullable();
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
        Schema::dropIfExists('working');
    }
}
