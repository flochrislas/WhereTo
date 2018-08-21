<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaraokeKaraokeTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karaoke_karaoke_tag', function (Blueprint $table) {
          // We use on cascade deelte so if any entry is deleted,
          // its relationships will as well, and we dont need to manually detach from code

          $table->integer('karaoke_id')->unsigned()->nullable();
          $table->foreign('karaoke_id')->references('id')
                ->on('karaokes')->onDelete('cascade');

          $table->integer('karaoke_tag_id')->unsigned()->nullable();
          $table->foreign('karaoke_tag_id')->references('id')
                ->on('karaoke_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karaoke_karaoke_tag');
    }
}
