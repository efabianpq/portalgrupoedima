<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Contenido que la persona editora puede publicar u ocultar del sitio público.
 *
 * @mixin Model
 */
trait Publishable
{
    /**
     * Sólo el contenido visible en el sitio público.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Sólo el contenido oculto (borradores).
     */
    public function scopeDraft(Builder $query): void
    {
        $query->where('is_published', false);
    }
}
