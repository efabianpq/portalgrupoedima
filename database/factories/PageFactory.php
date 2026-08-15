<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $keys = Page::keys();
        $key = $keys[$index % count($keys)];
        $round = intdiv($index, count($keys));

        return [
            'key' => $round > 0 ? $key.'-'.($round + 1) : $key,
            'title' => [
                'es' => Str::ucfirst(fake('es_ES')->words(3, true)),
                'en' => Str::ucfirst(fake('en_US')->words(3, true)),
            ],
            'subtitle' => [
                'es' => fake('es_ES')->sentence(10),
                'en' => fake('en_US')->sentence(10),
            ],
            'body' => [
                'es' => '<p>'.implode('</p><p>', fake('es_ES')->paragraphs(3)).'</p>',
                'en' => '<p>'.implode('</p><p>', fake('en_US')->paragraphs(3)).'</p>',
            ],
            'sections' => null,
            'meta_title' => [
                'es' => fake('es_ES')->sentence(6),
                'en' => fake('en_US')->sentence(6),
            ],
            'meta_description' => [
                'es' => fake('es_ES')->sentence(20),
                'en' => fake('en_US')->sentence(20),
            ],
        ];
    }

    public function home(): static
    {
        return $this->state(fn () => ['key' => Page::HOME]);
    }

    public function about(): static
    {
        return $this->state(fn () => ['key' => Page::ABOUT]);
    }

    public function contact(): static
    {
        return $this->state(fn () => ['key' => Page::CONTACT]);
    }
}
