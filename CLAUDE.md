# Grupo Edima — Sitio web corporativo

Reemplazo del sitio actual en WordPress (grupoedima.com, alojado en Hostinger)
para Grupo Edima S.A.S., consultoría en arquitectura empresarial, gobierno de
datos, GRC y HOPEX/Bizzdesign.

---

## Estado actual

**Lo que ya funciona:** la base de datos bilingüe completa y el panel de
administración en español. **Lo que todavía NO existe: el sitio público.**
`routes/web.php` sigue sirviendo la pantalla de bienvenida por defecto de
Laravel — no hay rutas `/es/...` ni `/en/...`, ni layout, ni vistas.

El plan por fases está en `prompts.md` (en la raíz del repo). Va así:

| Fase | Qué | Estado |
|---|---|---|
| 0 | Instalación Laravel + Filament + translatable | ✅ hecho |
| 1 | Modelo de datos bilingüe | ✅ hecho |
| 2 | Panel de administración (Filament) | ✅ hecho |
| 2.5 | Identidad de marca y sistema de diseño | ⬜ siguiente |
| 3 | Enrutamiento y layout bilingüe | ⬜ pendiente |
| 4 | Páginas públicas | ⬜ pendiente |
| 5 | Formulario de contacto | ⬜ pendiente |
| 6 | Migración del contenido desde WordPress | ⬜ pendiente |
| 7 | SEO técnico y rendimiento | ⬜ pendiente |
| 8 | Pruebas y control de calidad | ⬜ pendiente |
| 9 | Despliegue en Hostinger | ⬜ pendiente |

Para la fase 2.5 hay un `Paleta de marca Edima.zip` en la raíz del repo.

Estado de la suite: **42 pruebas, 125 aserciones, todas en verde.**

---

## Decisiones técnicas (ya tomadas — no las cuestiones)

Versiones instaladas y verificadas:

| Paquete | Versión |
|---|---|
| `laravel/framework` | 13.25.0 |
| `filament/filament` | 3.3.54 |
| `spatie/laravel-translatable` | 6.14.1 |
| `spatie/laravel-medialibrary` | 11.23.5 |
| `filament/spatie-laravel-media-library-plugin` | 3.3.54 |
| `livewire/livewire` | 3.8.4 |
| PHP | 8.3 |

- **Backend:** Laravel 13.
- **Panel de administración:** Filament **v3**, en `/admin`.
  ⚠️ **No actualizar a Filament v4/v5 sin pedirlo.** Ya existen v4 y v5, y en
  agosto de 2026 se evaluó y decidió expresamente quedarse en v3: todo lo que
  el proyecto necesita funciona ahí y migrar obligaría a reescribir los siete
  Resources. La decisión se revisa cuando el usuario lo pida, no por iniciativa
  propia.
- **Bilingüe español/inglés en TODO el contenido editable**, con URLs por
  idioma (`/es/...` y `/en/...`). Español es el idioma por defecto
  (`APP_LOCALE=es`).
- **Traducciones de contenido:** `spatie/laravel-translatable`, con columnas
  JSON traducibles en la misma tabla. NO usar tablas separadas por idioma ni
  paquetes basados en tablas hijas: esta es la única estrategia aprobada.
- **Frontend:** Blade + Tailwind CSS. Sin framework JS pesado (nada de
  Vue/React). Alpine.js sólo para interactividad puntual (selector de idioma,
  menú móvil).
- **Base de datos:** MySQL, igual que en producción.
- **El editor de contenido final NO es técnico.** El panel debe ser simple,
  con etiquetas en español, sólo los campos necesarios y sin exponer
  configuración técnica (JSON crudo, ajustes de sistema) a esa persona.
- **Hosting compartido de Hostinger** con SSH y Composer — NO es VPS ni Docker:
  - Nada de colas con workers persistentes. `QUEUE_CONNECTION=sync`.
  - Trabajo diferido: driver `sync` o el cron de Laravel (`schedule:run`), no
    un worker en segundo plano.
  - Nada de websockets / Reverb / Pusher ni procesos persistentes.

---

## Estructura de contenido del sitio

1. Páginas institucionales: Inicio, Nosotros, Contacto.
2. Servicios (HOPEX/Bizzdesign, arquitectura empresarial, gobierno de datos,
   GRC, gestión de procesos, arquitectura de información).
