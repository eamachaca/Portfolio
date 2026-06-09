# Portfolio — Documento del proyecto

Portfolio personal de Eduardo Machaca + backOffice para administrar el
contenido sin tocar código. Este archivo es la fuente de verdad del proyecto:
qué es, cómo está armado y por qué. Vive en el repo.

## 1. Qué es

Sitio one-page (template **ReFrame**) servido por Laravel + un panel admin
con **Filament v5** en `/admin`. Un solo usuario hoy (Eduardo), pero el
esquema lleva `owner_id` desde el día uno: abrir el sitio a más personas en
el futuro es activar capas ya previstas, no reescribir.

## 2. Stack

| Capa | Tecnología |
|------|-----------|
| Framework | Laravel 13 (PHP 8.3+, en dev 8.4 vía devbox) |
| Admin | Filament v5.6 |
| Front | Blade + assets de ReFrame |
| BD dev | SQLite |
| BD prod | MySQL 8 |
| Entorno local | Windows + devbox/WSL |
| Deploy | VPS Contabo (alias SSH `portfolio-vps`) |

## 3. Modelo de datos

Todo lo del usuario lleva `owner_id` (FK a `users`). Excepción:
`contact_messages` no tiene owner (es buzón global).

### `users`
Campos base + `full_name` (nullable), `username` (login), `headline`, `bio`,
`avatar`, `social_links` (json). Implementa `FilamentUser`;
`canAccessPanel()` devuelve `true` para cualquier autenticado en el MVP.
Login del admin es por `username` (no email — ver
`App\Filament\Pages\Auth\Login`).

- **`name`** vs **`full_name`**: `name` es el nombre corto que se ve en
  todas partes (title, sidebar, loading screen, blog headers); `full_name`
  es opcional, se usa solo en la sección About del home. Si no se setea,
  About cae a `name`.

### `experiences`
`company`, `slug` (auto desde company), `logo`, `summary` (nullable, una
descripción única que cubre varios niveles), `tech_stack` (json),
`levels` (json), `sort_order`. Tiene `hasMany Project` vía `experience_id`.

- **`levels`** — array de promociones/roles dentro de la misma empresa:
  `[{role, start_date, end_date, in_progress, description, highlights}]`.
  `description` y `highlights` son opcionales por nivel. Permite todas las
  combinaciones (sin descripción, descripción única en `summary`, descripción
  por nivel, mix).
- **`tech_stack`** vive a nivel empresa (compartido por todos los niveles).
- La página `/experiences/{slug}` muestra el detalle + grid de projects
  shipped en esa empresa.

### `projects`
`title`, `slug`, `excerpt`, `description`, `cover_image`, `gallery` (json),
`tech_stack` (json), `apps` (json, nullable), `experience_id` (FK nullable),
`url`, `repo_url`, `featured`, `sort_order`, `published_at`.

- **`apps`** — proyectos que se entregan como varias apps (web + mobile,
  backend + panel, …). Estructura: `[{name, platform, description,
  tech_stack, links}]`. Null para single-app. La card renderiza una
  sección por app debajo del description.
- **`experience_id`** — FK a `experiences`. Si está seteado, el pill del
  project muestra `#<Company>` y linkea a `/experiences/{slug}`.
  Si es null, renderiza `#Personal` (default de vista, no se almacena).
- **`featured`** — cap duro de `Project::FEATURED_LIMIT` (3) por owner.
  Hook en `saved`: al destacar un cuarto, el más viejo (`updated_at` DESC,
  tiebreak `id` DESC) se des-destaca con `saveQuietly()`.
- **`published_at`** — null = draft (oculto del front).

### `studies`
`institution`, `title`, `field`, `description`, `start_date`, `end_date`,
`in_progress`, `logo`, `sort_order`. Sección "Education" del Resume,
compacta debajo de Experience.

### `skills`
`name`, `category` (nullable), `sort_order`. El front agrupa por categoría
y renderiza pills (sin porcentajes — explícito).

### `services`
`title`, `description`, `icon` (clase de themify, ej. `ti-server`),
`sort_order`. Grid en el home.

### `faqs`
`question`, `answer`, `sort_order`. Render con `<details>` accordion.

### `testimonials`
`author`, `role`, `company`, `quote`, `avatar`, `sort_order`. Grid en home,
sección oculta si la tabla está vacía.

