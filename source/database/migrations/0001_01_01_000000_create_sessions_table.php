<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * From the Laravel skeleton, without the users and password_reset_tokens tables: the app has no accounts.
     * The sessions table is only used when SESSION_DRIVER=database.
     */
    public function up(): void
    {
        // A database created before November 2026 already has this table (from the users migration of the time)
        if (Schema::hasTable('sessions')) {
            return;
        }
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
