<?php

namespace App\Http\Controllers;

use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
* Could be called UserController
*/
class VipController extends Controller
{

    /**
    * Add a comment from logged in user to specified restaurant
    * TODO: think about this and how to handle vists and comments from users
    */
    public function addRestaurantComment($restaurantId, $comment) {
        // Get the authenticated user
        $user = Auth::user();
        $restaurant = Restaurant::find($restaurantId);
        // TODO: Cannot create mor ethan one comment per user per restaurant
        $restaurant->users()->attach($user, ['comment'=> $comment]);
    }

    /**
    * Returns all restaurants from logged in user
    */
    public function showVipRestaurants() {
        // Get the authenticated user
        $user = Auth::user();
        $user->restaurants();
    }

}
