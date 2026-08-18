<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Solution;
use App\Support\PublicContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Carga la arquitectura de contenido v2 (storage/migration/content-v2.json) en
 * los modelos del sitio.
 *
 * A diferencia de WordPressContentSeeder —que trasladaba el sitio anterior tal
 * cual— este seeder aplica la estrategia de contenido acordada:
 * ver storage/migration/CONTENT-STRATEGY.md.
 *
 * Los datos que no se pudieron verificar quedan como "[PENDIENTE: …]" visibles
 * en el panel. Nunca se inventó una cifra, una certificación ni un cliente.
 *
 * Es idempotente: se puede correr las veces que haga falta.
 */
class ContentV2Seeder extends Seeder
{
    protected const SOURCE = 'migration/content-v2.json';

    /** @var array<string, mixed> */
    protected array $c = [];

    public function run(): void
    {
        $path = storage_path(self::SOURCE);

        if (! File::exists($path)) {
            throw new RuntimeException("No se encontró {$path}.");
        }

        $this->c = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        $this->seedSettings();
        $this->seedServices();
        $this->seedSolutions();
        $this->seedPages();
        $this->seedResourceDrafts();

        $this->command?->info('Contenido v2 cargado.');
    }

    protected function seedSettings(): void
    {
        $s = $this->c['site_settings'];

        SiteSetting::current()->fill([
            'company_name' => $s['company_name'],
            'email' => $s['email']['value'],
            'phone' => $s['phone']['value'],
            'address' => $this->pair($s['address']),
            'social_links' => $s['social_links']['value'],
            'footer_text' => $this->pair($s['footer_text']),
            'meta_title' => $this->pair($s['meta_title']),
            'meta_description' => $this->pair($s['meta_description']),
        ])->save();
    }

    /**
     * Los 6 servicios, con la profundidad acordada: a quién aplica, método por
     * fases, entregables, duración, participación del cliente y resultado.
     */
    protected function seedServices(): void
    {
        foreach ($this->c['services'] as $data) {
            // Se emparejan por el ícono del sitio anterior, que es el único
            // identificador estable entre la v1 y la v2.
            $service = Service::query()->firstOrNew(['icon' => $data['icon']]);

            $service->fill([
                'title' => $this->pair($data['name']),
                'summary' => $this->pair($data['summary']),
                'body' => [
                    'es' => $this->serviceBody($data, 'es'),
                    'en' => $this->serviceBody($data, 'en'),
                ],
                'icon' => $data['icon'],
                'order' => $data['order'],
                'is_published' => true,
            ]);

            // Los slugs se fijan explícitamente: el trait sólo autogenera los
            // que faltan, y aquí cambian respecto a la carga anterior.
            foreach ($this->locales() as $locale) {
                $service->setTranslation('slug', $locale, $data['slug'][$locale]);
            }

            $service->save();
        }
    }

    /**
     * Compone el cuerpo de la página de servicio a partir de los bloques
     * estructurados del JSON.
     */
    protected function serviceBody(array $d, string $locale): string
    {
        $h = fn (string $t) => '<h2>'.e($t).'</h2>';
        $p = fn (?string $t) => $t ? '<p>'.e($t).'</p>' : '';
        $t = fn (array $node) => $node[$locale] ?? null;

        // Una sección cuyo dato todavía no está confirmado se envuelve para
        // que se vea en el panel pero no en el sitio público
        // (ver App\Support\PublicContent).
        $section = function (string $heading, string $content) {
            $html = '<h2>'.e($heading).'</h2>'.$content;

            return str_contains($content, '[PENDIENTE')
                ? '<div data-pendiente="1">'.$html.'</div>'
                : $html;
        };

        $html = $p($t($d['applies_to']));

        $phases = '<ol>';
        foreach ($d['method']['phases'] as $phase) {
            $phases .= '<li>'.e($t($phase)).'</li>';
        }
        $phases .= '</ol>';
        $html .= $section($t($d['method']['heading']), $phases);

        $deliverables = '<ul>';
        foreach ($d['deliverables'] as $item) {
            $deliverables .= '<li>'.e($t($item)).'</li>';
        }
        $deliverables .= '</ul>';
        $html .= $h($locale === 'es' ? 'Entregables' : 'Deliverables').$deliverables;

        $html .= $section(
            $locale === 'es' ? 'Duración típica' : 'Typical duration',
            $p($t($d['duration'])),
        );

        $html .= $section(
            $locale === 'es' ? 'Participación de tu equipo' : "Your team's involvement",
            $p($t($d['client_involvement'])),
        );

        $html .= $section(
            $locale === 'es' ? 'Resultado' : 'Outcome',
            $p($t($d['outcome'])),
        );

        return $html;
    }

