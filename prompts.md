# Prompts para Claude Code — Sitio web Grupo Edima (Laravel + Filament)

Esta es la secuencia de prompts para pegarle a Claude Code, uno por uno, dentro de la carpeta `C:\laragon\www\PortalGrupoEdima`. Está basada en la Opción A del documento de recomendación tecnológica.

## Cómo usarlos

1. Abre una terminal en `C:\laragon\www\PortalGrupoEdima` y ejecuta `claude` (o `claude code`, según tengas instalado el CLI).
2. Pega el **Prompt 0** primero — crea la memoria del proyecto (`CLAUDE.md`) que los demás prompts asumen que existe.
3. Sigue los prompts en orden. No pases al siguiente hasta revisar que el anterior funcione: abre el sitio en el navegador (Laragon crea automáticamente `http://portalgrupoedima.test` una vez exista un `public/index.php`) y prueba lo que se construyó.
4. Al final de cada prompt pídele a Claude Code `haz commit de estos cambios con un mensaje descriptivo` — así queda un historial claro por fase y puedes volver atrás si algo sale mal.
5. Si Claude Code te pregunta algo que no sabes responder (nombre exacto de un paquete, versión de PHP disponible, etc.), pídele que lo verifique él mismo en el proyecto (`composer show`, `php -v`) antes de decidir.
6. Los textos reales (español/inglés) de servicios, proyectos, equipo, etc. no están en estos prompts — se cargan después, desde el panel de administración (Prompt 6 cubre la migración del contenido actual).

## Modelo y esfuerzo recomendado por prompt

En Claude Code puedes fijar el modelo con `/model` y el nivel de esfuerzo con `/effort <nivel>` (low, medium, high, xhigh, max) antes de pegar cada prompt. En términos generales: usa Opus 5 en los prompts donde una mala decisión de arquitectura es cara de corregir después, y Sonnet 5 en los que son más mecánicos o repetitivos, para cuidar el costo. Cambiar el esfuerzo invalida el cache del prompt, así que fija ambos (modelo y esfuerzo) justo antes de pegar el texto, no a mitad de la respuesta.

| # | Prompt | Modelo | Esfuerzo | Por qué |
|---|--------|--------|----------|---------|
| 0 | Setup inicial + CLAUDE.md | Sonnet 5 | medium | Instalación estándar de Laravel/Filament, bien documentada, poco riesgo de decisiones irreversibles. |
| 1 | Modelo de datos bilingüe | **Opus 5** | **high** | Decisión de arquitectura (esquema de traducciones, diseño de SiteSetting/Page) que todo lo demás va a depender de ella — vale la pena que quede bien pensada desde el inicio. |
| 2 | Paneles de administración (Filament) | **Opus 5** | **xhigh** | El prompt más complejo: evaluar y combinar varios paquetes (campos traducibles, media library, editor enriquecido) y diseñar la experiencia para un usuario no técnico. |
| 2.5 | Identidad de marca y sistema de diseño | **Opus 5** | **xhigh** | Es juicio estético y de marca, no un patrón técnico conocido — el modelo con mejor criterio de diseño evita rondas de ajuste innecesarias. |
| 3 | Enrutamiento y layout bilingüe | Sonnet 5 | high | Middleware de idioma y hreflang tienen su detalle, pero son patrones conocidos; conviene algo de cuidado sin llegar al máximo. |
| 4 | Páginas públicas | Sonnet 5 | medium | Mayormente repetitivo: aplica el mismo patrón de las páginas anteriores a cada sección de contenido. |
| 5 | Formulario de contacto | Sonnet 5 | high | Tiene matices de seguridad (spam, límites de envío, correo) que ameritan algo más de cuidado que una vista simple. |
| 6 | Migración desde WordPress | **Opus 5** | high | Requiere interpretar un XML ambiguo y decidir cómo mapear contenido real a los modelos — más razonamiento, menos mecánico. |
| 7 | SEO técnico y rendimiento | Sonnet 5 | medium | Sitemap, hreflang y meta tags son patrones estándar de Laravel, bien conocidos. |
| 8 | Pruebas y control de calidad | **Opus 5** | high | Para que las pruebas sean útiles debe "pensar" en los casos límite de todo el sitio, no solo seguir una receta. |
| 9 | Preparación de despliegue en Hostinger | Sonnet 5 | low | Es esencialmente documentación de pasos a seguir, no construcción de código nuevo — prioriza velocidad y costo. |

