<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The four place tables (doc/11-data-freshness.md, section 4) */
    private const TABLES = ['restaurants', 'stores', 'bars', 'karaokes'];

    /**
     * Run the migrations.
     * business_status: is the place still in business (values of App\Enums\BusinessStatus). The default fills the
     * existing rows and every seeder block without the key, so only the closed places need to carry it.
     * google_place_id: Google's id of the place, written into the seeders by tools/freshness.
     */
    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('business_status')->default('OPERATIONAL');
                $table->string('google_place_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['business_status', 'google_place_id']);
            });
        }
    }
};
