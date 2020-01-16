<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_restaurant', function (Blueprint $table) {
          // We use on cascade delete so if any entry is deleted,
          // its relationships will as well, and we dont need to manually detach from code

          $table->integer('user_id')->unsigned()->nullable();
          $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');

          $table->integer('restaurant_id')->unsigned()->nullable();
          $table->foreign('restaurant_id')->references('id')
                ->on('restaurants')->onDelete('cascade');

            // $table->increments('id');
            $table->timestamps();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_restaurant');
    }
}
