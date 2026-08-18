<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relación muchos a muchos: una solución (disciplina de HOPEX) se entrega
     * típicamente con varios servicios, y un servicio puede cubrir varias
     * soluciones. Nombre de tabla en orden alfabético de los modelos
     * (Service, Solution), como resuelve Eloquent por convención.
     */
    public function up(): void
    {
        Schema::create('service_solution', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('solution_id')->constrained()->cascadeOnDelete();

            $table->primary(['service_id', 'solution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_solution');
    }
};
