<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;

        return [
            'label' => [
                'es' => 'Enlace '.($index + 1),
                'en' => 'Link '.($index + 1),
            ],
            'url' => [
                'es' => '/es/pagina-'.($index + 1),
                'en' => '/en/page-'.($index + 1),
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
