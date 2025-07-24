<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'api_secret',
        'is_live'
    ];

    protected $casts = [
        'is_live' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
