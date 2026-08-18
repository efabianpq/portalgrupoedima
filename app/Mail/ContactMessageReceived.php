<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso al correo de administración cuando alguien llena el formulario de
 * contacto público. Se envía de forma síncrona (Mail::send, no ->queue()):
 * el hosting de Hostinger no tiene workers de colas, y QUEUE_CONNECTION=sync
 * ya ejecuta cualquier cola en el mismo request de todas formas.
 */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de contacto — '.$this->contactMessage->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message',
        );
    }
}
