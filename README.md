# Portfolio — Eduardo Machaca

Portfolio personal con backOffice propio para administrar proyectos, estudios e
información de perfil sin tocar código.

## Stack

- **Laravel 13** (PHP 8.3+)
- **Filament v5** — panel de administración (backOffice) en `/admin`
- **Blade** para el front público
- **SQLite** en desarrollo (MySQL en producción)

## Estructura

- `app/Models` — `User`, `Project`, `Study` (cada registro con `owner_id`, listo para multi-persona a futuro).
- `app/Filament` — recursos del backOffice (CRUD de proyectos y estudios, perfil).
- `resources/views` — front público.
- `database/migrations` — esquema.

---

## ⚠️ Sobre la plantilla del front (ReFrame)

El diseño del front está construido sobre **ReFrame**, una plantilla **de pago**
de ThemeForest creada por su autor original. **No es software libre.**

Por respeto al trabajo de su autor y porque **no estoy a favor de la piratería**,
**nada del theme se incluye en este repositorio**. Lo que queda fuera (todo en
`.gitignore`):

- `public/reframe/` — assets crudos del theme (CSS, JS, fuentes, imágenes).
- `resources/views/themes/reframe/` — vistas Blade con el markup del theme.
  Aunque tengan `@foreach`/mapeo de datos propio, el HTML/CSS sigue siendo
  IP del autor.
- `config/theme.php` — config con las keys privadas del theme.

Todo eso se despliega por separado (`scp`/`rsync`), nunca vía este repo público.

Lo que **sí** está versionado y es código propio: Laravel + Filament, modelos,
controladores, migraciones, seeders, traducciones, lógica de negocio. Es lo que
hace funcionar el portafolio — pero el "cómo se ve" no se redistribuye.

Este proyecto **usa** ReFrame, pero **no lo redistribuye**. Si te gusta la
plantilla y quieres usarla, **cómprala** directamente al autor:

👉 https://themeforest.net/item/reframe-personal-one-page-portfolio-html-template/33840600

---

## Desarrollo local

```bash
composer install
npm install
cp .env.example .env             # configurar SQLite
php artisan key:generate
php artisan migrate
php artisan storage:link         # symlink para subidas de imágenes
php artisan make:filament-user   # crear el usuario admin
npm run build
php artisan serve
```

Filament v5 ya está declarado en `composer.json` y el panel admin está
registrado en `bootstrap/providers.php` — `composer install` lo deja listo,
no hace falta correr `filament:install`.

> Nota: el front no se verá completo sin los assets de ReFrame
> (`public/reframe/`), que no vienen en el repo. Colócalos manualmente tras
> adquirir la licencia.

- Front público: http://localhost:8000
- BackOffice: http://localhost:8000/admin
