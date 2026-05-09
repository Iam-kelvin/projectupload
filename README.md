# Project Library

A Laravel 10 repository for uploading, browsing, searching, previewing, and managing project PDF documents.

## Features

- Public homepage with search and links to browse projects.
- Project metadata: title, author/student, supervisor, type, year, category, tags, keywords, and abstract.
- Existing tags can be selected during upload, and new comma-separated tags can be created inline when nothing fits.
- PDF upload, inline preview, download, duplicate-file protection, and best-effort PDF text extraction for search.
- Advanced search across metadata, tags, categories, abstracts, keywords, and extracted PDF text.
- Role-based admin panel:
  - `super_admin`: full control, including user management and protected site controls.
  - `admin`: manage projects, categories, tags, and project deletion.
  - `moderator`: upload and edit projects only.
  - `user`: browse public content.
- Dashboard stats for total projects, uploads by year, top supervisors, and project types.
- JSON API for auth, projects, categories, tags, and stats.
- Feature tests for upload, search, show, download/preview, validation, API, and permissions.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Update `.env` with your database settings before running migrations. The current local project uses MySQL.

`php artisan db:seed` adds broad starter categories and tags for a general project library across sciences, arts, business, law, health, education, agriculture, engineering, media, public policy, and interdisciplinary work. It uses safe upserts, so rerunning it will not duplicate the starter taxonomy.

## First Admin Account

Register from `/register`.

If the database has no users, the first registered account becomes `super_admin`. Older `site_control` records are migrated into `super_admin`.

## Useful URLs

- `/` - Homepage
- `/projects` - Public project browser and advanced search
- `/login` - Login
- `/register` - Register
- `/admin` - Admin dashboard for moderator and above
- `/api/projects` - Public project API
- `/api/stats` - Public stats API

## Vercel Deployment Notes

This repository includes `vercel.json` and `api/index.php` so Vercel does not treat the app as a plain Vite/static project looking for `dist`. The config sends Laravel requests through a PHP serverless entrypoint and deploys the `public` directory for assets.

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
DB_CONNECTION=mysql
```

`APP_STORAGE_PATH=/tmp/laravel_storage` is intentional on Vercel. Laravel still needs a writable place for generated views, cache files, sessions if enabled, and logs. Vercel functions can use `/tmp`, but it is temporary and must not be treated as permanent file storage.

Use an external MySQL database. If the provider gives one connection string, set `DATABASE_URL` and keep `DB_CONNECTION=mysql`. If it gives separate values, set:

```env
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

PlanetScale-style Vercel variables such as `PLANETSCALE_DB_HOST`, `PLANETSCALE_DB`, `PLANETSCALE_DB_USERNAME`, and `PLANETSCALE_DB_PASSWORD` are also supported by `config/database.php`.

After setting production database variables, run migrations and the starter taxonomy seed against that database:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Local file uploads on Vercel are not persistent. For production PDFs, use external object storage and store only the file URL/path, file hash, and metadata in MySQL. The most Laravel-native path is S3-compatible storage such as Cloudflare R2, AWS S3, DigitalOcean Spaces, or Supabase Storage through Laravel's `s3` disk. Vercel Blob can also work, but the cleanest implementation is to upload from the browser or a small Vercel function to Blob, then save the returned Blob URL in Laravel.

## PDF Text Extraction

The app tries to use the `pdftotext` command if it is installed. You can configure its path:

```env
PDFTOTEXT_PATH=pdftotext
```

If `pdftotext` is unavailable, the app falls back to a simple built-in extractor. That fallback is enough for some PDFs but not every compressed or scanned document.

## API Auth

Use Sanctum token endpoints:

```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
```

Authenticated staff roles can create, update, and delete projects through `/api/projects`.

## Tests

This PHP install has MySQL support but not SQLite, so tests use database transactions against the configured testing database connection. Run migrations first, then:

```bash
php artisan test
```

## Cache Reset After Moving The Project

If the app was moved to a new folder and Laravel still points to old paths, clear generated caches:

```bash
php artisan optimize:clear
php artisan view:clear
```
