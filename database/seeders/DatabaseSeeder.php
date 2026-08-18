<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Contenido mínimo que el sitio necesita para funcionar.
     */
    public function run(): void
    {
        $this->call(SiteContentSeeder::class);
        $this->call(MenuItemSeeder::class);
    }
}