Si en algún punto notas que Sonnet 5 se está quedando corto (por ejemplo, te propone algo que no cuadra con el resto del proyecto), sube a Opus 5 con `/model` y repite el prompt — no pasa nada por ajustar sobre la marcha.

## Cuándo y cómo generar el diseño con Claude Code

Decidiste que Claude Code también haga de diseñador (no hay un diseñador
externo entregando maquetas). Eso cambia el enfoque, pero la lógica de
fondo es la misma: **no dejes el diseño para el final**, porque construir
el layout y las páginas dos veces (una genérica, otra ya con marca) es
retrabajo evitable y obliga a repetir el Prompt 8 de pruebas.

La diferencia es que ahora no hay que "esperar" a nadie — el diseño se
genera con un prompt dedicado, igual que el resto. Por eso se agregó un
**Prompt 2.5**, justo después del panel de administración y antes del
layout público:

1. **Prompt 2.5** le pide a Claude Code que actúe como diseñador de marca
   y UI: define paleta de colores, tipografía, logo (como SVG) y los
   componentes visuales base (botones, tarjetas, header, hero), y arma
   todo eso en un archivo HTML independiente (`style-guide.html`) que
   puedes abrir y revisar en segundos, sin tocar el sitio real todavía.
2. Tú lo revisas, pides ajustes si algo no te convence (colores, logo,
   tono visual) — como Claude Code no es un diseñador humano, es normal
   necesitar 2-3 rondas de ajuste sobre ese único archivo, lo cual es
   barato porque no afecta nada más del proyecto.
3. Cuando lo apruebes, el **Prompt 3** (layout) y el **Prompt 4** (páginas)
   ya están ajustados más abajo para tomar esos mismos colores, tipografía
   y componentes desde `tailwind.config.js` — así el diseño se construye
   una sola vez y el resto del sitio lo hereda automáticamente.

Ten en cuenta una limitación real: Claude Code puede generar un logo tipo
wordmark o monograma (texto/iniciales en SVG, con formas simples), que es
una elección válida y común para una consultora B2B — pero no va a producir
un ícono o ilustración compleja como lo haría un diseñador gráfico. Si más
adelante quieres explorar opciones de logo más ilustradas, esa parte sí
convendría encargarla aparte (a un diseñador o una herramienta de
generación de imágenes) y luego pedirle a Claude Code que la integre.

---

## Prompt 0 — Memoria del proyecto (CLAUDE.md) y arranque del proyecto

