<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bars', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->float('lat', 3, 7)->nullable();
            $table->float('lon', 3, 7)->nullable();
            $table->string('type')->nullable();
            $table->text('points')->nullable();
            $table->text('experience')->nullable();
            $table->boolean('visited')->default(false);
            $table->timestamp('visit_date')->nullable();
            $table->text('google_maps_link')->nullable();
            $table->string('tabelog_link')->nullable();
            $table->string('official_website')->nullable();
            $table->smallInteger('score')->nullable();
            $table->smallInteger('score_place')->nullable();
            $table->smallInteger('score_mingling')->nullable();
            $table->smallInteger('score_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bars');
    }
}
