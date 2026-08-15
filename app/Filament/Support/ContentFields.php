<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Campos reutilizables del panel, ya etiquetados en español.
 *
 * Centralizarlos aquí mantiene los Resources cortos y garantiza que todas las
 * secciones se comporten igual para la persona editora.
 */
final class ContentFields
{
    /**
     * Título del idioma. Al crear, genera automáticamente el slug de ESE idioma.
     */
    public static function title(string $locale, string $label = 'Título'): TextInput
    {
        return TextInput::make("title.{$locale}")
            ->label($label)
            ->maxLength(255)
            // Sólo el idioma por defecto es obligatorio: el contenido puede
            // quedar sin traducir al inglés sin bloquear el guardado.
            ->required($locale === config('site.default_locale'))
            ->live(onBlur: true)
            ->afterStateUpdated(function (?string $state, Set $set, string $operation) use ($locale): void {
                // Sólo al crear: cambiar el slug de algo ya publicado rompería
                // los enlaces existentes.
                if ($operation !== 'create') {
                    return;
                }

                $set("slug.{$locale}", Str::slug((string) $state));
            });
    }

    /**
     * Slug del idioma, editable a mano y validado para no repetirse.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function slug(string $locale, string $modelClass): TextInput
    {
        return TextInput::make("slug.{$locale}")
            ->label('Dirección de la página (URL)')
            ->helperText('Se genera sola a partir del título. Cámbiala solo si es necesario.')
            ->maxLength(255)
            ->rule(static function (?Model $record) use ($locale, $modelClass): Closure {
                return static function (string $attribute, mixed $value, Closure $fail) use ($locale, $modelClass, $record): void {
                    if (blank($value)) {
                        return;
                    }

                    $yaExiste = $modelClass::query()
                        ->where("slug->{$locale}", $value)
                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                        ->exists();

                    if ($yaExiste) {
                        $fail('Ya hay otro contenido con esta misma dirección en este idioma. Escribe una diferente.');
                    }
                };
            });
    }

    /**
     * Editor de texto enriquecido con lo básico: negrita, cursiva, listas,
     * enlaces e imágenes dentro del texto.
     */
    public static function richText(string $name, string $label = 'Contenido'): RichEditor
    {
        return RichEditor::make($name)
            ->label($label)
            ->toolbarButtons([
                'bold', 'italic', 'underline', 'strike',
                'h2', 'h3',
                'bulletList', 'orderedList',
                'link', 'blockquote',
                'attachFiles',
                'undo', 'redo',
            ])
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory('contenido')
            ->fileAttachmentsVisibility('public')
            ->columnSpanFull();
    }

    /**
     * Subida de una imagen con vista previa y recorte, gestionada por
     * spatie/laravel-medialibrary.
     */
    public static function image(string $collection, string $label = 'Imagen'): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make($collection)
            ->label($label)
            ->collection($collection)
            ->image()
            ->imageEditor()
            ->maxSize(5120)
            ->helperText('Formatos JPG, PNG o WebP. Tamaño máximo 5 MB.')
            ->columnSpanFull();
    }

    /**
     * Interruptor de publicación.
     */
    public static function publishToggle(): Toggle
    {
        return Toggle::make('is_published')
            ->label('Publicado')
            ->helperText('Si está apagado, el contenido no se ve en el sitio web.')
            ->default(false);
    }
}
