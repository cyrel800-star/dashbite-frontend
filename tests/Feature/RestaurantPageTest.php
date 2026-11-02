<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_restaurant_page_supports_search_and_cuisine_filtering(): void
    {
        Restaurant::factory()->create([
            'name' => 'Orange Noodle House',
            'cuisine' => 'Filipino',
        ]);
        Restaurant::factory()->create([
            'name' => 'Orange Pizza House',
            'cuisine' => 'Italian',
        ]);
        Restaurant::factory()->create([
            'name' => 'Disabled Kitchen',
            'cuisine' => 'Filipino',
            'is_disabled' => true,
        ]);

        $this->get('/restaurants?search=Orange&cuisine=Filipino')
            ->assertOk()
            ->assertSee('Orange Noodle House')
            ->assertDontSee('Orange Pizza House')
            ->assertDontSee('Disabled Kitchen');
    }

    public function test_restaurant_detail_and_recommendations_use_available_catalog_items(): void
    {
        $restaurant = Restaurant::factory()->create();
        $available = MenuItem::factory()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Available Adobo',
        ]);
        MenuItem::factory()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Unavailable Pancit',
            'is_available' => false,
        ]);

        $this->get(route('customer.restaurants.show', $restaurant))
            ->assertOk()
            ->assertSee($available->name)
            ->assertDontSee('Unavailable Pancit');

        $this->getJson(route('customer.menu.random'))
            ->assertOk()
            ->assertJsonPath('items.0.name', $available->name)
            ->assertJsonPath('items.0.restaurant_name', $restaurant->name);
    }

    public function test_customer_can_toggle_restaurant_favorites(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::Customer,
            'account_status' => AccountStatus::Approved,
        ]);
        $restaurant = Restaurant::factory()->create();

        $this->actingAs($customer)
            ->postJson(route('favorites.toggle', $restaurant))
            ->assertOk()
            ->assertJson(['ok' => true, 'is_favorite' => true]);

        $this->assertDatabaseHas('restaurant_user_favorites', [
            'user_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
        ]);

        $this->postJson(route('favorites.toggle', $restaurant))
            ->assertOk()
            ->assertJson(['ok' => true, 'is_favorite' => false]);
    }

    public function test_unified_web_sign_in_authenticates_customers_for_browsing(): void
    {
        $customer = User::factory()->create([
            'role' => UserRole::Customer,
            'account_status' => AccountStatus::Approved,
            'password' => 'secret123',
        ]);

        $this->postJson(route('login.store'), [
            'email' => $customer->email,
            'password' => 'secret123',
        ])->assertOk()->assertJson(['role' => UserRole::Customer->value]);

        $this->assertAuthenticatedAs($customer);
    }

    public function test_unified_web_sign_in_returns_a_workspace_token_for_restaurant_accounts(): void
    {
        $restaurantUser = User::factory()->create([
            'role' => UserRole::Restaurant,
            'account_status' => AccountStatus::Approved,
            'password' => 'secret123',
        ]);

        $response = $this->postJson(route('login.store'), [
            'email' => $restaurantUser->email,
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('user.role', UserRole::Restaurant->value)
            ->assertJsonPath('user.email', $restaurantUser->email);

        $this->assertGuest();
        $this->withToken($response->json('token'))
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('email', $restaurantUser->email);
    }

    public function test_login_routes_redirect_to_the_shared_browsing_login_modal(): void
    {
        $this->get(route('login'))
            ->assertRedirect(route('customer.restaurants', ['login' => 1]));

        $this->get(route('customer.restaurants', ['login' => 1]))
            ->assertOk()
            ->assertSee('One sign in for every DashBite account.')
            ->assertSee('loginModal(true)');
    }
}
