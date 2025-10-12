<?php

namespace App\Enums;

enum Scope: int
{

    case Item = 1;
    case Shipping = 2;
    case Order = 3;
    public function label(): string
    {
        return match ($this) { self::Item => 'Item', self::Shipping => 'Frete', self::Order => 'Pedido'};
    }


    public static function values(): array
    {
        // [1,2,3]
        return array_column(self::cases(), 'value');
    }
}