```
Vamos a construir desde cero el sitio web corporativo de Grupo Edima S.A.S.
(consultoría en arquitectura empresarial, gobierno de datos, GRC y HOPEX/Bizzdesign)
en esta carpeta vacía. Es el reemplazo de un sitio actual en WordPress
(grupoedima.com), que se aloja en Hostinger.

Decisiones ya tomadas (no las cuestiones, constrúyelas así):
- Backend: Laravel (última versión estable).
- Panel de administración: Filament v3 (o la última estable), gratuito.
- Bilingüe español/inglés en TODO el contenido editable, con URLs por idioma
  (/es/... y /en/...), español como idioma por defecto.
- Traducciones de contenido con el paquete spatie/laravel-translatable
  (columnas JSON traducibles), NO con múltiples tablas por idioma.
- Frontend: Blade + Tailwind CSS (sin framework JS pesado; usa Alpine.js
  solo si hace falta interactividad puntual, como el selector de idioma
  o un menú móvil).
- Base de datos: MySQL (así funciona en Hostinger).
- El editor de contenido final es una persona NO técnica: el panel de
  Filament debe ser simple, con etiquetas en español y solo los campos
  necesarios — nada de configuración técnica visible ahí.
- El sitio se desplegará en un hosting compartido de Hostinger con acceso
  SSH y Composer (no es VPS ni Docker), así que evita depender de colas
  (queues) con workers persistentes o de websockets; si necesitas envío de
  correo asíncrono usa el driver "sync" o el cron de Laravel, no un worker.

Estructura de contenido del sitio (la desarrollaremos en prompts siguientes,
por ahora solo tenlo como contexto):
1. Páginas institucionales: Inicio, Nosotros, Contacto.
2. Servicios (HOPEX/Bizzdesign, arquitectura empresarial, gobierno de datos,
   GRC, gestión de procesos, arquitectura de información).
3. Proyectos / casos de éxito.
4. Equipo.
5. Blog / noticias.
6. Testimonios.
7. Formulario de contacto.

Por ahora, quiero que hagas esto:

1. Crea un archivo CLAUDE.md en la raíz del proyecto con todo el contexto de
   arriba (decisiones técnicas, estructura de contenido, y el hecho de que
   el editor final no es técnico), para que cualquier sesión futura de
   Claude Code lo lea automáticamente y no tengamos que repetir el contexto.
2. Instala un proyecto Laravel nuevo en esta misma carpeta (usando composer).
3. Instala y configura Filament v3 con un usuario administrador (pídeme el
   correo y contraseña que quiero usar, o usa unos valores de ejemplo y
   dime cómo cambiarlos después).
4. Instala el paquete spatie/laravel-translatable.
5. Configura el archivo .env para MySQL con una base de datos local llamada
   portalgrupoedima (dime si necesito crearla yo mismo en Laragon primero).
6. Inicializa un repositorio git con un .gitignore adecuado para Laravel y
   haz el primer commit.
7. Confirma que el proyecto corre y que puedo entrar al panel de Filament.

Al terminar, dime exactamente qué URL debo abrir para ver el sitio y para
entrar al panel de administración, y qué credenciales quedaron configuradas.
```

---

## Prompt 1 — Modelo de datos bilingüe

```
Ahora vamos a crear el modelo de datos. Todos los campos de texto visibles
al público deben ser traducibles (spatie/laravel-translatable), con español
como locale por defecto e inglés como segundo idioma.

Crea las migraciones, modelos y factories para:

1. Service (Servicio): title, slug (traducible por idioma, único por
   idioma), summary, body (rich text), icon (string, opcional), order
   (para ordenar manualmente), is_published (boolean).
2. Project (Proyecto / caso de éxito): title, slug, client_name (NO
   traducible, es un nombre propio), summary, body, cover_image, order,
   is_published. Relación opcional a uno o varios Service.
3. TeamMember (Equipo): name (no traducible), role/cargo (traducible),
   bio (traducible), photo, order, is_published.
4. Post (Blog / noticias): title, slug, excerpt, body (rich text),
   cover_image, published_at (fecha), category (string simple, traducible),
   is_published.
5. Testimonial (Testimonio): author_name (no traducible), author_role
   (traducible), quote (traducible), photo (opcional), order,
   is_published.
6. ContactMessage: name, email, phone (opcional), message, created_at.
   Esta tabla NO es traducible, son datos que llenan los visitantes.
7. SiteSetting o Page: para textos de la portada, "Nosotros" y "Contacto"
   que no encajan en los modelos anteriores (por ejemplo, el texto de
   bienvenida del home, la dirección de la oficina, teléfono, redes
   sociales). Decide tú si esto es un modelo tipo "Page" con bloques
   flexibles o un settings singleton — explícame brevemente por qué
   elegiste ese enfoque.

Para cada modelo con slug traducible, asegúrate de que la ruta pública use
el slug del idioma activo y que exista un método para resolver el modelo
correcto por slug + locale (para las rutas que crearemos después).

Al terminar, muéstrame un resumen de las tablas creadas y confirma que las
migraciones corren sin error.
```

---

## Prompt 2 — Paneles de administración en Filament

