<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\TeamMemberResource;
use App\Filament\Resources\TestimonialResource;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // La memorización de SiteSetting vive por petición; entre pruebas hay
        // que olvidarla o quedaría apuntando a una fila de la prueba anterior.
        SiteSetting::forgetCurrent();

        $this->actingAs(User::factory()->create());
    }

    /**
     * @return array<string, array{0: class-string}>
     */
    public static function recursos(): array
    {
        return [
            'servicios' => [ServiceResource::class],
            'proyectos' => [ProjectResource::class],
            'equipo' => [TeamMemberResource::class],
            'blog' => [PostResource::class],
            'testimonios' => [TestimonialResource::class],
            'páginas' => [PageResource::class],
            'mensajes de contacto' => [ContactMessageResource::class],
        ];
    }

    /**
     * @param  class-string  $resource
     */
    #[DataProvider('recursos')]
    public function test_el_listado_de_cada_recurso_carga_sin_errores(string $resource): void
    {
        Livewire::test($resource::getPages()['index']->getPage())
            ->assertSuccessful();
    }

    public function test_los_formularios_de_creacion_cargan_sin_errores(): void
    {
        $paginasDeCreacion = [
            ServiceResource::class,
            ProjectResource::class,
            TeamMemberResource::class,
            PostResource::class,
            TestimonialResource::class,
        ];

        foreach ($paginasDeCreacion as $resource) {
            Livewire::test($resource::getPages()['create']->getPage())
                ->assertSuccessful();
        }
    }

    public function test_crea_un_servicio_en_los_dos_idiomas_desde_el_panel(): void
    {
        Livewire::test(ServiceResource::getPages()['create']->getPage())
            ->fillForm([
                'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
                'summary' => ['es' => 'Resumen en español', 'en' => 'English summary'],
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $servicio = Service::query()->firstOrFail();

        $this->assertSame('Gobierno de datos', $servicio->getTranslation('title', 'es'));
        $this->assertSame('Data Governance', $servicio->getTranslation('title', 'en'));
        // El slug de cada idioma se genera solo a partir de su propio título.
        $this->assertSame('gobierno-de-datos', $servicio->getTranslation('slug', 'es'));
        $this->assertSame('data-governance', $servicio->getTranslation('slug', 'en'));
        $this->assertTrue($servicio->is_published);
    }

    public function test_crea_una_entrada_de_blog_en_los_dos_idiomas(): void
    {
        Livewire::test(PostResource::getPages()['create']->getPage())
            ->fillForm([
                'title' => ['es' => 'Novedades de HOPEX', 'en' => 'HOPEX news'],
                'category' => ['es' => 'Noticias', 'en' => 'News'],
                'published_at' => now(),
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $entrada = Post::query()->firstOrFail();

        $this->assertSame('Novedades de HOPEX', $entrada->getTranslation('title', 'es'));
        $this->assertSame('HOPEX news', $entrada->getTranslation('title', 'en'));
        $this->assertSame('novedades-de-hopex', $entrada->getTranslation('slug', 'es'));
        $this->assertSame('hopex-news', $entrada->getTranslation('slug', 'en'));
    }

    public function test_crea_un_integrante_del_equipo_con_cargo_bilingue(): void
    {
        Livewire::test(TeamMemberResource::getPages()['create']->getPage())
            ->fillForm([
                'name' => 'Ana Gómez',
                'role' => ['es' => 'Arquitecta de datos', 'en' => 'Data Architect'],
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $persona = TeamMember::query()->firstOrFail();

        $this->assertSame('Ana Gómez', $persona->name);
        $this->assertSame('Arquitecta de datos', $persona->getTranslation('role', 'es'));
        $this->assertSame('Data Architect', $persona->getTranslation('role', 'en'));
    }

    public function test_crea_un_testimonio_bilingue(): void
    {
        Livewire::test(TestimonialResource::getPages()['create']->getPage())
            ->fillForm([
                'author_name' => 'Carlos Ruiz',
                'author_role' => ['es' => 'Director de TI', 'en' => 'IT Director'],
                'quote' => ['es' => 'Excelente acompañamiento.', 'en' => 'Excellent support.'],
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $testimonio = Testimonial::query()->firstOrFail();

        $this->assertSame('Carlos Ruiz', $testimonio->author_name);
        $this->assertSame('Excelente acompañamiento.', $testimonio->getTranslation('quote', 'es'));
        $this->assertSame('Excellent support.', $testimonio->getTranslation('quote', 'en'));
    }

    public function test_el_formulario_de_edicion_muestra_los_dos_idiomas(): void
    {
        $servicio = Service::factory()->create([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
        ]);

        Livewire::test(ServiceResource::getPages()['edit']->getPage(), ['record' => $servicio->getKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'title.es' => 'Gobierno de datos',
                'title.en' => 'Data Governance',
            ]);
    }

    public function test_avisa_si_el_slug_ya_esta_usado_en_ese_idioma(): void
    {
        Service::factory()->create([
            'title' => ['es' => 'Gobierno de datos', 'en' => 'Data Governance'],
            'slug' => ['es' => 'gobierno-de-datos', 'en' => 'data-governance'],
        ]);

        Livewire::test(ServiceResource::getPages()['create']->getPage())
            ->fillForm([
                'title' => ['es' => 'Otro servicio'],
                'slug' => ['es' => 'gobierno-de-datos'],
            ])
            ->call('create')
            ->assertHasFormErrors(['slug.es']);
    }

    public function test_el_proyecto_puede_relacionarse_con_servicios(): void
    {
        $servicio = Service::factory()->create();

        Livewire::test(ProjectResource::getPages()['create']->getPage())
            ->fillForm([
                'title' => ['es' => 'Caso Banco', 'en' => 'Bank case'],
                'client_name' => 'Banco de Bogotá',
                'services' => [$servicio->getKey()],
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $proyecto = Project::query()->firstOrFail();

        $this->assertSame('Banco de Bogotá', $proyecto->client_name);
        $this->assertTrue($proyecto->services->contains($servicio));
    }

    public function test_las_paginas_institucionales_se_editan_pero_no_se_crean(): void
    {
        $this->seed(SiteContentSeeder::class);

        $this->assertFalse(PageResource::canCreate());

        $inicio = Page::key(Page::HOME);
        $this->assertNotNull($inicio);
        $this->assertFalse(PageResource::canDelete($inicio));

        Livewire::test(PageResource::getPages()['edit']->getPage(), ['record' => $inicio->getKey()])
            ->assertSuccessful()
            ->fillForm([
                'title' => ['es' => 'Inicio', 'en' => 'Home'],
                'subtitle' => ['es' => 'Bienvenidos', 'en' => 'Welcome'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Bienvenidos', $inicio->fresh()->getTranslation('subtitle', 'es'));
        $this->assertSame('Welcome', $inicio->fresh()->getTranslation('subtitle', 'en'));
    }

    public function test_la_configuracion_del_sitio_carga_y_guarda(): void
    {
        Livewire::test(SiteSettings::class)
            ->assertSuccessful()
            ->fillForm([
                'company_name' => 'Grupo Edima S.A.S.',
                'email' => 'contacto@grupoedima.com',
                'phone' => '+57 300 000 0000',
                'address' => ['es' => 'Bogotá, Colombia', 'en' => 'Bogotá, Colombia'],
                'footer_text' => ['es' => 'Consultoría', 'en' => 'Consulting'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        SiteSetting::forgetCurrent();
        $config = SiteSetting::current();

        $this->assertSame('Grupo Edima S.A.S.', $config->company_name);
        $this->assertSame('Consultoría', $config->getTranslation('footer_text', 'es'));
        $this->assertSame('Consulting', $config->getTranslation('footer_text', 'en'));
    }

    public function test_los_mensajes_de_contacto_son_de_solo_lectura(): void
    {
        $mensaje = ContactMessage::factory()->create();

        $this->assertFalse(ContactMessageResource::canCreate());
        $this->assertFalse(ContactMessageResource::canEdit($mensaje));

        Livewire::test(ContactMessageResource::getPages()['view']->getPage(), ['record' => $mensaje->getKey()])
            ->assertSuccessful();
    }

    public function test_se_puede_marcar_un_mensaje_como_leido_desde_la_tabla(): void
    {
        $mensaje = ContactMessage::factory()->create();

        $this->assertFalse($mensaje->isRead());

        Livewire::test(ContactMessageResource::getPages()['index']->getPage())
            ->callTableAction('marcar_leido', $mensaje);

        $this->assertTrue($mensaje->fresh()->isRead());
    }
}