    /**
     * Las soluciones (disciplinas de HOPEX): eje "qué problema de negocio
     * resuelve", complementario a los servicios ("cómo lo entrega Grupo
     * Edima"). Alcance conservador: sólo las 4 disciplinas con evidencia de
     * práctica actual — ver storage/migration/ANALISIS-REFERENCIA.md A5.
     */
    protected function seedSolutions(): void
    {
        // Icono de cada servicio, para relacionar solutions[].related_services
        // (ids del JSON) con los registros ya guardados en seedServices().
        $iconById = collect($this->c['services'])->pluck('icon', 'id');

        foreach ($this->c['solutions'] as $data) {
            $solution = Solution::query()->firstOrNew(['title->es' => $data['name']['es']]);

            $solution->fill([
                'title' => $this->pair($data['name']),
                'summary' => $this->pair($data['intro']),
                'body' => [
                    'es' => $this->solutionBody($data, 'es'),
                    'en' => $this->solutionBody($data, 'en'),
                ],
                'cta_label' => $this->pair($data['cta']),
                'order' => $data['order'],
                'is_published' => true,
            ]);

            foreach ($this->locales() as $locale) {
                $solution->setTranslation('slug', $locale, $data['slug'][$locale]);
            }

            $solution->save();

            $serviceIds = collect($data['related_services'])
                ->map(fn (string $id) => $iconById->get($id))
                ->filter()
                ->map(fn (string $icon) => Service::query()->where('icon', $icon)->value('id'))
                ->filter()
                ->all();

            $solution->services()->sync($serviceIds);
        }
    }

    /**
     * Compone el cuerpo de la página de solución: qué resuelve, preguntas que
     * responde y cómo lo implementamos (el eje diferencial frente al
     * fabricante, ver ANALISIS-REFERENCIA.md A4).
     */
    protected function solutionBody(array $d, string $locale): string
    {
        $t = fn (array $node) => $node[$locale] ?? null;

        $html = '<p>'.e($t($d['intro'])).'</p>';

        $html .= '<h2>'.e($t($d['questions']['heading'])).'</h2><ul>';
        foreach ($d['questions']['items'] as $item) {
            $html .= '<li>'.e($t($item)).'</li>';
        }
        $html .= '</ul>';

        $html .= '<h2>'.e($t($d['how_we_implement']['heading'])).'</h2>';
        $html .= '<p>'.e($t($d['how_we_implement']['body'])).'</p>';

        return $html;
    }

