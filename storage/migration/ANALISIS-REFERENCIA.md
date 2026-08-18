# Análisis de referencia — Bizzdesign Hopex (Fase A)

**Fecha:** 2026-08-16
**Referencia analizada:** bizzdesign.com/es/suite-de-transformacion/hopex,
bizzdesign.com/es/soluciones (+ 2 páginas hija en profundidad), y
bizzdesign.com/es/empresa/nuestros-partners.
**Método:** WebFetch con prompts que piden estructura, taxonomía y nomenclatura,
nunca el texto de marketing. No se copió ningún párrafo de la referencia; lo que
sigue son notas propias sobre organización y vocabulario del fabricante.
**Estado:** Fase A — pendiente de tu aprobación antes de redactar nada (Fase B).

---

## A1. Taxonomía de la referencia

Bizzdesign organiza su oferta en **10 disciplinas ("soluciones")**, agrupadas en
3 categorías de transformación, más una capa transversal:

**Transformation Collaboration** *(transversal, no es una disciplina)*
- Colaboración visual entre equipos durante la toma de decisiones.

**Planificación**
1. Gestión Estratégica de Portafolios — alinea inversión de TI con objetivos de negocio.
2. Gestión del Portafolio de Aplicaciones — racionaliza el catálogo de aplicaciones.
3. Gestión del Portafolio Tecnológico — consolida el stack tecnológico.

**Diseño**
4. Gestión de Arquitectura Empresarial — mapea decisiones de inversión y ejecución.
5. Gestión de Arquitectura de Negocio — capacidades, cadenas de valor, modelo operativo.
6. Gestión de Arquitectura de Soluciones — alinea estrategia con diseño de soluciones.

**Gobernanza**
7. Gestión de Procesos de Negocio — modelado y optimización de procesos.
8. Gobierno, Riesgo y Cumplimiento (GRC) — con 3 sub-módulos: gestión de riesgos,
   gestión de cumplimiento, gestión de ciberresiliencia.
9. Gestión de Datos — estrategia y calidad de datos.

*(La página de Soluciones lista 9 disciplinas con página propia + Transformation
Collaboration = 10 entradas de menú; el listado de "6 módulos integrados" que
aparece en la página del producto Hopex es una síntesis comercial de las mismas
disciplinas, no una taxonomía distinta.)*

El patrón estructural de una página de disciplina (verificado en dos ejemplos:
Arquitectura Empresarial y GRC) es consistente:
1. Hero con propuesta de valor
2. Logos de clientes (prueba social)
3. Planteamiento del problema/brecha que resuelve
4. 3 bloques de capacidades principales (paralelos, con imagen)
5. Sección de cierre/refuerzo
6. CTA final

Dato relevante: **ninguna de las dos páginas de disciplina nombra TOGAF,
ArchiMate, COBIT, ISO 31000 ni analistas (Gartner/Forrester) en el cuerpo**. Esos
marcos aparecen en otras partes del sitio del fabricante (comparativas,
reconocimientos), no como parte del pitch de cada disciplina. Es decir: el
fabricante vende por problema de negocio resuelto, no por marco metodológico.

---

## A2. Comparación contra mi contenido actual (`content-v2.json`)

Hoy no existe un eje "Soluciones" en el sitio de Grupo Edima. Lo más cercano es
el bloque `home.capabilities` ("Áreas de trabajo"), que cubre **4 de las 9
disciplinas del fabricante**, con nombres distintos y menos profundidad:

| Disciplina del fabricante | ¿La cubro hoy? | Cómo |
|---|---|---|
| Gestión de Arquitectura Empresarial | Parcial | `capability_1` ("Capacidades de negocio y estrategia") mezcla esto con Arquitectura de Negocio |
| Gestión de Arquitectura de Negocio | Parcial | Mezclado en `capability_1`, sin nombre propio |
| Gestión de Arquitectura de Soluciones | **No cubierta** | Sin mención |
| Gestión Estratégica de Portafolios | **No cubierta** | Sin mención (distinto de portafolio de aplicaciones) |
| Gestión del Portafolio de Aplicaciones | Parcial | Mezclado en `capability_2` ("Portafolio de TI y gobierno") |
| Gestión del Portafolio Tecnológico | **No cubierta** | Sin mención (no es lo mismo que portafolio de aplicaciones) |
| Gestión de Procesos de Negocio | Sí | `capability_4`, con nombre equivalente |
| Gobierno, Riesgo y Cumplimiento | Parcial | `capability_3` sólo dice "Riesgo y cumplimiento", sin los 3 sub-módulos (riesgo, cumplimiento, ciberresiliencia) |
| Gestión de Datos | **No cubierta en absoluto** | Ni mencionada. Es una omisión notable: el fabricante la trata como disciplina propia y hoy es un tema de interés alto (calidad de datos para IA) |

