<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'status',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'payment_method',
        'payment_reference',
        'subtotal',
        'delivery_fee',
        'total',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'delivery_lat' => 'decimal:7',
        'delivery_lng' => 'decimal:7',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function review()
    {
        return $this->hasOne(RestaurantReview::class);
    }
}
