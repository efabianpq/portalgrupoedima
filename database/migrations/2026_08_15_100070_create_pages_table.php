<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Páginas institucionales de contenido fijo: Inicio, Nosotros, Contacto.
     *
     * Son un conjunto cerrado de filas identificadas por `key`. La persona
     * editora edita sus textos pero no crea ni borra páginas (eso se controla
     * en el panel), así que las rutas nunca apuntan a una página inexistente.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            // Identificador técnico e inmutable: home, about, contact.
            $table->string('key')->unique();

            // Campos traducibles
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('body')->nullable();

            // Bloques flexibles de la página (hero, cifras, llamados a la acción...).
            // Se guarda por idioma: {"es": [...bloques...], "en": [...bloques...]}
            $table->json('sections')->nullable();

            $table->string('hero_image')->nullable();

            // SEO por idioma
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