```
Ahora crea los Filament Resources para que el editor de contenido (una
persona NO técnica) administre todo desde el panel, en español, de forma
simple.

Para cada modelo (Service, Project, TeamMember, Post, Testimonial), crea un
Filament Resource con:

- Formulario con pestañas o secciones separadas "Español" / "English" para
  cada campo traducible (usa un plugin de Filament para campos
  traducibles compatible con spatie/laravel-translatable — evalúa
  filament-translatable-fields o el enfoque equivalente más mantenido
  actualmente, y dime cuál elegiste y por qué).
- Subida de imágenes con vista previa (usa Spatie Media Library si no
  está instalado, con conversión automática a un tamaño razonable para
  web).
- Editor de texto enriquecido (rich text) para los campos largos como
  "body", con las opciones básicas (negrita, cursiva, listas, enlaces,
  imágenes dentro del texto).
- Un campo "Publicado" (is_published) tipo interruptor, visible en la
  tabla de listado con un ícono de color.
- Ordenamiento manual por drag-and-drop en el listado (campo "order").
- Traducción automática del slug en español a partir del título en
  español, y del slug en inglés a partir del título en inglés, editable
  a mano si se necesita.
- Etiquetas de navegación del panel, nombres de campo y mensajes en
  español natural (nada de "Title" o "Slug" sin traducir).
- Agrupa los recursos en el menú del panel bajo una sección "Contenido del
  sitio".

También crea:
- Un Filament Resource de solo lectura para ver los ContactMessage
  recibidos (ordenados por fecha, más reciente primero), con opción de
  marcarlos como leídos.
- Una página de "Configuración del sitio" en Filament para editar los
  textos de SiteSetting/Page que definimos en el prompt anterior
  (bienvenida del home, datos de contacto, redes sociales), también con
  campos separados ES/EN donde aplique.

Al terminar, entra tú mismo al panel (o dime los pasos) y confirma que
cada recurso carga sin errores y que se puede crear un registro de
prueba en ambos idiomas.
```

---

## Prompt 2.5 — Identidad de marca y sistema de diseño

```
Ahora quiero que actúes como diseñador de marca y de interfaz para el
sitio, ya que no tengo un diseñador externo en este proyecto.

Contexto para tus decisiones de diseño:
- Grupo Edima S.A.S. es una consultora B2B de arquitectura empresarial,
  gobierno de datos, GRC y transformación digital, especializada en
  HOPEX/Bizzdesign. Sus clientes son organizaciones grandes (gerentes de
  TI, arquitectos empresariales, directores de datos).
- Personalidad de marca deseada: seria, confiable y técnicamente sólida,
  pero cercana y moderna — NO quiero que se vea como una plantilla
  genérica de WordPress ni como una "big four" fría e impersonal.
- Evita clichés: nada de degradados azules genéricos tipo "corporate
  stock", ni iconografía de negocios trillada (apretones de manos,
  engranajes, globos terráqueos).

Con eso, entrega lo siguiente:

1. Una paleta de colores completa: color primario, secundario, un acento,
   y una escala de neutros (para texto, fondos, bordes), en formato hex.
   Propón la paleta que mejor creas que funciona (no me des 3 opciones a
   elegir, decide y justifica brevemente tu elección) — pero déjala fácil
   de ajustar si te pido cambiarla.
2. Una pareja tipográfica de Google Fonts (una para títulos, otra para
   texto de párrafo) que se vea profesional y legible, con su respectivo
   fallback a fuentes del sistema.
3. Un logotipo simple en SVG para "Grupo Edima" — puede ser un wordmark
   (el nombre estilizado) o un monograma con las iniciales "GE" más el
   nombre al lado. Hazlo en 2 variantes (una para fondo claro, otra para
   fondo oscuro) y una versión reducida cuadrada para usar como favicon.
4. Estilo base de componentes: botón primario, botón secundario, tarjeta
   (para servicios/proyectos/posts), badge/etiqueta, y estructura visual
   del header y el footer.
5. Define TODO lo anterior como tokens en tailwind.config.js (colors.brand.*,
   fontFamily.heading, fontFamily.body, y los border-radius/shadows que
   uses de forma consistente), para que el resto del sitio los reutilice
   en vez de tener valores sueltos repetidos.
6. Arma un archivo HTML independiente y autocontenido, style-guide.html
   (en la raíz del proyecto, fuera de las vistas de Laravel, sin depender
   de que el servidor esté corriendo), que muestre en una sola página:
   el logo en sus variantes, la paleta de colores con sus valores hex, la
   tipografía en varios tamaños, y ejemplos reales de cada componente
   (botones, tarjetas, un header y un hero de ejemplo) usando esos mismos
   colores y fuentes.

No toques todavía ninguna vista real de Laravel — solo el
tailwind.config.js y el archivo style-guide.html. Cuando termines, dime
cómo abrir style-guide.html en el navegador para revisarlo. Voy a pedirte
ajustes sobre este archivo antes de aplicarlo al resto del sitio.
```

