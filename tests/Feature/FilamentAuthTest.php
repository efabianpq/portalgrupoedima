<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Pages\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El panel de administración vive en /admin. Debe quedar fuera del alcance
 * de cualquier visitante no autenticado, y accesible para cualquier usuario
 * con sesión iniciada (no hay roles/permisos adicionales todavía).
 */
class FilamentAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_autenticado_no_puede_entrar_al_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_un_usuario_autenticado_puede_entrar_al_panel(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertOk();
    }

    public function test_la_pagina_de_login_del_panel_es_publica(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_un_usuario_puede_iniciar_sesion_desde_el_formulario_del_panel(): void
    {
        // El login de Filament corre por Livewire (wire:submit), no por un
        // POST convencional a /admin/login.
        $user = User::factory()->create(['password' => bcrypt('contraseña-segura')]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'contraseña-segura',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_una_contrasena_incorrecta_no_inicia_sesion(): void
    {
        $user = User::factory()->create(['password' => bcrypt('contraseña-segura')]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'contraseña-equivocada',
            ])
            ->call('authenticate');

        $this->assertGuest();
    }

    public function test_un_recurso_del_panel_no_es_accesible_sin_autenticarse(): void
    {
        $response = $this->get('/admin/services');

        $response->assertRedirect('/admin/login');
    }
}
