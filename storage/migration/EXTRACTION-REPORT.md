# Informe de extracción — grupoedima.com

**Fecha:** 2026-08-16
**Origen:** <https://grupoedima.com/> — WordPress 6.9.6, tema `local-business-wordpress-theme` (VW Themes)
**Salida:** `storage/migration/content.json` + `storage/migration/assets/`

## Método

El contenido real **no está en posts ni páginas de WordPress**: vive en opciones del
Customizer del tema. Por eso la fuente de verdad fue el **HTML renderizado** de cada
URL, parseado con DOM/XPath. `/wp-json/` se usó sólo como fuente complementaria para
inventariar posts, páginas, medios y tipos de contenido.

El texto se transcribió **literalmente**, sin corregir, reescribir ni traducir.

---

## URLs recorridas

| URL | Estado | Qué es |
|---|---|---|
| `https://grupoedima.com/` | 200 | **Home — todo el contenido real del sitio** |
| `https://grupoedima.com/blog/` | 200 | Stub vacío (sólo `<h1>Blog`) |
| `https://grupoedima.com/page/` | 200 | Stub vacío (sólo `<h1>Page`) |
| `https://grupoedima.com/contact/` | 200 | Stub vacío (sólo `<h1>Contact`) |
| `https://grupoedima.com/es/home-espanol/` | **404** | ⚠️ Página en español: existe en WP pero la URL está rota |
| `/wp-json/wp/v2/pages`, `/posts`, `/media` | 200 | Inventario complementario |
| `/wp-json/wp/v2/testimonials`, `/team` | 404 | CPT no expuestos en REST |
| `/wp-sitemap.xml` (+ sub-índices) | 200 | Descubrimiento de URLs |

El menú principal tiene **un solo enlace** ("Home"), así que no había navegación interna
que recorrer más allá de lo que apareció en el sitemap.

---

## Árbol de secciones encontrado

```
content.json
├── extraction              metadatos de la extracción
├── urls_visited            11 URLs con su estado
├── source_inventory        inventario WordPress (páginas, posts, CPT)
├── seo                     metadatos por URL (home, blog, page, contact)
├── top_bar                 país, teléfono, correo, 4 redes sociales
├── branding                logo + 3 favicons
├── navigation              1 elemento de menú + CTA de cabecera
├── hero_slides             3 slides
├── about                   encabezado, subtítulo, misión, 4 bullets, imagen, CTA
├── services                6 servicios (icono, descripción, CTA, enlace)
├── why_choose_us           3 bloques (imagen, título, descripción)
├── expertise               párrafo + 4 barras de habilidad
├── facts                   4 contadores
├── clients                 7 logos
├── footer                  copyright + crédito del tema (separados)
├── secondary_pages         blog / page / contact (los 3 vacíos)
├── assets                  31 archivos (URL original + ruta local)
└── inconsistencies         hallazgos, agrupados por tipo
```

### Conteos

| Sección | Elementos |
|---|---|
| `hero_slides` | 3 |
| `services` | 6 (IMPLEMENTATION, TRAINING, CUSTOM DEVELOPMENT, SUPPORT, MIGRATION, ACCOMPANIMENT) |
| `why_choose_us` | 3 |
| `expertise.skills` | 4 (todas al 100 %) |
| `facts` | 4 |
| `clients` | 7 |
| `navigation.items` | 1 |
| `assets` | 31 (todos descargados, 0 fallos) |

---

## Assets

Los 31 archivos se descargaron a `storage/migration/assets/`, conservando su ruta
relativa original. Todos verificados como PNG válidos (~2.2 MB en total).

- **Propios de Grupo Edima** (`wp-content/uploads/`): el logo (+3 favicons) y los 7
  logos de clientes. **11 archivos.**
- **Genéricos del tema** (`wp-content/themes/...`): imágenes de los 3 slides, 6 iconos
  de servicio, 3 iconos de "why choose us", 4 iconos de contador, la imagen de "About"
  y 4 fondos. **20 archivos** — son ilustraciones de stock del tema, no material de la
  marca; conviene reemplazarlas al migrar.

---

## Campos vacíos y enlaces rotos

### SEO: prácticamente inexistente
- **`<title>` vacío en las 4 páginas** (`<title></title>`).
- **Sin meta description** en ninguna página.
- **Sin Open Graph ni Twitter Card** en ninguna página.
- Sin `meta robots`. Sólo hay `canonical`.

Hay que redactar títulos y descripciones desde cero para las dos versiones de idioma;
no hay nada que migrar aquí.

### 14 CTA sin destino (`href=""`)
Todos los botones del sitio apuntan a la nada. Registrados como `null`:

- Hero: "Get Started" y "Contact Us" en los 3 slides (6)
- About: "READ MORE" (1)
- Servicios: "Read More" en los 6 servicios (6)
- Cabecera: "CONTACT US" (1)

