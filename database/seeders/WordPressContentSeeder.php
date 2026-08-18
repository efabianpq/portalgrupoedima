<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Migra el contenido del sitio anterior en WordPress (grupoedima.com) a los
 * modelos del sitio nuevo, a partir de storage/migration/content.json.
 *
 * El sitio de origen estaba SÓLO en inglés (su página /es/ devolvía 404), así
 * que el inglés se transcribe literal del JSON y el español se redactó como
 * traducción para revisión de la persona editora. Ver
 * storage/migration/EXTRACTION-REPORT.md para el detalle de lo que había y lo
 * que faltaba en el origen.
 *
 * Es idempotente: se puede correr varias veces sin duplicar contenido.
 */
class WordPressContentSeeder extends Seeder
{
    /** Ruta del JSON producido por la extracción. */
    protected const SOURCE = 'migration/content.json';

    /** @var array<string, mixed> */
    protected array $source = [];

    public function run(): void
    {
        $path = storage_path(self::SOURCE);

        if (! File::exists($path)) {
            throw new RuntimeException("No se encontró {$path}. Corre primero la extracción.");
        }

        $this->source = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        $this->seedSiteSettings();
        $this->seedServices();
        $this->seedHome();
        $this->seedAbout();
        $this->seedContact();
        $this->copyClientLogos();

        $this->command?->info('Contenido de WordPress migrado.');
    }

    /**
     * Datos globales. Ojo: los enlaces de redes sociales del sitio anterior
     * eran los valores por defecto del tema (twitter.com/, facebook.com/, …),
     * no perfiles reales, así que NO se migran: quedan vacíos hasta que se
     * tengan las URLs verdaderas.
     */
    protected function seedSiteSettings(): void
    {
        $s = SiteSetting::current();

        $s->fill([
            'company_name' => 'Grupo Edima',
            'email' => $this->source['top_bar']['email'] ?? null,
            'phone' => $this->source['top_bar']['phone'] ?? null,
            'address' => [
                'es' => 'Colombia',
                'en' => 'Colombia',
            ],
            'social_links' => [],
            'footer_text' => [
                'es' => 'Expertos en arquitectura empresarial y transformación digital.',
                'en' => 'Enterprise Architecture & Digital Transformation Experts.',
            ],
            // El sitio anterior no tenía <title> ni meta description en ninguna
            // página: estos textos son nuevos, para revisión.
            'meta_title' => [
                'es' => 'Grupo Edima — Consultoría en arquitectura empresarial y HOPEX',
                'en' => 'Grupo Edima — Enterprise Architecture & HOPEX Consulting',
            ],
            'meta_description' => [
                'es' => 'Consultora colombiana especializada en la implementación, personalización y adopción de la plataforma HOPEX de Bizzdesign para arquitectura empresarial, gobierno de TI, gestión de riesgos y transformación digital.',
                'en' => 'Colombian consulting firm specialized in the implementation, customization and adoption of the HOPEX platform by Bizzdesign for enterprise architecture, IT governance, risk management and digital transformation.',
            ],
        ])->save();
    }

    /**
     * Los 6 servicios del sitio anterior.
     *
     * `summary` = la descripción del servicio (sirve para las tarjetas).
     * `body`    = la descripción más el llamado a la acción, como HTML.
     * `icon`    = el archivo de ícono del tema anterior, sólo como referencia:
     *             son PNG de 85×85 genéricos, no material de marca, y por eso
     *             NO se cargan como imagen del servicio (las tarjetas muestran
     *             el marcador de posición de marca hasta que haya fotos reales).
     */
    protected function seedServices(): void
    {
        foreach ($this->servicesContent() as $order => $data) {
            $service = Service::query()->firstOrNew([
                'icon' => $data['icon'],
            ]);

            $service->fill([
                'title' => $data['title'],
                'summary' => $data['summary'],
                'body' => [
                    'es' => '<p>'.e($data['summary']['es']).'</p><p>'.e($data['cta']['es']).'</p>',
                    'en' => '<p>'.e($data['summary']['en']).'</p><p>'.e($data['cta']['en']).'</p>',
                ],
                'icon' => $data['icon'],
                'order' => $order + 1,
                'is_published' => true,
            ])->save();
        }
    }

