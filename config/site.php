<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Idiomas del sitio
    |--------------------------------------------------------------------------
    |
    | Fuente única de verdad para los idiomas del sitio. La usan el modelo de
    | datos (slugs traducibles), las rutas (/es/..., /en/...) y el panel de
    | administración. Si algún día se agrega un idioma, se agrega aquí Y se
    | crea una migración que añada la columna generada `slug_<locale>` a las
    | tablas con slug traducible (services, projects, posts).
    |
    */

    'locales' => [
        'es' => 'Español',
        'en' => 'English',
    ],

    /*
    |--------------------------------------------------------------------------
    | Idioma por defecto
    |--------------------------------------------------------------------------
    |
    | Español. Es el idioma de respaldo cuando un contenido no está traducido
    | y el idioma que se asume cuando no se puede determinar otro.
    |
    */

    'default_locale' => 'es',

];
