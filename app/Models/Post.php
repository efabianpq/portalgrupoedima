<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use App\Support\ImageConversions;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Entrada de blog / noticia.
 */
#[Translatable(['title', 'slug', 'excerpt', 'body', 'category'])]
#[Fillable(['title', 'slug', 'excerpt', 'body', 'category', 'published_at', 'is_published'])]
class Post extends Model implements HasMedia
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, InteractsWithMedia, Publishable;

    /** Colección de imagen de portada de la entrada. */
    public const COVER = 'portada';

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
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
     * Sobrescribe el scope del trait Publishable: además de estar marcada como
     * publicada, la entrada no debe tener una fecha de publicación futura
     * (permite dejar entradas programadas).
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Más recientes primero.
     */
    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }
}
