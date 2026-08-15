<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use App\Support\ImageConversions;
use Database\Factories\ProjectFactory;
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
 * Proyecto / caso de éxito.
 *
 * `client_name` es un nombre propio y por eso no es traducible.
 */
#[Translatable(['title', 'slug', 'summary', 'body'])]
#[Fillable(['title', 'slug', 'summary', 'body', 'client_name', 'cover_image', 'order', 'is_published'])]
class Project extends Model implements HasMedia
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, InteractsWithMedia, Publishable, Sortable;

    /** Colección de imagen de portada del caso de éxito. */
    public const COVER = 'portada';

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COVER)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        ImageConversions::register($this);
    }

    /**
     * Servicios a los que corresponde este proyecto.
     *
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