3. Proyectos / casos de éxito.
4. Equipo.
5. Blog / noticias.
6. Testimonios.
7. Formulario de contacto.

Los modelos y el panel para administrar todo esto ya existen. Falta mostrarlo
en el sitio público (fases 3 y 4).

---

## Modelo de datos

Idiomas: `config/site.php` es la **fuente única de verdad** (`locales`,
`default_locale`). No escribas `['es','en']` a mano en otros archivos.

### Modelos

| Modelo | Traducible | No traducible | Imagen (Media Library) |
|---|---|---|---|
| `Service` | title, slug, summary, body | icon, order, is_published | `imagen` |
| `Project` | title, slug, summary, body | client_name *(nombre propio)*, order, is_published | `portada` |
| `TeamMember` | role, bio | name *(nombre propio)*, order, is_published | `foto` |
| `Post` | title, slug, excerpt, body, category | published_at, is_published | `portada` |
| `Testimonial` | author_role, quote | author_name *(nombre propio)*, order, is_published | `foto` |
| `ContactMessage` | — *(lo escriben los visitantes)* | name, email, phone, message, locale, read_at | — |
| `Page` | title, subtitle, body, sections, meta_* | key | `portada` |
| `SiteSetting` | address, footer_text, meta_* | company_name, email, phone, whatsapp, google_maps_url, social_links | — |

- `Project` ↔ `Service` es muchos a muchos (pivote `project_service`).
- Los nombres de colección de imagen están como constantes en cada modelo
  (`Service::IMAGE`, `Project::COVER`, `TeamMember::PHOTO`, …). Úsalas, no
  escribas la cadena a mano.
- Las imágenes **no tienen columna propia**: viven en la tabla `media`.

### Por qué Page **y** SiteSetting (no uno solo)

Son dos formas distintas de dato:

- **`Page`** = contenido de una página concreta (Inicio, Nosotros, Contacto).
  Una fila por página, identificada por `key` inmutable (`home`, `about`,
  `contact`). Las URLs las definen las rutas, no la base de datos, así que
  quien edita cambia textos sin poder romper la navegación. Tiene `sections`
  (JSON traducible) para bloques flexibles.
- **`SiteSetting`** = datos globales que se repiten en TODAS las páginas
  (teléfono, correo, dirección, redes, pie). Una sola fila,
  `SiteSetting::current()`.

Meter el teléfono dentro de la página "Contacto" obligaría al pie de página
—que aparece en todo el sitio— a cargar esa página; y meter los cuerpos de
texto en settings haría crecer esa tabla sin límite.

⚠️ Las tres páginas y la fila de configuración las crea
`database/seeders/SiteContentSeeder.php` (idempotente). Si una instalación no
las tiene, el panel y el sitio fallan: corre `php artisan db:seed`.

### Slugs traducibles

Se guardan en columna JSON: `{"es": "gobierno-de-datos", "en": "data-governance"}`.

La **unicidad por idioma la garantiza MySQL** con columnas generadas
(`slug_es`, `slug_en`) más índice único. Si un idioma no está traducido el
valor es NULL, y en MySQL varios NULL no chocan entre sí, así que puede haber
varios registros sin traducir al inglés.

⚠️ **Si algún día se agrega un idioma**: además de `config/site.php` hay que
crear una migración que añada la columna generada `slug_<locale>` a
`services`, `projects` y `posts`.

Las consultas usan la ruta JSON (`slug->es`), no esas columnas, para no
romperse si la config y las migraciones quedan desfasadas.

### Traits reutilizables (`app/Models/Concerns/`)

- `HasTranslatableSlug` — resolución por slug + idioma. Métodos clave:
  - `findBySlug($slug, $locale)` — estricto, **para las rutas públicas**.
  - `findBySlugInAnyLocale($slug)` — para redirigir a la URL canónica cuando
    alguien llega con el slug del otro idioma *(útil en la fase 3)*.
  - `slugFor($locale)`, `generateMissingSlugs()`, scopes `whereSlug()` y
    `translatedIn()`.
  - `getRouteKey()` cae al idioma por defecto si falta la traducción, para que
    las URLs generadas nunca queden vacías.
  - En `saving` rellena automáticamente los slugs que falten, a partir del
    título **de ese mismo idioma** (sin respaldo: sin título en inglés no hay
    slug en inglés).
