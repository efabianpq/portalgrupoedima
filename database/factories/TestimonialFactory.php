<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * @var array<int, array{es: string, en: string}>
     */
    protected const ROLES = [
        ['es' => 'Directora de Tecnología', 'en' => 'Chief Technology Officer'],
        ['es' => 'Gerente de Riesgos', 'en' => 'Risk Manager'],
        ['es' => 'Líder de Datos', 'en' => 'Head of Data'],
        ['es' => 'Gerente de Procesos', 'en' => 'Process Manager'],
    ];

    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $role = self::ROLES[$index % count(self::ROLES)];

        return [
            'author_name' => fake('es_ES')->name(),
            'author_role' => [
                'es' => $role['es'].', '.fake()->company(),
                'en' => $role['en'].', '.fake()->company(),
            ],
            'quote' => [
                'es' => fake('es_ES')->paragraph(3),
                'en' => fake('en_US')->paragraph(3),
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
