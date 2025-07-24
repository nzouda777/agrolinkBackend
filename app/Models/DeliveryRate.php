<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_option_id',
        'city_id',
        'base_price',
        'per_km_price'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'per_km_price' => 'decimal:2'
    ];

    public function deliveryOption()
    {
        return $this->belongsTo(DeliveryOption::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
