<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'account_status', 'restaurant_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'account_status' => AccountStatus::class,
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function favoriteRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user_favorites')->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function restaurantReviews()
    {
        return $this->hasMany(RestaurantReview::class);
    }

    public function hasFavorited(Restaurant $restaurant): bool
    {
        return $this->favoriteRestaurants()
            ->where('restaurants.id', $restaurant->id)
            ->exists();
    }

    public function isApproved(): bool
    {
        return $this->account_status === AccountStatus::Approved;
    }
}
