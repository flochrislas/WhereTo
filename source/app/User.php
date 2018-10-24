<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
* This class has been created via Laravel using auth
*/
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'level',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}

/**
* The restaaurants visited by the user
*/
public function restaurants()
{
    //return $this->belongsToMany(Restaurant::class);
    return $this->belongsToMany(Restaurant::class)->withPivot('comment');
    //return $this->belongsToMany('App\Restaurant','user_restaurant')->withPivot('comment');
}
