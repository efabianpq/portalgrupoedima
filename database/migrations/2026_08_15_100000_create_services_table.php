<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Campos traducibles (spatie/laravel-translatable): {"es": "...", "en": "..."}
            $table->json('title');
            $table->json('slug');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();

            $table->string('icon')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['is_published', 'order']);
        });

        // Unicidad del slug por idioma: columnas generadas desde el JSON con
        // índice único. Si un idioma no está traducido el valor es NULL, y en
        // MySQL varios NULL no chocan entre sí — así se pueden tener varios
        // registros sin traducir al inglés.
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug_es')->storedAs("json_unquote(json_extract(`slug`, '\$.es'))")->nullable();
            $table->string('slug_en')->storedAs("json_unquote(json_extract(`slug`, '\$.en'))")->nullable();

            $table->unique('slug_es');
            $table->unique('slug_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
