<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantOperationsController extends Controller
{
    private function ownRestaurant(Request $request): Restaurant
    {
        $restaurantId = $request->user()->restaurant_id;

        abort_if($restaurantId === null, 403, 'No restaurant linked to this account.');

        return Restaurant::findOrFail($restaurantId);
    }

    public function profile(Request $request)
    {
        return response()->json(['data' => $this->ownRestaurant($request)]);
    }

    public function updateProfile(Request $request)
    {
        $restaurant = $this->ownRestaurant($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'cuisine' => ['nullable', 'string', 'max:100'],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'profile_image_url' => ['nullable', 'url', 'max:2048'],
            'display_badge' => ['nullable', 'string', 'max:60'],
            'prep_time_minutes' => ['required', 'integer', 'between:1,240'],
            'opening_hours' => ['nullable', 'array'],
            'opening_hours.*' => ['nullable', 'string', 'max:60'],
        ]);

        $restaurant->update($data);

        return response()->json(['data' => $restaurant->fresh()]);
    }

    public function orders(Request $request)
    {
        $orders = Order::query()
            ->where('restaurant_id', $this->ownRestaurant($request)->id)
            ->with([
                'customer:id,name,email,phone',
                'items:id,order_id,item_name,unit_price,quantity,subtotal',
            ])
            ->latest()
            ->limit(100)
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function updateOrderStatus(Request $request, int $id)
    {
        $restaurant = $this->ownRestaurant($request);
        $data = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $order = Order::query()
            ->where('restaurant_id', $restaurant->id)
            ->findOrFail($id);
        $nextStatus = OrderStatus::from($data['status']);

        if (! $order->status->canTransitionTo($nextStatus)) {
            return response()->json([
                'message' => "An order cannot move from {$order->status->value} to {$nextStatus->value}.",
            ], 422);
        }

        $order->update(['status' => $nextStatus]);

        return response()->json([
            'data' => $order->fresh()->load([
                'customer:id,name,email,phone',
                'items:id,order_id,item_name,unit_price,quantity,subtotal',
            ]),
        ]);
    }

    public function reviews(Request $request)
    {
        $reviews = $this->ownRestaurant($request)
            ->reviews()
            ->with('customer:id,name')
            ->latest()
            ->limit(100)
            ->get();

        return response()->json(['data' => $reviews]);
    }
}
