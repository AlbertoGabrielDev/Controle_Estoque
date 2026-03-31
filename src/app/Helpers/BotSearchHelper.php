<?php

namespace App\Helpers;

class BotSearchHelper
{
    /**
     * Resolve aliases de categoria para aproximar linguagem natural do catalogo.
     *
     * @return array<int, string>
     */
    public static function resolveCategoryHints(string $term): array
    {
        $normalized = mb_strtolower(trim($term));
        if ($normalized === '') {
            return [];
        }

        if (self::isFruitCategoryQuery($normalized)) {
            // Evita falso positivo com "hortifruti" em catalogos inconsistentes.
            return ['fruta', 'frutas'];
        }

        $hints = [$normalized];
        $aliases = [
            'hortifruti' => ['hortifruti', 'fruta', 'frutas', 'verdura', 'legume'],
            'verdura' => ['verdura', 'verduras', 'hortifruti'],
            'legume' => ['legume', 'legumes', 'hortifruti'],
        ];

        foreach ($aliases as $needle => $mapped) {
            if (! str_contains($normalized, $needle)) {
                continue;
            }

            $hints = array_merge($hints, $mapped);
        }

        return array_values(array_unique(array_filter($hints, static fn (string $value): bool => $value !== '')));
    }

    /**
     * Resolve pistas semânticas por nome de produto para buscas de categoria ampla.
     *
     * @return array<int, string>
     */
    public static function resolveSemanticProductHints(string $term): array
    {
        $normalized = mb_strtolower(trim($term));
        if (! self::isFruitCategoryQuery($normalized)) {
            return [];
        }

        return [
            'fruta',
            'banana',
            'maca',
            'maçã',
            'laranja',
            'uva',
            'morango',
            'abacaxi',
            'manga',
            'pera',
            'melancia',
            'melão',
            'melao',
            'mamão',
            'mamao',
            'kiwi',
            'goiaba',
            'limão',
            'limao',
            'tangerina',
            'mexerica',
            'acerola',
            'caju',
            'ameixa',
            'pêssego',
            'pessego',
            'maracujá',
            'maracuja',
            'abacate',
        ];
    }

    /**
     * Indica quando a consulta do usuário pede explicitamente por frutas.
     */
    public static function isFruitCategoryQuery(string $normalizedTerm): bool
    {
        return str_contains(mb_strtolower(trim($normalizedTerm)), 'fruta');
    }

    /**
     * Gera regex de palavra inteira para reduzir falso positivo de substring.
     */
    public static function toWholeWordRegexPattern(string $term): string
    {
        $normalized = mb_strtolower(trim($term));
        $escaped = preg_quote($normalized, '/');

        return '(^|[^[:alnum:]])' . $escaped . '([^[:alnum:]]|$)';
    }
}