---

## Prompt 3 — Enrutamiento bilingüe y layout público

```
Ahora arma el esqueleto del sitio público con soporte bilingüe real por URL.

1. Configura las rutas para que todo el sitio público viva bajo un prefijo
   de idioma: /es/... y /en/.... Si alguien entra a la raíz "/", redirige
   a "/es" (español es el idioma por defecto).
2. Crea un middleware que detecte el prefijo de idioma de la URL, active
   ese locale con App::setLocale(), y lo deje disponible durante toda la
   petición.
3. Crea un layout Blade principal con Tailwind CSS (instálalo si no está)
   que incluya: header con logo y menú de navegación (Inicio, Servicios,
   Proyectos, Equipo, Blog, Nosotros, Contacto — usando los nombres
   traducidos según el idioma activo), un selector de idioma ES/EN en el
   header que cambie de idioma manteniendo la misma página cuando exista
   una versión equivalente, y un footer con los datos de contacto y redes
   sociales que vienen de SiteSetting.
4. Agrega las etiquetas SEO básicas en el <head>: <html lang="es"> o
   "en" según corresponda, y las etiquetas hreflang alternativas entre
   ambos idiomas para cada página.
5. Usa exactamente el sistema de diseño que ya definimos en el Prompt
   2.5: los colores y tipografías de tailwind.config.js
   (colors.brand.*, fontFamily.heading, fontFamily.body) y el estilo de
   botones, tarjetas, header y footer que quedaron en style-guide.html —
   revísalo antes de construir el HTML/Tailwind y sigue ese mismo estilo,
   no un diseño genérico nuevo. El sitio debe ser responsivo (móvil
   primero), igual que los ejemplos del style guide.

Al terminar, muéstrame cómo se ve el layout base comparado con
style-guide.html (puedo abrirlo yo en el navegador) y confirma que el
selector de idioma funciona.
```

---

## Prompt 4 — Páginas y contenido público

```
Con el layout ya listo, construye las páginas públicas usando los modelos
que creamos, manteniendo el mismo sistema de diseño (colores, tipografía y
componentes de tailwind.config.js / style-guide.html) en todas las
páginas para que se sienta como un solo sitio coherente:

1. Inicio (/es, /en): bienvenida (desde SiteSetting), resumen de
   servicios destacados (con enlace a cada uno), 2-3 proyectos
   destacados, testimonios en un carrusel simple, llamado a la acción
   hacia Contacto.
2. Servicios: listado de todos los Service publicados, y una página de
   detalle por servicio (usando su slug traducido).
3. Proyectos: listado de Project publicados, y página de detalle por
   proyecto.
4. Equipo: listado de TeamMember publicados (foto, nombre, cargo, bio
   corta).
5. Blog: listado de Post publicados (ordenados por fecha, más reciente
   primero), con paginación, y página de detalle por artículo.
6. Nosotros: página de contenido institucional (desde SiteSetting/Page).
7. Contacto: información de contacto + el formulario (lo construiremos a
   fondo en el siguiente prompt, por ahora deja el formulario visualmente
   listo aunque no envíe nada todavía).

Todas las páginas deben:
- Mostrar SOLO contenido publicado (is_published = true).
- Usar el contenido en el idioma activo de la URL.
- Tener un <title> y meta description que vengan del contenido de cada
  registro (o un valor por defecto razonable si no se definió).
- Manejar bien el caso de "no hay contenido todavía" (por ejemplo, antes
  de migrar los datos reales) sin verse roto.

Al terminar, recorre conmigo (o dime cómo probar) cada sección con al
menos un registro de prueba cargado en ambos idiomas.
```

