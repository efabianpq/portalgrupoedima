# Estrategia de contenido — Grupo Edima

**Fecha:** 2026-08-16
**Entrada:** `storage/migration/content.json` (extracción literal del sitio anterior)
**Salida:** `storage/migration/content-v2.json` + este documento
**Estado:** Fase A aprobada. Fase B redactada y cargada en el portal.

---

## 0. Posicionamiento aprobado

> **Consultora colombiana especializada en HOPEX. Implementamos, migramos y damos
> soporte a la plataforma en español para organizaciones de Colombia y Ecuador, y
> acompañamos su adopción hasta que los equipos de arquitectura, gobierno y riesgo
> la usan en sus decisiones.**

Combina el posicionamiento **C** (cercanía regional — verificable hoy, defendible
frente a consultoras globales) con el refuerzo **A** (adopción — ataca el temor real
del comprador: pagar una plataforma que nadie termina de usar).

Se descartó el posicionamiento **B** (foco en decisión) porque exige casos con cifras
que todavía no existen.

**Compradores:** CIO, gerente de arquitectura empresarial, líder de gobierno de TI,
oficial de riesgo y cumplimiento. Organizaciones grandes en Colombia y Ecuador.

---

## 1. Diagnóstico del sitio anterior

El sitio describía *qué* se vendía con corrección, pero no daba a un CIO ninguna razón
para llamar a Grupo Edima en vez de a la competencia, ni un camino para hacerlo. El
problema no era la redacción: era la ausencia de evidencia y de arquitectura de
conversión.

| Sección anterior | Veredicto | Motivo |
|---|---|---|
| Datos de contacto | Conservado | Verificables. Se añade Ecuador al alcance declarado. |
| Nombres de los 6 servicios | Conservado como taxonomía | La descomposición del ciclo (implementar / migrar / personalizar / soportar / formar / asesorar) es correcta. El texto se reescribió por completo. |
| Logo y marca | Conservado | Único material propio junto a los logos de cliente. |
| Hero de 3 slides | Reescrito → 1 mensaje | Los tres slides decían casi lo mismo. Un carrusel reparte la atención y ningún mensaje gana. |
| "About Us" | Reescrito | Base correcta, pero no sostenía credibilidad: sin origen, sin método, sin certificaciones. |
| 6 descripciones de servicio | Reescrito y expandido | Eran párrafos intercambiables de ~60 palabras. No respondían qué se entrega, en cuánto tiempo, quién participa ni cómo se mide. |
| "Why Choose Us" | Reescrito | Los títulos 2 y 3 estaban cruzados respecto a sus textos, y las afirmaciones no tenían evidencia. |
| "We are expert in" | Reescrito, sustancia conservada | El mejor contenido del sitio, pero eran ~200 palabras en un bloque único, con una errata de espaciado. |
| 4 barras de habilidad al 100 % | **Eliminado** | Autoevaluarse 100 % en las cuatro resta credibilidad. No es un dato. |
| 4 contadores (99 %, 19 %, 59 %, 99 %) | **Eliminado** | Valores de demostración del tema. "19 % Clientes" no significa nada. |
| Logos de clientes | Conservado condicionado | Principal activo de credibilidad, pero requiere autorización escrita y depuración. |
| 4 enlaces de redes sociales | **Eliminado** | Apuntaban a los dominios por defecto del tema, no a perfiles reales. |
| Páginas `/blog/`, `/page/`, `/contact/` | **Eliminado** | Stubs vacíos. |
| 4 entradas de blog demo | **Eliminado** | Contenido de relleno del tema. |
| Crédito "VW Themes" | **Eliminado** | Crédito del tema anterior. |
| 14 CTA con `href=""` | Reescrito por completo | Ningún botón llevaba a ningún lado: había tráfico sin forma de convertir. |
| SEO | Nuevo | No había `<title>`, ni meta description, ni Open Graph. |
| Contenido en español | Nuevo | La página `/es/` del sitio anterior devolvía 404. |

