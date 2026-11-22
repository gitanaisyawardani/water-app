<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'driver_id',
        'truck_id',
        'tank_capacity',
        'delivery_date',
        'delivery_time',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'distance_km',
        'water_price',
        'delivery_fee',
        'total_price',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'distance_km' => 'decimal:2',
            'water_price' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
            'delivery_latitude' => 'decimal:7',
            'delivery_longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
