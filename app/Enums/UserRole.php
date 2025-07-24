<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SELLER = 'seller';
    case CUSTOMER = 'customer';
    case MODERATOR = 'moderator';
    case SUPPORT = 'support';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::SELLER => 'Seller',
            self::CUSTOMER => 'Customer',
            self::MODERATOR => 'Moderator',
            self::SUPPORT => 'Support',
        };
    }

    public static function options(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->label(),
            self::SELLER->value => self::SELLER->label(),
            self::CUSTOMER->value => self::CUSTOMER->label(),
            self::MODERATOR->value => self::MODERATOR->label(),
            self::SUPPORT->value => self::SUPPORT->label(),
        ];
    }

    public static function permissions(): array
    {
        return [
            self::ADMIN => [
                'users.manage',
                'products.manage',
                'orders.manage',
                'settings.manage',
            ],
            self::SELLER => [
                'products.create',
                'products.update',
                'orders.view',
            ],
            self::CUSTOMER => [
                'products.view',
                'orders.create',
                'wishlist.manage',
            ],
            self::MODERATOR => [
                'products.review',
                'reports.manage',
                'flags.manage',
            ],
            self::SUPPORT => [
                'tickets.manage',
                'users.view',
                'orders.view',
            ],
        ];
    }
}
