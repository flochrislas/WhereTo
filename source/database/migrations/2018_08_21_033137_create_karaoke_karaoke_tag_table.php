<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karaoke_karaoke_tag', function (Blueprint $table) {
            $table->foreignId('karaoke_id')->constrained('karaokes')->cascadeOnDelete();
            $table->foreignId('karaoke_tag_id')->constrained('karaoke_tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karaoke_karaoke_tag');
    }
};
