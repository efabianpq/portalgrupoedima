<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Servicios reales de Grupo Edima, para que los datos de prueba se parezcan
     * al contenido definitivo.
     *
     * @var array<int, array{es: string, en: string}>
     */
    protected const CATALOG = [
        ['es' => 'Arquitectura empresarial', 'en' => 'Enterprise Architecture'],
        ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
        ['es' => 'Gobierno, riesgo y cumplimiento', 'en' => 'Governance, Risk and Compliance'],
        ['es' => 'Implementación de HOPEX', 'en' => 'HOPEX Implementation'],
        ['es' => 'Implementación de Bizzdesign', 'en' => 'Bizzdesign Implementation'],
        ['es' => 'Gestión de procesos', 'en' => 'Business Process Management'],
        ['es' => 'Arquitectura de información', 'en' => 'Information Architecture'],
    ];

    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $item = self::CATALOG[$index % count(self::CATALOG)];

        // Tras recorrer el catálogo se añade un sufijo para no repetir slugs.
        $round = intdiv($index, count(self::CATALOG));
        $suffix = $round > 0 ? '-'.($round + 1) : '';

        return [
            'title' => [
                'es' => $item['es'],
                'en' => $item['en'],
            ],
            'slug' => [
                'es' => Str::slug($item['es']).$suffix,
                'en' => Str::slug($item['en']).$suffix,
            ],
            'summary' => [
                'es' => fake('es_ES')->sentence(14),
                'en' => fake('en_US')->sentence(14),
            ],
            'body' => [
                'es' => '<p>'.implode('</p><p>', fake('es_ES')->paragraphs(3)).'</p>',
                'en' => '<p>'.implode('</p><p>', fake('en_US')->paragraphs(3)).'</p>',
            ],
            'icon' => fake()->randomElement(['chart-bar', 'cube', 'shield-check', 'circle-stack', 'squares-2x2']),
            'order' => $index,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    /**
     * Servicio sin traducir al inglés, para probar el comportamiento del sitio
     * cuando falta una traducción.
     */
    public function onlySpanish(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => ['es' => $attributes['title']['es']],
                'slug' => ['es' => $attributes['slug']['es']],
                'summary' => ['es' => $attributes['summary']['es']],
                'body' => ['es' => $attributes['body']['es']],
            ];
        });
    }
}
