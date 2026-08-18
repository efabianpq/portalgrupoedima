# Verificación de propiedad intelectual — Fase B

**Fecha:** 2026-08-16
**Alcance:** copy nuevo escrito en `content-v2.json` — página `solutions_index` y
las 4 páginas de `solutions[]` (Arquitectura empresarial, Portafolio de
aplicaciones, Procesos de negocio, Gobierno, riesgo y cumplimiento), más los
ajustes al bloque `home.capabilities` y los campos `related_solutions` en
`services[]`.

---

## Método

Todo el copy de estas páginas se redactó desde cero, a partir de:
- El problema de negocio que resuelve cada disciplina (razonamiento propio).
- La taxonomía y el vocabulario de disciplina tomados de A1 (nombres oficiales
  en español: "Arquitectura empresarial", "Portafolio de aplicaciones",
  "Procesos de negocio", "Gobierno, riesgo y cumplimiento" — permitido por la
  regla de propiedad intelectual, son terminología de producto/sector).
- El posicionamiento y las reglas de contenido ya aprobadas en
  `CONTENT-STRATEGY.md` y `CLAUDE.md`.

Importante: en ningún momento de la Fase A se recibió texto literal del sitio
del fabricante. Las tres consultas de WebFetch se formularon explícitamente
pidiendo estructura, taxonomía y nomenclatura, no transcripción; las respuestas
que llegaron a este análisis ya eran resúmenes generados por otro modelo a
partir de la página, no citas. Por diseño, esto hace prácticamente imposible
que el copy final reproduzca una secuencia larga del original: nunca hubo una
oración original en el contexto de redacción para parafrasear de cerca.

## Verificación de coincidencia textual (8+ palabras)

Se revisó cada bloque redactado (`intro`, `questions.items`, `how_we_implement`,
`cta`) contra las notas de estructura obtenidas en Fase A (que son la única
referencia textual disponible, y ya son resúmenes, no el original). No hay
ninguna secuencia de 8 o más palabras en común, en español ni en inglés:

- Los `intro` de cada solución están escritos desde el problema de negocio de
  Grupo Edima ("sin ese modelo, las decisiones... se toman con información
  parcial"), no desde ningún planteamiento visto en el resumen de Bizzdesign.
- Los bloques `questions` son preguntas operativas propias (p. ej. "qué
  aplicaciones están duplicadas o resuelven la misma función"), no aparecen en
  ninguna forma en las notas de estructura del fabricante.
- El bloque `how_we_implement` es, por construcción, el eje que la referencia
  no tiene (A4): describe cómo entrega Grupo Edima, no cómo se presenta el
  producto. No hay overlap posible con una página que no incluye ese contenido.
- No se reutilizó ninguna metáfora del fabricante (p. ej. "360°", "fuente única
  de verdad", "conecta equipos e ideas" — todas evitadas a propósito aunque
  aparecían en los resúmenes de estructura).

**Resultado: sin coincidencias de 8+ palabras detectadas.**

## Riesgos de marca pendientes de autorización

Ninguno nuevo introducido por esta fase. Los ya conocidos (`content-v2.json →
pending[]`) siguen vigentes y no se tocaron:

1. Logos de clientes (Pronaca, Colanta, Solunion, Sura, Celec) — bloque
   `home.clients` sigue sin publicar, sin autorización escrita.
2. Nivel de partnership con Bizzdesign — sigue `[PENDIENTE]`. **Corrección
   aplicada en el análisis (A3):** cuando se redacte, usar la categoría real
   del fabricante ("Solutions partner"), nunca "Official Partner" — ese término
   no corresponde a ninguna categorización vigente de Bizzdesign.
3. No se usó ningún logo, ícono, captura de producto ni badge de analista
   (Gartner/Forrester) de Bizzdesign en ningún bloque nuevo.
4. Los nombres de disciplina se usan como taxonomía (permitido), nunca
   presentados como si Grupo Edima fuera dueña o fabricante de HOPEX — cada
   página de Solución distingue explícitamente "qué resuelve la disciplina"
   (genérico, del producto) de "cómo lo implementamos" (Grupo Edima).

## Pendiente de verificación humana

Esta verificación es de coincidencia textual y de tono, hecha por el mismo
proceso que redactó el copy. Antes de publicar, conviene que una persona lea
las 4 páginas de Solución completas una vez más contra bizzdesign.com/es —no
sólo contra estos resúmenes— como control final independiente.
