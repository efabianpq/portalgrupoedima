<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Tabs;

/**
 * Pestañas "Español" / "English" para los campos traducibles.
 *
 * No usa ningún plugin. Los campos apuntan a `campo.es` / `campo.en`: Filament
 * rellena el formulario desde `attributesToArray()` —que con
 * spatie/laravel-translatable ya devuelve un arreglo por idioma— y al guardar
 * le entrega el arreglo completo, que spatie convierte en traducciones.
 *
 * Se eligió mostrar los dos idiomas en el mismo formulario (y no un selector
 * de idioma que obliga a guardar dos veces) para que la persona editora no
 * pueda olvidarse de la versión en inglés.
 */
final class TranslatableTabs
{
    /**
     * @param  Closure(string $locale): array<int, mixed>  $schema  Campos de cada idioma.
     */
    public static function make(Closure $schema, string $label = 'Contenido'): Tabs
    {
        $tabs = [];

        foreach (config('site.locales') as $locale => $nombre) {
            $tabs[] = Tabs\Tab::make($nombre)->schema($schema($locale));
        }

        return Tabs::make($label)
            ->tabs($tabs)
            ->columnSpanFull();
    }
}
