<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'type_id',
        'city_id',
        'bio',
        'profile_image',
        'status',
        'last_active',
        'email_verified',
        'phone_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
    ];

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }

    /**
     * Get the type associated with the user.
     */
    public function type()
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Get the city associated with the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the settings for the user.
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the products sold by the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Get the orders placed by the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the conversations the user is participating in.
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    /**
     * Get the messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the favorites of the user.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the payments made by the user.
     */
    public function payments()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Get the API keys associated with the user.
     */
    public function apiKeys()
    {
        return $this->hasMany(PaymentApiKey::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the sessions for the user.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the subscription for the user.
     */
    public function subscription()
    {
        return $this->hasOne(UserSubscription::class);
    }

    /**
     * Get the cart for the user.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
