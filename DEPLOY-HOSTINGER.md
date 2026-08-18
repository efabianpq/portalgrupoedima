# Despliegue en Hostinger

Guía paso a paso para publicar el sitio en el hosting compartido de
Hostinger. Corresponde a la Fase 9 de `prompts.md`. Antes de seguir esta guía,
repasa `CHECKLIST_QA.md` en local — es más barato encontrar un problema en
`localhost:8000` que en producción.

Contexto que ya está resuelto en el código (no lo repitas ni lo cuestiones,
ver `CLAUDE.md`): `QUEUE_CONNECTION=sync`, conversiones de imagen
`nonQueued()`, sin websockets, sin procesos persistentes — todo pensado para
hosting compartido sin workers.

---

## 0. Qué necesitas antes de empezar

- Plan de Hostinger con **PHP 8.3** y acceso **SSH** habilitado (hPanel →
  Avanzado → SSH Access).
- Una base de datos MySQL creada desde hPanel (Bases de datos → Nueva base de
  datos MySQL), con usuario y contraseña. Anota host, nombre, usuario y clave.
- Un dominio o subdominio apuntando a la cuenta.
- Composer disponible por SSH (Hostinger lo trae instalado; confirma con
  `composer --version`). Node **no** hace falta en el servidor: los assets se
  compilan en local y se suben ya construidos.

---

## 1. Compilar en local antes de subir

```bash
npm run build       # genera public/build — ya se hizo y se probó en esta sesión
composer install    # confirma que no falta ninguna dependencia
php artisan test     # 76 pruebas deben seguir en verde antes de subir
./vendor/bin/pint    # formato de código
```

`public/build/` **debe subirse tal cual** al servidor: no se recompila ahí.

---

## 2. Subir el código

Dos formas, según lo que tengas configurado:

**Opción A — Git (recomendada si Hostinger tiene acceso a tu repositorio):**
```bash
ssh usuario@tu-servidor.hostinger.com
cd ~/domains/tudominio.com
git clone <url-del-repo> temp && rsync -a temp/ public_html/ && rm -rf temp
```

⚠️ **`public/build/` está en `.gitignore`** (es lo normal en un proyecto
Laravel: los assets compilados no se versionan). Un `git clone` en el
servidor **no trae los assets** — súbelos aparte por SCP desde tu máquina,
después de haber corrido `npm run build` en el paso 1:
```bash
scp -r public/build usuario@tu-servidor.hostinger.com:~/domains/tudominio.com/public_html/public/build
```

**Opción B — subir un .zip por el Administrador de Archivos de hPanel** con
todo el proyecto (incluyendo `public/build/`, generado en el paso 1, y sin
`node_modules/`, `.git/` ni `vendor/` — esta última se instala en el paso 3) y
descomprimirlo en el servidor. Esta opción es más simple justamente porque no
tienes que preocuparte por el `.gitignore`: subes el árbol completo tal cual
quedó tras `npm run build`.

En ambos casos, **la carpeta pública del proyecto (`public/`) debe ser la que
Hostinger sirve como raíz del dominio**, no todo el repositorio. Dos maneras
de lograrlo:

- Si tu plan permite cambiar el "Document Root" del dominio (hPanel →
  Dominios → Gestionar → cambiar la carpeta raíz): apúntalo directamente a
  `public_html/nombre-del-proyecto/public`.
- Si no lo permite (algunos planes fuerzan `public_html` como raíz): sube el
  proyecto **fuera** de `public_html` (p. ej. en `~/portalgrupoedima`) y deja
  en `public_html` sólo un enlace simbólico:
  ```bash
  rm -rf public_html   # sólo si está vacía / es la instalación por defecto
  ln -s ~/portalgrupoedima/public public_html
  ```

---

## 3. Instalar dependencias PHP en el servidor

```bash
cd ~/portalgrupoedima   # o la ruta donde quedó el proyecto
composer install --no-dev --optimize-autoloader
```

`--no-dev` excluye PHPUnit, Faker y demás herramientas de desarrollo — no se
necesitan en producción y reducen superficie de ataque.

---

## 4. Configurar `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con un editor por SSH (`nano .env`) y completa:

