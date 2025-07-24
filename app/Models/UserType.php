<?php

namespace App\Models;

use App\Enums\UserType as UserTypeEnum;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'name' => UserTypeEnum::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function options()
    {
        return UserTypeEnum::options();
    }

    public function getLabelAttribute()
    {
        return UserTypeEnum::from($this->name)->label();
    }
}