### `posts` (blog)
`title`, `slug` (auto), `excerpt`, `content` (RichEditor → HTML), `cover_image`,
`published_at`, `sort_order`. Rutas `/blog` (paginated index, 9 por página)
y `/blog/{slug}` (detail). Home muestra los 3 más recientes con link a
`/blog`. Sección home oculta si no hay posts publicados.

### `contact_messages`
`name`, `email`, `phone`, `message`, `read_at`. **Sin** `owner_id` — buzón
global. Se crea desde `POST /contact` (formulario público). El admin
(`/admin/inbox`) es read-only: ver/eliminar, no crear ni editar. Mensajes
se marcan como leídos al abrir el detalle. No hay envío por email aún
(ver `NEXT-STEPS.md`).

## 4. BackOffice (Filament)

- Panel en `/admin`, provider `App\Providers\Filament\AdminPanelProvider`
  registrado en `bootstrap/providers.php`.
- Login custom (`App\Filament\Pages\Auth\Login`): autentica por
  `username` + `password`. El email queda como dato de contacto.
- Profile custom (`App\Filament\Pages\Auth\EditProfile`): extiende
  Filament añadiendo `username`, `headline`, `bio`, `avatar` (FileUpload
  con editor) y `social_links` (KeyValue).
- Resources (orden de navegación):
  - `ExperienceResource` — repeater para `levels` (cada uno con DatePicker,
    descripción y sub-repeater de highlights/bullets).
  - `ProjectResource` — repeater para `apps`, Select `experience_id` con
    relationship, FileUpload para cover/gallery, TagsInput para tech_stack.
  - `StudyResource` — básico con DatePicker.
  - `SkillResource` — name + category Select + sort_order.
  - `ServiceResource` — title, description, icon themify, sort_order.
  - `FaqResource` — question, answer, sort_order.
  - `TestimonialResource` — author, role, company, quote, avatar.
  - `ContactMessageResource` — **read-only** (canCreate=false). Inbox con
    filtro Read/Unread, acción "Reply by email" (mailto).
  - `PostResource` — RichEditor para content, FileUpload cover_image,
    DateTimePicker published_at, slug auto.
- **Costura multi-tenant (invisible hoy):**
  - `getEloquentQuery()` en cada resource scopea a `auth()->id()`.
  - `mutateFormDataBeforeCreate()` autosetea `owner_id` al crear.
  - Cada user verá solo lo suyo el día que se active multi-persona.

## 5. Front público

Rutas en `routes/web.php`:

| Ruta | Nombre | Handler |
|------|--------|---------|
| `GET /` | `home` | `PortfolioController@home` |
| `GET /projects` | `projects.index` | `ProjectController@index` |
| `GET /experiences/{slug}` | `experiences.show` | `ExperienceController@show` |
| `GET /blog` | `posts.index` | `PostController@index` |
| `GET /blog/{slug}` | `posts.show` | `PostController@show` |
| `POST /contact` | `contact.store` | `ContactController@store` |

`PortfolioController@home` carga: user, featured projects (top 3),
studies, experiences, services, skills agrupados por category, faqs,
testimonials, recent posts. Si `User::first()` es null, cae a
`App\Support\SampleContent` para que el front no se rompa en BD vacía.

Vistas en `resources/views/theme/`:
- `layouts/app.blade.php` — layout base con menú (`Home`, `About`,
  `Resume`, `Portfolio`, `All Projects`, `Blog`, `Contact`).
- `home.blade.php` — secciones: INTRO, ABOUT, RESUME (Experience grande +
  Education compacto), SERVICES, SKILLS, PORTFOLIO, TESTIMONIALS, FAQ,
  BLOG, CONTACT (formulario funcional). Las secciones de testimonials,
  faq y blog se ocultan si están vacías.
- `projects/index.blade.php` — listado completo.
- `experiences/show.blade.php` — detalle empresa + projects shipped.
- `posts/index.blade.php`, `posts/show.blade.php` — blog.
- `partials/project-item.blade.php` — card de proyecto con lightbox.
  Resuelve imagen según origen, renderiza pill `#<Company>` (linkeable)
  o `#Personal`, sub-apps y links.
- `partials/theme-config.blade.php` — inyecta `config/theme.php` como
  globals JS antes de los scripts de ReFrame.

## 6. Imágenes

Convención: en BD ruta **relativa al disco `public`** (ej.
`projects/covers/abc.webp`), nunca rutas absolutas. Subidas a
`storage/app/public/` + symlink `public/storage` (`php artisan storage:link`).

