<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Confirma que el sitio público responde en los dos idiomas y que el
 * contenido no publicado (is_published = false) nunca es visible: ni en los
 * listados ni entrando directo a su URL de detalle.
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // El sitio da por sentado que existen las páginas institucionales y
        // la fila de configuración; sin esto las rutas fijas igual
        // responderían (las vistas toleran $page nulo), pero no reflejarían
        // el comportamiento real de producción.
        SiteSetting::forgetCurrent();
        $this->seed(SiteContentSeeder::class);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function locales(): array
    {
        return [
            'español' => ['es'],
            'inglés' => ['en'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function paginasFijas(): array
    {
        return [
            'inicio' => ['home'],
            'hopex' => ['hopex'],
            'servicios' => ['services'],
            'proyectos' => ['projects'],
            'blog' => ['blog'],
            'equipo' => ['team'],
            'nosotros' => ['about'],
            'contacto' => ['contact'],
        ];
    }

    #[DataProvider('locales')]
    public function test_las_paginas_fijas_responden_200_en_el_idioma(string $locale): void
    {
        foreach (self::paginasFijas() as [$ruta]) {
            $this->get(route("{$locale}.{$ruta}"))->assertOk();
        }
    }

    #[DataProvider('locales')]
    public function test_el_detalle_de_un_servicio_publicado_responde_200(string $locale): void
    {
        $service = Service::factory()->create();

        $this->get(route("{$locale}.services.show", $service->slugFor($locale)))->assertOk();
    }

    #[DataProvider('locales')]
    public function test_el_detalle_de_un_proyecto_publicado_responde_200(string $locale): void
    {
        $project = Project::factory()->create();

        $this->get(route("{$locale}.projects.show", $project->slugFor($locale)))->assertOk();
    }

    #[DataProvider('locales')]
    public function test_el_detalle_de_una_entrada_de_blog_publicada_responde_200(string $locale): void
    {
        $post = Post::factory()->create();

        $this->get(route("{$locale}.blog.show", $post->slugFor($locale)))->assertOk();
    }

    public function test_un_servicio_sin_publicar_no_aparece_en_el_listado(): void
    {
        $publicado = Service::factory()->create();
        $borrador = Service::factory()->draft()->create();

        $response = $this->get(route('es.services'));

        $response->assertOk();
        $response->assertSee($publicado->title);
        $response->assertDontSee($borrador->title);
    }

    public function test_un_servicio_sin_publicar_devuelve_404_al_entrar_directo(): void
    {
        $borrador = Service::factory()->draft()->create();

        $this->get(route('es.services.show', $borrador->slugFor('es')))->assertNotFound();
    }

    public function test_un_proyecto_sin_publicar_no_aparece_en_el_listado(): void
    {
        $publicado = Project::factory()->create();
        $borrador = Project::factory()->draft()->create();

        $response = $this->get(route('es.projects'));

        $response->assertOk();
        $response->assertSee($publicado->title);
        $response->assertDontSee($borrador->title);
    }

    public function test_un_proyecto_sin_publicar_devuelve_404_al_entrar_directo(): void
    {
        $borrador = Project::factory()->draft()->create();

        $this->get(route('es.projects.show', $borrador->slugFor('es')))->assertNotFound();
    }

    public function test_una_entrada_de_blog_sin_publicar_no_aparece_en_el_listado(): void
    {
        $publicada = Post::factory()->create();
        $borrador = Post::factory()->draft()->create();

        $response = $this->get(route('es.blog'));

        $response->assertOk();
        $response->assertSee($publicada->title);
        $response->assertDontSee($borrador->title);
    }

    public function test_una_entrada_de_blog_sin_publicar_devuelve_404_al_entrar_directo(): void
    {
        $borrador = Post::factory()->draft()->create();

        $this->get(route('es.blog.show', $borrador->slugFor('es')))->assertNotFound();
    }

    public function test_una_entrada_de_blog_programada_a_futuro_no_aparece_en_el_listado(): void
    {
        $futura = Post::factory()->scheduled()->create();

        $response = $this->get(route('es.blog'));

        $response->assertOk();
        $response->assertDontSee($futura->title);
    }

    public function test_un_integrante_del_equipo_sin_publicar_no_aparece_en_el_listado(): void
    {
        $publicado = TeamMember::factory()->create();
        $borrador = TeamMember::factory()->draft()->create();

        $response = $this->get(route('es.team'));

        $response->assertOk();
        $response->assertSee($publicado->name);
        $response->assertDontSee($borrador->name);
    }

    /**
     * El menú es administrable desde el panel (App\Models\MenuItem): sólo
     * aparece lo que la persona editora publique ahí, sin lógica automática
     * ligada al contenido.
     */
    public function test_el_menu_solo_muestra_items_publicados(): void
    {
        MenuItem::factory()->create(['label' => ['es' => 'Enlace visible', 'en' => 'Visible link']]);
        MenuItem::factory()->draft()->create(['label' => ['es' => 'Enlace oculto', 'en' => 'Hidden link']]);

        $response = $this->get(route('es.home'));

        $response->assertOk();
        $response->assertSee('Enlace visible');
        $response->assertDontSee('Enlace oculto');
    }

    /**
     * Regresión: @section('meta_description', $valor) deja un output buffer
     * sin cerrar cuando $valor es null (Blade interpreta null como "modo
     * bloque, espera @endsection"). Pasaba en cualquier página cuyo propio
     * resumen y el meta_description global de SiteSetting estuvieran vacíos
     * a la vez —el estado por defecto de una instalación nueva—, y se
     * "tragaba" el resto del HTML de la página. Ver la vista pública: el
     * valor siempre debe caer a '' en vez de null.
     */
    public function test_una_pagina_sin_meta_description_propia_ni_global_no_pierde_contenido(): void
    {
        SiteSetting::forgetCurrent();
        SiteSetting::current()->update(['meta_description' => null]);
        SiteSetting::forgetCurrent();

        $response = $this->get(route('es.about'));

        $response->assertOk();
        // El pie de página va después del meta_description en el layout: si
        // el buffer se hubiera quedado abierto, esto nunca llegaría a salida.
        $response->assertSee(__('site.footer.rights'));
    }

    public function test_un_testimonio_sin_publicar_no_aparece_en_la_portada(): void
    {
        $publicado = Testimonial::factory()->create();
        $borrador = Testimonial::factory()->draft()->create();

        $response = $this->get(route('es.home'));

        $response->assertOk();
        $response->assertSee($publicado->quote);
        $response->assertDontSee($borrador->quote);
    }
}
