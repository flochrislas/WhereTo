<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karaoke_tags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('label');
            $table->unsignedInteger('type')->default(0);   // tag family, drives colour and ordering
            $table->unsignedInteger('weight')->default(0); // importance inside the family
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karaoke_tags');
    }
};