**Lo que digo yo que ya no corresponde a nomenclatura vigente:**
- Nada activo en el sitio actual usa nomenclatura obsoleta (el contenido-v2 ya
  es cuidadoso). El riesgo estaba en el sitio *anterior* de WordPress
  ("HOPEX by Bizzdesign", "OFFICIAL BIZZDESIGN HOPEX PARTNER" — ver A3), que ya
  no está publicado pero conviene no reintroducir por accidente al redactar
  Fase B.

**Dónde tengo menos profundidad que la esperable:**
- Los 6 `services[]` describen *cómo entrego* (implementación, migración, etc.)
  pero ninguno dice *sobre qué disciplina* trabaja el servicio. Un comprador que
  llega buscando "gestión de riesgo tecnológico" no tiene página a la que
  aterrizar; sólo puede inferir que "Asesoría en arquitectura empresarial" lo
  cubre parcialmente.
- El bloque `capabilities` de la home es 4 tarjetas de 1-2 líneas. El fabricante
  sostiene el mismo contenido con una página completa por disciplina.

---

## A3. Nomenclatura vigente del producto y del partnership

**Producto:** el fabricante se refiere hoy al producto como **"Bizzdesign
Hopex"**, dentro de una familia de productos más amplia (la "Enterprise
Transformation Suite", que también incluye Bizzdesign Unify, Alfabet y
Horizzon). **No usa la variante "HOPEX by Bizzdesign"** en ningún lugar
verificado.

→ Corrección para el sitio: seguir escribiendo **"HOPEX"** a secas, como ya
indica CLAUDE.md, es la opción correcta y más segura — es el nombre del
producto específico que Grupo Edima implementa, sin necesitar mencionar la
suite completa. **No usar "HOPEX by Bizzdesign"** si en algún momento se
redacta la atribución al fabricante.

**Partners:** la página de partners de Bizzdesign **no usa niveles jerárquicos
con nombre** (nada de "Official Partner", "Gold Partner", "Certified Partner").
Usa tres categorías de colaboración, sin ranking entre ellas:
- **Reseller** — revende la suite.
- **Solutions** — implementa soluciones de Bizzdesign (esta es la categoría que
  describe lo que hace Grupo Edima).
- **Technology** — integra su propio producto con el de Bizzdesign.

→ Corrección necesaria: **"OFFICIAL BIZZDESIGN HOPEX PARTNER"**, la frase del
sitio anterior, **no corresponde a ninguna categorización vigente del
fabricante** — no es que esté desactualizada en el nivel, es un término que el
fabricante no usa. Cuando se confirme el estatus contractual, lo verificable
públicamente en la nomenclatura del fabricante sería algo del tipo "Solutions
partner de Bizzdesign", nunca "Official Partner". Esto ya está correctamente
marcado como `[PENDIENTE]` en `content-v2.json` (`about.about_credentials`,
`home.hero`) — la instrucción para Fase B es: cuando se redacte ese bloque, usar
la categoría real que confirme el usuario, no reintroducir "Official Partner".

---

## A4. Mi eje diferencial (lo que la referencia no puede cubrir)

Un sitio de fabricante global no puede, por diseño, hablar de:
- **Cercanía e idioma**: atención en español, mismo huso horario que Colombia y
  Ecuador.
- **Implementación local**: conocimiento del contexto regulatorio de cada país
  (superintendencias, marcos de riesgo locales) que un fabricante global no va
  a detallar en su sitio.
- **Acompañamiento post-implementación**: el fabricante vende la plataforma; el
  riesgo real del comprador (confirmado por el propio posicionamiento ya
  aprobado en `content-v2.json`) es que nadie la termine adoptando. Ese es
  territorio exclusivo del implementador, no del fabricante.
- **Relación continua, no transaccional**: soporte, formación y asesoría
  recurrente en el mismo idioma.

Esto ya es, en esencia, el posicionamiento aprobado en la Fase A anterior
(`CONTENT-STRATEGY.md`, sección 0): cercanía regional + adopción. Propongo
mantenerlo como eje central y **anclarlo explícitamente en cada página de
Solución nueva**: cada página de disciplina debe cerrar con un bloque "Cómo lo
implementamos" que traduzca la disciplina del fabricante a la entrega local de
Grupo Edima — ese bloque es el que el sitio del fabricante no tiene y no puede
tener.

