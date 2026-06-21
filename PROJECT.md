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
Campos base + `full_name` (nullable), `username` (login), `headline`,
`bio`, `avatar`, `resume` (PDF), `active_locales` (json), `default_locale`
(string). Implementa `FilamentUser`; `canAccessPanel()` devuelve `true`
para cualquier autenticado en el MVP. Login del admin es por `username`
(no email — ver `App\Filament\Pages\Auth\Login`). Los social links viven
en una relación `hasMany SocialLink` — ver `networks` / `social_links`.

- **`name`** vs **`full_name`**: `name` es el nombre corto que se ve en
  todas partes (title, sidebar, loading screen, blog headers); `full_name`
  es opcional, se usa solo en la sección About del home. Si no se setea,
  About cae a `name`.
- **`resume`** — ruta relativa en disco `public` (ej.
  `resumes/eduardo-en.pdf`). Translatable: cada locale puede tener su
  propio PDF. El hero del front muestra botón "Download CV" si está
  cargado para el locale actual.
- **`active_locales`** — array de códigos de idioma que el owner se
  compromete a traducir (ej. `["en", "es"]`). El visitante solo puede ver
  el site en los locales activos.
- **`default_locale`** — locale que se usa como fallback cuando un campo
  traducible no tiene valor para el locale actual (ej. `"en"`). Debe
  pertenecer a `active_locales`.
- **Campos traducibles** (cast JSON `{en: "...", es: "..."}` vía
  Spatie `HasTranslations`): `headline`, `bio`, `resume`, más todos los
  campos editoriales del theme abajo.
- **Campos editoriales del theme** (manejados desde la page
  `/admin/translations`):
  - `hero_tag` — string libre **no translatable** (e.g. `"WEB APPS /
    LARAVEL / AUTOMATION"`, mayormente nombres de tecnologías sin
    traducción).
  - Translatable: `hero_title`, `hero_copy`, `hero_note`, `about_heading`,
    `about_body`, `strengths_heading`, `experience_heading`,
    `experience_intro`, `education_heading`, `portfolio_heading`,
    `portfolio_intro`, `skills_heading`, `skills_intro`,
    `workstyle_heading`, `workstyle_intro`, `testimonials_heading`,
    `faq_heading`, `blog_heading`, `contact_heading`, `contact_intro`.
  - Todos opcionales por locale. Si un heading está vacío para el locale
    visible, el bloque del front lo omite (sin fallback al otro idioma
    para evitar mezclar idiomas en una sola página). Si el body/intro
    está vacío también se omite.
- **Relaciones**: además de las existentes (projects/studies/experiences/
  skills/services/faqs/testimonials/posts/socialLinks), hay `strengths` y
  `workStyleItems` para los modelos editoriales del theme.

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
- **Campos traducibles**: `summary` (Spatie `HasTranslations`).
  `levels.*.description` y `levels.*.highlights` también son traducibles
  pero a nivel JSON anidado: cada string se guarda como `{en: "...",
  es: "..."}` por elemento. Las vistas resuelven con la directive Blade
  `@t($value)`.

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
- **Campos traducibles**: `title`, `excerpt`, `description` (Spatie
  `HasTranslations`). `apps.*.description` translatable a nivel JSON
  anidado, resuelto en vistas con `@t($app['description'])`.

### `studies`
`institution`, `title`, `field`, `description`, `start_date`, `end_date`,
`in_progress`, `logo`, `sort_order`. Sección "Education" del Resume,
compacta debajo de Experience. **Traducibles**: `title`, `description`.

### `skills`
`name`, `category` (nullable), `sort_order`. El front agrupa por categoría
y renderiza pills (sin porcentajes — explícito).

### `services`
`title`, `description`, `icon` (clase de themify, ej. `ti-server`),
`sort_order`. Grid en el home. **Traducibles**: `title`, `description`.

### `strengths`
`label`, `title`, `body`, `tech_stack` (json), `sort_order`. Cards de la
sección "Core Strengths" del home (lo que antes vivía hardcoded como
`$coreStrengths`). Editable desde `/admin/strengths`.
**Traducibles**: `label`, `title`, `body`.

