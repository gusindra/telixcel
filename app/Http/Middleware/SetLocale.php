<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /** Locales the app supports. */
    public const SUPPORTED = ['en', 'id'];

    /**
     * Apply the user's chosen locale (stored in session) to the app.
     * Falls back to the configured default (en) when none/invalid.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