    /**
     * Inglés: transcrito literal de content.json (sitio anterior).
     * Español: traducción redactada para esta migración, pendiente de revisión.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function servicesContent(): array
    {
        $en = collect($this->source['services']['items'] ?? [])
            ->keyBy(fn ($s) => basename((string) $s['icon_url']));

        $es = [
            'serviceicon1.png' => [
                'title' => 'Implementación',
                'summary' => 'Implementamos la plataforma HOPEX de Bizzdesign siguiendo las mejores prácticas del sector, asegurando la alineación con tu marco de arquitectura empresarial, tu modelo de gobierno y tus objetivos estratégicos. Nuestro enfoque garantiza un despliegue estructurado, seguro y orientado a generar valor, adaptado a las necesidades de tu organización.',
                'cta' => 'Comienza tu recorrido con HOPEX mediante una implementación estructurada y sin riesgos. Deja que nuestros expertos diseñen y desplieguen la base de arquitectura adecuada para tu organización.',
            ],
            'serviceicon2.png' => [
                'title' => 'Capacitación',
                'summary' => 'Impartimos programas de formación especializados para arquitectos, líderes de gobierno y equipos técnicos, que hacen posible una adopción efectiva de la plataforma HOPEX y la generación de valor a largo plazo.',
                'cta' => 'Dale a tus equipos el conocimiento que necesitan para impulsar la transformación. Agenda hoy una sesión de formación en HOPEX adaptada a tu organización.',
            ],
            'serviceicon3.png' => [
                'title' => 'Desarrollo a la medida',
                'summary' => 'Desarrollamos configuraciones, integraciones y extensiones a la medida dentro del ecosistema HOPEX para responder a tus requisitos específicos de negocio y regulatorios. Nuestras soluciones son escalables, preparadas para el futuro y alineadas con tu hoja de ruta de transformación digital.',
                'cta' => 'Aprovecha todo el potencial de HOPEX con soluciones diseñadas específicamente para tu organización. Construyamos lo que tu estrategia exige.',
            ],
            'serviceicon4.png' => [
                'title' => 'Soporte',
                'summary' => 'Nuestros servicios especializados de soporte para HOPEX aseguran la estabilidad de la plataforma, la optimización del rendimiento y la mejora continua. Resolvemos las incidencias a tiempo y ofrecemos orientación proactiva para mantener la excelencia operativa.',
                'cta' => 'Garantiza la continuidad del negocio con soporte experto y confiable. Trabaja con nosotros para mantener tu entorno HOPEX funcionando al máximo rendimiento.',
            ],
            'serviceicon5.png' => [
                'title' => 'Migración',
                'summary' => 'Gestionamos la migración de repositorios de arquitectura heredados, modelos de datos y estructuras de gobierno hacia HOPEX con precisión y control. Nuestra metodología reduce al mínimo la interrupción operativa y protege la información crítica del negocio.',
                'cta' => 'Moderniza tu entorno de arquitectura sin comprometer la integridad de los datos. Habla con nuestro equipo para planear una estrategia de migración sin sobresaltos.',
            ],
            'serviceicon6.png' => [
                'title' => 'Acompañamiento',
                'summary' => 'Brindamos servicios de asesoría estratégica para guiar a las organizaciones en su madurez de arquitectura empresarial, la optimización del gobierno de TI y las iniciativas de transformación digital apoyadas en HOPEX.',
                'cta' => 'Acelera tu proceso de transformación con orientación experta en cada etapa. Definamos juntos el siguiente paso para tu organización.',
            ],
        ];

        $out = [];
        foreach ($es as $icon => $spanish) {
            $english = $en->get($icon);

            $out[] = [
                'icon' => $icon,
                'title' => [
                    'es' => $spanish['title'],
                    // El origen venía en MAYÚSCULAS por estilo del tema; se
                    // normaliza a capitalización normal (el diseño nuevo ya
                    // aplica mayúsculas por CSS donde hace falta).
                    'en' => ucfirst(strtolower((string) ($english['name'] ?? ''))),
                ],
                'summary' => [
                    'es' => $spanish['summary'],
                    'en' => (string) ($english['description'] ?? ''),
                ],
                'cta' => [
                    'es' => $spanish['cta'],
                    'en' => (string) ($english['call_to_action'] ?? ''),
                ],
            ];
        }

        return $out;
    }

    /**
     * Página de inicio: el carrusel, "por qué elegirnos", la experiencia, las
     * cifras y los clientes van en `sections` (JSON traducible), porque son
     * bloques propios de esta página y no entidades reutilizables.
     */
    protected function seedHome(): void
    {
        $slides = $this->source['hero_slides'] ?? [];
        $choose = $this->source['why_choose_us']['items'] ?? [];
        $skills = $this->source['expertise']['skills'] ?? [];
        $facts = $this->source['facts']['items'] ?? [];
        $clients = $this->source['clients']['items'] ?? [];

        $heroEs = [
            [
                'eyebrow' => 'Grupo Edima — socio oficial de Bizzdesign HOPEX',
                'title' => 'Arquitectura empresarial',
                'subtitle' => 'Ayudamos a las organizaciones a alinear estrategia, tecnología y capacidades de negocio a través de HOPEX.',
                'paragraph' => 'Habilitamos a empresas de toda América Latina para diseñar, gobernar y hacer evolucionar sus ecosistemas de arquitectura con claridad, estructura y valor medible. Desde la optimización del portafolio de TI hasta el gobierno del riesgo y la transformación basada en capacidades, ayudamos a los líderes a tomar decisiones informadas con confianza.',
            ],
            [
                'eyebrow' => 'Grupo Edima — expertos en portafolio de TI con HOPEX',
                'title' => 'Optimización y gobierno estratégico del portafolio de TI',
                'subtitle' => 'Alineamos las inversiones de TI con la estrategia de negocio y su valor medible.',
                'paragraph' => 'Ayudamos a las organizaciones a tener visibilidad completa de sus portafolios de aplicaciones, proyectos y tecnología mediante marcos de gobierno estructurados con HOPEX. Desde iniciativas de racionalización hasta la priorización de inversiones, habilitamos decisiones basadas en datos que maximizan el retorno y reducen el riesgo operativo.',
            ],
            [
                'eyebrow' => 'Grupo Edima — arquitectura de capacidades de negocio',
                'title' => 'Capacidades de negocio y transformación digital',
                'subtitle' => 'Diseñamos organizaciones orientadas a capacidades, listas para crecer de forma sostenible.',
                'paragraph' => 'Estructuramos y mapeamos las capacidades de negocio para conectar estrategia, operación y tecnología, creando una hoja de ruta de transformación clara y apoyada en HOPEX. Nuestro enfoque permite a los líderes priorizar iniciativas, ganar agilidad e impulsar la transformación de toda la empresa con claridad y gobierno.',
            ],
        ];

        $chooseEs = [
            [
                'title' => 'Metodología de implementación probada',
                'description' => 'Aplicamos un marco estructurado y probado para la implementación de HOPEX, asegurando la alineación con tu estrategia de arquitectura empresarial, tu modelo de gobierno y tus objetivos de negocio. Nuestro enfoque por fases reduce el riesgo y acelera el tiempo hasta obtener valor.',
            ],
            [
                'title' => 'Alianza estratégica a largo plazo',
                'description' => 'Nos enfocamos en entregar valor de negocio medible, no sólo en desplegar tecnología. Cada iniciativa se diseña para optimizar procesos, fortalecer el gobierno, gestionar el riesgo y alinear la TI con la estrategia corporativa.',
            ],
            [
                'title' => 'Enfoque orientado al negocio',
                'description' => 'Construimos alianzas duraderas mediante asesoría continua, transferencia de conocimiento y soporte experto. Nuestro objetivo es empoderar a tus equipos y asegurar la evolución sostenible de tu entorno HOPEX.',
            ],
        ];

        $skillsEs = [
            'Capacidades de negocio y alineación estratégica',
            'Gestión y gobierno del portafolio de TI',
            'Riesgo, cumplimiento e integración GRC',
            'Arquitectura y automatización de procesos de negocio',
        ];

        // Las etiquetas de las cifras ya venían en español en el sitio anterior
        // (era la única sección en español); el inglés es traducción nueva.
        $factsEn = [
            'Support hours',
            'Clients',
            'Projects',
            'Training hours',
        ];

        $expertiseEs = 'En Grupo Edima, nuestra experiencia combina un dominio técnico profundo de la plataforma HOPEX con capacidades de asesoría estratégica en arquitectura empresarial. Nos especializamos en la instalación, personalización, migración y optimización de entornos HOPEX, asegurando que la plataforma quede plenamente alineada con el modelo de gobierno y los objetivos de transformación de cada organización. Nuestra experiencia abarca el modelado de capacidades de negocio y la alineación con la estrategia, la gestión del portafolio de TI, los marcos de riesgo y cumplimiento (GRC) y el diseño y automatización de procesos de negocio. Acompañamos a las organizaciones desde la definición de capacidades y la estructuración de cadenas de valor hasta la racionalización de aplicaciones, el gobierno del riesgo y las iniciativas de optimización de procesos. Al integrar excelencia técnica con visión arquitectónica, permitimos que las empresas conviertan HOPEX en una plataforma estratégica de apoyo a la decisión, conectando la estrategia de negocio, las inversiones tecnológicas, los controles de gobierno y la ejecución operativa en un ecosistema único y estructurado.';

        $build = function (string $locale) use ($slides, $choose, $skills, $facts, $clients, $heroEs, $chooseEs, $skillsEs, $factsEn, $expertiseEs) {
            $es = $locale === 'es';

            return [
                'hero' => collect($slides)->map(fn ($s, $i) => [
                    'eyebrow' => $es ? ($heroEs[$i]['eyebrow'] ?? null) : $s['eyebrow'],
                    'title' => $es ? ($heroEs[$i]['title'] ?? null) : $s['title'],
                    'subtitle' => $es ? ($heroEs[$i]['subtitle'] ?? null) : $s['subtitle'],
                    'paragraph' => $es ? ($heroEs[$i]['paragraph'] ?? null) : $s['paragraph'],
                ])->all(),

                'why_choose_us' => [
                    'heading' => $es ? 'Por qué elegirnos' : ($this->source['why_choose_us']['heading'] ?? null),
                    'items' => collect($choose)->map(fn ($b, $i) => [
                        'title' => $es ? ($chooseEs[$i]['title'] ?? null) : $b['title'],
                        'description' => $es ? ($chooseEs[$i]['description'] ?? null) : $b['description'],
                    ])->all(),
                ],

                'expertise' => [
                    'heading' => $es ? 'Somos expertos en' : ($this->source['expertise']['heading'] ?? null),
                    'paragraph' => $es ? $expertiseEs : ($this->source['expertise']['paragraphs'][0] ?? null),
                    'skills' => collect($skills)->map(fn ($s, $i) => [
                        'label' => $es ? ($skillsEs[$i] ?? null) : $s['label'],
                        'percent' => $s['percent'],
                    ])->all(),
                ],

                'facts' => [
                    'heading' => $es ? 'Algunas cifras sobre nosotros' : ($this->source['facts']['heading'] ?? null),
                    'items' => collect($facts)->map(fn ($f, $i) => [
                        'value' => $f['value'],
                        'label' => $es ? $f['label'] : ($factsEn[$i] ?? null),
                    ])->all(),
                ],

                'clients' => [
                    'heading' => $es ? 'Nuestros clientes' : 'Our Clients',
                    'items' => collect($clients)->map(fn ($c) => [
                        'name' => $c['inferred_name'],
                        'logo' => 'images/clientes/'.basename((string) $c['logo_url']),
                    ])->all(),
                ],
            ];
        };

        $page = Page::query()->firstOrNew(['key' => Page::HOME]);
        $page->fill([
            'key' => Page::HOME,
            'title' => ['es' => 'Inicio', 'en' => 'Home'],
            'subtitle' => [
                'es' => $heroEs[0]['subtitle'],
                'en' => $slides[0]['subtitle'] ?? null,
            ],
            'sections' => [
                'es' => $build('es'),
                'en' => $build('en'),
            ],
            'meta_title' => [
                'es' => 'Grupo Edima — Arquitectura empresarial y HOPEX en Colombia',
                'en' => 'Grupo Edima — Enterprise Architecture & HOPEX in Colombia',
            ],
            'meta_description' => [
                'es' => 'Diseñamos, gobernamos y hacemos evolucionar ecosistemas de arquitectura empresarial con HOPEX de Bizzdesign, en toda América Latina.',
                'en' => 'We design, govern and evolve enterprise architecture ecosystems with HOPEX by Bizzdesign, across Latin America.',
            ],
        ])->save();
    }

