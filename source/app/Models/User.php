<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Admin user. Started from Laravel's own User model; 'level' is a WhereTo column (never read).
 * The attributes below replace the $fillable (mass assignable) and $hidden (left out of JSON) properties.
 */
#[Fillable(['name', 'email', 'password', 'level'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     * 'hashed' means the password is hashed automatically when set, so the seeder and the tests can pass plain text.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The restaurants visited by the user (pivot table user_restaurant, with a comment column).
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class)->withPivot('comment');
    }
}
