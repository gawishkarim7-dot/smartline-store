<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = config('app.locale', 'ar');
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return back();
    }
}
