<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use App\Support\ImageConversions;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Servicio de consultoría (HOPEX/Bizzdesign, arquitectura empresarial,
 * gobierno de datos, GRC, gestión de procesos, arquitectura de información).
 */
#[Translatable(['title', 'slug', 'summary', 'body'])]
#[Fillable(['title', 'slug', 'summary', 'body', 'icon', 'order', 'is_published'])]
class Service extends Model implements HasMedia
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, InteractsWithMedia, Publishable, Sortable;

    /** Colección de imagen de la tarjeta del servicio. */
    public const IMAGE = 'imagen';

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        ImageConversions::register($this);
    }

    /**
     * Proyectos / casos de éxito asociados a este servicio.
     *
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Soluciones (disciplinas de HOPEX) que típicamente involucran este servicio.
     *
     * @return BelongsToMany<Solution, $this>
     */
    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class);
    }
}
