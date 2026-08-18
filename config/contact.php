<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Correo de notificación
    |--------------------------------------------------------------------------
    |
    | A dónde se envía el aviso cuando alguien llena el formulario de
    | contacto del sitio público. Si queda vacío, el mensaje se sigue
    | guardando en la tabla contact_messages (visible en el panel), pero no
    | se envía ningún correo.
    |
    */

    'notification_email' => env('CONTACT_NOTIFICATION_EMAIL'),

];
