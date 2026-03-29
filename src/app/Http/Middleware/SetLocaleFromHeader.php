<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('Accept-Language')) {
            $locale = $request->header('Accept-Language');
            
            // Extract the first locale code (ignoring variants for basic parsing like en-US)
            $locale = explode(',', $locale)[0];
            $locale = explode('-', $locale)[0];
            
            // Limit to valid system locales
            if (in_array($locale, ['pt', 'en', 'es'])) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
