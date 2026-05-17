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
- Optional direct-to-Vercel-Blob upload so large PDFs bypass Laravel request body limits.

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

## Vercel Blob Direct Upload

Direct upload is the recommended production flow for large PDFs on Vercel:

1. The browser uploads the PDF straight to Vercel Blob.
2. Laravel receives only the Blob URL, file key, size, MIME type, client hash, and metadata.
3. Laravel saves those values in MySQL and does best-effort text extraction from the Blob URL.

Install dependencies:

```bash
npm install
composer install
npm run build
```

Set these variables in Vercel:

```env
BLOB_READ_WRITE_TOKEN=your-vercel-blob-token
BLOB_UPLOAD_SECRET=make-this-a-long-random-secret
PROJECT_DIRECT_UPLOAD_DRIVER=vercel_blob
PROJECT_BLOB_HANDLE_URL=/blob/project-upload
PROJECT_BLOB_ACCESS=public
PROJECT_UPLOAD_MAX_BYTES=104857600
PDF_REMOTE_EXTRACT_MAX_BYTES=31457280
```

The route `/blob/project-upload` is handled by `api/blob-project-upload.js`. Laravel creates a signed upload intent for the form; the Node function verifies it before allowing the browser to upload to Blob.

Notes:

- Direct upload avoids PHP `post_max_size` and serverless request body limits.
- The database never stores the full PDF binary.
- `PROJECT_UPLOAD_MAX_BYTES=104857600` allows 100 MB direct uploads.
- `PDF_REMOTE_EXTRACT_MAX_BYTES=31457280` means Laravel only downloads PDFs up to 30 MB for immediate text extraction. Larger files can still be stored, but text extraction should be moved to a queue/background worker later.
- `PROJECT_BLOB_ACCESS=public` makes Blob URLs public if someone has the URL. The app still hides those URLs from guests. For strict file privacy, switch to private Blob access and add signed download URLs later.

For local development without Vercel Blob, leave `PROJECT_DIRECT_UPLOAD_DRIVER` empty and use the normal local upload flow.

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
