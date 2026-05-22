<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $supported = array_keys(config('laravellocalization.supportedLocales', ['en']));

        // 1) route parameter 'locale' (e.g. /api/v1/posts/ar)
        $routeLocale = null;
        $route = $request->route();
        if ($route) {
            $routeLocale = $route->parameter('locale') ?? null;
        }

        if ($routeLocale && in_array($routeLocale, $supported, true)) {
            $requested = $routeLocale;
        } else {
            // 2) ?lang= override
            // 3) Accept-Language header via getPreferredLanguage
            $requested = $request->query('lang')
                ?: $request->getPreferredLanguage($supported)
                ?: config('app.locale', 'en');
        }

        LaravelLocalization::setLocale($requested);
        app()->setLocale($requested);

        return $next($request);
    }
}
