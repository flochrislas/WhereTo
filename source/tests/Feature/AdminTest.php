<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\RestaurantType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/register')->assertNotFound();
    }

    /** The 'admin' gate: a user whose level is below 1 is logged in but not an admin */
    public function test_users_without_level_are_forbidden(): void
    {
        $user = User::factory()->create(['level' => '0']);
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/restaurants')->assertForbidden();
        $this->actingAs(User::factory()->create(['level' => null]))->get('/admin')->assertForbidden();
    }

    /** The form request rejects bad values with the field errors, and the row is not created */
    public function test_restaurant_form_is_validated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/restaurants', ['name' => '', 'type' => 'Ramen', 'score_lunch' => 9, 'lunch_price' => 'cheap'])
            ->assertSessionHasErrors(['name', 'score_lunch', 'lunch_price']);
        $this->assertDatabaseCount('restaurants', 0);
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'secret']);
        $this->get('/login')->assertOk();
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'secret'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Manage restaurants');
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_create_edit_and_delete_a_restaurant(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/restaurants', ['name' => 'New place', 'type' => 'Ramen', 'lunch_price' => 900])
            ->assertRedirect(route('restaurants.index'));
        $id = Restaurant::where('name', 'New place')->value('id');
        $this->assertNotNull($id);

        $this->actingAs($user)->put("/admin/restaurants/$id", ['name' => 'Renamed', 'type' => 'Ramen'])->assertRedirect();
        $this->assertDatabaseHas('restaurants', ['id' => $id, 'name' => 'Renamed']);

        $this->actingAs($user)->get('/admin/restaurants')->assertOk()->assertSee('Renamed');
        $this->actingAs($user)->delete("/admin/restaurants/$id")->assertRedirect();
        $this->assertDatabaseMissing('restaurants', ['id' => $id]);
    }

    public function test_created_restaurant_with_coordinates_shows_up_in_public_results(): void
    {
        $user = User::factory()->create();
        // Warm the forever-cache first, so the test proves it is flushed on create.
        $this->get('/restaurants/results?orderBy=distance&position=null')->assertOk()->assertDontSee('Fresh place');

        $this->actingAs($user)->post('/admin/restaurants', ['name' => 'Fresh place', 'type' => 'Ramen', 'coord' => '35.6590, 139.7020'])
            ->assertRedirect(route('restaurants.index'));
        $restaurant = Restaurant::where('name', 'Fresh place')->firstOrFail();
        $this->assertEqualsWithDelta(35.6590, (float) $restaurant->lat, 0.00001);
        $this->assertEqualsWithDelta(139.7020, (float) $restaurant->lon, 0.00001);

        $this->get('/restaurants/results?orderBy=distance&position=null')->assertOk()->assertSee('Fresh place');

        $this->actingAs($user)->put("/admin/restaurants/{$restaurant->id}", ['name' => 'Fresh place', 'type' => 'Ramen', 'coord' => 'garbage'])->assertRedirect();
        $this->assertEqualsWithDelta(35.6590, (float) $restaurant->fresh()->lat, 0.00001, 'invalid coordinates are ignored');
    }

    public function test_admin_can_create_a_tag(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/restaurantTag', ['label' => 'newtag', 'type' => 5, 'weight' => 1])->assertRedirect();
        $this->assertDatabaseHas('restaurant_tags', ['label' => 'newtag']);
        $this->actingAs($user)->post('/admin/restaurantTag', ['label' => ''])->assertSessionHasErrors('label');
    }

    public function test_admin_can_edit_and_delete_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = RestaurantTag::create(['label' => 'oldtag', 'type' => 5, 'weight' => 1]);

        $this->actingAs($user)->get("/admin/restaurantTag/{$tag->id}/edit")->assertOk()->assertSee('name="_method" value="PUT"', false)->assertSee('oldtag');
        $this->actingAs($user)->put("/admin/restaurantTag/{$tag->id}", ['label' => 'newlabel'])->assertRedirect(route('restaurantTag.index'));
        $this->assertDatabaseHas('restaurant_tags', ['id' => $tag->id, 'label' => 'newlabel']);

        $this->actingAs($user)->get('/admin/restaurantTag')->assertOk()->assertSee('newlabel');
        $this->actingAs($user)->delete("/admin/restaurantTag/{$tag->id}")->assertRedirect(route('restaurantTag.index'));
        $this->assertDatabaseMissing('restaurant_tags', ['id' => $tag->id]);
    }

    public function test_admin_can_manage_restaurant_types(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/restaurantType/insert', ['label' => 'Izakaya'])->assertRedirect(route('restaurantType.index'));
        $type = RestaurantType::where('label', 'Izakaya')->firstOrFail();

        $this->actingAs($user)->get("/admin/restaurantType/edit/{$type->id}")->assertOk()->assertSee('Izakaya');
        $this->actingAs($user)->put("/admin/restaurantType/update/{$type->id}", ['label' => 'Pub'])->assertRedirect();
        $this->assertDatabaseHas('restaurant_types', ['id' => $type->id, 'label' => 'Pub']);

        // Deleting on GET is no longer possible.
        $this->actingAs($user)->get("/admin/restaurantType/delete/{$type->id}")->assertStatus(405);
        $this->actingAs($user)->delete("/admin/restaurantType/delete/{$type->id}")->assertRedirect();
        $this->assertDatabaseMissing('restaurant_types', ['id' => $type->id]);
    }

    public function test_login_is_throttled(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertStatus(429);
    }
}
