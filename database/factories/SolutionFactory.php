<?php

namespace Database\Factories;

use App\Models\Solution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Solution>
 */
class SolutionFactory extends Factory
{
    /**
     * Las 4 disciplinas publicadas (alcance conservador de
     * storage/migration/ANALISIS-REFERENCIA.md A5).
     *
     * @var array<int, array{es: string, en: string}>
     */
    protected const CATALOG = [
        ['es' => 'Arquitectura empresarial', 'en' => 'Enterprise Architecture'],
        ['es' => 'Portafolio de aplicaciones', 'en' => 'Application Portfolio'],
        ['es' => 'Procesos de negocio', 'en' => 'Business Processes'],
        ['es' => 'Gobierno, riesgo y cumplimiento', 'en' => 'Governance, Risk and Compliance'],
    ];

    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $item = self::CATALOG[$index % count(self::CATALOG)];
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
                'es' => fake('es_ES')->sentence(16),
                'en' => fake('en_US')->sentence(16),
            ],
            'body' => [
                'es' => '<p>'.implode('</p><p>', fake('es_ES')->paragraphs(3)).'</p>',
                'en' => '<p>'.implode('</p><p>', fake('en_US')->paragraphs(3)).'</p>',
            ],
            'cta_label' => [
                'es' => 'Conversar sobre esta solución',
                'en' => 'Talk about this solution',
            ],
            'order' => $index,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
