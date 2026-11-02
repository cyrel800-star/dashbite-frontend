<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $search = is_string($search) ? trim($search) : '';
        $cuisine = $request->query('cuisine');
        $cuisine = is_string($cuisine) ? $cuisine : null;

        $restaurants = Restaurant::query()
            ->where('is_disabled', false)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($cuisine, fn (Builder $query) => $query->where('cuisine', $cuisine))
            ->withCount([
                'menuItems as menu_items_count' => fn (Builder $query) => $query->where('is_available', true),
            ])
            ->orderBy('name')
            ->get();

        $cuisines = Restaurant::query()
            ->where('is_disabled', false)
            ->whereNotNull('cuisine')
            ->where('cuisine', '!=', '')
            ->distinct()
            ->orderBy('cuisine')
            ->pluck('cuisine');

        return view('customer.restaurants', compact('restaurants', 'cuisines'));
    }

    public function show(Restaurant $restaurant, Request $request)
    {
        abort_if($restaurant->is_disabled, 404);

        $restaurant = Restaurant::query()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($restaurant->id);

        $restaurant->load([
            'menuItems' => fn ($query) => $query
                ->where('is_available', true)
                ->orderBy('name'),
            'reviews' => fn ($query) => $query
                ->with('customer:id,name')
                ->latest()
                ->limit(20),
        ]);

        $reviewableOrders = collect();
        if ($request->user()?->role?->value === 'customer') {
            $reviewableOrders = $request->user()->orders()
                ->where('restaurant_id', $restaurant->id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->latest()
                ->get();
        }

        return view('customer.restaurant-detail', compact('restaurant', 'reviewableOrders'));
    }

    public function randomMenu()
    {
        $items = MenuItem::query()
            ->where('is_available', true)
            ->whereHas('restaurant', fn (Builder $query) => $query->orderable())
            ->with('restaurant:id,name')
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->map(fn (MenuItem $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'image_url' => null,
                'restaurant_name' => $item->restaurant->name,
                'restaurant_url' => route('customer.restaurants.show', $item->restaurant),
            ]);

        return response()->json(['items' => $items]);
    }
}
