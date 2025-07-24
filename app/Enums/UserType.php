<?php

namespace App\Enums;

enum UserType: string
{
    case INDIVIDUAL = 'individual';
    case BUSINESS = 'business';
    case ORGANIZATION = 'organization';
    case NONPROFIT = 'nonprofit';

    public function label(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'Individual',
            self::BUSINESS => 'Business',
            self::ORGANIZATION => 'Organization',
            self::NONPROFIT => 'Non-Profit Organization',
        };
    }

    public static function options(): array
    {
        return [
            self::INDIVIDUAL->value => self::INDIVIDUAL->label(),
            self::BUSINESS->value => self::BUSINESS->label(),
            self::ORGANIZATION->value => self::ORGANIZATION->label(),
            self::NONPROFIT->value => self::NONPROFIT->label(),
        ];
    }
}