---

## 2. Afirmaciones no verificables

Ninguna se publicó sin respaldo. Las que no se pudieron confirmar quedaron como
`[PENDIENTE: …]` visibles en el panel, para completarlas o retirarlas.

### Riesgo alto — comercial o legal

| # | Afirmación anterior | Qué se necesita | Estado |
|---|---|---|---|
| 1 | "OFFICIAL BIZZDESIGN HOPEX PARTNER" | Nivel exacto de partnership y su denominación oficial en el acuerdo vigente | `[PENDIENTE]` — no publicado |
| 2 | 7 logos de cliente | Autorización escrita de uso de marca de cada organización | `[PENDIENTE]` — bloque no publicado |
| 3 | Logo de Bizzdesign entre los "clientes" | Es el fabricante, no un cliente | Retirado de clientes |
| 4 | "BizzDesign HOPEX" / "HOPEX by Bizzdesign" | Nomenclatura oficial de producto y fabricante | Se usa **"HOPEX"** solo; la atribución del fabricante queda `[PENDIENTE]` |

### Riesgo medio — cifras

| # | Afirmación anterior | Estado |
|---|---|---|
| 5-8 | 99 % horas de soporte · 19 % clientes · 59 % proyectos · 99 % horas de capacitación | Eliminadas. Bloque de cifras `[PENDIENTE]`, sin publicar |
| 9 | 4 barras al 100 % | Eliminadas sin reemplazo |

### Riesgo medio — capacidad sin evidencia

| # | Afirmación anterior | Tratamiento |
|---|---|---|
| 10 | "proven framework" / "proven methodology" | Sustituido por la descripción de las fases reales del método. El adjetivo se eliminó. |
| 11 | "industry best practices" | Sustituido por los marcos nombrados: TOGAF, ArchiMate, COBIT, ISO 31000 |
| 12 | "enterprises across Latin America" | Reducido a **Colombia y Ecuador** |
| 13 | "deep technical mastery" | Eliminado. Se reemplaza por certificaciones cuando existan |

### Datos pendientes que fortalecerían el sitio

Año de fundación · certificaciones del equipo · tamaño y composición del equipo ·
número de implementaciones e industrias · módulos de HOPEX implementados · versiones
soportadas · duración típica real por servicio · al menos un caso con resultado
medible · testimonios atribuibles · modalidades y SLA de soporte.

---

## 3. Mapa de sitio

Se conservaron los nombres de ruta existentes; sólo cambiaron los URI donde se indica.

| URL (ES / EN) | Ruta | Propósito | Intención | Justificación |
|---|---|---|---|---|
| `/es` · `/en` | `home` | Calificar y orientar | Navegacional | Único punto donde el comprador decide si eres relevante |
| `/es/servicios` · `/en/services` | `services` | Hub de los 6 servicios | Comercial | Página comercial principal y nodo de enlazado interno |
| `/es/servicios/{slug}` ×6 | `services.show` | Profundidad real | Comercial de cola larga | Cambio de mayor impacto: un comprador corporativo no decide con 60 palabras |
| `/es/plataforma-hopex` · `/en/hopex-platform` | `hopex` **(nueva)** | Qué es HOPEX y qué se implementa | Informativa alta | Captura al que investiga la herramienta antes de buscar quién la implementa |
| `/es/casos-de-exito` · `/en/case-studies` | `projects` *(URI nuevo)* | Evidencia | Evaluación | "Casos de éxito" comunica evidencia; "proyectos" no |
| `/es/casos-de-exito/{slug}` | `projects.show` | Caso con resultado medible | Evaluación | Plantilla lista, contenido pendiente del cliente |
| `/es/nosotros` · `/en/about-us` | `about` | Credibilidad | Due diligence | Se revisa antes de invitar a una licitación |
| `/es/equipo` · `/en/team` | `team` | Personas y certificaciones | Due diligence | **Oculta del menú hasta que existan perfiles reales** |
| `/es/recursos` · `/en/resources` | `blog` *(URI nuevo)* | Blog técnico | Informativa | "Recursos" fija expectativa de utilidad |
| `/es/recursos/{slug}` | `blog.show` | Artículo | Informativa | Captación de cola larga |
| `/es/contacto` · `/en/contact` | `contact` | Conversión | Transaccional | Formulario ya operativo |

