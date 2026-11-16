<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            // Coordinates with 7 decimals (about 1 cm); nullable because some places have none
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('bars');
    }
};