URLs en vista:
- Externa (`http://`, `https://`) → tal cual.
- Asset de ReFrame (`reframe/...`) → `asset($path)`.
- Cualquier otra cosa → `asset('storage/' . $path)`.

Directorios usados por Filament: `projects/covers/`, `projects/gallery/`,
`studies/logos/`, `experiences/logos/`, `testimonials/`, `posts/covers/`,
`avatars/`.

## 7. ReFrame — licencia

El front usa **ReFrame**, plantilla **de pago** de ThemeForest. No se
redistribuye. Tres paths fuera del repo (ver `.gitignore`):

- `/public/reframe/` — assets crudos (css/js/fonts/images).
- `/resources/views/theme/` — Blades portados (markup ReFrame con lógica
  Laravel; sigue siendo IP del theme).
- `/config/theme.php` — config con keys que son la API privada del theme.

Esos paths se suben al VPS por scp/rsync, separados del deploy automático.

Comprar la plantilla:
https://themeforest.net/item/reframe-personal-one-page-portfolio-html-template/33840600

## 8. Dev local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan filament:upgrade      # publica los assets del panel + limpia caches
php artisan db:seed
npm run build
php artisan serve
```

> Re-correr `php artisan filament:upgrade` después de cualquier
> `composer update` o cuando se agregue un plugin Filament — sino el panel
> se renderiza sin CSS (sólo HTML pelado).

- Front: http://localhost:8000
- Admin: http://localhost:8000/admin (usuario `eduardo`, password `password`)

Seeders ejecutados por `db:seed`:
- `UserSeeder` — usuario admin de Eduardo (lookup por `username = eduardo`,
  email `eamachaca@icloud.com`, password default `password`,
  **cambiar al primer login** desde `/admin/profile`).
- `StudySeeder` — Bachelor of Informatics Engineering (UAGRM).
- `ExperienceSeeder` — 5 empresas del CV: VAN, Workana, iCorebiz
  (2 niveles Ssr+Jr), CenturySoft, OsBolivia.
- `SkillSeeder` — 39 skills en 6 categorías (Languages, Frameworks,
  Databases, Tools, Cloud, Methodology).
- `ServiceSeeder` — 4 servicios (Backend Development, System Integrations,
  Internal Tools & BackOffice, Process Automation).
- `FaqSeeder` — 4 preguntas frecuentes.
- `ProjectSeeder` — Gastos (slug `gastos`, brand HormiguitaX, multi-app),
  único project seedeado. `experience_id` null → renderiza `#Personal`.

> El front no se ve completo sin los assets de ReFrame en `public/reframe/`.
> Adquirí la licencia y colocá los archivos manualmente.

## 9. Deploy (cuando llegue producción)

Patrón replicado del proyecto Gastos:
- VPS Contabo (Ubuntu 24.04), PHP 8.4, MySQL 8, Redis, Nginx, Supervisor.
- GitHub Actions: push a `main` → checkout, build de assets (`npm ci` +
  `npm run build`), `rsync` del código al VPS (excluye `.env`, `vendor/`,
  `node_modules/`, caches, sqlite). **Nunca `--delete`** en rsync — los
  assets de ReFrame viven solo en el VPS, los borraría.
- Por SSH como user `deploy`: `composer install --no-dev`,
  `migrate --force`, `optimize:clear` + `config/route/view/event:cache`,
  reload php-fpm, restart workers.
- Secrets: `VPS_HOST`, `VPS_USER`, `VPS_SSH_KEY`. `concurrency` con
  `cancel-in-progress: false`.

## 10. Convenciones

- **Idioma del código:** todo el código, comentarios, nombres de
  identificadores y mensajes de commit van en **inglés**. La conversación
  con Eduardo es en español; los docs pueden quedar en español.
- **Multi-tenant:** cada record con `owner_id` se autosetea al usuario
  actual en `mutateFormDataBeforeCreate` y se scopea con `getEloquentQuery`.
- **Slugs:** se autogeneran desde el título en el `booted()` del modelo
  (`Project`, `Post`, `Experience`).
- **Routing por slug:** los modelos relevantes overridean
  `getRouteKeyName()` para devolver `slug`.

## 11. Repo

`git@github.com:eamachaca/Portfolio.git` (público). Reemplaza un portfolio
viejo. (Hubo un repo `Portfoilo` con typo que se descarta.)
