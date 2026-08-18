<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Carga el menú público con los enlaces que el sitio ya tenía definidos en
 * código (ver AppServiceProvider antes de que el menú pasara a ser
 * administrable). Sin este seeder, un sitio recién instalado no tendría
 * ningún ítem de menú.
 *
 * "Equipo" y "Contacto" no se incluyen a propósito: Equipo porque la sección
 * está vacía hasta que haya integrantes reales (ver el aviso en
 * MenuItemResource), y Contacto porque el encabezado ya tiene su propio botón
 * "Contáctanos" siempre visible.
 *
 * Idempotente: se identifica cada ítem por su URL en español, así que
 * correrlo de nuevo no duplica nada.
 */
class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $locale = config('site.default_locale');

        $items = [
            ['route' => 'home', 'label' => ['es' => 'Inicio', 'en' => 'Home']],
            ['route' => 'hopex', 'label' => ['es' => 'Plataforma HOPEX', 'en' => 'HOPEX platform']],
            ['route' => 'solutions', 'label' => ['es' => 'Soluciones', 'en' => 'Solutions']],
            ['route' => 'services', 'label' => ['es' => 'Servicios', 'en' => 'Services']],
            ['route' => 'projects', 'label' => ['es' => 'Casos de éxito', 'en' => 'Case studies']],
            ['route' => 'blog', 'label' => ['es' => 'Recursos', 'en' => 'Resources']],
            ['route' => 'about', 'label' => ['es' => 'Nosotros', 'en' => 'About us']],
        ];

        foreach ($items as $order => $item) {
            $urlEs = route("{$locale}.{$item['route']}", absolute: false);

            $menuItem = MenuItem::query()->firstOrNew(['url->es' => $urlEs]);

            $menuItem->fill([
                'label' => $item['label'],
                'url' => [
                    'es' => route("es.{$item['route']}", absolute: false),
                    'en' => route("en.{$item['route']}", absolute: false),
                ],
                'order' => $order,
                'is_published' => true,
            ])->save();
        }
    }
}
