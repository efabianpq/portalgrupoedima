<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            // El nombre de la persona es un nombre propio: NO se traduce.
            $table->string('name');

            // Campos traducibles: el cargo y la biografía sí cambian por idioma.
            $table->json('role')->nullable();
            $table->json('bio')->nullable();

            $table->string('photo')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['is_published', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
