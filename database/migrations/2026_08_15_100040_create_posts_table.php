<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Campos traducibles
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->json('category')->nullable();

            $table->string('cover_image')->nullable();

            // Fecha de publicación: controla el orden del blog y permite
            // programar una entrada para que aparezca más adelante.
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug_es')->storedAs("json_unquote(json_extract(`slug`, '\$.es'))")->nullable();
            $table->string('slug_en')->storedAs("json_unquote(json_extract(`slug`, '\$.en'))")->nullable();

            $table->unique('slug_es');
            $table->unique('slug_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
