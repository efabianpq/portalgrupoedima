<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use App\Support\ImageConversions;
use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Integrante del equipo.
 *
 * `name` es un nombre propio y por eso no es traducible; el cargo y la
 * biografía sí lo son.
 */
#[Translatable(['role', 'bio'])]
#[Fillable(['name', 'role', 'bio', 'photo', 'order', 'is_published'])]
class TeamMember extends Model implements HasMedia
{
    /** @use HasFactory<TeamMemberFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia, Publishable, Sortable;

    /** Colección de la foto de la persona. */
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
