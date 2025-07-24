<?php

namespace App\Models;

use App\Enums\UserRole as UserRoleEnum;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'name' => UserRoleEnum::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function options()
    {
        return UserRoleEnum::options();
    }

    public function getLabelAttribute()
    {
        return UserRoleEnum::from($this->name)->label();
    }

    public function getPermissionsAttribute()
    {
        return UserRoleEnum::from($this->name)->permissions();
    }
}
