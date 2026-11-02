<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'cuisine',
        'cover_image_url',
        'profile_image_url',
        'prep_time_minutes',
        'display_badge',
        'opening_hours',
        'service_boundary',
        'is_open',
        'is_disabled',
    ];

    protected $casts = [
        'service_boundary' => 'array',
        'is_open' => 'boolean',
        'is_disabled' => 'boolean',
        'opening_hours' => 'array',
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'restaurant_user_favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(RestaurantReview::class);
    }

    // Scope: orderable = open AND not disabled
    public function scopeOrderable($query)
    {
        return $query->where('is_open', true)->where('is_disabled', false);
    }

    public function isOrderable(): bool
    {
        return $this->is_open && ! $this->is_disabled;
    }

    public function isOpenNow(): bool
    {
        return $this->isOrderable();
    }
}
