# Grupo Edima — Sitio web corporativo

Reemplazo del sitio actual en WordPress (grupoedima.com, alojado en Hostinger)
para Grupo Edima S.A.S., consultoría en arquitectura empresarial, gobierno de
datos, GRC y HOPEX/Bizzdesign.

## Decisiones técnicas (ya tomadas — no las cuestiones)

- **Backend:** Laravel (última versión estable; instalado con Laravel 13).
- **Panel de administración:** Filament v3, gratuito. Instalado en `/admin`.
- **Bilingüe español/inglés en TODO el contenido editable**, con URLs por
  idioma (`/es/...` y `/en/...`). Español es el idioma por defecto
  (`APP_LOCALE=es`).
- **Traducciones de contenido:** paquete `spatie/laravel-translatable`
  (columnas JSON traducibles en la misma tabla). NO usar tablas separadas
  por idioma ni paquetes de traducción basados en tablas hijas (como
  `spatie/laravel-translatable` es la única estrategia aprobada).
- **Frontend:** Blade + Tailwind CSS. Sin framework JS pesado (nada de
  Vue/React). Usar Alpine.js solo para interactividad puntual (selector de
  idioma, menú móvil, etc.).
- **Base de datos:** MySQL (`portalgrupoedima`), igual que en producción
  (Hostinger).
- **El editor de contenido final NO es técnico.** El panel de Filament debe
  ser simple, con etiquetas en español, solo los campos necesarios y sin
  exponer configuración técnica (slugs complejos, JSON crudo, ajustes de
  sistema, etc.) a esa persona.
- **Hosting compartido de Hostinger** con acceso SSH y Composer — NO es VPS
  ni Docker. Por lo tanto:
  - Evitar depender de colas (queues) con workers persistentes.
    `QUEUE_CONNECTION=sync` por defecto.
  - Si se necesita envío de correo u otro trabajo diferido, usar el driver
    `sync` o el cron de Laravel (`schedule:run` vía cron de Hostinger), no
    un worker en segundo plano.
  - Evitar websockets / Laravel Reverb / Pusher u otras dependencias que
    requieran procesos persistentes.

## Estructura de contenido del sitio

1. Páginas institucionales: Inicio, Nosotros, Contacto.
2. Servicios (HOPEX/Bizzdesign, arquitectura empresarial, gobierno de datos,
   GRC, gestión de procesos, arquitectura de información).
3. Proyectos / casos de éxito.
4. Equipo.
5. Blog / noticias.
6. Testimonios.
7. Formulario de contacto.

(Esta estructura se desarrollará en detalle en prompts/sesiones siguientes;
por ahora es solo contexto de hacia dónde va el proyecto.)

## Entorno local

- Laragon (Windows), PHP 8.3, MySQL vía Laragon.
- Base de datos local: `portalgrupoedima` (MySQL, utf8mb4).
- Servidor de desarrollo: `php artisan serve` → http://localhost:8000
  (o el vhost de Laragon si se configura, típicamente
  http://portalgrupoedima.test).
- Panel admin: http://localhost:8000/admin
