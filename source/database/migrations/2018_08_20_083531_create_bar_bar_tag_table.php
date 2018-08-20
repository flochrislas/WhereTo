<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarBarTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bar_bar_tag', function (Blueprint $table) {
          // We use on cascade deelte so if any entry is deleted,
          // its relationships will as well, and we dont need to manually detach from code

          $table->integer('bar_id')->unsigned()->nullable();
          $table->foreign('bar_id')->references('id')
                ->on('bars')->onDelete('cascade');

          $table->integer('bar_tag_id')->unsigned()->nullable();
          $table->foreign('bar_tag_id')->references('id')
                ->on('bar_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bar_bar_tag');
    }
}