    protected function seedPages(): void
    {
        $pages = $this->c['pages'];

        // --- Inicio: los bloques van en `sections` (JSON traducible).
        $this->page(Page::HOME, $pages['home'], function (string $locale) use ($pages) {
            $blocks = collect($pages['home']['blocks'])->keyBy('id');

            return [
                'hero' => [
                    'eyebrow' => $blocks['hero']['eyebrow'][$locale],
                    'subtitle' => $blocks['hero']['subtitle'][$locale],
                    'cta_primary' => $blocks['hero']['cta_primary'][$locale],
                    'cta_secondary' => $blocks['hero']['cta_secondary'][$locale],
                ],
                'intro' => [
                    'heading' => $blocks['intro']['heading'][$locale],
                    'body' => $blocks['intro']['body'][$locale],
                ],
                'services_summary' => [
                    'heading' => $blocks['services_summary']['heading'][$locale],
                    'subheading' => $blocks['services_summary']['subheading'][$locale],
                ],
                'hopex_teaser' => [
                    'heading' => $blocks['hopex_teaser']['heading'][$locale],
                    'body' => $blocks['hopex_teaser']['body'][$locale],
                    'cta' => $blocks['hopex_teaser']['cta'][$locale],
                ],
                'approach' => [
                    'heading' => $blocks['approach']['heading'][$locale],
                    'items' => collect($blocks['approach']['items'])->map(fn ($i) => [
                        'title' => $i['title'][$locale],
                        'description' => $i['description'][$locale],
                    ])->all(),
                ],
                'capabilities' => [
                    'heading' => $blocks['capabilities']['heading'][$locale],
                    'subheading' => $blocks['capabilities']['subheading'][$locale] ?? null,
                    'items' => collect($blocks['capabilities']['items'])->map(fn ($i) => [
                        'title' => $i['title'][$locale],
                        'description' => $i['description'][$locale],
                        'slug' => $i['slug'][$locale] ?? null,
                    ])->all(),
                ],
                // Sin publicar hasta tener autorización de uso de marca.
                'clients' => [
                    'published' => false,
                    'heading' => $blocks['clients']['heading'][$locale],
                    'note' => $blocks['clients']['note'],
                    'items' => $blocks['clients']['items'],
                ],
                // Sin publicar hasta tener cifras reales.
                'facts' => [
                    'published' => false,
                    'items' => collect($blocks['facts']['items'])->map(fn ($i) => [
                        'label' => $i['label'][$locale],
                        'value' => $i['value'],
                    ])->all(),
                ],
                'cta_final' => [
                    'heading' => $blocks['cta_final']['heading'][$locale],
                    'body' => $blocks['cta_final']['body'][$locale],
                    'cta' => $blocks['cta_final']['cta'][$locale],
                ],
            ];
        });

        // --- Plataforma HOPEX
        $hopex = $this->c['pages']['hopex'];
        $blocks = collect($hopex['blocks'])->keyBy('id');
        $this->page(Page::HOPEX, $hopex, null, function (string $locale) use ($blocks) {
            $html = '<p>'.e($blocks['hopex_intro']['body'][$locale]).'</p>';

            $html .= '<h2>'.e($blocks['hopex_questions']['heading'][$locale]).'</h2><ul>';
            foreach ($blocks['hopex_questions']['items'] as $i) {
                $html .= '<li>'.e($i[$locale]).'</li>';
            }
            $html .= '</ul>';

            // Los módulos concretos están sin confirmar: el bloque queda
            // visible en el panel pero oculto en el sitio público.
            $html .= '<div data-pendiente="1"><h2>'.e($blocks['hopex_modules']['heading'][$locale]).'</h2>';
            $html .= '<p>'.e($blocks['hopex_modules']['body'][$locale]).'</p></div>';

            $html .= '<h2>'.e($blocks['hopex_frameworks']['heading'][$locale]).'</h2>';
            $html .= '<p>'.e($blocks['hopex_frameworks']['body'][$locale]).'</p>';

            return $html;
        }, subtitleFrom: $blocks['hopex_intro']['subtitle']);

        // --- Nosotros
        $about = $this->c['pages']['about'];
        $aboutBlocks = collect($about['blocks'])->keyBy('id');
        $this->page(Page::ABOUT, $about, null, function (string $locale) use ($aboutBlocks) {
            $html = '';
            foreach (['about_who', 'about_mission', 'about_frameworks', 'about_credentials'] as $key) {
                $b = $aboutBlocks[$key];
                $paragraphs = array_map(
                    fn ($para) => $this->paragraph(trim($para)),
                    preg_split("/\n\n+/", $b['body'][$locale]),
                );
                $body = implode('', $paragraphs);
                $heading = '<h2>'.e($b['heading'][$locale]).'</h2>';

                // Si tras retirar lo pendiente el bloque queda sin nada que
                // decir, se oculta también el encabezado; si conserva prosa
                // real, el encabezado se mantiene.
                $html .= PublicContent::isEmpty($body)
                    ? '<div data-pendiente="1">'.$heading.$body.'</div>'
                    : $heading.$body;
            }

            return $html;
        }, subtitleFrom: $about['subtitle']);

        // --- Contacto
        $contact = $this->c['pages']['contact'];
        $this->page(Page::CONTACT, $contact, null, fn (string $locale) => '<p>'.e($contact['body'][$locale]).'</p>',
            subtitleFrom: $contact['subtitle']);
    }