**Fuera del mapa por ahora:** páginas por industria y por país. Tienen sentido
comercial, pero sólo cuando existan casos de éxito que las sostengan.

---

## 4. Arquitectura de conversión

Dos niveles, para no exigir una decisión grande a quien acaba de llegar.

| Página | CTA primario → destino | CTA secundario |
|---|---|---|
| Inicio | Conversemos sobre tu arquitectura → `/es/contacto` | Ver servicios |
| Servicios (hub) | Solicitar una conversación técnica → `/es/contacto` | Cada tarjeta → su detalle |
| Servicio (detalle) | Solicitar propuesta → `/es/contacto?servicio={slug}` | Ver casos relacionados |
| Plataforma HOPEX | Evaluar HOPEX para tu organización → `/es/contacto` | Cómo lo implementamos |
| Caso de éxito | Conversemos sobre un caso similar → `/es/contacto` | Servicios relacionados |
| Nosotros | Hablar con el equipo → `/es/contacto` | — |
| Recurso | Conversemos sobre esto → `/es/contacto` | Artículos relacionados |

El parámetro `?servicio={slug}` precarga el asunto en el formulario, para saber qué
servicio origina cada consulta.

---

## 5. Plantilla de caso de éxito

Campos previstos. Los que queden vacíos se ocultan solos, así se puede publicar un
caso parcial sin que se vea roto.

Sector · Perfil de la organización · Situación inicial · Alcance · Módulos de HOPEX ·
Marcos aplicados · Duración · Equipo · Entregables · Resultados medibles · Cita
atribuible del cliente · Servicios relacionados.

---

## 6. Recursos — 10 títulos propuestos

Cargados en el panel como **borradores sin publicar**, alineados a búsquedas reales de
CIO, gerente de arquitectura, líder de gobierno de TI y oficial de riesgo.

1. Qué es HOPEX y para qué sirve en una organización grande
2. Cuánto dura realmente una implementación de HOPEX (y de qué depende)
3. Cómo justificar ante el comité la inversión en una herramienta de arquitectura empresarial
4. Racionalización de aplicaciones: qué se conserva, qué se integra y qué se retira
5. TOGAF, ArchiMate y HOPEX: qué aporta cada uno y dónde se confunden
6. Errores frecuentes al migrar un repositorio de arquitectura a HOPEX
7. Cómo estructurar un mapa de capacidades de negocio que el negocio use
8. Gobierno de TI con COBIT sobre HOPEX: del marco al control operativo
9. Gestión de riesgo tecnológico con ISO 31000 dentro de HOPEX
10. Qué pedirle a un implementador de HOPEX antes de firmar

---

## 7. Qué falta para retirar los `[PENDIENTE]`

Prioridad, de mayor a menor impacto comercial:

1. **Autorización de los logos de cliente** → desbloquea el bloque de mayor credibilidad
2. **Nivel de partnership y nomenclatura oficial del fabricante** → desbloquea el hero
3. **Un caso de éxito con resultado medible** → desbloquea toda la sección de evidencia
4. **Certificaciones del equipo** → desbloquea "Nosotros" y "Equipo"
5. **Duración típica por servicio** → completa las 6 páginas de servicio
6. **Cifras reales** (clientes, proyectos, horas) → permite reponer el bloque de cifras
7. **Modalidades y SLA de soporte** → completa la página de soporte
