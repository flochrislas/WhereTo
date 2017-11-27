<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('coord')->nullable();
            $table->string('type')->nullable();
            $table->integer('lunch_price')->nullable();
            $table->text('points')->nullable();
            $table->text('experience')->nullable();
            $table->boolean('visited')->default(false);
            $table->timestamp('visit_date')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
