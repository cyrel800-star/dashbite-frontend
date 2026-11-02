<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'delivery_address' => ['required', 'string', 'max:1000'],
            'delivery_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'delivery_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'payment_method' => ['required', 'in:cod,gcash,maya'],
            'payment_reference' => ['required_if:payment_method,gcash,maya', 'nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'between:1,50'],
        ]);

        if (! $restaurant->isOrderable()) {
            return back()->withErrors(['order' => 'This restaurant is not accepting orders right now.']);
        }

        $items = collect($data['items'])->keyBy('menu_item_id');
        $menuItems = MenuItem::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
            ->whereIn('id', $items->keys())
            ->get()
            ->keyBy('id');

        if ($menuItems->count() !== $items->count()) {
            return back()->withErrors(['items' => 'One or more selected items are no longer available. Please review your cart.']);
        }

        $subtotal = $menuItems->sum(
            fn (MenuItem $item) => (float) $item->price * (int) $items->get($item->id)['quantity']
        );
        $deliveryFee = (float) config('orders.delivery_fee');

        $order = DB::transaction(function () use ($request, $restaurant, $data, $menuItems, $items, $subtotal, $deliveryFee) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'restaurant_id' => $restaurant->id,
                'status' => OrderStatus::Pending,
                'delivery_address' => $data['delivery_address'],
                'delivery_lat' => $data['delivery_lat'] ?? null,
                'delivery_lng' => $data['delivery_lng'] ?? null,
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
            ]);

            foreach ($menuItems as $menuItem) {
                $quantity = (int) $items->get($menuItem->id)['quantity'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $quantity,
                    'subtotal' => (float) $menuItem->price * $quantity,
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('customer.restaurants.show', $restaurant)
            ->with('order_success', $order->id);
    }
}
