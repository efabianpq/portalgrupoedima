@component('mail::message')
# Nuevo mensaje de contacto

Se recibió un nuevo mensaje desde el formulario de contacto del sitio (idioma: {{ strtoupper($contactMessage->locale) }}).

**Nombre:** {{ $contactMessage->name }}
**Correo:** {{ $contactMessage->email }}
@if ($contactMessage->phone)
**Teléfono:** {{ $contactMessage->phone }}
@endif

**Mensaje:**

{{ $contactMessage->message }}

@component('mail::button', ['url' => route('filament.admin.resources.contact-messages.view', $contactMessage)])
Ver en el panel
@endcomponent

Grupo Edima
@endcomponent
