<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateBotApiKey
{
    /**
     * Valida a API Key usada pelo bot-zap para consultar dados do ERP.
     *
     * Header esperado: X-Bot-Api-Key
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-Bot-Api-Key');
        $expected = config('services.bot_api.key');

        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
