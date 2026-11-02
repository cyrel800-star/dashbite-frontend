<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * GET /api/catalog/restaurants
     * List orderable restaurants (open + not disabled).
     */
    public function restaurants(Request $request)
    {
        $restaurants = Restaurant::orderable()
            ->select(['id', 'name', 'address', 'phone', 'cuisine', 'cover_image_url', 'profile_image_url', 'display_badge', 'prep_time_minutes', 'is_open'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $restaurants,
            'meta' => ['count' => $restaurants->count()],
        ]);
    }

    /**
     * GET /api/catalog/restaurants/{id}
     * Restaurant detail. Only orderable restaurants are accessible.
     */
    public function showRestaurant(int $id)
    {
        $restaurant = Restaurant::orderable()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->find($id);

        if (! $restaurant) {
            return response()->json([
                'error'   => 'restaurant_not_orderable',
                'message' => 'Restaurant is not available for ordering.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id'       => $restaurant->id,
                'name'     => $restaurant->name,
                'address'  => $restaurant->address,
                'phone'    => $restaurant->phone,
                'cuisine' => $restaurant->cuisine,
                'cover_image_url' => $restaurant->cover_image_url,
                'profile_image_url' => $restaurant->profile_image_url,
                'display_badge' => $restaurant->display_badge,
                'prep_time_minutes' => $restaurant->prep_time_minutes,
                'rating_avg' => $restaurant->reviews_avg_rating,
                'rating_count' => $restaurant->reviews_count,
                'is_open'  => $restaurant->is_open,
            ],
        ]);
    }

    /**
     * GET /api/catalog/restaurants/{id}/menu
     * Menu items for an orderable restaurant.
     * Only currently available items are returned (AC #1 prep).
     */
    public function menu(int $id)
    {
        $restaurant = Restaurant::orderable()->find($id);

        if (! $restaurant) {
            return response()->json([
                'error'   => 'restaurant_not_orderable',
                'message' => 'Restaurant is not available for ordering.',
            ], 404);
        }

        $items = $restaurant->menuItems()
            ->available()
            ->select(['id', 'name', 'description', 'price', 'is_available'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $items,
            'meta' => [
                'restaurant_id'   => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'count'           => $items->count(),
            ],
        ]);
    }
}