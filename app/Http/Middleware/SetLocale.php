<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale')
            ?? $request->query('lang')
            ?? session('locale')
            ?? config('app.locale');

        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = config('app.locale', 'ar');
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
