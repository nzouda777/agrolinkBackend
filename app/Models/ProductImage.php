<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
