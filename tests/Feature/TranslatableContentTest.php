<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslatableContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_contenido_se_guarda_en_los_dos_idiomas(): void
    {
        $service = Service::factory()->create([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
        ]);

        $this->assertSame('Gobierno de datos', $service->getTranslation('title', 'es'));
        $this->assertSame('Data Governance', $service->getTranslation('title', 'en'));
    }

    public function test_devuelve_el_texto_del_idioma_activo(): void
    {
        $service = Service::factory()->create([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
        ]);

        app()->setLocale('es');
        $this->assertSame('Gobierno de datos', $service->fresh()->title);

        app()->setLocale('en');
        $this->assertSame('Data Governance', $service->fresh()->title);
    }

    public function test_resuelve_el_modelo_por_slug_del_idioma_activo(): void
    {
        Service::factory()->create([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
            'slug' => ['es' => 'gobierno-de-datos', 'en' => 'data-governance'],
        ]);

        app()->setLocale('es');
        $this->assertNotNull(Service::findBySlug('gobierno-de-datos'));
        // El slug en inglés no debe resolver dentro del sitio en español.
        $this->assertNull(Service::findBySlug('data-governance'));

        app()->setLocale('en');
        $this->assertNotNull(Service::findBySlug('data-governance'));
        $this->assertNull(Service::findBySlug('gobierno-de-datos'));
    }

    public function test_encuentra_por_slug_de_cualquier_idioma_para_redirigir(): void
    {
        Service::factory()->create([
            'slug' => ['es' => 'gobierno-de-datos', 'en' => 'data-governance'],
        ]);

        app()->setLocale('en');

        // Alguien llega a /en/services/gobierno-de-datos: se puede localizar el
        // contenido para redirigirlo a la URL correcta del idioma.
        $service = Service::findBySlugInAnyLocale('gobierno-de-datos');

        $this->assertNotNull($service);
        $this->assertSame('data-governance', $service->slugFor('en'));
    }

    public function test_la_url_usa_el_slug_del_idioma_activo(): void
    {
        $service = Service::factory()->create([
            'slug' => ['es' => 'gobierno-de-datos', 'en' => 'data-governance'],
        ]);

        app()->setLocale('es');
        $this->assertSame('gobierno-de-datos', $service->getRouteKey());

        app()->setLocale('en');
        $this->assertSame('data-governance', $service->getRouteKey());
    }

    public function test_la_url_cae_al_idioma_por_defecto_si_falta_la_traduccion(): void
    {
        $service = Service::factory()->create([
            'slug' => ['es' => 'gobierno-de-datos'],
        ]);

        app()->setLocale('en');

        // No hay slug en inglés: la URL usa el español en vez de quedar vacía.
        $this->assertSame('gobierno-de-datos', $service->getRouteKey());
    }

    public function test_la_base_de_datos_impide_slugs_repetidos_en_el_mismo_idioma(): void
    {
        Service::factory()->create(['slug' => ['es' => 'grc', 'en' => 'grc-en']]);

        $this->expectException(QueryException::class);

        Service::factory()->create(['slug' => ['es' => 'grc', 'en' => 'otro']]);
    }

    public function test_permite_el_mismo_texto_de_slug_en_idiomas_distintos(): void
    {
        Service::factory()->create(['slug' => ['es' => 'bizzdesign', 'en' => 'bizzdesign-en']]);
        Service::factory()->create(['slug' => ['es' => 'otro-servicio', 'en' => 'bizzdesign']]);

        // 'bizzdesign' existe como slug español de uno y como slug inglés de otro.
        $this->assertSame(2, Service::count());
    }

    public function test_varios_registros_pueden_quedar_sin_traducir_al_ingles(): void
    {
        Service::factory()->create(['slug' => ['es' => 'servicio-uno']]);
        Service::factory()->create(['slug' => ['es' => 'servicio-dos']]);

        // Los NULL de la columna generada slug_en no chocan entre sí.
        $this->assertSame(2, Service::count());
    }

    public function test_filtra_el_contenido_traducido_en_un_idioma(): void
    {
        Service::factory()->create(['slug' => ['es' => 'con-ingles', 'en' => 'with-english']]);
        Service::factory()->create(['slug' => ['es' => 'sin-ingles']]);

        $this->assertSame(2, Service::query()->translatedIn('es')->count());
        $this->assertSame(1, Service::query()->translatedIn('en')->count());
    }

    public function test_genera_los_slugs_que_falten_a_partir_del_titulo(): void
    {
        $service = Service::factory()->make([
            'title' => ['es' => 'Arquitectura de Información', 'en' => 'Information Architecture'],
            'slug' => ['es' => 'temporal'],
        ]);

        $service->generateMissingSlugs();

        // Respeta el slug español que ya existía y crea el inglés que faltaba.
        $this->assertSame('temporal', $service->getTranslation('slug', 'es'));
        $this->assertSame('information-architecture', $service->getTranslation('slug', 'en'));
    }

    public function test_al_generar_un_slug_repetido_le_agrega_un_sufijo(): void
    {
        Service::factory()->create(['slug' => ['es' => 'gobierno-de-datos', 'en' => 'data-governance']]);

        $otro = Service::factory()->make([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data governance'],
            'slug' => [],
        ]);

        $otro->generateMissingSlugs();

        $this->assertSame('gobierno-de-datos-2', $otro->getTranslation('slug', 'es'));
        $this->assertSame('data-governance-2', $otro->getTranslation('slug', 'en'));
    }

    public function test_solo_muestra_el_contenido_publicado(): void
    {
        Service::factory()->count(2)->create();
        Service::factory()->draft()->create();

        $this->assertSame(3, Service::count());
        $this->assertSame(2, Service::query()->published()->count());
    }

    public function test_no_muestra_entradas_de_blog_programadas_a_futuro(): void
    {
        Post::factory()->create(['published_at' => now()->subDay()]);
        Post::factory()->scheduled()->create();

        $this->assertSame(2, Post::count());
        $this->assertSame(1, Post::query()->published()->count());
    }

    public function test_relaciona_proyectos_con_servicios(): void
    {
        $servicios = Service::factory()->count(2)->create();
        $proyecto = Project::factory()->create();

        $proyecto->services()->attach($servicios->pluck('id'));

        $this->assertCount(2, $proyecto->fresh()->services);
        $this->assertCount(1, $servicios->first()->fresh()->projects);
    }

    public function test_el_nombre_del_cliente_no_es_traducible(): void
    {
        $proyecto = Project::factory()->create(['client_name' => 'Banco de Bogotá']);

        app()->setLocale('en');

        $this->assertSame('Banco de Bogotá', $proyecto->fresh()->client_name);
        $this->assertFalse($proyecto->isTranslatableAttribute('client_name'));
    }

    public function test_el_equipo_y_los_testimonios_traducen_solo_lo_que_corresponde(): void
    {
        $persona = TeamMember::factory()->create([
            'name' => 'Ana Gómez',
            'role' => ['es' => 'Arquitecta de datos', 'en' => 'Data Architect'],
        ]);

        $testimonio = Testimonial::factory()->create([
            'author_name' => 'Carlos Ruiz',
            'quote' => ['es' => 'Excelente acompañamiento.', 'en' => 'Excellent support.'],
        ]);

        $this->assertFalse($persona->isTranslatableAttribute('name'));
        $this->assertTrue($persona->isTranslatableAttribute('role'));
        $this->assertFalse($testimonio->isTranslatableAttribute('author_name'));
        $this->assertTrue($testimonio->isTranslatableAttribute('quote'));

        app()->setLocale('en');
        $this->assertSame('Ana Gómez', $persona->fresh()->name);
        $this->assertSame('Data Architect', $persona->fresh()->role);
    }

    public function test_las_paginas_institucionales_se_buscan_por_su_identificador(): void
    {
        Page::factory()->home()->create([
            'title' => ['es' => 'Inicio', 'en' => 'Home'],
        ]);

        $pagina = Page::key(Page::HOME);

        $this->assertNotNull($pagina);

        app()->setLocale('en');
        $this->assertSame('Home', Page::key(Page::HOME)->title);
    }

    public function test_la_configuracion_del_sitio_es_una_sola_fila(): void
    {
        SiteSetting::forgetCurrent();

        $primera = SiteSetting::current();
        SiteSetting::forgetCurrent();
        $segunda = SiteSetting::current();

        $this->assertTrue($primera->is($segunda));
        $this->assertSame(1, SiteSetting::count());
    }

    public function test_los_mensajes_de_contacto_no_son_traducibles_y_se_marcan_como_leidos(): void
    {
        $mensaje = ContactMessage::factory()->create(['locale' => 'en']);

        $this->assertFalse($mensaje->isRead());
        $this->assertSame(1, ContactMessage::query()->unread()->count());

        $mensaje->markAsRead();

        $this->assertTrue($mensaje->fresh()->isRead());
        $this->assertSame(0, ContactMessage::query()->unread()->count());
    }
}
