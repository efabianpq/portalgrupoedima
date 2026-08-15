<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @var array<int, array{es: string, en: string}>
     */
    protected const CATEGORIES = [
        ['es' => 'Arquitectura empresarial', 'en' => 'Enterprise Architecture'],
        ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
        ['es' => 'Noticias', 'en' => 'News'],
        ['es' => 'Eventos', 'en' => 'Events'],
    ];

    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $category = self::CATEGORIES[$index % count(self::CATEGORIES)];

        $titleEs = Str::ucfirst(fake('es_ES')->sentence(6));
        $titleEn = Str::ucfirst(fake('en_US')->sentence(6));

        return [
            'title' => [
                'es' => $titleEs,
                'en' => $titleEn,
            ],
            'slug' => [
                'es' => Str::slug($titleEs).'-'.$index,
                'en' => Str::slug($titleEn).'-'.$index,
            ],
            'excerpt' => [
                'es' => fake('es_ES')->sentence(18),
                'en' => fake('en_US')->sentence(18),
            ],
            'body' => [
                'es' => '<p>'.implode('</p><p>', fake('es_ES')->paragraphs(4)).'</p>',
                'en' => '<p>'.implode('</p><p>', fake('en_US')->paragraphs(4)).'</p>',
            ],
            'category' => [
                'es' => $category['es'],
                'en' => $category['en'],
            ],
            'published_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    /**
     * Entrada programada para publicarse más adelante.
     */
    public function scheduled(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
            'published_at' => now()->addWeek(),
        ]);
    }
}
