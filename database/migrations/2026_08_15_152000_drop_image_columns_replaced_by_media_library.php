<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las imágenes pasan a gestionarse con spatie/laravel-medialibrary (tabla
     * `media`), que da vista previa, miniaturas y conversiones automáticas.
     * Las columnas de texto que guardaban una ruta quedan sin uso.
     */
    public function up(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn('cover_image'));
        Schema::table('posts', fn (Blueprint $table) => $table->dropColumn('cover_image'));
        Schema::table('team_members', fn (Blueprint $table) => $table->dropColumn('photo'));
        Schema::table('testimonials', fn (Blueprint $table) => $table->dropColumn('photo'));
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn('hero_image'));
    }

    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->string('cover_image')->nullable());
        Schema::table('posts', fn (Blueprint $table) => $table->string('cover_image')->nullable());
        Schema::table('team_members', fn (Blueprint $table) => $table->string('photo')->nullable());
        Schema::table('testimonials', fn (Blueprint $table) => $table->string('photo')->nullable());
        Schema::table('pages', fn (Blueprint $table) => $table->string('hero_image')->nullable());
    }
};