### Redes sociales: son los valores por defecto del tema
Los 4 enlaces apuntan a `twitter.com/`, `facebook.com/`, `linkedin.com` y
`pinterest.com/` — dominios raíz, **no perfiles de Grupo Edima**. El cuarto icono usa
la clase `gplus` (Google+, extinto) pero enlaza a Pinterest.

### Página en español rota
`/es/home-espanol/` existe en WordPress (página id 96, estado `publish`) pero devuelve
**404**. No fue posible extraer ningún contenido en español desde ahí: **todo el
contenido en español del nuevo sitio habrá que escribirlo o traducirlo.**

### Pie de página
Las 4 columnas de widgets están vacías. **No hay textos legales** (privacidad,
términos, cookies) en ninguna parte del sitio.

---

## Inconsistencias detectadas

### 1. Mezcla de idiomas: la sección de contadores está en español
Todo el sitio está en inglés **excepto** las etiquetas de "Some Facts About us"
(encabezado en inglés, etiquetas en español). Marcadas con `locale: "es"` en el JSON:

| Valor | Etiqueta | Idioma |
|---|---|---|
| 99% | Horas de soporte | es |
| 19% | Clientes | es |
| 59% | Proyectos | es |
| 99% | Horas de capacitación | es |

### 2. Los contadores muestran porcentajes sin sentido
Los valores son porcentajes heredados del tema, pero las etiquetas describen
cantidades: "19% Clientes" y "59% Proyectos" no significan nada. Son valores de
demostración, **no métricas reales** — hay que pedir las cifras verdaderas.

### 3. Barras de habilidad, todas al 100 %
Las 4 barras de "we are expert in" están al 100 %. Es el valor por defecto del tema;
conviene confirmar si es intencional antes de reproducirlo.

### 4. Contenido de demostración del tema, no migrable
- Las **4 entradas del blog** son demo: "Hello world!" y "Praesent suscipit m1/m2/m3"
  (texto lorem ipsum).
- Los CPT **`team1`–`team4`** y **`testimonial1`–`testimonial4`** aparecen en el sitemap
  pero **no se renderizan en la home** ni están expuestos en REST. Se asumen demo.
  ⚠️ Si hay integrantes del equipo o testimonios reales, **no están publicados en el
  sitio actual** y habrá que capturarlos aparte.
- Las páginas `/blog/`, `/page/` y `/contact/` no tienen contenido propio.

### 5. Textos duplicados
- Los 3 slides repiten los mismos dos textos de botón.
- Los 6 servicios repiten el mismo texto de botón ("Read More").
- El nombre de cada servicio aparece dos veces en el HTML (pestaña + panel);
  consolidado en un solo registro por servicio.

### 6. Errata en el origen, conservada sin corregir
En el párrafo de `expertise` faltan espacios entre dos oraciones
(`...objectives.Our experience spans...`). Se transcribió **tal cual**; corregirlo es
decisión de quien edite el contenido.

### 7. Logos de clientes
El atributo `alt` de los 7 logos es genérico ("Image"), así que el nombre se **infirió
del nombre de archivo** (`Logo_pronaca-1.png` → Pronaca): Pronaca, Colanta, Bizzdesign,
Solunion, Sura, Celec, Evolve.
⚠️ **Bizzdesign es el fabricante de HOPEX** (socio/proveedor), no un cliente — está
mezclado en la misma fila de logos. Conviene separarlo al migrar.

### 8. El correo estaba ofuscado
El correo del top bar está protegido por Cloudflare (`data-cfemail`). Se decodificó:
`fabian.pachon@grupoedima.com`.

### 9. Crédito del tema en el pie
El pie incluye "Design & Developed by VW Themes" (con enlace a vwthemes.com). Se guardó
en un campo aparte (`footer.theme_credit`) marcado como **no migrar**.

---

## Recomendaciones para la migración

1. **Todo el contenido en español hay que crearlo**: el sitio actual sólo tiene inglés
   utilizable y la página `/es/` está rota.
2. **Redactar todo el SEO desde cero** (títulos y descripciones, ES e EN).
3. **Pedir las cifras reales** de los contadores y decidir qué hacer con las barras al 100 %.
4. **Definir los destinos de los 14 CTA** y las URL reales de redes sociales.
5. **Conseguir equipo y testimonios reales** — no existen en el sitio actual.
6. **Reemplazar las 20 imágenes genéricas del tema** por material propio.
7. **Separar Bizzdesign** de los logos de clientes (es socio, no cliente).
8. Redactar los **textos legales**, que hoy no existen.

## Correspondencia con el modelo de datos nuevo

| Sección extraída | Destino sugerido |
|---|---|
| `about`, `hero_slides` | `Page` (`home`, `about`) + `sections` |
| `services` (6) | `Service` |
| `clients` (7) | *(sin modelo aún — evaluar)* |
| `facts`, `expertise`, `why_choose_us` | `Page.sections` de la home |
| `top_bar`, `branding`, `footer` | `SiteSetting` |
| `secondary_pages`, blog demo | descartar |
| `team`, `testimonials` | `TeamMember` / `Testimonial` — **contenido por conseguir** |
