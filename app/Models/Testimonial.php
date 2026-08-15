<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use App\Support\ImageConversions;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Testimonio de un cliente.
 *
 * `author_name` es un nombre propio y por eso no es traducible.
 */
#[Translatable(['author_role', 'quote'])]
#[Fillable(['author_name', 'author_role', 'quote', 'order', 'is_published'])]
class Testimonial extends Model implements HasMedia
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia, Publishable, Sortable;

    /** Colección de la foto de quien da el testimonio. */
    public const PHOTO = 'foto';

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTO)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        ImageConversions::register($this);
    }
}
