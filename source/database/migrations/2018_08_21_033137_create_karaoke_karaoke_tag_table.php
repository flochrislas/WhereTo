<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot table of the many-to-many relation between the places and their tags.
        // http://laraveldaily.com/pivot-tables-and-many-to-many-relationships/
        Schema::create('karaoke_karaoke_tag', function (Blueprint $table) {
            // Two foreign keys and nothing else. ON DELETE CASCADE: when a place or a tag is deleted,
            // its pivot rows go with it, so we never need to detach from code.
            $table->foreignId('karaoke_id')->constrained('karaokes')->cascadeOnDelete();
            $table->foreignId('karaoke_tag_id')->constrained('karaoke_tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karaoke_karaoke_tag');
    }
};
