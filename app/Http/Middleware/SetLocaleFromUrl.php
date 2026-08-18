<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Activa el idioma de una petición según el prefijo de la URL (/es/..., /en/...).
 *
 * routes/web.php registra un grupo por cada idioma de config('site.locales')
 * con un prefijo literal ("es", "en") y pasa ese idioma como parámetro del
 * middleware: SetLocaleFromUrl::class.':es'. No usa un comodín de ruta
 * {locale}, así que no hay nada que leer desde $request->route().
 */
class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next, string $locale): Response
    {
        abort_unless(array_key_exists($locale, config('site.locales')), 404);

        App::setLocale($locale);

        return $next($request);
    }
}
