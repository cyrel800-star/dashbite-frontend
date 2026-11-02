<?php

namespace App\Http\Controllers\Customer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Restaurant $restaurant)
    {
        abort_if($restaurant->is_disabled, 404);
        abort_unless($request->user()->role === UserRole::Customer, 403);

        $isFavorite = $request->user()->favoriteRestaurants()->toggle($restaurant->id);

        return response()->json([
            'ok' => true,
            'is_favorite' => $isFavorite['attached'] !== [],
        ]);
    }
}
