<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
#[Fillable(['key', 'title', 'subtitle', 'body', 'sections', 'hero_image', 'meta_title', 'meta_description'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, HasTranslations;

    public const HOME = 'home';

    public const ABOUT = 'about';

    public const CONTACT = 'contact';

    /**
     * Las únicas páginas que existen. La persona editora no crea ni borra
     * páginas; sólo edita estas.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [self::HOME, self::ABOUT, self::CONTACT];
    }

    /**
     * Obtiene una página por su identificador: Page::key(Page::HOME).
     */
    public static function key(string $key): ?self
    {
        return static::query()->where('key', $key)->first();
    }
}
