<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class RestaurantReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->status === OrderStatus::Completed, 403, 'Only completed orders can be reviewed.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($order->review()->exists()) {
            return back()->withErrors(['review' => 'This order has already been reviewed.']);
        }

        $order->review()->create([
            ...$data,
            'restaurant_id' => $order->restaurant_id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('customer.restaurants.show', $order->restaurant_id)
            ->with('review_success', 'Thanks for sharing your feedback.');
    }
}
