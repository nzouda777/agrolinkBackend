<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'public_profile',
        'show_phone',
        'show_email',
        'show_exact_location',
        'notification_preferences'
    ];

    protected $casts = [
        'notification_preferences' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
