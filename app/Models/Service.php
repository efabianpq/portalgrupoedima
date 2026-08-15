<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Servicio de consultoría (HOPEX/Bizzdesign, arquitectura empresarial,
 * gobierno de datos, GRC, gestión de procesos, arquitectura de información).
 */
#[Translatable(['title', 'slug', 'summary', 'body'])]
#[Fillable(['title', 'slug', 'summary', 'body', 'icon', 'order', 'is_published'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, Publishable, Sortable;

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
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
}
