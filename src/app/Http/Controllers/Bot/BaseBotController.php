<?php

namespace App\Http\Controllers\Bot;

use Illuminate\Http\JsonResponse;

abstract class BaseBotController
{
    /**
     * Responde com sucesso.
     *
     * @param  array<string, mixed>  $data
     */
    protected function responseSuccess(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * Responde com erro.
     */
    protected function responseError(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }

    /**
     * Formata um valor monetário para a IA.
     */
    protected function formatCurrency(mixed $value): float
    {
        return round((float) $value, 2);
    }
}
