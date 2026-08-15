<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * Slugs traducibles por idioma.
 *
 * El slug se guarda en una columna JSON: {"es": "gobierno-de-datos", "en": "data-governance"}.
 * Las rutas públicas (/es/servicios/{slug} y /en/services/{slug}) resuelven el
 * modelo por el slug del idioma activo.
 *
 * La unicidad por idioma la garantiza la base de datos mediante columnas
 * generadas (`slug_es`, `slug_en`) con índice único — ver las migraciones.
 * Las consultas de este trait usan la ruta JSON (`slug->es`) en vez de esas
 * columnas: es equivalente y no se rompe si se agrega un idioma en
 * config/site.php antes de correr la migración correspondiente.
 *
 * @mixin Model
 * @mixin HasTranslations
 */
trait HasTranslatableSlug
{
    /**
     * Red de seguridad: antes de guardar, rellena los slugs que falten a
     * partir del título de cada idioma. Así nunca queda un registro sin slug,
     * venga del panel, de un seeder o de la migración de WordPress.
     */
    public static function bootHasTranslatableSlug(): void
    {
        static::saving(function (self $model) {
            $model->generateMissingSlugs();
        });
    }

    /**
     * Atributo traducible del que se genera el slug.
     */
    public function slugSource(): string
    {
        return 'title';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Slug del idioma activo, para generar URLs.
     */
    public function getRouteKey()
    {
        return $this->slugFor(app()->getLocale());
    }

    /**
     * Slug de un idioma concreto. Si no está traducido cae al idioma por
     * defecto para que las URLs generadas nunca queden vacías.
     */
    public function slugFor(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $slug = $this->getTranslation('slug', $locale, useFallbackLocale: false);

        if (filled($slug)) {
            return $slug;
        }

        $fallback = $this->getTranslation('slug', config('site.default_locale'), useFallbackLocale: false);

        return filled($fallback) ? $fallback : null;
    }

    /**
     * Route model binding: resuelve por el slug del idioma activo.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field !== null && $field !== 'slug') {
            return parent::resolveRouteBinding($value, $field);
        }

        return static::query()->whereSlug($value)->first();
    }

    /**
     * Filtra por slug en un idioma concreto (estricto, sin idioma de respaldo).
     */
    public function scopeWhereSlug(Builder $query, string $slug, ?string $locale = null): void
    {
        $locale ??= app()->getLocale();

        $query->where("slug->{$locale}", $slug);
    }

    /**
     * Filtra por slug en cualquier idioma. Útil para detectar que la persona
     * llegó con el slug de otro idioma y redirigirla a la URL canónica.
     */
    public function scopeWhereSlugInAnyLocale(Builder $query, string $slug): void
    {
        $query->where(function (Builder $query) use ($slug) {
            foreach (static::siteLocales() as $locale) {
                $query->orWhere("slug->{$locale}", $slug);
            }
        });
    }

    /**
     * Sólo los registros que tienen slug en el idioma dado.
     */
    public function scopeTranslatedIn(Builder $query, ?string $locale = null): void
    {
        $locale ??= app()->getLocale();

        $query->whereNotNull("slug->{$locale}");
    }

    /**
     * Busca un registro por su slug en un idioma concreto.
     */
    public static function findBySlug(string $slug, ?string $locale = null): ?static
    {
        return static::query()->whereSlug($slug, $locale)->first();
    }

    /**
     * Busca un registro por su slug en cualquier idioma.
     */
    public static function findBySlugInAnyLocale(string $slug): ?static
    {
        return static::query()->whereSlugInAnyLocale($slug)->first();
    }

    /**
     * Genera los slugs que falten a partir del atributo fuente de cada idioma.
     * No toca los slugs que ya tengan valor (para no romper URLs publicadas).
     */
    public function generateMissingSlugs(): static
    {
        foreach (static::siteLocales() as $locale) {
            if (blank($this->getTranslation('slug', $locale, useFallbackLocale: false))) {
                $this->generateSlugFor($locale);
            }
        }

        return $this;
    }

    /**
     * (Re)genera el slug de un idioma a partir del atributo fuente,
     * garantizando que no choque con el de otro registro.
     */
    public function generateSlugFor(string $locale): static
    {
        // Sin idioma de respaldo a propósito: si no hay título en ese idioma,
        // el contenido no está traducido y no debe tener slug en ese idioma
        // (un slug en español dentro del sitio en inglés sería peor que nada).
        $source = $this->getTranslation($this->slugSource(), $locale, useFallbackLocale: false);

        if (blank($source)) {
            return $this;
        }

        $this->setTranslation('slug', $locale, $this->uniqueSlug(Str::slug($source), $locale));

        return $this;
    }

    /**
     * Añade un sufijo numérico si el slug ya existe en ese idioma.
     */
    protected function uniqueSlug(string $slug, string $locale): string
    {
        $base = $slug;
        $suffix = 1;

        while ($this->slugExists($slug, $locale)) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    protected function slugExists(string $slug, string $locale): bool
    {
        return static::query()
            ->whereSlug($slug, $locale)
            ->when($this->exists, fn (Builder $query) => $query->whereKeyNot($this->getKey()))
            ->exists();
    }

    /**
     * @return array<int, string>
     */
    protected static function siteLocales(): array
    {
        return array_keys(config('site.locales', ['es' => 'Español']));
    }
}