- `Publishable` — scopes `published()` / `draft()`. `Post` lo sobrescribe para
  excluir además las entradas con `published_at` futura.
- `Sortable` — scope `ordered()` según la columna `order`.

---

## Panel de administración (Filament)

Siete Resources + una página de configuración, todo en español:

- **Contenido del sitio:** Páginas del sitio, Servicios, Proyectos, Equipo,
  Blog y noticias, Testimonios.
- **Administración:** Mensajes de contacto (sólo lectura), Configuración del
  sitio.

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

- Los Resources con slug declaran `$recordRouteKeyName = 'id'`. **Sin eso
  Filament resuelve los registros por `slug`** (porque `HasTranslatableSlug`
  cambia `getRouteKeyName()`) y las URLs del panel se rompen al cambiar un
  slug. Si agregas un Resource sobre un modelo con slug, no lo olvides.
- Sólo el idioma por defecto es obligatorio en los formularios: el contenido
  puede quedar sin traducir al inglés sin bloquear el guardado.
- El slug se autogenera desde el título **sólo al crear**; al editar se
  respeta, para no romper enlaces ya publicados.
- Páginas institucionales y mensajes de contacto tienen `canCreate()` /
  `canDelete()` / `canEdit()` restringidos a propósito. No los "arregles".
- Helpers compartidos en `app/Filament/Support/`:
  - `TranslatableTabs` — pestañas ES/EN.
  - `ContentFields` — título, slug, editor enriquecido, imagen, publicado.
  - `ContentColumns` — columna traducible (con búsqueda en ambos idiomas),
    ícono de publicado, miniatura.

### Imágenes

`spatie/laravel-medialibrary` con el plugin oficial de Filament. Las
conversiones (`miniatura` 400×400, `web` máx. 1600×1200) se definen en
`app/Support/ImageConversions.php` y se declaran `nonQueued()` **a propósito**:
Hostinger es hosting compartido sin workers de colas, así que deben generarse
al subir la imagen.

Requiere `php artisan storage:link` (ya hecho en local; repetir en producción).

---

## Pruebas

Las pruebas corren contra **MySQL** (`portalgrupoedima_testing`), no contra
SQLite en memoria: el esquema usa columnas generadas con funciones JSON de
MySQL que SQLite no soporta. Esa base debe existir localmente.

- `tests/Feature/TranslatableContentTest.php` — comportamiento bilingüe:
  resolución por idioma, unicidad de slugs, respaldo de traducción, scopes de
  publicación.
- `tests/Feature/PanelAdminTest.php` — el panel: que cada recurso cargue, que
  se pueda crear contenido en los dos idiomas, que un slug repetido avise, y
  que los mensajes de contacto sean de sólo lectura.

⚠️ `SiteSetting::current()` memoriza la fila en una propiedad estática que vive
**por petición**. En las pruebas hay que llamar `SiteSetting::forgetCurrent()`
en el `setUp`, o quedaría apuntando a la fila de la prueba anterior.

---

## Entorno local

- Laragon (Windows), PHP 8.3, MySQL 8.0 vía Laragon.
- Base de datos: `portalgrupoedima` (utf8mb4).
- Base de pruebas: `portalgrupoedima_testing` (utf8mb4).
- Sitio: http://localhost:8000 · Panel: http://localhost:8000/admin
  (o el vhost de Laragon, típicamente http://portalgrupoedima.test).
- Usuario del panel en local: `admin@grupoedima.com`. **Credenciales de
  desarrollo, no sirven para producción**: en el despliegue hay que crear un
  usuario nuevo con `php artisan make:filament-user`.

### Comandos frecuentes

```bash
php artisan serve                 # servidor de desarrollo
php artisan migrate               # migraciones
php artisan db:seed               # páginas institucionales + configuración
php artisan test                  # suite completa (necesita MySQL)
./vendor/bin/pint                 # formato de código (correr antes de commit)
php artisan optimize:clear        # limpiar cachés si el panel se ve raro
```

### Git

El repositorio es local (sin remoto). Hay archivos `.docx` del usuario en la
raíz que sí se versionan; los temporales de Word (`~$*`, `~WRL*.tmp`) están
ignorados.
