<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->float('lat', 3, 7)->nullable();
            $table->float('lon', 3, 7)->nullable();
            $table->string('type')->nullable();
            $table->text('points')->nullable();
            $table->text('experience')->nullable();
            $table->text('google_maps_link')->nullable();
            $table->string('official_website')->nullable();
            $table->smallInteger('score')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
