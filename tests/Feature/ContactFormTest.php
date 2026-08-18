<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_el_mensaje_y_avisa_por_correo_cuando_hay_direccion_configurada(): void
    {
        config(['contact.notification_email' => 'admin@grupoedima.com']);
        Mail::fake();

        $response = $this->postJson(route('es.contact.store'), [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'phone' => '3001234567',
            'message' => 'Quisiera más información sobre HOPEX.',
        ]);

        $response->assertOk()->assertJsonStructure(['message']);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'locale' => 'es',
        ]);

        Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo('admin@grupoedima.com')
            && $mail->contactMessage->email === 'ana@example.com');
    }

    public function test_no_envia_correo_si_no_hay_direccion_de_notificacion_configurada(): void
    {
        config(['contact.notification_email' => null]);
        Mail::fake();

        $this->postJson(route('es.contact.store'), [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'message' => 'Quisiera más información sobre HOPEX.',
        ])->assertOk();

        $this->assertDatabaseCount('contact_messages', 1);
        Mail::assertNothingSent();
    }

    public function test_valida_los_campos_requeridos_con_mensajes_en_el_idioma_activo(): void
    {
        $response = $this->postJson(route('es.contact.store'), []);

        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'message']);
        $this->assertSame('Escribe tu nombre.', $response->json('errors.name.0'));
    }

    public function test_valida_en_ingles_cuando_el_idioma_activo_es_ingles(): void
    {
        $response = $this->postJson(route('en.contact.store'), []);

        $response->assertStatus(422);
        $this->assertSame('Enter your name.', $response->json('errors.name.0'));
    }

    public function test_el_campo_honeypot_lleno_descarta_el_envio_en_silencio(): void
    {
        Mail::fake();

        $response = $this->postJson(route('es.contact.store'), [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Mensaje automático de spam.',
            'website' => 'https://spam.example.com',
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_limita_los_envios_por_ip_a_cinco_por_hora(): void
    {
        $payload = [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'message' => 'Quisiera más información sobre HOPEX.',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('es.contact.store'), $payload)->assertOk();
        }

        $this->postJson(route('es.contact.store'), $payload)->assertStatus(429);

        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_el_mensaje_guardado_no_es_editable_ni_borrable_desde_el_panel(): void
    {
        // Cubierto en PanelAdminTest; aquí sólo confirmamos que el modelo
        // queda marcado como no leído al llegar por el formulario público.
        $this->postJson(route('es.contact.store'), [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'message' => 'Quisiera más información sobre HOPEX.',
        ])->assertOk();

        $this->assertFalse(ContactMessage::first()->isRead());
    }
}
