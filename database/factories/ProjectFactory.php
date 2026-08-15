<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;

        $titleEs = 'Caso de éxito '.Str::ucfirst(fake('es_ES')->words(3, true));
        $titleEn = 'Success story '.Str::ucfirst(fake('en_US')->words(3, true));

        return [
            'title' => [
                'es' => $titleEs,
                'en' => $titleEn,
            ],
            'slug' => [
                'es' => Str::slug($titleEs).'-'.$index,
                'en' => Str::slug($titleEn).'-'.$index,
            ],
            'summary' => [
                'es' => fake('es_ES')->sentence(14),
                'en' => fake('en_US')->sentence(14),
            ],
            'body' => [
                'es' => '<p>'.implode('</p><p>', fake('es_ES')->paragraphs(3)).'</p>',
                'en' => '<p>'.implode('</p><p>', fake('en_US')->paragraphs(3)).'</p>',
            ],
            'client_name' => fake()->company(),
            'order' => $index,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
