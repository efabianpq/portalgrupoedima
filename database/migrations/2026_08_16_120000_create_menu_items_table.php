<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menú público, administrable desde el panel. No tiene slug propio: cada
     * ítem enlaza a una URL que la persona editora escribe directamente
     * (interna, como "/es/soluciones", o externa).
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // Campos traducibles (spatie/laravel-translatable): {"es": "...", "en": "..."}
            $table->json('label');
            $table->json('url');

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(true);

            $table->timestamps();

            $table->index(['is_published', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
