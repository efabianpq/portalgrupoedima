# Grupo Edima — Sitio web corporativo

Reemplazo del sitio anterior en WordPress (grupoedima.com, alojado en Hostinger)
para Grupo Edima S.A.S., consultora colombiana especializada en la
implementación, personalización y adopción de la plataforma **HOPEX**. Vende a
organizaciones grandes en **Colombia y Ecuador**; los compradores típicos son
CIO, gerente de arquitectura empresarial, líder de gobierno de TI y oficial de
riesgo y cumplimiento.

---

## Estado actual

El sitio público **ya existe y funciona en los dos idiomas**, con el contenido
real cargado en la base de datos.

El plan por fases está en `prompts.md` (en la raíz del repo):

| Fase | Qué | Estado |
|---|---|---|
| 0 | Instalación Laravel + Filament + translatable | ✅ hecho |
| 1 | Modelo de datos bilingüe | ✅ hecho |
| 2 | Panel de administración (Filament) | ✅ hecho |
| 2.5 | Identidad de marca y sistema de diseño | ✅ hecho |
| 3 | Enrutamiento y layout bilingüe | ✅ hecho |
| 4 | Páginas públicas | ✅ hecho |
| 5 | Formulario de contacto | ✅ hecho |
| 6 | Migración del contenido desde WordPress | ✅ hecho |
| 7 | SEO técnico y rendimiento | ⬜ siguiente |
| 8 | Pruebas y control de calidad | ⬜ pendiente |
| 9 | Despliegue en Hostinger | ⬜ pendiente |

Estado de la suite: **48 pruebas, 151 aserciones, todas en verde.**

### Lo que falta de contenido (no de código)

El sitio se publicó con datos verificables únicamente. Hay **20 marcadores
`[PENDIENTE: …]`** en la base, visibles en el panel y ocultos en el sitio
público (ver *Contenido pendiente*). Lo que falta, por impacto comercial:

1. Autorización de uso de marca de los logos de cliente *(bloque sin publicar)*
2. Nivel de partnership y nomenclatura oficial del fabricante de HOPEX
3. Al menos un caso de éxito con resultado medible *(sección vacía)*
4. Certificaciones y perfiles reales del equipo *(página fuera del menú)*
5. Duración típica de cada servicio
6. Cifras reales y año de fundación *(bloque sin publicar)*

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
- **Frontend:** Blade + Tailwind CSS **v4** (tokens en CSS, no `tailwind.config.js`).
  Sin framework JS pesado (nada de Vue/React). Alpine.js sólo para
  interactividad puntual (menú móvil, carrusel de testimonios, formulario de
  contacto).
- **Base de datos:** MySQL, igual que en producción.
- **El editor de contenido final NO es técnico.** El panel debe ser simple,
  con etiquetas en español, sólo los campos necesarios y sin exponer
  configuración técnica (JSON crudo, ajustes de sistema) a esa persona.
- **Hosting compartido de Hostinger** con SSH y Composer — NO es VPS ni Docker:
  - Nada de colas con workers persistentes. `QUEUE_CONNECTION=sync`.
  - Trabajo diferido: driver `sync` o el cron de Laravel (`schedule:run`), no
    un worker en segundo plano.
  - Nada de websockets / Reverb / Pusher ni procesos persistentes.

### Reglas de contenido (innegociables)

Vienen del usuario y aplican a cualquier texto que se escriba para el sitio:

- **No inventar hechos verificables**: métricas, años de experiencia, número de
  proyectos, certificaciones, nombres de clientes, testimonios, premios o
  tamaño del equipo. Si un dato fortalecería el texto pero no se tiene, se
  escribe `[PENDIENTE: <qué dato hace falta>]` y se sigue.