---

## Prompt 5 — Formulario de contacto funcional

```
Haz funcional el formulario de contacto de la página Contacto:

1. Campos: nombre, correo, teléfono (opcional), mensaje. Validación en
   español (o inglés según el idioma activo) con mensajes de error claros
   junto a cada campo, sin recargar la página si es posible (puedes usar
   Livewire o una petición simple, tú decides el enfoque más simple de
   mantener).
2. Al enviarse correctamente: guarda el mensaje en la tabla
   ContactMessage Y envía un correo de notificación a una dirección de
   administración (usa una variable de entorno CONTACT_NOTIFICATION_EMAIL
   en .env, déjala vacía con un comentario explicando que debo poner mi
   correo real ahí).
3. Protege el formulario contra spam básico: un campo honeypot oculto y
   un límite de envíos por IP (por ejemplo, máximo 5 por hora) usando el
   rate limiter de Laravel.
4. Muestra un mensaje de confirmación claro tras el envío exitoso, en el
   idioma activo.
5. Configura el driver de correo en .env dejando comentado un ejemplo
   para usar SMTP de Hostinger (host, puerto, usuario, contraseña) — no
   inventes credenciales, deja placeholders.

Al terminar, dime cómo probar el envío en local (por ejemplo, usando el
driver "log" de correo para ver el email en el archivo de log si no
tengo SMTP configurado todavía).
```

---

## Prompt 6 — Migración del contenido actual desde WordPress

```
Necesito pasar el contenido real que ya existe en grupoedima.com (WordPress)
hacia este nuevo sitio. Ayúdame así:

1. Explícame, en un archivo MIGRACION_CONTENIDO.md, las dos formas
   posibles de hacerlo: (a) exportar el contenido desde el panel de
   WordPress (Herramientas > Exportar, genera un XML) y que tú escribas un
   comando Artisan que lo lea y cree los registros correspondientes
   (Service, Project, TeamMember, Post, etc.) dejándolos como borrador
   (is_published = false) para que yo los revise antes de publicar; o
   (b) cargarlo manualmente desde el panel de Filament si el volumen de
   contenido es bajo (recomiéndame cuál conviene más según cuántas
   páginas/artículos suele tener un sitio institucional típico de este
   tamaño).
2. Si elegimos la opción (a), crea el comando Artisan
   "importar:wordpress {archivo}" que reciba el XML exportado y haga el
   mejor mapeo posible a nuestros modelos, avisando en pantalla qué
   contenido no pudo clasificar automáticamente para que lo revise a
   mano.
3. En cualquier caso, dime también qué hacer con las imágenes actuales
   del sitio (cómo descargarlas y dónde deben quedar guardadas en el
   nuevo proyecto).

No inventes contenido: si no tienes el export de WordPress todavía, deja
el comando lista para cuando yo te pase el archivo, y explícame en el
archivo MIGRACION_CONTENIDO.md los pasos exactos que debo seguir en mi
panel de WordPress para generar ese export.
```

---

## Prompt 7 — SEO técnico y rendimiento

```
Antes de desplegar, deja el sitio optimizado en SEO técnico y rendimiento:

1. Genera un sitemap.xml dinámico que incluya todas las páginas
   publicadas en ambos idiomas, con las etiquetas hreflang correctas
   entre la versión ES y EN de cada página.
2. Crea un robots.txt que permita la indexación y apunte al sitemap.
3. Agrega Open Graph y Twitter Card básicos (título, descripción, imagen)
   en el layout, tomando los datos de cada página/registro cuando
   existan.
4. Optimiza las imágenes subidas (usa las conversiones de Spatie Media
   Library para generar tamaños apropiados para web, no las originales
   sin comprimir).
5. Activa el cache de vistas y rutas de Laravel para producción
   (recuérdame los comandos exactos que debo correr después de cada
   despliegue: config:cache, route:cache, view:cache).
6. Revisa que el sitio cargue rápido en local y dime si detectas algo
   obviamente pesado (JS/CSS sin minificar, imágenes gigantes, etc.).

Al terminar, dame un resumen de qué quedó configurado y qué debo revisar
manualmente en Google Search Console una vez el sitio esté en línea.
```

