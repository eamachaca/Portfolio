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
los archivos de la plantilla **no se incluyen en este repositorio**:

- Los assets del theme (`public/reframe/`) y las vistas portadas del template
  (`resources/views/theme/`) están en `.gitignore`.
- Se despliegan por separado (scp/rsync), nunca vía este repo público.

Este proyecto **usa** ReFrame, pero **no lo redistribuye**. Si te gusta la
plantilla y quieres usarla, **cómprala** directamente al autor:

👉 https://themeforest.net/item/reframe-personal-one-page-portfolio-html-template/33840600

El código de esta aplicación (Laravel, Filament, modelos, lógica) es propio y sí
está en el repositorio. Lo que queda fuera es únicamente el diseño licenciado.

---

## Desarrollo local

```bash
composer install
npm install
cp .env.example .env   # configurar SQLite
php artisan key:generate
php artisan migrate
php artisan make:filament-user   # crear el usuario admin
npm run build
php artisan serve
```

> Nota: el front no se verá completo sin los assets de ReFrame
> (`public/reframe/`), que no vienen en el repo. Colócalos manualmente tras
> adquirir la licencia.

- Front público: http://localhost:8000
- BackOffice: http://localhost:8000/admin
