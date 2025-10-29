<?php

namespace App\Enums;

enum TipoOperacao:string
{
    case Venda         = 'venda';
    case Devolucao     = 'devolucao';
    case Bonificacao   = 'bonificacao';
    case Transferencia = 'transferencia';
    case Remessa       = 'remessa';
    case Complementar  = 'complementar';
    case Ajuste        = 'ajuste';
    case Consignacao   = 'consignacao';

    public function label(): string
    {
        return match ($this) {
            self::Venda         => 'Venda',
            self::Devolucao     => 'Devolução',
            self::Bonificacao   => 'Bonificação',
            self::Transferencia => 'Transferência',
            self::Remessa       => 'Remessa',
            self::Complementar  => 'Complementar',
            self::Ajuste        => 'Ajuste',
            self::Consignacao   => 'Consignação',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
