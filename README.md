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
