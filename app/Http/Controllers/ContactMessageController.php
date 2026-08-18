<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactMessageController extends Controller
{
    /**
     * Guarda un mensaje del formulario de contacto público y avisa por
     * correo a la dirección de administración (config('contact.notification_email')).
     *
     * Devuelve JSON siempre: el formulario público lo consume por fetch()
     * sin recargar la página (ver resources/js/app.js, Alpine.data('contactForm')).
     */
    public function store(Request $request): JsonResponse
    {
        // Honeypot: campo oculto que ningún visitante humano llena (está
        // fuera de pantalla en el formulario). Si viene con contenido, es un
        // bot — respondemos como si hubiera funcionado, sin guardar nada ni
        // enviar correo, para no revelar que lo detectamos.
        if (filled($request->input('website'))) {
            return response()->json(['message' => __('site.contact.success')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], $this->validationMessages());

        $contactMessage = ContactMessage::create([
            ...$validated,
            'locale' => app()->getLocale(),
        ]);

        $this->notifyAdmin($contactMessage);

        return response()->json(['message' => __('site.contact.success')]);
    }

    /**
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        return collect([
            'name.required', 'name.max',
            'email.required', 'email.email', 'email.max',
            'phone.max',
            'message.required', 'message.min', 'message.max',
        ])->mapWithKeys(fn (string $key) => [$key => __('site.contact.validation.'.$key)])->all();
    }

    /**
     * Si el correo de administración no está configurado, o el envío falla
     * (por ejemplo SMTP sin configurar todavía), el mensaje ya quedó
     * guardado en la base de datos — no rompemos la respuesta al visitante.
     */
    protected function notifyAdmin(ContactMessage $contactMessage): void
    {
        $to = config('contact.notification_email');

        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el aviso de mensaje de contacto: '.$e->getMessage(), [
                'contact_message_id' => $contactMessage->id,
            ]);
        }
    }
}
