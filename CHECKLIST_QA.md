# Checklist de QA manual — antes de publicar

Esto complementa la suite automatizada (`php artisan test`), que cubre lo
verificable por código: rutas, permisos, formularios, contenido publicado vs.
borrador. Lo de aquí abajo necesita ojos humanos.

## 1. Visual — móvil y escritorio

- [ ] Recorrer las 8 páginas principales (Inicio, HOPEX, Servicios, Casos de
      éxito, Recursos, Equipo, Nosotros, Contacto) en escritorio (~1440px) y
      en móvil (~375px, usa las devtools de Chrome o un teléfono real).
- [ ] Menú móvil: abre y cierra bien, no deja scroll bloqueado, los enlaces
      llevan a donde dicen.
- [ ] Ninguna imagen se ve estirada, recortada mal o pixelada.
- [ ] El formulario de contacto se ve y usa bien en una pantalla angosta
      (campos no se salen, botón alcanzable).
- [ ] Revisar en al menos un navegador que no sea Chrome (Firefox, Safari o
      Edge) — sobre todo el menú móvil y el formulario, que usan Alpine.js.

## 2. Selector de idioma

- [ ] Desde cada una de las 8 páginas, cambiar ES → EN y EN → ES: debe caer
      en la página *equivalente*, no en el inicio (salvo que la traducción no
      exista, en cuyo caso cae al inicio de ese idioma — comportamiento
      esperado, ver `LocaleSwitcher`).
- [ ] Un servicio o caso de éxito visto en español, cambiar a inglés: debe
      llevar al mismo contenido en inglés (si está traducido) usando su
      propio slug en inglés, no el slug en español.
- [ ] El idioma activo se ve resaltado en el selector.

## 3. Formulario de contacto (de verdad, no sólo el test automatizado)

- [ ] Enviar un mensaje real desde el sitio en español y confirmar que:
      - aparece en el panel → Mensajes de contacto,
      - si `CONTACT_NOTIFICATION_EMAIL` está configurado, llega el correo.
- [ ] Repetir en inglés y revisar que los mensajes de validación salen en
      inglés si se dejan campos vacíos.
- [ ] Entrar desde una página de servicio a "Solicitar propuesta" y confirmar
      que el asunto llega precargado con el nombre del servicio.
- [ ] Probar un envío con datos inválidos (correo mal escrito, campos vacíos)
      y confirmar que el error se ve junto al campo, sin recargar la página.
- [ ] Enviar 6 mensajes seguidos desde la misma conexión y confirmar que el
      sexto se bloquea con un mensaje de límite alcanzado (máx. 5 por hora).

## 4. Idioma incorrecto en algún lado

- [ ] Leer cada página en español buscando frases o palabras en inglés que se
      hayan colado (encabezados, botones, textos de marcador de posición).
- [ ] Repetir al revés: inglés con texto en español mezclado.
- [ ] Revisar los correos de notificación de contacto (asunto y cuerpo) en
      los dos idiomas.
- [ ] Revisar el panel de administración: todas las etiquetas deben estar en
      español (es la audiencia de quien edita).

## 5. Ortografía y redacción del contenido migrado

- [ ] Leer completo el contenido de `storage/migration/content-v2.json` (o
      directamente cada página ya renderizada) buscando errores de tipeo,
      tildes faltantes y errores de concordancia.
- [ ] Confirmar que ningún `[PENDIENTE: ...]` quedó visible en el sitio
      público (deben estar filtrados por `PublicContent::render()`, pero vale
      la pena mirar con los ojos).
- [ ] Repasar los 20 marcadores `[PENDIENTE: ...]` documentados en
      `CLAUDE.md` y confirmar cuáles siguen pendientes de dato real.
- [ ] Pasar el contenido en español y en inglés por un corrector ortográfico
      (Word, LanguageTool, Grammarly) — el proyecto no tiene uno automatizado
      integrado.

## 6. SEO / metadatos (de la fase anterior, vale la pena revisar una vez más)

- [ ] Ver el código fuente de 2-3 páginas y confirmar que `<title>` y meta
      description tienen contenido real, no vacío ni genérico.
- [ ] Confirmar que `/sitemap.xml` y `/robots.txt` responden bien en el
      dominio final una vez desplegado (no sólo en local).

## 7. Panel de administración

- [ ] Iniciar sesión con el usuario real de producción (no el de desarrollo)
      una vez creado con `php artisan make:filament-user`.
- [ ] Crear/editar un registro de cada tipo (servicio, proyecto, entrada de
      blog, integrante de equipo, testimonio) y confirmar que se ve
      correctamente en el sitio público tras guardar.
- [ ] Subir una imagen en cada tipo de contenido y confirmar que la miniatura
      y la versión web se generan (revisar la carpeta de medios o el propio
      sitio).

## Notas para quien haga esta revisión

- Todo lo de la sección 1-3 se puede hacer directo en
  `http://localhost:8000` (o el vhost de Laragon) corriendo
  `php artisan serve` + `npm run dev`.
- Si algo se ve raro que no debería (estilos rotos, 500 inesperado), correr
  `php artisan optimize:clear` antes de reportarlo — puede ser caché local.
