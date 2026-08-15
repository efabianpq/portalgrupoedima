<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mensajes enviados desde el formulario de contacto.
     * NO es contenido traducible: son datos que escriben los visitantes.
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');

            // Idioma en que la persona navegaba al escribir, para responderle igual.
            $table->string('locale', 5)->default('es');

            // Permite marcar el mensaje como leído desde el panel.
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
