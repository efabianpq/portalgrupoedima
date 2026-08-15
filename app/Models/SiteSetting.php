<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Datos globales del sitio (una sola fila): teléfono, correo, dirección,
 * redes sociales, texto del pie de página.
 *
 * Se usa como singleton: SiteSetting::current().
 */
#[Translatable(['address', 'footer_text', 'meta_title', 'meta_description'])]
#[Fillable([
    'company_name', 'email', 'phone', 'whatsapp', 'google_maps_url',
    'address', 'social_links', 'footer_text', 'meta_title', 'meta_description',
])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory, HasTranslations;

    /**
     * Memorizado sólo durante la petición actual: así el panel refleja los
     * cambios apenas se guardan, sin caché que invalidar.
     */
    protected static ?self $current = null;

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    /**
     * La fila única de configuración. La crea si todavía no existe, para que
     * las vistas nunca reciban null.
     */
    public static function current(): self
    {
        return static::$current ??= static::query()->firstOrCreate([]);
    }

    /**
     * Olvida la instancia memorizada (útil tras guardar en el panel o en tests).
     */
    public static function forgetCurrent(): void
    {
        static::$current = null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCurrent());
    }
}
