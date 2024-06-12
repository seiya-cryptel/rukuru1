<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Set the locale based on the first segment of the URL
         */
        $locale = $request->segment(1);
        // if $locale is 'en' or 'ja'
        if (!in_array($locale, ['en', 'ja'])) {
            $locale = session('localization', config('app.locale'));
        }
        app()->setLocale($locale);
        session(['localization' => $locale]);
        /**
         * Set the URL default locale
         */
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