### `work_style_items`
`text`, `sort_order`. Bullets numerados de la sección "Work Style" del
home (lo que antes vivía hardcoded como `$workStyle`). Editable desde
`/admin/work-style-items`. **Traducibles**: `text`.

### `faqs`
`question`, `answer`, `sort_order`. Render con `<details>` accordion.
**Traducibles**: `question`, `answer`.

### `testimonials`
`author`, `role`, `company`, `quote`, `avatar`, `sort_order`. Grid en home,
sección oculta si la tabla está vacía.

### `posts` (blog)
`title`, `slug` (auto), `excerpt`, `content` (RichEditor → HTML), `cover_image`,
`published_at`, `sort_order`. Rutas `/blog` (paginated index, 9 por página)
y `/blog/{slug}` (detail). Home muestra los 3 más recientes con link a
`/blog`. Sección home oculta si no hay posts publicados.
**Traducibles**: `title`, `excerpt`, `content`.

### `networks` y `social_links`
Catálogo global de redes (`networks`) + relación N:N entre usuario y red
(`social_links`). Cada `Network` = `{ slug (unique), name, themify_class
(nullable), icon_path (nullable), is_approved, merged_into_id (FK self,
nullable), created_by (FK users, nullable para seeds) }`. El render del
front prefiere `themify_class` si existe (clase `ti-*` del set de ReFrame);
si no, cae a `icon_path` (SVG/PNG subido al disk `public`).

- **Catálogo seedeado** (`NetworkSeeder`): ~22 redes comunes (GitHub,
  LinkedIn, X, etc.) todas con `is_approved=true`. Las que tienen ícono
  en el set de ReFrame traen `themify_class`; el resto (Workana, Mastodon,
  Bluesky, …) quedan sin ícono hasta que un admin suba el SVG desde
  `/admin/networks`.
- **Creación por owner**: desde `/admin/profile`, el Select de network
  tiene `createOptionForm` (modal name + icon upload). La nueva queda
  `is_approved=false`, `created_by=auth()->id()`. **El creador la ve
  inmediatamente** en su propio Profile; **otros owners no la ven**
  en su Select hasta que el superadmin la apruebe desde el CRUD.