    /**
     * Crea o actualiza una página institucional.
     *
     * @param  callable(string):array|null  $sections
     * @param  callable(string):string|null  $body
     * @param  array<string,string>|null  $subtitleFrom
     */
    protected function page(string $key, array $data, ?callable $sections = null, ?callable $body = null, ?array $subtitleFrom = null): void
    {
        $page = Page::query()->firstOrNew(['key' => $key]);

        $attributes = [
            'key' => $key,
            'title' => $this->pair($data['h1']),
            'meta_title' => $this->pair($data['seo']['title']),
            'meta_description' => $this->pair($data['seo']['meta_description']),
        ];

        if ($subtitleFrom !== null) {
            $attributes['subtitle'] = $this->pair($subtitleFrom);
        }

        if ($sections !== null) {
            $attributes['sections'] = $this->perLocale($sections);
        }

        if ($body !== null) {
            $attributes['body'] = $this->perLocale($body);
        }

        $page->fill($attributes)->save();
    }

    /**
     * Los 10 títulos propuestos, como borradores SIN publicar: son propuestas
     * de tema, no artículos escritos.
     */
    protected function seedResourceDrafts(): void
    {
        foreach ($this->c['resources_proposed']['items'] as $item) {
            // Se busca por el título en español, que es único por propuesta.
            $post = Post::query()->where('title->es', $item['es'])->first() ?? new Post;

            $post->fill([
                'title' => $this->pair($item),
                'excerpt' => [
                    'es' => '[PENDIENTE: redactar el artículo. Público objetivo: '.$item['audience'].']',
                    'en' => '[PENDIENTE: write the article. Target audience: '.$item['audience'].']',
                ],
                'category' => ['es' => 'Propuesta', 'en' => 'Proposed'],
                'is_published' => false,
                'published_at' => null,
            ])->save();
        }
    }

    /**
     * Convierte un párrafo en HTML, aislando los marcadores "[PENDIENTE: …]"
     * en su propio bloque oculto. Así un párrafo con prosa utilizable y un
     * dato sin confirmar no se pierde entero en el sitio público.
     */
    protected function paragraph(string $text): string
    {
        if (! str_contains($text, '[PENDIENTE')) {
            return '<p>'.e($text).'</p>';
        }

        $pending = [];
        $clean = preg_replace_callback(
            '/\[PENDIENTE[^\]]*\]/u',
            function (array $m) use (&$pending) {
                $pending[] = $m[0];

                return '';
            },
            $text,
        );

        $html = filled(trim((string) $clean)) ? '<p>'.e(trim((string) $clean)).'</p>' : '';

        foreach ($pending as $marker) {
            $html .= '<div data-pendiente="1"><p>'.e($marker).'</p></div>';
        }

        return $html;
    }

    /**
     * @return array<string, string|null>
     */
    protected function pair(array $node): array
    {
        return collect($this->locales())
            ->mapWithKeys(fn (string $l) => [$l => $node[$l] ?? null])
            ->all();
    }

    /**
     * @param  callable(string):mixed  $fn
     * @return array<string, mixed>
     */
    protected function perLocale(callable $fn): array
    {
        return collect($this->locales())
            ->mapWithKeys(fn (string $l) => [$l => $fn($l)])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function locales(): array
    {
        return array_keys(config('site.locales'));
    }
}