```
APP_NAME="Grupo Edima"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<el que creaste en hPanel>
DB_USERNAME=<el que creaste en hPanel>
DB_PASSWORD=<la contraseña de esa base>

QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_SCHEME=ssl
MAIL_USERNAME=tu-correo@grupoedima.com
MAIL_PASSWORD=<contraseña del correo>
MAIL_FROM_ADDRESS="tu-correo@grupoedima.com"
MAIL_FROM_NAME="Grupo Edima"

CONTACT_NOTIFICATION_EMAIL=<correo que debe recibir los mensajes del formulario>
```

⚠️ **`APP_DEBUG=false` es obligatorio en producción** — con `true`, cualquier
error PHP expone rutas del servidor, consultas SQL y variables de entorno a
cualquier visitante.

---

## 5. Base de datos y contenido

```bash
php artisan migrate --force
php artisan db:seed --force                        # páginas institucionales + configuración
php artisan db:seed --class=ContentV2Seeder --force # servicios, soluciones, blog (borradores)
```

Los tres seeders son idempotentes (ver `CLAUDE.md`): si necesitas volver a
correrlos por cualquier motivo, no duplican contenido.

**Ningún seeder crea Proyectos, Equipo ni Testimonios** — esas secciones
quedan vacías hasta que haya datos reales, tal como en local.

---

## 6. Enlaces, permisos y caché

```bash
php artisan storage:link
```

Confirma que `storage/` y `bootstrap/cache/` sean escribibles por el proceso
PHP (normalmente ya lo son en Hostinger; si da error de permisos):
```bash
chmod -R 775 storage bootstrap/cache
```

Cachea configuración para producción (mejora el rendimiento en cada request):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

⚠️ Si después de esto cambias algo en `.env` o en las rutas, tienes que volver
a correr `php artisan optimize:clear` y luego cachear de nuevo — si no, el
sitio sigue usando la configuración vieja.

---

## 7. Crear el usuario real del panel

Las credenciales de `admin@grupoedima.com` son **sólo de desarrollo local**
(ver `CLAUDE.md`). En producción:

```bash
php artisan make:filament-user
```

Usa un correo y contraseña reales de quien va a administrar el contenido.

---

## 8. Cron (opcional, recomendado)

Hoy el proyecto no tiene tareas programadas propias (`routes/console.php`
sólo trae el comando de ejemplo de Laravel), pero Hostinger permite dejar el
scheduler de Laravel corriendo para cuando se necesite (limpieza de sesiones,
tareas futuras) sin tener que volver a esta guía. En hPanel → Avanzado →
Cron Jobs, añade, cada minuto:

```
* * * * * cd /home/usuario/portalgrupoedima && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Verificación final

- Abre `https://tudominio.com` y repite la sección 1 de `CHECKLIST_QA.md`
  (visual, selector de idioma) ya en el dominio real.
- `https://tudominio.com/sitemap.xml` y `/robots.txt` deben responder 200.
- Entra a `/admin`, inicia sesión con el usuario creado en el paso 7 y sube al
  menos una imagen de servicio o solución para confirmar que las conversiones
  (`miniatura`, `web`) se generan sin cola (Hostinger no tiene workers).
- Envía un mensaje real desde `/contacto` y confirma que llega el correo a
  `CONTACT_NOTIFICATION_EMAIL`.
- Fuerza un error 500 temporalmente (p. ej. renombrando `.env`) para
  confirmar que con `APP_DEBUG=false` el visitante ve una página genérica, no
  el detalle del error — y vuelve a poner `.env` en su sitio.

---

## Actualizaciones futuras

Cuando haya cambios de código que subir:

```bash
git pull            # o volver a subir el .zip
composer install --no-dev --optimize-autoloader   # sólo si cambió composer.lock
```
```bash
# En LOCAL, antes o después del git pull en el servidor:
npm run build
scp -r public/build usuario@tu-servidor.hostinger.com:~/domains/tudominio.com/public_html/public/build
```
```bash
# De vuelta en el servidor:
php artisan migrate --force                        # sólo si hay migraciones nuevas
php artisan optimize:clear && php artisan optimize  # refresca toda la caché
```

Nunca corras `php artisan db:seed` sin `--class=` en producción una vez que
el panel ya tenga contenido real editado: `DatabaseSeeder` no borra nada, pero
tampoco hace falta volver a sembrar páginas que ya existen.
