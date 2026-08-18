<?php

namespace App\Support;

use App\Models\Service;
use App\Models\Solution;
use Illuminate\Support\Facades\File;

/**
 * Ilustraciones de marca (SVG, dibujadas a mano) que acompañan servicios,
 * soluciones y páginas institucionales mientras no hay fotografía real.
 *
 * No sustituyen la Media Library: si un servicio o solución tiene una imagen
 * subida desde el panel, esa imagen gana siempre (ver uso en las vistas). Este
 * soporte sólo cubre el vacío visual con un recurso propio, nunca con una foto
 * simulada — ver CLAUDE.md, "Los servicios no tienen imagen cargada".
 */
final class Illustrations
{
    protected const BASE = 'images/illustrations';

    /**
     * La ilustración se busca por el slug en español, que siempre existe
     * (es el idioma por defecto y es obligatorio).
     */
    public static function forService(Service $service): ?string
    {
        return self::find('services', $service->slugFor('es'));
    }

    public static function forSolution(Solution $solution): ?string
    {
        return self::find('solutions', $solution->slugFor('es'));
    }

    /**
     * Páginas institucionales: se busca por su `key` (home, hopex, about, contact).
     */
    public static function forPage(string $key): ?string
    {
        return self::find('pages', $key);
    }

    protected static function find(string $type, ?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        $relative = self::BASE."/{$type}/{$name}.svg";

        return File::exists(public_path($relative)) ? asset($relative) : null;
    }
}
