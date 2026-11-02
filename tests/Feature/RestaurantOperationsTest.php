<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestaurantOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_checkout_calculates_prices_and_only_accepts_available_items_from_that_restaurant(): void
    {
        config(['orders.delivery_fee' => 35]);
        $restaurant = Restaurant::factory()->create();
        $item = MenuItem::factory()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Chicken Adobo',
            'price' => 120,
            'is_available' => true,
        ]);
        $customer = User::factory()->create([
            'role' => UserRole::Customer,
            'account_status' => AccountStatus::Approved,
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.store', $restaurant), [
                'delivery_address' => '1 Main Street',
                'payment_method' => 'cod',
                'items' => [['menu_item_id' => $item->id, 'quantity' => 2]],
            ])
            ->assertRedirect(route('customer.restaurants.show', $restaurant));

        $order = Order::query()->with('items')->sole();
        $this->assertSame(240.0, (float) $order->subtotal);
        $this->assertSame(35.0, (float) $order->delivery_fee);
        $this->assertSame(275.0, (float) $order->total);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(1, $order->items->count());
        $this->assertSame(240.0, (float) $order->items->first()->subtotal);

        $unavailable = MenuItem::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_available' => false,
        ]);

        $this->from(route('customer.restaurants.show', $restaurant))
            ->post(route('customer.orders.store', $restaurant), [
                'delivery_address' => '1 Main Street',
                'payment_method' => 'cod',
                'items' => [['menu_item_id' => $unavailable->id, 'quantity' => 1]],
            ])
            ->assertSessionHasErrors('items');
        $this->assertSame(1, Order::count());
    }

    public function test_restaurant_staff_can_advance_only_their_own_order_through_valid_statuses(): void
    {
        $restaurant = Restaurant::factory()->create();
        $otherRestaurant = Restaurant::factory()->create();
        $staff = User::factory()->create([
            'role' => UserRole::Restaurant,
            'account_status' => AccountStatus::Approved,
            'restaurant_id' => $restaurant->id,
        ]);
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'restaurant_id' => $restaurant->id,
            'status' => OrderStatus::Pending,
            'delivery_address' => '1 Main Street',
            'payment_method' => 'cod',
            'subtotal' => 120,
            'delivery_fee' => 0,
            'total' => 120,
        ]);
        $otherOrder = Order::create([
            'user_id' => User::factory()->create()->id,
            'restaurant_id' => $otherRestaurant->id,
            'status' => OrderStatus::Pending,
            'delivery_address' => '2 Main Street',
            'payment_method' => 'cod',
            'subtotal' => 120,
            'delivery_fee' => 0,
            'total' => 120,
        ]);

        Sanctum::actingAs($staff);

        $this->getJson('/api/restaurant/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id);

        $this->patchJson("/api/restaurant/orders/{$order->id}/status", ['status' => 'accepted'])
            ->assertOk()
            ->assertJsonPath('data.status', 'accepted');
        $this->patchJson("/api/restaurant/orders/{$order->id}/status", ['status' => 'completed'])
            ->assertUnprocessable();
        $this->patchJson("/api/restaurant/orders/{$otherOrder->id}/status", ['status' => 'accepted'])
            ->assertNotFound();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('orders', ['id' => $otherOrder->id, 'status' => 'pending']);
    }

    public function test_restaurant_staff_can_update_their_profile_and_cannot_access_another_restaurants_data(): void
    {
        $restaurant = Restaurant::factory()->create();
        $otherRestaurant = Restaurant::factory()->create();
        $staff = User::factory()->create([
            'role' => UserRole::Restaurant,
            'account_status' => AccountStatus::Approved,
            'restaurant_id' => $restaurant->id,
        ]);
        Sanctum::actingAs($staff);

        $this->getJson('/api/restaurant/profile')
            ->assertOk()
            ->assertJsonPath('data.id', $restaurant->id);
        $this->putJson('/api/restaurant/profile', [
            'name' => 'Updated Kitchen',
            'address' => '3 Main Street',
            'cuisine' => 'Filipino',
            'prep_time_minutes' => 35,
            'opening_hours' => ['monday' => '9:00 AM - 8:00 PM'],
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Kitchen')
            ->assertJsonPath('data.prep_time_minutes', 35);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Updated Kitchen',
            'address' => '3 Main Street',
        ]);

        $otherReview = RestaurantReview::create([
            'order_id' => Order::create([
                'user_id' => User::factory()->create()->id,
                'restaurant_id' => $otherRestaurant->id,
                'status' => OrderStatus::Completed,
                'delivery_address' => '9 Main Street',
                'payment_method' => 'cod',
                'subtotal' => 10,
                'delivery_fee' => 0,
                'total' => 10,
            ])->id,
            'restaurant_id' => $otherRestaurant->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
        ]);
        $this->getJson('/api/restaurant/reviews')
            ->assertOk()
            ->assertJsonCount(0, 'data');
        $this->assertNotNull($otherReview->id);
    }

    public function test_only_customers_can_submit_reviews_for_their_completed_orders(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = User::factory()->create([
            'role' => UserRole::Customer,
            'account_status' => AccountStatus::Approved,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'status' => OrderStatus::Pending,
            'delivery_address' => '1 Main Street',
            'payment_method' => 'cod',
            'subtotal' => 10,
            'delivery_fee' => 0,
            'total' => 10,
        ]);

        $this->actingAs($customer)
            ->from(route('customer.restaurants.show', $restaurant))
            ->post(route('customer.reviews.store', $order), ['rating' => 5])
            ->assertForbidden();
        $this->assertDatabaseMissing('restaurant_reviews', ['order_id' => $order->id]);

        $order->update(['status' => OrderStatus::Completed]);
        $this->post(route('customer.reviews.store', $order), [
            'rating' => 5,
            'comment' => 'Great food.',
        ])->assertRedirect(route('customer.restaurants.show', $restaurant));

        $this->assertDatabaseHas('restaurant_reviews', [
            'order_id' => $order->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Great food.',
        ]);

        $staff = User::factory()->create([
            'role' => UserRole::Restaurant,
            'account_status' => AccountStatus::Approved,
            'restaurant_id' => $restaurant->id,
        ]);
        $this->actingAs($staff)
            ->post(route('customer.reviews.store', $order), ['rating' => 5])
            ->assertForbidden();
    }
}
