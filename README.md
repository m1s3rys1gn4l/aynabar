# Aynabar Laravel

Laravel SaaS for static and dynamic QR/barcode generation with superadmin and user accounts.

## Features

- Email/password authentication (register, login, logout)
- Role-based access (`SUPERADMIN`, `USER`)
- Static QR/barcode generation
- Dynamic QR/barcode generation with stable redirect URL
- Dynamic destination update history
- Scan analytics logging for dynamic redirects
- Per-code analytics page and CSV export
- Superadmin user management page

## Tech

- Laravel 13
- SQLite (default local)
- PostgreSQL (recommended production)
- `simplesoftwareio/simple-qrcode`
- `picqer/php-barcode-generator`

## Local Setup

1. Install dependencies:

```bash
composer install
```

2. Copy environment file:

```bash
cp .env.example .env
php artisan key:generate
```

3. Run migrations and seed:

```bash
php artisan migrate --seed
```

4. Start local server:

```bash
php artisan serve
```

## Default Seeded Superadmin

- Email: `admin@example.com`
- Password: `ChangeMe123!`

Change this password immediately after first login.

## Key Routes

- `/register`
- `/login`
- `/dashboard`
- `/dashboard/create`
- `/dashboard/code/{id}`
- `/admin/users`
- `/r/{slug}`

## CyberPanel Deployment

See [DEPLOY_CYBERPANEL_LARAVEL.md](DEPLOY_CYBERPANEL_LARAVEL.md).
