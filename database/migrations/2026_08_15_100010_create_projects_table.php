<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Campos traducibles
            $table->json('title');
            $table->json('slug');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();

            // El nombre del cliente es un nombre propio: NO se traduce.
            $table->string('client_name')->nullable();

            $table->string('cover_image')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['is_published', 'order']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug_es')->storedAs("json_unquote(json_extract(`slug`, '\$.es'))")->nullable();
            $table->string('slug_en')->storedAs("json_unquote(json_extract(`slug`, '\$.en'))")->nullable();

            $table->unique('slug_es');
            $table->unique('slug_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
