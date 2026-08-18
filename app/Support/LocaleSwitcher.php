<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;

/**
 * Calcula la URL de la página actual en otro idioma, para el selector de
 * idioma del header y las etiquetas hreflang del <head>.
 *
 * Las rutas públicas se nombran "{locale}.{página}" (ver routes/web.php), así
 * que basta con cambiar el prefijo del nombre de ruta actual. Si la ruta
 * equivalente no existe, o el contenido no está traducido a ese idioma
 * (por ejemplo un slug sin traducir), cae a la portada de ese idioma.
 */
class LocaleSwitcher
{
    public static function urlFor(string $locale): string
    {
        $route = Route::current();

        if ($route === null || $route->getName() === null) {
            return url("/{$locale}");
        }

        $target = $locale.'.'.Str::after($route->getName(), '.');

        if (! Route::has($target)) {
            return url("/{$locale}");
        }

        try {
            $parameters = collect($route->parameters())->map(
                fn ($value) => $value instanceof Model && method_exists($value, 'slugFor')
                    ? $value->slugFor($locale)
                    : $value,
            );

            if ($parameters->contains(null)) {
                return url("/{$locale}");
            }

            return route($target, $parameters->all());
        } catch (Throwable) {
            return url("/{$locale}");
        }
    }

    /**
     * @return array<string, string> idioma => URL equivalente de la página actual.
     */
    public static function allUrls(): array
    {
        return collect(config('site.locales'))
            ->keys()
            ->mapWithKeys(fn (string $locale) => [$locale => static::urlFor($locale)])
            ->all();
    }
}
