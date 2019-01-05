<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreStoreTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_store_tag', function (Blueprint $table) {
          // We use on cascade deelte so if any entry is deleted,
          // its relationships will as well, and we dont need to manually detach from code

          $table->integer('store_id')->unsigned()->nullable();
          $table->foreign('store_id')->references('id')
                ->on('stores')->onDelete('cascade');

          $table->integer('store_tag_id')->unsigned()->nullable();
          $table->foreign('store_tag_id')->references('id')
                ->on('store_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_store_tag');
    }
}
