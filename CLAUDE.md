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

## Modelo de datos

Idiomas: `config/site.php` es la **fuente única de verdad** (`locales`,
`default_locale`). No escribas `['es','en']` a mano en otros archivos.

### Modelos

| Modelo | Traducible | No traducible |
|---|---|---|
| `Service` | title, slug, summary, body | icon, order, is_published |
| `Project` | title, slug, summary, body | client_name (nombre propio), cover_image, order, is_published |
| `TeamMember` | role, bio | name (nombre propio), photo, order, is_published |
| `Post` | title, slug, excerpt, body, category | cover_image, published_at, is_published |
| `Testimonial` | author_role, quote | author_name (nombre propio), photo, order, is_published |
| `ContactMessage` | — (lo escriben los visitantes) | name, email, phone, message, locale, read_at |
| `Page` | title, subtitle, body, sections, meta_* | key, hero_image |
| `SiteSetting` | address, footer_text, meta_* | company_name, email, phone, whatsapp, social_links |

`Project` ↔ `Service` es muchos a muchos (pivote `project_service`).

### Por qué Page **y** SiteSetting (no uno solo)

Son dos formas distintas de dato y por eso son dos tablas:

- **`Page`** = contenido de una página concreta (Inicio, Nosotros, Contacto).
  Varias filas, una por página, identificadas por `key` inmutable
  (`home`, `about`, `contact`). Las URLs las definen las rutas, no la base de
  datos, así que la persona editora cambia textos sin poder romper la
  navegación. Tiene `sections` (JSON traducible) para bloques flexibles.
- **`SiteSetting`** = datos globales que se repiten en TODAS las páginas
  (teléfono, correo, dirección, redes, texto del pie). Una sola fila,
  `SiteSetting::current()`.

Meter el teléfono dentro de la página "Contacto" obligaría al pie de página
—que aparece en todo el sitio— a cargar esa página; y meter los cuerpos de
texto en settings haría crecer la tabla sin límite.

### Slugs traducibles

Se guardan en columna JSON: `{"es": "gobierno-de-datos", "en": "data-governance"}`.

La **unicidad por idioma la garantiza MySQL** con columnas generadas
(`slug_es`, `slug_en`) más índice único. Si un idioma no está traducido el
valor es NULL y en MySQL varios NULL no chocan, así que puede haber varios
registros sin traducir al inglés.

⚠️ **Si algún día se agrega un idioma**: además de `config/site.php` hay que
crear una migración que añada la columna generada `slug_<locale>` a
`services`, `projects` y `posts`.

Las consultas usan la ruta JSON (`slug->es`), no esas columnas, para no
romperse si la config y las migraciones quedan desfasadas.

### Traits reutilizables (`app/Models/Concerns/`)

- `HasTranslatableSlug` — resolución por slug + idioma. Métodos clave:
  `findBySlug($slug, $locale)` (estricto, para las rutas),
  `findBySlugInAnyLocale($slug)` (para redirigir a la URL canónica cuando
  alguien llega con el slug del otro idioma), `slugFor($locale)`,
  `generateMissingSlugs()`, y los scopes `whereSlug()` / `translatedIn()`.
  `getRouteKey()` cae al idioma por defecto si falta la traducción, para que
  las URLs generadas nunca queden vacías.
- `Publishable` — scopes `published()` / `draft()`. `Post` lo sobrescribe
  para excluir además las entradas con `published_at` futura.
- `Sortable` — scope `ordered()` según la columna `order`.

## Panel de administración (Filament)

### Campos traducibles: sin plugin, a propósito

Las pestañas "Español / English" se arman con componentes nativos de Filament
(`app/Filament/Support/TranslatableTabs.php`), no con un plugin. Razones:

- El plugin oficial `filament/spatie-laravel-translatable-plugin` está
  **abandonado** (remite a `lara-zeus/spatie-translatable`), y además usa un
  selector de idioma que obliga a guardar dos veces — fácil olvidar el inglés.
- `outerweb/filament-translatable-fields` v4 exige Filament v4/v5 y PHP 8.4;
  este proyecto va en Filament v3 y PHP 8.3.
- Filament rellena el formulario desde `attributesToArray()`, que con spatie
  ya devuelve `['es' => ..., 'en' => ...]`. Por eso basta con nombrar los
  campos `title.es` / `title.en`: el viaje de ida y vuelta funciona solo.

Resultado: cero dependencias que se puedan abandonar, control total de las
etiquetas en español y migración trivial cuando se pase a Filament v4/v5.

### Convenciones del panel

- Todos los Resources con slug declaran `$recordRouteKeyName = 'id'`. Sin eso
  Filament resolvería los registros por `slug` (porque el trait
  `HasTranslatableSlug` cambia `getRouteKeyName()`) y las URLs del panel se
  romperían al cambiar un slug.
- Sólo el idioma por defecto es obligatorio en los formularios: el contenido
  puede quedar sin traducir al inglés sin bloquear el guardado.
- El slug se autogenera desde el título **sólo al crear**; al editar se
  respeta para no romper enlaces ya publicados. Como red de seguridad, el
  trait rellena en `saving` cualquier slug que falte.
- Helpers compartidos en `app/Filament/Support/`: `TranslatableTabs`,
  `ContentFields` (título, slug, editor enriquecido, imagen, publicado) y
  `ContentColumns` (columna traducible, ícono de publicado, miniatura).
- Grupos del menú: **Contenido del sitio** (páginas, servicios, proyectos,
  equipo, blog, testimonios) y **Administración** (mensajes, configuración).

### Imágenes

`spatie/laravel-medialibrary` con el plugin oficial de Filament. Las
conversiones (`miniatura` 400×400, `web` máx. 1600×1200) se declaran
`nonQueued()` **a propósito**: Hostinger es hosting compartido sin workers de
colas, así que deben generarse al subir la imagen.

Las columnas `cover_image` / `photo` / `hero_image` se eliminaron: con Media
Library las imágenes no necesitan columna propia.

## Pruebas

Las pruebas corren contra **MySQL** (`portalgrupoedima_testing`), no contra
SQLite en memoria: el esquema usa columnas generadas con funciones JSON de
MySQL que SQLite no soporta. Esa base debe existir localmente.

`tests/Feature/TranslatableContentTest.php` cubre el comportamiento bilingüe
(resolución por idioma, unicidad de slugs, respaldo de traducción, scopes de
publicación).

`tests/Feature/PanelAdminTest.php` cubre el panel: que cada recurso cargue,
que se pueda crear contenido en los dos idiomas, que el slug repetido avise,
y que los mensajes de contacto sean de sólo lectura.

⚠️ `SiteSetting::current()` memoriza la fila en una propiedad estática que
vive **por petición**. En las pruebas hay que llamar
`SiteSetting::forgetCurrent()` en el `setUp`, o quedaría apuntando a la fila
de la prueba anterior.

## Entorno local

- Laragon (Windows), PHP 8.3, MySQL vía Laragon.
- Base de datos local: `portalgrupoedima` (MySQL, utf8mb4).
- Base de datos de pruebas: `portalgrupoedima_testing` (MySQL, utf8mb4).
- Servidor de desarrollo: `php artisan serve` → http://localhost:8000
  (o el vhost de Laragon si se configura, típicamente
  http://portalgrupoedima.test).
- Panel admin: http://localhost:8000/admin