- **Detección de duplicados**: al crear, se calcula `Str::slug($name)` y
  se chequea contra todas las networks (incluidos aliases). Si match,
  reutiliza la existente — si la existente es alias, redirige al target
  y notifica con `Notification::danger` ("'wasap' was merged into
  'WhatsApp'").
- **Merge / aliases** (acción "Merge into…" en `NetworkResource`):
  1. Reasigna `social_links.network_id` de la origen al target, evitando
     violar el `unique(user_id, network_id)` (si un user ya tenía las dos,
     se descarta la link de la origen para él).
  2. La origen no se borra: se marca `merged_into_id = target.id` y queda
     como **alias inactivo**. Solo sirve para detección de duplicados —
     el render del front filtra por `merged_into_id IS NULL` así que no
     aparece nunca más.
- **`social_links`** pivot: `(id, user_id, network_id, url, sort_order)`
  con `unique(user_id, network_id)` (un user no puede registrar la misma
  network dos veces).

### `contact_messages`
`name`, `email`, `phone`, `message`, `read_at`. **Sin** `owner_id` — buzón
global. Se crea desde `POST /contact` (formulario público). El admin
(`/admin/inbox`) es read-only: ver/eliminar, no crear ni editar. Mensajes
se marcan como leídos al abrir el detalle. No hay envío por email aún
(ver `NEXT-STEPS.md`).

## 4. BackOffice (Filament)

- Panel en `/admin`, provider `App\Providers\Filament\AdminPanelProvider`
  registrado en `bootstrap/providers.php`. Profile registrado con
  `isSimple: false` para que use el layout completo (sidebar + topbar) y
  no la SimplePage centrada — así el usuario no queda "atrapado" en
  `/admin/profile` sin navegación. Global search **off**
  (`->globalSearch(false)`) — no hay nada que indexar todavía y agregaba
  ruido al header.
- **No Dashboard**: el Dashboard default de Filament fue eliminado del
  panel. Profile es la landing page del admin — `/admin` redirige a
  `/admin/profile` vía `->authenticatedRoutes()` con un `Route::redirect`
  nombrado `home` (así `Filament::getUrl()` lo resuelve también). Profile
  aparece como **primer ítem del sidebar** (NavigationItem manual con
  `sort(-1)`, ícono `OutlinedUserCircle`) y se **oculta del avatar
  dropdown** vía `->userMenuItems(['profile' => Action::make('profile')->hidden()])`.
- Login custom (`App\Filament\Pages\Auth\Login`): autentica por
  `username` + `password`. El email queda como dato de contacto. Override
  de `throwFailureValidationException()`: dispara un `Notification::danger()`
  (toast siempre visible) **y** keyea el error inline a `data.username`
  (default de Filament es `data.email`).
- Profile custom (`App\Filament\Pages\Auth\EditProfile`): extiende
  Filament añadiendo `username` (disabled + dehydrated:false — login key
  inmutable post-creación), `headline`, `bio`, `avatar` (FileUpload con
  editor), `resume` (FileUpload PDF, max 5 MB, `storage/resumes/`),
  **Social links** (Repeater sobre la relación `socialLinks` — Select
  network + TextInput url; el Select tiene `createOptionForm` que abre
  modal name + icon upload para crear networks nuevas al vuelo, con
  detección de duplicados/aliases y notificación si el nombre fue
  baneado), y una Section **"Languages"** con `active_locales`
  (CheckboxList) y `default_locale` (Select condicionado al subset
  activo). Ancho `Width::FiveExtraLarge` para que los Tabs por locale
  tengan espacio. Los campos `headline`, `bio`, `resume` viven dentro
  de `LocaleTabs::for(...)`.
- **Cambio de password**: NO está inline en el form. Header action
  "Change password" (ícono `OutlinedKey`) abre un modal con
  `current_password` (validado contra el guard activo vía
  `->currentPassword(guard: ...)`) + `new_password` (rule
  `Password::default()`) + `new_password_confirmation`. Al guardar
  hashea, actualiza, y reescribe `password_hash_{guard}` en sesión para
  que el usuario actual no quede deslogueado. Decisión: separa la
  operación del flujo normal de edición, evita cambios accidentales y
  verifica que la persona conoce el password actual.
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
  - `NetworkResource` — catálogo global de redes (§ 3 `networks` /
    `social_links`). Tabla con columna `Profiles` (count de social_links
    apuntando), filtros Approved/Pending/Aliases, acciones por fila
    **Approve** (solo si pending) y **Merge into…** (modal con Select
    target + confirmación → reasigna links y marca origen como alias).
    Delete deshabilitado si la network tiene profiles vinculados.
- **Costura multi-tenant (invisible hoy):**
  - `getEloquentQuery()` en cada resource scopea a `auth()->id()`.
  - `mutateFormDataBeforeCreate()` autosetea `owner_id` al crear.
  - Cada user verá solo lo suyo el día que se active multi-persona.

## 4.5 i18n (multi-idioma)

- Paquete: `spatie/laravel-translatable`.
- Cada owner declara qué idiomas soporta su portfolio en
  `users.active_locales` (json) + `users.default_locale` (string). Lista
  global de locales disponibles en `App\Support\Locales::available()`
  (12 idiomas precargados: en, es, pt, fr, de, it, nl, ja, zh, ko, ru, ar).
- Modelos translatables y sus campos: ver § 3 (cada bloque indica los
  campos `HasTranslations`).
- Para JSON anidado con strings traducibles (`experiences.levels.*`,
  `projects.apps.*`), el contenido se guarda como `{en: "...", es: "..."}`
  por elemento. Las vistas resuelven con la directive Blade `@t($value)`
  registrada en `AppServiceProvider`, que delega a
  `App\Support\Locales::translate()`.
- Filament: cada form translatable usa el helper
  `App\Filament\Support\LocaleTabs::for($fieldsFactory)` que envuelve los
  campos en `Tabs` con un tab por locale activo del owner. Los nombres
  llevan suffix de locale (`title.en`, `summary.es`), que Spatie
  `HasTranslations` mapea al JSON `{en: "...", es: "..."}`. Aplica a
  EditProfile, ExperienceResource (summary + levels.*.description +
  highlights), ProjectResource (title/excerpt/description + apps.*.description),
  StudyResource, ServiceResource, FaqResource, PostResource,
  StrengthResource, WorkStyleItemResource, y la page `Translations`.
- **Todos los campos translatable son opcionales por locale.** Se quitó
  el `required()` por locale a propósito: el dueño llena al menos su
  `default_locale` y los demás los va completando cuando puede. Si un
  visitante pide un locale sin traducción, Spatie cae al
  `config('app.fallback_locale')` (default `en`); para JSON anidados, el
  helper `App\Support\Locales::translate()` cae a current → `en` →
  primer valor con `filled`.
- Middleware `App\Http\Middleware\SetLocale` resuelve el locale en este
  orden: `?locale=` query → cookie `locale` → `Accept-Language` del
  navegador → `users.default_locale` → primer locale activo del owner.
  Solo acepta valores que pertenezcan a `users.active_locales`. Setea
  `app()->setLocale(...)` y persiste la cookie por 1 año cuando cambia.
  Aplicado a **dos stacks**:
  - Grupo `web` (front público) — registrado en `bootstrap/app.php`.
  - Panel admin de Filament — al final del array
    `->middleware([...])` en `AdminPanelProvider`, para que el panel
    respete cookie+query (Filament UI viene traducida al `es` bundled).
- Switchers visibles:
  - **Front**: `<ul>` tipo `tab-nav` del theme con dos pills
    (`EN` / `ES`), fijo al lado del botón hamburger y siempre visible.
  - **Admin**: render hook `USER_MENU_BEFORE` (topbar) +
    `AUTH_LOGIN_FORM_BEFORE` (página de login). Mismo blade en
    `resources/views/filament/locale-switch.blade.php`. Estilo
    Tailwind/Filament-flavor (color primary del panel en hover/active).
  - Ambos linkean a `request()->fullUrlWithQuery(['locale' => $code])`.
- Chrome del theme (menu items, form labels, buttons, empty states) →
  `__()` con JSON keys en `lang/en.json` + `lang/es.json`. Editorial del
  owner vive en BD (ver § 3 y `/admin/translations`), no en lang files.
- Panel admin (Filament) traducido también: todos los Resources,
  EditProfile, Translations y Login usan `__()` para labels / helperText /
  placeholders / descriptions / addActionLabel; los `navigationLabel` /
  `modelLabel` / `pluralModelLabel` se resuelven en runtime vía los
  overrides `getNavigationLabel()` / `getModelLabel()` /
  `getPluralModelLabel()` (no se pueden envolver en `__()` directamente
  porque las propiedades estáticas se evalúan a build-time). Las keys
  viven en `lang/{en,es}.json` (~170 keys totales con front + admin).
  Filament además trae sus strings propias (Save/Cancel, paginación,
  validation) traducidas en `vendor/filament/filament/resources/lang/es/`,
  no hay que escribirlas.

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
testimonials, recent posts. Asume BD poblada (`User::firstOrFail()`);
en producción el seeder (`php artisan db:seed`) garantiza la data.

Vistas en `resources/views/themes/reframe/` (namespace Blade
`themes.reframe.*`). El layout `themes/` (plural) deja lugar para sumar
otras plantillas más adelante — ver `NEXT-STEPS.md` § Multi-theme.
- `layouts/app.blade.php` — layout base con menú (`Home`, `About`,
  `Strengths`, `Experience`, `Work`, `Skills`, `Testimonials`, `FAQ`,
  `Contact`).
- `home.blade.php` — secciones: INTRO, ABOUT, RESUME (Experience grande +
  Education compacto), SERVICES, SKILLS, PORTFOLIO, TESTIMONIALS, FAQ,
  BLOG, CONTACT (formulario funcional). Las secciones de testimonials,
  faq y blog se ocultan si están vacías. El INTRO (hero) muestra botón
  "Download CV" al lado de "Get in touch" cuando el owner tiene un PDF
  cargado en `users.resume` para el locale actual.
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
`avatars/`, `resumes/` (PDFs).

## 7. ReFrame — licencia

El front usa **ReFrame**, plantilla **de pago** de ThemeForest. No se
redistribuye. Tres paths fuera del repo (ver `.gitignore`):

- `/public/reframe/` — assets crudos (css/js/fonts/images).
- `/resources/views/themes/reframe/` — Blades portados (markup ReFrame con
  lógica Laravel; sigue siendo IP del theme).
- `/config/theme.php` — config con keys que son la API privada del theme.

Esos paths se suben al VPS por scp/rsync, separados del deploy automático.

Comprar la plantilla:
https://themeforest.net/item/reframe-personal-one-page-portfolio-html-template/33840600

**Accent color** = `#10b981` (emerald 500). Vive en dos capas:
- `config/theme.php` — color de las líneas animadas del background del
  hero (claves `background.circle.line_color`,
  `background.lines.line_color`, `background.twisted.line_color`).
- `--portfolio-accent` en `public/reframe/assets/css/style.css:1806` —
  color del texto de los tags (`p.tag` tipo `CONTACT`,
  `WEB APPS / ...`) y de la línea separadora. Capa custom sumada por
  encima del theme.

**Scrollbar oculto.** ReFrame oculta el scrollbar nativo solo si su
panel de demo está activo (función `hide_default_scrollbar` en
`scripts.js`). Como ese panel no se usa en producción, el CSS
equivalente está hardcoded en el `<style>` inline del layout
(`resources/views/themes/reframe/layouts/app.blade.php`).

**Reload empieza arriba.** `history.scrollRestoration = 'manual'` en el
`<head>` del layout desactiva la restauración de scroll del browser.
Cualquier F5 carga la home desde el INTRO en vez de a mitad de scroll.

**Claves de `config/theme.php` y qué mueven:**

- `color_scheme` — paleta global del theme (`eco_green` es la actual).
  Cambia el accent de buttons, separators y badges del theme. Independiente
  de `--portfolio-accent` (esa es nuestra capa custom).
- `scroll_bar`, `cursor_mode` — variantes que ReFrame ofrece de scrollbar
  y cursor custom. `cursor_2` es el círculo que sigue el mouse.
- `menu_close_on_click`, `smooth_page_scroll_intensity` — comportamiento del
  menú hamburger y del scroll suave. Subir intensidad → scroll más amortiguado.
- `scroll_animation_defaults` — animación que dispara cualquier elemento con
  clase `scroll-animation` (fade desde abajo por default).
- `profile.fallback_image` — qué imagen usa el hero cuando el user no tiene
  `avatar`. `profile.effect_map` + `effect_intensity` controlan el efecto
  visual que ReFrame aplica sobre la foto (un displacement map). Subir
  `effect_intensity` → distorsión más fuerte (0 = sin efecto).
- `background.mode` — animación detrás del hero. Valores posibles:
  `twisted` (líneas onduladas, **actual**), `lines` (grilla recta),
  `circle` (anillo), `asteroids` (cubos 3D). Cada uno tiene su sub-bloque
  con sus colores y velocidad.
- `background.mode_mobile` — `match` repite el desktop, o se puede setear
  un mode distinto para no sobrecargar mobile.
- `background.{mode}.line_color` / `bg_color` / `scene_opacity` — color de
  las líneas animadas, fondo y opacidad de la escena (subir `scene_opacity`
  → background más visible).

> **Convención:** si vas a cambiar `mode`, cambiá también el bloque
> correspondiente del mismo nombre — los demás bloques quedan en el config
> pero no se usan. No los borres: son referencia rápida para volver.

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
  (`Project`, `Post`, `Experience`). Para modelos con título translatable,
  el slug se calcula tomando la primera traducción con valor (no se rompe
  si el user crea contenido solo en otro idioma).
- **Routing por slug:** los modelos relevantes overridean
  `getRouteKeyName()` para devolver `slug`.
- **Translatables:** todo campo con contenido de texto que mira un
  visitante (no nombres de empresa, slugs, ni paths) lleva
  `HasTranslations`. Storage = JSON `{locale: value}` en la misma columna.
  Para nested JSON (`levels.*`, `apps.*`), cada string interno también es
  `{locale: value}` y se resuelve con la directive Blade `@t($value)`.

## 11. Repo

`git@github.com:eamachaca/Portfolio.git` (público). Reemplaza un portfolio
viejo. (Hubo un repo `Portfoilo` con typo que se descarta.)
