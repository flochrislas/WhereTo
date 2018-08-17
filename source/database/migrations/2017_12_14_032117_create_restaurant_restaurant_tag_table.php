<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
* Pivot table
* http://www.easylaravelbook.com/blog/2016/04/06/introducing-laravel-many-to-many-relations/
* http://laraveldaily.com/pivot-tables-and-many-to-many-relationships/
*/
class CreateRestaurantRestaurantTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_restaurant_tag', function (Blueprint $table) {
          // We use on cascade deelte so if any entry is deleted,
          // its relationships qwill as well, and we dont need to manually detach from code

          $table->integer('restaurant_id')->unsigned()->nullable();
          $table->foreign('restaurant_id')->references('id')
                ->on('restaurants')->onDelete('cascade');

          $table->integer('restaurant_tag_id')->unsigned()->nullable();
          $table->foreign('restaurant_tag_id')->references('id')
                ->on('restaurant_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_restaurant_tag');
    }
}
