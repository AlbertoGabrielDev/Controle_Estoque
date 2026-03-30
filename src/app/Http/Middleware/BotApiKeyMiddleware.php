<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BotApiKeyMiddleware
{
    /**
     * Valida o header X-Bot-Api-Key para consumo dos endpoints /api/bot/*.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = trim((string) config('services.bot_api.api_key'));
        $provided = trim((string) ($request->header('X-Bot-Api-Key') ?: $request->header('X-API-KEY')));

        if ($expected === '' || $provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