    /**
     * Página "Nosotros": encabezado, misión y los 4 focos de trabajo.
     */
    protected function seedAbout(): void
    {
        $a = $this->source['about'] ?? [];

        $bulletsEs = [
            'Arquitectura empresarial',
            'Gobierno de TI',
            'Gestión de riesgos',
            'Transformación digital',
        ];

        $listEs = collect($bulletsEs)->map(fn ($b) => '<li>'.e($b).'</li>')->implode('');
        $listEn = collect($a['bullets'] ?? [])->map(fn ($b) => '<li>'.e($b).'</li>')->implode('');

        $page = Page::query()->firstOrNew(['key' => Page::ABOUT]);
        $page->fill([
            'key' => Page::ABOUT,
            'title' => ['es' => 'Nosotros', 'en' => 'About Us'],
            'subtitle' => [
                'es' => 'Grupo Edima es una consultora colombiana especializada en la implementación, personalización y adopción de la plataforma HOPEX de Bizzdesign.',
                'en' => $a['subheading'] ?? null,
            ],
            'body' => [
                'es' => '<p><strong>Nuestra misión:</strong> entregar soluciones integrales basadas en la plataforma HOPEX de Bizzdesign, que permitan a las organizaciones optimizar su arquitectura empresarial, su gobierno de TI, su gestión de riesgos y sus iniciativas de transformación digital.</p><ul>'.$listEs.'</ul>',
                'en' => '<p>'.e($a['paragraphs'][0] ?? '').'</p><ul>'.$listEn.'</ul>',
            ],
            'meta_title' => [
                'es' => 'Nosotros — Grupo Edima',
                'en' => 'About Us — Grupo Edima',
            ],
            'meta_description' => [
                'es' => 'Consultora colombiana especializada en la implementación, personalización y adopción de HOPEX de Bizzdesign.',
                'en' => 'Colombian consulting firm specialized in the implementation, customization and adoption of HOPEX by Bizzdesign.',
            ],
        ])->save();
    }