---

## A5. Mapa de sitio propuesto — dos ejes

### Eje 1 — Soluciones (por disciplina, vocabulario del fabricante)

Antes de nombrar las 9 páginas, hay una decisión que requiere tu confirmación
(ver pregunta al final): **¿en cuáles de las 9 disciplinas Grupo Edima tiene
práctica real?** Publicar una página de "Gestión de Datos" sin haber hecho un
solo proyecto de esa disciplina sería inventar capacidad, algo que las reglas
de contenido prohíben. Dos escenarios:

- **Opción conservadora**: publicar sólo páginas de Solución para las
  disciplinas donde ya hay evidencia (hoy: Arquitectura Empresarial, Portafolio
  de Aplicaciones, Procesos de Negocio, GRC — las 4 que ya insinúa
  `home.capabilities`). Las otras 5 quedan fuera del mapa hasta tener caso o
  experiencia que lo sostenga.
- **Opción ampliada**: publicar las 9, pero marcando con `[PENDIENTE:
  confirmar alcance de práctica]` las que no tengan evidencia, igual que se
  hace hoy con cifras y certificaciones.

Propuesta de URLs (siguiendo el patrón `/es/soluciones/{slug}`,
`/en/solutions/{slug}`), condicionadas a la decisión anterior:

| Solución | Slug propuesto | Intención de búsqueda | Audiencia | Lugar en el embudo |
|---|---|---|---|---|
| Arquitectura empresarial | `arquitectura-empresarial` | "qué es arquitectura empresarial", "herramienta EA" | Arquitectura, CIO | Descubrimiento → evaluación |
| Portafolio de aplicaciones | `portafolio-de-aplicaciones` | "racionalización de aplicaciones" | Arquitectura, TI | Evaluación |
| Procesos de negocio | `procesos-de-negocio` | "modelado de procesos" | Arquitectura, negocio | Evaluación |
| Gobierno, riesgo y cumplimiento | `gobierno-riesgo-y-cumplimiento` | "gestión de riesgo tecnológico" | Riesgo y cumplimiento, gobierno TI | Evaluación → decisión |
| *(condicionales a evidencia)* Portafolio tecnológico, Arquitectura de negocio, Arquitectura de soluciones, Gestión estratégica de portafolios, Gestión de datos | — | — | — | — |

Cada página de Solución: qué problema de negocio resuelve la disciplina (sin
copiar al fabricante) → preguntas que responde → bloque "Cómo lo implementamos"
(el eje diferencial de A4) → servicios relacionados → CTA.

### Eje 2 — Servicios (cómo entrego, ya existe)

Se mantiene el catálogo actual de 6 servicios (`content-v2.json`), sin cambios
de fondo en la taxonomía — ya está validado. Cambia el cruce: cada servicio
pasa a enlazar qué Soluciones/disciplinas típicamente involucra.

### Cómo se cruzan

- Página de Solución → CTA secundario "Cómo lo implementamos" enlaza a los
  Servicios relevantes (normalmente Implementación + Asesoría).
- Página de Servicio → nueva sección "Disciplinas que cubre" enlaza a las
  Soluciones relevantes.
- Casos de éxito (cuando existan) se etiquetan por Solución y por Servicio, para
  que ambos ejes puedan mostrar evidencia.
- La home gana un bloque "Soluciones" (grid de las páginas del Eje 1) además
  del bloque "Servicios" que ya tiene.

No se propone tocar `hopex` (la página "Qué es HOPEX"), `about`, `team`,
`contact`, `resources` ni `case_studies` — ya cumplen su función y no está en
el alcance de esta tarea.

---

## Verificación de propiedad intelectual (parcial — sólo Fase A)

Esta fase no redactó copy, sólo notas estructurales y de nomenclatura escritas
por mí a partir de resúmenes generados por WebFetch (no transcripciones). No
hay bloques de texto para chequear todavía. La verificación de coincidencia
textual de 8+ palabras se hará al cierre de la Fase B, sobre el copy final, y
quedará en `VERIFICACION-IP.md`.

Riesgos de marca ya identificados para esa fase, van a `VERIFICACION-IP.md`:
- Nombres de las disciplinas (p.ej. "Gestión de Arquitectura Empresarial") son
  términos de producto/sector: permitido adoptarlos como taxonomía, no como
  cita de su copy.
- No usar logos, íconos ni capturas de Bizzdesign.
- No usar badges de analistas (Gartner/Forrester) — ninguno pertenece a Grupo
  Edima.
