<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Crea las filas que el sitio da por sentado que existen: las tres páginas
 * institucionales y la configuración global.
 *
 * Es idempotente: se puede correr las veces que haga falta sin duplicar nada
 * ni pisar el contenido ya escrito desde el panel.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $titulos = [
            Page::HOME => ['es' => 'Inicio', 'en' => 'Home'],
            Page::ABOUT => ['es' => 'Nosotros', 'en' => 'About us'],
            Page::CONTACT => ['es' => 'Contacto', 'en' => 'Contact'],
        ];

        foreach (Page::keys() as $key) {
            Page::query()->firstOrCreate(
                ['key' => $key],
                ['title' => $titulos[$key]],
            );
        }

        // Crea la fila única de configuración si todavía no existe.
        SiteSetting::current();
    }
}
