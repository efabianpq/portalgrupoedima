<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => 'Grupo Edima S.A.S.',
            'email' => 'contacto@grupoedima.com',
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'google_maps_url' => 'https://maps.google.com/',
            'address' => [
                'es' => 'Bogotá, Colombia',
                'en' => 'Bogotá, Colombia',
            ],
            'social_links' => [
                ['platform' => 'linkedin', 'url' => 'https://www.linkedin.com/company/grupo-edima'],
            ],
            'footer_text' => [
                'es' => 'Consultoría en arquitectura empresarial y gobierno de datos.',
                'en' => 'Enterprise architecture and data governance consulting.',
            ],
            'meta_title' => [
                'es' => 'Grupo Edima — Arquitectura empresarial y gobierno de datos',
                'en' => 'Grupo Edima — Enterprise architecture and data governance',
            ],
            'meta_description' => [
                'es' => fake('es_ES')->sentence(20),
                'en' => fake('en_US')->sentence(20),
            ],
        ];
    }
}