- **Sin adjetivos sin evidencia** ("líderes", "los mejores", "de clase
  mundial"). Cada afirmación debe ser demostrable o se elimina.
- **Tono corporativo, sobrio y específico**: lenguaje de negocio, no de
  marketing. Se habla de decisiones, riesgo, gobierno, portafolio y valor
  medible.
- **Terminología** (TOGAF, ArchiMate, COBIT, ISO 31000) sólo donde es precisa,
  nunca como adorno.
- **HOPEX se nombra a secas.** La atribución al fabricante está pendiente de
  confirmar; no escribir "official partner" ni atribuir el producto a una
  empresa concreta hasta que el usuario lo confirme.

---

## Sitio público

### Rutas (`routes/web.php`)

Todo vive bajo un prefijo de idioma. La raíz redirige a
`config('site.default_locale')`. Cada página tiene un URI propio por idioma
pero el **mismo nombre lógico de ruta**, para que el selector de idioma
(`App\Support\LocaleSwitcher`) resuelva la equivalente cambiando sólo el
prefijo `{locale}.` del nombre.

| Nombre | ES | EN |
|---|---|---|
| `home` | `/es` | `/en` |
| `hopex` | `/es/plataforma-hopex` | `/en/hopex-platform` |
| `solutions` · `solutions.show` | `/es/soluciones/{slug}` | `/en/solutions/{slug}` |
| `services` · `services.show` | `/es/servicios/{slug}` | `/en/services/{slug}` |
| `projects` · `projects.show` | `/es/casos-de-exito/{slug}` | `/en/case-studies/{slug}` |
| `team` | `/es/equipo` | `/en/team` |
| `blog` · `blog.show` | `/es/recursos/{slug}` | `/en/resources/{slug}` |
| `about` | `/es/nosotros` | `/en/about-us` |
| `contact` (GET/POST) | `/es/contacto` | `/en/contact` |

El mapa de URIs está en un solo array `$uris` al inicio del archivo: cambiar
una URL es editar ahí, sin tocar controladores ni vistas.

⚠️ **`projects` sigue llamándose así en el código** aunque la URL pública sea
"casos de éxito". El modelo es `Project`. No renombrar sin necesidad.

### ⚠️ Orden del middleware (crítico)

`SetLocaleFromUrl` **debe correr antes que `SubstituteBindings`**. Se configura
en `bootstrap/app.php` con `prependToPriorityList()`.

Sin eso, el binding implícito de modelos por slug (`{service}`, `{project}`,
`{post}`) resuelve con el idioma equivocado y las páginas de detalle en inglés
fallan. El grupo `web` de Laravel pone `SubstituteBindings` primero por
defecto, así que esto **no es opcional**.

### Vistas (`resources/views/`)

- `layouts/public.blade.php` — cabecera, menú, selector de idioma, `hreflang`,
  pie, `<title>` y meta description por `@yield`.
- `public/pages/` — `home` (bloques desde `Page.sections`) y `generic`
  (Nosotros, Contacto, Plataforma HOPEX).
- `public/services/`, `public/projects/`, `public/team/`, `public/blog/` —
  listados y detalles.
- `public/partials/` — `content-card`, `empty-state`, `listing-header`.

Todas las secciones manejan el caso "no hay contenido todavía" sin verse rotas.

### Datos compartidos con las vistas

`AppServiceProvider` registra un View composer para `layouts.public` y
`public.*` que inyecta `$siteSettings`, `$localeUrls` y `$navItems`.

**El menú es dinámico:** "Equipo" sólo aparece si hay `TeamMember` publicados
(`AppServiceProvider::navItems()`). Un enlace a una página vacía resta
credibilidad en un sitio que se vende por la experiencia de su gente.

### Sistema de diseño

Los tokens de marca (colores, tipografías Lora/Karla, radios, sombras) están en
`resources/css/app.css` con la sintaxis `@theme` de Tailwind v4, traducidos 1:1
desde `resources/Paleta_de_marca_Edima/tailwind.config.js` (formato v3, sólo
referencia). Las clases de componente (`.btn-primary`, `.btn-secondary`,
`.badge`, `.card`) replican `style-guide.html` de esa misma carpeta.

⚠️ No cambiar valores en `app.css` sin actualizar el archivo de referencia.

---

## Contenido pendiente: `[PENDIENTE: …]` y `App\Support\PublicContent`

El contenido puede traer datos sin confirmar. Se escriben en la base como
`[PENDIENTE: <qué dato>]` dentro de un `<div data-pendiente="1">…</div>`.

- **En el panel se ven** — son la lista de tareas de quien edita.
- **En el sitio público NO se ven** — un visitante que lee
  "[PENDIENTE: duración típica]" ve una página rota.

`App\Support\PublicContent` hace ese filtrado:

- `render($html)` — devuelve el HTML sin los bloques pendientes.
- `isEmpty($html)` — ¿queda algo que mostrar después de filtrar? Sirve para
  ocultar también el encabezado de una sección que se queda sin cuerpo.
- Red de seguridad: si alguien escribe un `[PENDIENTE…]` a mano desde el panel
  sin envolverlo, igual se elimina el párrafo que lo contiene.

**Cualquier vista pública que imprima contenido enriquecido debe pasar por
`PublicContent::render()`**, no por `{!! $modelo->body !!}` directo.

---

## Estructura de contenido del sitio

1. Páginas institucionales: Inicio, Plataforma HOPEX, Nosotros, Contacto.
2. Servicios — los seis del ciclo de la plataforma: Implementación, Migración,
   Desarrollo y personalización, Soporte y mantenimiento, Formación y
   habilitación, Asesoría en arquitectura empresarial.
3. Casos de éxito (modelo `Project`) — **vacío**, a la espera de datos reales.
4. Equipo — **vacío**, la página está fuera del menú.
5. Recursos / blog — 10 borradores de tema, ninguno publicado.
6. Testimonios — **vacío**.
7. Formulario de contacto — funcional.

---

## Modelo de datos

Idiomas: `config/site.php` es la **fuente única de verdad** (`locales`,
`default_locale`). No escribas `['es','en']` a mano en otros archivos.

### Modelos

| Modelo | Traducible | No traducible | Imagen (Media Library) |
|---|---|---|---|
| `Solution` | title, slug, summary, body, cta_label | order, is_published | — |
| `Service` | title, slug, summary, body | icon, order, is_published | `imagen` |
| `Project` | title, slug, summary, body | client_name *(nombre propio)*, order, is_published | `portada` |
| `TeamMember` | role, bio | name *(nombre propio)*, order, is_published | `foto` |
| `Post` | title, slug, excerpt, body, category | published_at, is_published | `portada` |
| `Testimonial` | author_role, quote | author_name *(nombre propio)*, order, is_published | `foto` |
| `ContactMessage` | — *(lo escriben los visitantes)* | name, email, phone, message, locale, read_at | — |
| `Page` | title, subtitle, body, sections, meta_* | key | `portada` |
| `SiteSetting` | address, footer_text, meta_* | company_name, email, phone, whatsapp, google_maps_url, social_links | — |

- `Project` ↔ `Service` es muchos a muchos (pivote `project_service`).
- `Solution` ↔ `Service` es muchos a muchos (pivote `service_solution`, nombre
  por orden alfabético de los modelos — convención de Eloquent). `Solution` es
  el eje "qué problema de negocio resuelve" (taxonomía de disciplinas HOPEX:
  Arquitectura empresarial, Portafolio de aplicaciones, Procesos de negocio,
  Gobierno-riesgo-cumplimiento — alcance conservador, ver
  `storage/migration/ANALISIS-REFERENCIA.md` A5); `Service` sigue siendo
  "cómo lo entrega Grupo Edima". Se cruzan en ambas páginas de detalle.
- Los nombres de colección de imagen están como constantes en cada modelo
  (`Service::IMAGE`, `Project::COVER`, `TeamMember::PHOTO`, …). Úsalas, no
  escribas la cadena a mano.
- Las imágenes **no tienen columna propia**: viven en la tabla `media`.

### Por qué Page **y** SiteSetting (no uno solo)

Son dos formas distintas de dato:

- **`Page`** = contenido de una página concreta. Una fila por página,
  identificada por `key` inmutable (`home`, `hopex`, `about`, `contact`). Las
  URLs las definen las rutas, no la base de datos, así que quien edita cambia
  textos sin poder romper la navegación. Tiene `sections` (JSON traducible)
  para bloques flexibles: la home guarda ahí su hero, introducción, "Cómo
  trabajamos", "Áreas de trabajo", clientes, cifras y CTA final.
- **`SiteSetting`** = datos globales que se repiten en TODAS las páginas
  (teléfono, correo, dirección, redes, pie). Una sola fila,
  `SiteSetting::current()`.

Meter el teléfono dentro de la página "Contacto" obligaría al pie de página
—que aparece en todo el sitio— a cargar esa página; y meter los cuerpos de
texto en settings haría crecer esa tabla sin límite.

⚠️ **Al agregar una `key` a `Page`** hay que hacer tres cosas: la constante y
`Page::keys()`/`labels()` en el modelo, **el título en el array `$titulos` de
`SiteContentSeeder`** (si se olvida, el seeder revienta con "Undefined array
key" y las pruebas del panel fallan) y la ruta + método de controlador.

### Slugs traducibles

Se guardan en columna JSON: `{"es": "implementacion-hopex", "en": "hopex-implementation"}`.

La **unicidad por idioma la garantiza MySQL** con columnas generadas
(`slug_es`, `slug_en`) más índice único. Si un idioma no está traducido el
valor es NULL, y en MySQL varios NULL no chocan entre sí, así que puede haber
varios registros sin traducir al inglés.

⚠️ **Si algún día se agrega un idioma**: además de `config/site.php` hay que
crear una migración que añada la columna generada `slug_<locale>` a
`services`, `projects` y `posts`.

Las consultas usan la ruta JSON (`slug->es`), no esas columnas, para no
romperse si la config y las migraciones quedan desfasadas.

⚠️ **Un modelo con sólo `en` devuelve cadena vacía en `es`**, y `slugFor('es')`
devuelve `null` → `route()` rompe. Como `es` es el idioma por defecto, todo
contenido debe tener al menos español.

### Traits reutilizables (`app/Models/Concerns/`)

- `HasTranslatableSlug` — resolución por slug + idioma. Métodos clave:
  - `findBySlug($slug, $locale)` — estricto, **para las rutas públicas**.
  - `findBySlugInAnyLocale($slug)` — para redirigir a la URL canónica cuando
    alguien llega con el slug del otro idioma.
  - `slugFor($locale)`, `generateMissingSlugs()`, scopes `whereSlug()` y
    `translatedIn()`.
  - `getRouteKey()` cae al idioma por defecto si falta la traducción, para que
    las URLs generadas nunca queden vacías.
  - En `saving` rellena automáticamente los slugs que falten, a partir del
    título **de ese mismo idioma** (sin respaldo: sin título en inglés no hay
    slug en inglés). **Sólo rellena los que faltan**: para cambiar un slug
    existente hay que asignarlo explícitamente con `setTranslation()`.
- `Publishable` — scopes `published()` / `draft()`. `Post` lo sobrescribe para
  excluir además las entradas con `published_at` futura.
- `Sortable` — scope `ordered()` según la columna `order`.

---

## Formulario de contacto

`POST /{locale}/contacto` → `ContactMessageController@store`. Guarda en
`ContactMessage` y avisa por correo si `CONTACT_NOTIFICATION_EMAIL` está
configurado en `.env` (si está vacío, el mensaje igual se guarda).

- **Sin recargar la página**: componente Alpine `contactForm` en
  `resources/js/app.js`, que envía por `fetch()` y pinta los errores junto a
  cada campo. El backend responde siempre JSON.
- **Validación localizada**: los mensajes salen de
  `lang/{es,en}/site.php → contact.validation`.
  ⚠️ Las claves anidadas necesitan **arrays reales** (`'name' => ['required' => …]`),
  no claves con punto (`'name.required' => …`), que Laravel no resuelve.
- **Honeypot**: campo oculto `website`. Si viene lleno, se responde éxito sin
  guardar ni enviar nada — no se le avisa al bot que fue detectado.
- **Límite por IP**: `RateLimiter::for('contact')` en `AppServiceProvider`,
  máximo 5 por hora, con respuesta 429 en el idioma activo.
- **Precarga por servicio**: las páginas de servicio enlazan a
  `/contacto?servicio={slug}` y el controlador precarga el asunto del mensaje.
- El correo se envía **síncrono** (`Mail::send`, no `->queue()`): Hostinger no
  tiene workers.

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

⚠️ **Los servicios no tienen imagen cargada.** Los íconos del sitio anterior
eran PNG genéricos de 85×85 del tema de WordPress y se verían borrosos en las
tarjetas 4:3; sólo se guardó su nombre en la columna `icon` como referencia.
Las tarjetas muestran el marcador de posición de marca hasta que haya fotos
reales.

---

## Migración del contenido y seeders

### `storage/migration/`

- `content.json` — extracción **literal** del sitio anterior en WordPress
  (fuente: el HTML renderizado, porque el contenido vivía en opciones del
  Customizer del tema, no en posts/páginas).
- `EXTRACTION-REPORT.md` — qué se extrajo, URLs visitadas, campos vacíos,
  enlaces rotos e inconsistencias del sitio anterior.
- `content-v2.json` — **la arquitectura de contenido vigente**: copy ES/EN por
  bloque, con un campo `origen` (`conservado` / `reescrito` / `nuevo`) y los
  pendientes priorizados. Es la fuente de verdad del contenido actual.
- `CONTENT-STRATEGY.md` — diagnóstico, afirmaciones no verificables, mapa de
  sitio, arquitectura de conversión, plantilla de casos de éxito y los 10
  títulos propuestos para Recursos.
- `assets/` — los 31 archivos del sitio anterior, con su ruta original.

### Seeders

| Seeder | Qué hace | ¿En `DatabaseSeeder`? |
|---|---|---|
| `SiteContentSeeder` | Crea las páginas institucionales y la fila de configuración. El sitio y el panel fallan sin ellas. | ✅ sí |
| `WordPressContentSeeder` | Traslada `content.json` tal cual (histórico de la fase 6). | ❌ no |
| `ContentV2Seeder` | Carga `content-v2.json`: la arquitectura de contenido vigente. | ❌ no |

Los dos últimos están **fuera de `DatabaseSeeder` a propósito**: un
`php artisan db:seed` rutinario no debe pisar contenido que el editor ya haya
modificado desde el panel. Se corren a mano:

```bash
php artisan db:seed --class=ContentV2Seeder
```

Los tres son idempotentes.

⚠️ **Ningún seeder borra contenido.** La limpieza de datos de prueba se hizo
con un script aparte, no dentro de un seeder que podría correrse en producción.

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
- `tests/Feature/ContactFormTest.php` — el formulario: guardado y aviso por
  correo, validación en ambos idiomas, honeypot y límite de 5 envíos por hora.

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
- El sitio necesita **Vite corriendo** (`npm run dev`) para ver los estilos en
  desarrollo, o `npm run build` para generar `public/build`.

### Comandos frecuentes

```bash
php artisan serve                 # servidor de desarrollo
npm run dev                       # Vite (estilos y JS en desarrollo)
php artisan migrate               # migraciones
php artisan db:seed               # páginas institucionales + configuración
php artisan test                  # suite completa (necesita MySQL)
./vendor/bin/pint                 # formato de código (correr antes de commit)
php artisan optimize:clear        # limpiar cachés si el panel se ve raro
```

### Notas del entorno (Windows)

- La herramienta Bash es Git Bash. **Evita los heredocs y los `preg_replace`
  encadenados en `php -r`**: en este entorno han truncado archivos.
  Usa la herramienta de escritura de archivos.
- Para correr un script contra la aplicación:
  `php artisan tinker ruta/al/script.php`.

### Git

El repositorio es local (sin remoto). Hay archivos `.docx` del usuario en la
raíz que sí se versionan; los temporales de Word (`~$*`, `~WRL*.tmp`) están
ignorados.
