<?php

namespace App\Filament\Support;

use App\Support\ImageConversions;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

/**
 * Columnas reutilizables de las tablas del panel, etiquetadas en español.
 */
final class ContentColumns
{
    /**
     * Columna de un campo traducible. Muestra el texto del idioma activo y
     * busca en todos los idiomas a la vez.
     *
     * No es ordenable: la columna es JSON y ordenarla ordenaría por el texto
     * crudo `{"es":...,"en":...}`, que no significa nada para quien edita.
     */
    public static function translated(string $name, string $label): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->searchable(query: function (Builder $query, string $search) use ($name): Builder {
                return $query->where(function (Builder $query) use ($name, $search): void {
                    foreach (array_keys(config('site.locales')) as $locale) {
                        $query->orWhere("{$name}->{$locale}", 'like', "%{$search}%");
                    }
                });
            })
            ->limit(60)
            ->wrap();
    }

    /**
     * Interruptor de publicación con ícono de color.
     */
    public static function published(): IconColumn
    {
        return IconColumn::make('is_published')
            ->label('Publicado')
            ->boolean()
            ->trueIcon('heroicon-o-check-circle')
            ->falseIcon('heroicon-o-x-circle')
            ->trueColor('success')
            ->falseColor('gray')
            ->alignCenter();
    }

    /**
     * Miniatura de la imagen del registro.
     */
    public static function image(string $collection, string $label = 'Imagen'): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make($collection)
            ->label($label)
            ->collection($collection)
            ->conversion(ImageConversions::THUMB)
            ->circular(false);
    }
}
