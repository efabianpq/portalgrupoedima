<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();

            // Nombre propio de quien da el testimonio: NO se traduce.
            $table->string('author_name');

            // Campos traducibles: el cargo y la cita sí se traducen.
            $table->json('author_role')->nullable();
            $table->json('quote');

            $table->string('photo')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['is_published', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
