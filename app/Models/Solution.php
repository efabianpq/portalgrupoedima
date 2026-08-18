<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatableSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\SolutionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Solución (disciplina de HOPEX): arquitectura empresarial, portafolio de
 * aplicaciones, procesos de negocio, gobierno-riesgo-cumplimiento, etc.
 *
 * Es el eje "qué problema de negocio resuelve" del sitio, complementario al
 * de Service ("cómo lo entrega Grupo Edima"). Ver
 * storage/migration/ANALISIS-REFERENCIA.md A5 para el mapa de sitio completo.
 */
#[Translatable(['title', 'slug', 'summary', 'body', 'cta_label'])]
#[Fillable(['title', 'slug', 'summary', 'body', 'cta_label', 'order', 'is_published'])]
class Solution extends Model
{
    /** @use HasFactory<SolutionFactory> */
    use HasFactory, HasTranslatableSlug, HasTranslations, Publishable, Sortable;

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Servicios con los que típicamente se entrega esta solución.
     *
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
