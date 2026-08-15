<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Contenido que la persona editora ordena manualmente desde el panel
 * (columna `order`).
 *
 * @mixin Model
 */
trait Sortable
{
    /**
     * Orden manual definido en el panel; a igualdad de orden, el más antiguo primero.
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderBy('id');
    }
}
