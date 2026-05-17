# Project Library

A Laravel 10 project repository for uploading, organizing, searching, previewing, saving, and managing academic or professional project PDF documents across many fields.

## Features

- Public homepage, project browser, advanced filters, and full-text search.
- Project metadata: title, author/student, supervisor, type, year, category, tags, keywords, abstract, PDF text, uploader, and file metadata.
- Normal users can upload projects, edit their own uploads, save projects for later, and see recently viewed projects.
- Guests can browse project metadata and text snippets, but must sign in or create an account to continue reading or download files.
- Login/register can return readers to the exact project page they were viewing.
- PDF text extraction powers snippets, search, suggested abstracts, suggested categories, suggested keywords, and suggested tags.
- Duplicate-file protection by hash where a hash is available.
- Role-based admin panel:
  - `super_admin`: full control, including users and site controls.
  - `admin`: manage projects, categories, tags, and deletion.
  - `moderator`: manage projects.
  - `user`: upload, browse, save, and edit only their own projects.
- Dashboard sections for recommendations, latest uploads, user uploads, saved projects, viewed projects, and active fields/tags.
- JSON API for auth, projects, categories, tags, and stats.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

Set your local database values in `.env` before running migrations.

For larger local PDF uploads, start the server with higher PHP upload limits:

```bash
composer serve:large
```

## First Admin

Register from `/register`.

If the database has no users, the first registered account becomes `super_admin`. Old `site_control` records are migrated into `super_admin`.

## Useful URLs

- `/` - Homepage
- `/dashboard` - Signed-in research dashboard
- `/projects` - Project browser and advanced search
- `/projects/create` - Upload project
- `/login` - Login
- `/register` - Register
- `/admin` - Admin dashboard for moderator and above
- `/api/projects` - Public project API
- `/api/stats` - Public stats API

## Local vs Aiven Migrations

You do not run Artisan commands inside Aiven. Aiven is only the database server.

Run migrations from your local project terminal, but point Laravel at the database you want to change.

For local testing, keep `.env` pointed to your local database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projectupload_local
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate
php artisan db:seed
```

For Aiven production, keep a separate `.env.aiven` file with Aiven credentials:

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=your-aiven-host
DB_PORT=your-aiven-port
DB_DATABASE=your-aiven-database
DB_USERNAME=your-aiven-user
DB_PASSWORD=your-aiven-password
```

Then run this from your local project folder:

```bash
php artisan config:clear
php artisan migrate --env=aiven --force
php artisan db:seed --env=aiven --force
```

That command runs on your computer, but it changes the Aiven database because `--env=aiven` loads `.env.aiven`.

Keep `.env.aiven` ignored by Git.

## Vercel Deployment

This repository includes `vercel.json` and `api/index.php` so Vercel routes Laravel through the PHP serverless entrypoint instead of treating the app as a static Vite site.

Set these Vercel environment variables at minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://your-vercel-domain.vercel.app
LOG_CHANNEL=stderr
CACHE_DRIVER=array
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
APP_STORAGE_PATH=/tmp/laravel_storage
```

`APP_STORAGE_PATH=/tmp/laravel_storage` is intentional on Vercel. Laravel needs writable temporary storage for views/cache/logs, but `/tmp` is not permanent file storage.

Do not commit production secrets in `.env`, `.env.aiven`, or `vercel.json`. Add secrets in Vercel Dashboard -> Project -> Settings -> Environment Variables.

## MySQL on Vercel

Use an external MySQL-compatible database such as Aiven, Railway, PlanetScale, or another managed MySQL service.

If the provider gives a single connection string:

```env
DB_CONNECTION=mysql
DATABASE_URL=mysql://user:password@host:3306/database
```

If it gives separate values:

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

PlanetScale-style variables such as `PLANETSCALE_DB_HOST`, `PLANETSCALE_DB`, `PLANETSCALE_DB_USERNAME`, and `PLANETSCALE_DB_PASSWORD` are also supported in `config/database.php`.

## PostgreSQL / Neon

Neon can work, but it is PostgreSQL, not MySQL. Use:

```env
DB_CONNECTION=pgsql
DATABASE_URL=postgresql://user:password@host/database?sslmode=require
```

## PDF Text Extraction

The app attempts text extraction in this order:

1. `pdftotext`, if installed.
2. `smalot/pdfparser`, a PHP parser.
3. A simple built-in fallback for basic PDFs.

Configure `pdftotext` when available:

```env
PDFTOTEXT_PATH=pdftotext
```

Scanned/image-only PDFs still need OCR. That is a future background-processing feature.

## API Auth

Use Sanctum token endpoints:

```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
```

Authenticated users can create projects. Users can update their own projects; staff can update broader project records through their role permissions.

## Tests

Run:

```bash
php artisan test
npm run build
```

This PHP install uses MySQL for tests, so run migrations first against your testing database.

## Cache Reset After Moving The Project

If Laravel still points to an old folder after moving the project:

```bash
php artisan optimize:clear
php artisan view:clear
```
