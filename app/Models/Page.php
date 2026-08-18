<?php

namespace App\Models;

use App\Support\ImageConversions;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Página institucional de contenido fijo (Inicio, Nosotros, Contacto).
 *
 * Se identifica por `key`, no por slug: las URLs de estas páginas las definen
 * las rutas (/es/nosotros, /en/about-us), no la base de datos. Así la persona
 * editora cambia los textos sin poder romper la navegación del sitio.
 */
#[Translatable(['title', 'subtitle', 'body', 'sections', 'meta_title', 'meta_description'])]
#[Fillable(['key', 'title', 'subtitle', 'body', 'sections', 'meta_title', 'meta_description'])]
class Page extends Model implements HasMedia
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia;

    /** Colección de imagen principal de la página. */
    public const COVER = 'portada';

    public const HOME = 'home';

    public const ABOUT = 'about';

    public const CONTACT = 'contact';

    /** Página informativa sobre la plataforma HOPEX. */
    public const HOPEX = 'hopex';

    /**
     * Las únicas páginas que existen. La persona editora no crea ni borra
     * páginas; sólo edita estas.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [self::HOME, self::HOPEX, self::ABOUT, self::CONTACT];
    }

    /**
     * Nombres en español de cada página, para mostrarlos en el panel.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::HOME => 'Inicio',
            self::HOPEX => 'Plataforma HOPEX',
            self::ABOUT => 'Nosotros',
            self::CONTACT => 'Contacto',
        ];
    }

    /**
     * Nombre legible de esta página.
     */
    public function label(): string
    {
        return static::labels()[$this->key] ?? $this->key;
    }

    /**
     * Obtiene una página por su identificador: Page::key(Page::HOME).
     */
    public static function key(string $key): ?self
    {
        return static::query()->where('key', $key)->first();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COVER)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        ImageConversions::register($this);
    }
}
