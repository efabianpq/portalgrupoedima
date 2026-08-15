<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Entrada de blog / noticia.
 */
#[Translatable(['title', 'slug', 'excerpt', 'body', 'category'])]
#[Fillable(['title', 'slug', 'excerpt', 'body', 'category', 'cover_image', 'published_at', 'is_published'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, Publishable;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
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
