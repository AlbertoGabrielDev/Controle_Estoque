<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['pt', 'en', 'es'];
        $fallback = 'pt';

        // 1. Resolve locale by priority
        // a) Query param 'lang'
        $locale = $request->query('lang');

        // b) Session 'locale'
        if (!$locale || !in_array($this->normalize($locale), $allowedLocales)) {
            $locale = Session::get('locale');
        }

        // c) Cookie 'locale'
        if (!$locale || !in_array($this->normalize($locale), $allowedLocales)) {
            $locale = $request->cookie('locale');
        }

        // d) Accept-Language (Browser)
        if (!$locale || !in_array($this->normalize($locale), $allowedLocales)) {
            $locale = $request->getPreferredLanguage($allowedLocales);
        }

        // e) Fallback
        $locale = $this->normalize($locale ?: config('app.locale', $fallback));

        // Ensure it's allowed and 2 chars
        if (!in_array($locale, $allowedLocales)) {
            $locale = $fallback;
        }

        App::setLocale($locale);
        
        // Ensure session and cookie are in sync if changed via query
        if ($request->has('lang')) {
            Session::put('locale', $locale);
            Cookie::queue('locale', $locale, 525600); // 1 year
        }

        return $next($request);
    }

    /**
     * Normalize locale to 2 letters.
     */
    private function normalize($locale): string
    {
        if (!$locale) return '';
        $lang = explode('_', str_replace('-', '_', $locale))[0];
        return strtolower($lang);
    }
}
