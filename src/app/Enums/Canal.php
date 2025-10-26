<?php

namespace App\Enums;

enum Canal: string
{
    case Balcao = 'balcao';
    case Delivery = 'delivery';
    case Ecommerce = 'ecommerce';
    case Marketplace = 'marketplace';
    case Telemarketing = 'telemarketing';

    public function label(): string
    {
        return match ($this) {
            self::Balcao => 'Balcão',
            self::Delivery => 'Delivery',
            self::Ecommerce => 'E-commerce',
            self::Marketplace => 'Marketplace',
            self::Telemarketing => 'Telemarketing',
        };
    }

    /** Útil para montar selects */
    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
