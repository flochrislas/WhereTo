<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * The admin and the login were removed in November 2026: a database created before that still has the
     * users, password_reset_tokens and user_restaurant tables. Drop them. On a fresh database this does nothing.
     */
    public function up(): void
    {
        Schema::dropIfExists('user_restaurant'); // first: it references users
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }

    /**
     * Reverse the migrations: nothing to restore, the tables are gone for good (the seeders hold the data).
     */
    public function down(): void
    {
        //
    }
};
