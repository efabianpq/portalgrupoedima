<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Datos globales del sitio: una sola fila.
     *
     * Es información que se repite en TODAS las páginas (cabecera, pie de
     * página, datos de contacto), por eso no vive dentro de una página concreta.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Datos de contacto (no traducibles: son datos, no textos)
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('google_maps_url')->nullable();

            // La dirección sí puede llevar texto descriptivo ("Piso 4, Torre Norte")
            $table->json('address')->nullable();

            // Redes sociales: [{"platform": "linkedin", "url": "https://..."}]
            $table->json('social_links')->nullable();

            // Textos traducibles reutilizados en todo el sitio
            $table->json('footer_text')->nullable();

            // SEO por defecto, para páginas que no definan el suyo
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