    /**
     * Página de contacto. El sitio anterior no tenía contenido aquí (era un
     * stub vacío), así que el texto es nuevo; los datos de contacto salen de
     * SiteSetting, no de esta página.
     */
    protected function seedContact(): void
    {
        $page = Page::query()->firstOrNew(['key' => Page::CONTACT]);
        $page->fill([
            'key' => Page::CONTACT,
            'title' => ['es' => 'Contacto', 'en' => 'Contact'],
            'subtitle' => [
                'es' => 'Cuéntanos en qué punto está tu organización y cómo podemos ayudarte.',
                'en' => 'Tell us where your organization stands and how we can help.',
            ],
            'meta_title' => [
                'es' => 'Contacto — Grupo Edima',
                'en' => 'Contact — Grupo Edima',
            ],
            'meta_description' => [
                'es' => 'Escríbenos para conversar sobre tus iniciativas de arquitectura empresarial, gobierno de datos y HOPEX.',
                'en' => 'Get in touch to discuss your enterprise architecture, data governance and HOPEX initiatives.',
            ],
        ])->save();
    }

    /**
     * Copia los logos de clientes a public/ para poder mostrarlos. Son, junto
     * con el logo del sitio, las únicas imágenes propias del origen: el resto
     * eran ilustraciones genéricas del tema.
     */
    protected function copyClientLogos(): void
    {
        $dest = public_path('images/clientes');
        File::ensureDirectoryExists($dest);

        $paths = collect($this->source['assets'] ?? [])->keyBy('original_url');

        foreach ($this->source['clients']['items'] ?? [] as $client) {
            $asset = $paths->get($client['logo_url'] ?? '');

            if ($asset === null) {
                continue;
            }

            $from = base_path($asset['local_path']);

            if (File::exists($from)) {
                File::copy($from, $dest.'/'.basename($from));
            }
        }
    }
}
