<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * @var array<int, array{es: string, en: string}>
     */
    protected const ROLES = [
        ['es' => 'Director de arquitectura empresarial', 'en' => 'Enterprise Architecture Director'],
        ['es' => 'Consultora senior en gobierno de datos', 'en' => 'Senior Data Governance Consultant'],
        ['es' => 'Especialista en GRC', 'en' => 'GRC Specialist'],
        ['es' => 'Arquitecto de información', 'en' => 'Information Architect'],
        ['es' => 'Gerente de proyectos', 'en' => 'Project Manager'],
    ];

    public function definition(): array
    {
        static $sequence = 0;

        $index = $sequence++;
        $role = self::ROLES[$index % count(self::ROLES)];

        return [
            'name' => fake('es_ES')->name(),
            'role' => [
                'es' => $role['es'],
                'en' => $role['en'],
            ],
            'bio' => [
                'es' => fake('es_ES')->paragraph(4),
                'en' => fake('en_US')->paragraph(4),
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
