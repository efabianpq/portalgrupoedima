<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

/**
 * Ítem del menú público, administrado desde el panel.
 *
 * `url` se escribe directamente (no se deriva de una ruta con nombre): admite
 * tanto rutas internas ("/es/soluciones") como enlaces externos. Esto le da a
 * la persona editora control total para agregar, quitar y reordenar el menú
 * sin tocar código — a cambio, el panel no puede detectar por sí solo si un
 * enlace apunta a una sección todavía vacía (p. ej. Equipo sin integrantes);
 * ver el aviso en MenuItemResource.
 */
#[Translatable(['label', 'url'])]
#[Fillable(['label', 'url', 'order', 'is_published'])]
class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory, HasTranslations, Publishable, Sortable;

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}
