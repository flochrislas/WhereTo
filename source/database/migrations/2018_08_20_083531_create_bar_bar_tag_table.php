<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bar_bar_tag', function (Blueprint $table) {
            $table->foreignId('bar_id')->constrained('bars')->cascadeOnDelete();
            $table->foreignId('bar_tag_id')->constrained('bar_tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_bar_tag');
    }
};
