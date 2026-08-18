<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Prepara el contenido enriquecido para mostrarlo en el sitio público.
 *
 * El contenido puede traer bloques marcados como pendientes —datos que todavía
 * no se han confirmado (duraciones, certificaciones, cifras)—. Esos bloques
 * deben ser visibles para quien edita en el panel, porque son su lista de
 * tareas, pero NO para quien visita el sitio: un visitante que lee
 * "[PENDIENTE: duración típica]" ve una página rota.
 *
 * El seeder envuelve esos bloques en <div data-pendiente="1">…</div>, y aquí se
 * eliminan junto con su encabezado.
 */
final class PublicContent
{
    public static function render(?string $html): HtmlString
    {
        return new HtmlString(self::strip($html));
    }

    public static function strip(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        // Bloques marcados explícitamente como pendientes.
        $html = preg_replace('#<div\s+data-pendiente="1">.*?</div>#is', '', $html);

        // Red de seguridad: si algún marcador quedó suelto (por ejemplo escrito
        // a mano desde el panel), se elimina el párrafo que lo contiene.
        $html = preg_replace('#<(p|li)\b[^>]*>(?:(?!</\1>).)*\[PENDIENTE.*?</\1>#is', '', $html);

        return trim((string) $html);
    }

    /**
     * ¿El contenido queda vacío una vez retirados los pendientes?
     */
    public static function isEmpty(?string $html): bool
    {
        return blank(trim(strip_tags(self::strip($html))));
    }
}