---

## Prompt 8 — Pruebas y control de calidad

```
Antes de desplegar a producción, necesito confianza de que todo funciona.

1. Escribe pruebas automatizadas (Pest o PHPUnit, el que ya use el
   proyecto) que cubran al menos: que las páginas públicas principales
   responden 200 en ambos idiomas, que el contenido no publicado
   (is_published = false) no aparece en el sitio público, que el
   formulario de contacto guarda el mensaje y rechaza datos inválidos,
   y que un usuario autenticado puede entrar al panel de Filament
   mientras que uno no autenticado no puede.
2. Corre toda la suite de pruebas y muéstrame el resultado.
3. Dame una checklist manual (en un archivo CHECKLIST_QA.md) de cosas que
   debo revisar yo mismo en el navegador antes de dar por bueno el sitio:
   por ejemplo, ver el sitio en móvil y escritorio, probar el selector de
   idioma en cada página, probar el formulario de contacto de verdad,
   revisar que no haya texto en el idioma equivocado en ningún lado, y
   revisar ortografía de los textos migrados.

Al terminar, dime si hay algo que quedó frágil o que recomiendas revisar
con más cuidado antes de publicar.
```

---

## Prompt 9 — Preparación para desplegar en Hostinger

```
Vamos a preparar todo para desplegar este proyecto en mi hosting compartido
de Hostinger (con acceso SSH y Composer, sin Docker ni VPS).

1. Crea un archivo DESPLIEGUE_HOSTINGER.md con la guía paso a paso para
   subir este proyecto a Hostinger, incluyendo: cómo conectarse por SSH,
   cómo subir el código (git clone/pull o subida manual), cómo correr
   composer install --no-dev --optimize-autoloader, cómo configurar el
   archivo .env de producción (con los datos reales que yo debo llenar:
   base de datos, correo SMTP, APP_URL, APP_KEY), cómo correr las
   migraciones (php artisan migrate --force), cómo enlazar el storage
   (php artisan storage:link), y los comandos de cache de producción del
   prompt anterior.
2. Explica también cómo debe quedar apuntado el dominio: como Laravel
   sirve desde la carpeta "public/", indícame cómo configurar el
   "Document root" en Hostinger para que apunte ahí (o la alternativa de
   mover el contenido de "public/" si Hostinger no permite cambiar el
   document root en mi plan).
3. Dime qué debo verificar ANTES de migrar el dominio real
   (grupoedima.com) hacia este nuevo sitio: certificado SSL, redirecciones
   301 desde las URLs viejas de WordPress hacia las nuevas si cambian de
   estructura, y un plan de respaldo por si algo falla (cómo volver
   rápido al WordPress actual mientras se resuelve).
4. Prepara un archivo .env.example completo y actualizado con todas las
   variables que el proyecto necesita, con comentarios explicando cada
   una en español.

No ejecutes ningún despliegue real todavía — solo deja todo documentado y
listo para que lo hagamos juntos paso a paso.
```

---

## Notas finales

- Después del Prompt 9 el sitio queda listo para desplegar; el despliegue en sí conviene hacerlo conmigo acompañando paso a paso (no es un prompt más, es una sesión guiada).
- Cuando el sitio esté en línea y con el contenido real cargado, dime y te preparo un manual corto en Word para la persona que va a administrar el contenido en el panel de Filament — ahí sí conviene un documento aparte, ya con capturas de pantalla del panel real.
- Si en algún prompt Claude Code te propone una librería distinta a la que menciono aquí (por ejemplo, otro paquete de campos traducibles para Filament), está bien mientras cumpla el mismo objetivo — pídele que te explique brevemente por qué la eligió y sigue adelante.
