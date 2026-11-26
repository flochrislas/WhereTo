<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The four place tables (doc/11-data-freshness.md, section 8) */
    private const TABLES = ['restaurants', 'stores', 'bars', 'karaokes'];

    /**
     * Run the migrations.
     * name_ja: the Japanese name of the place, shown first on the /ja pages. Null until the place has one, and its
     * Japanese page stays noindex until then (Location::hasContentIn()). Written into the seeders by tools/freshness.
     */
    public function up(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                // after('name'): right after the name in MySQL; PostgreSQL and SQLite ignore it and add the column last
                $table->string('name_ja')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn('name_ja');
            });
        }
    }
};
