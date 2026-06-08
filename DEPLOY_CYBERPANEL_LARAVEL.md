# Deploy Aynabar Laravel on CyberPanel Subdomain

This guide deploys the Laravel app on a CyberPanel subdomain using OpenLiteSpeed + PHP.

## 1) Subdomain and SSL

1. Create subdomain in CyberPanel (example: `qr.yourdomain.com`).
2. Point DNS `A` record to your server IP.
3. Issue Let's Encrypt SSL for that subdomain.

## 2) Upload App

Upload project to:

`/home/aynabar/apps/aynabar-laravel`

Set subdomain document root to:

`/home/aynabar/apps/aynabar-laravel/public`

## 3) Install Dependencies

```bash
cd /home/aynabar/apps/aynabar-laravel
composer install --no-dev --optimize-autoloader
```

Optional but recommended:

```bash
php artisan storage:link
```

## 4) Configure Environment

Create/update `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://qr.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aynabar
DB_USERNAME=aynabar_user
DB_PASSWORD=strong_password

SEED_SUPERADMIN_EMAIL=admin@example.com
SEED_SUPERADMIN_PASSWORD=ChangeMe123!
SEED_SUPERADMIN_NAME="Super Admin"
```

Use PostgreSQL instead of MySQL if preferred.

## 5) Permissions

```bash
cd /home/aynabar/apps/aynabar-laravel
chmod -R 775 storage bootstrap/cache
```

Ensure web user can write `storage` and `bootstrap/cache`.

If your site user is `aynabar` and web group is `lshttpd`:

```bash
sudo chown -R aynabar:lshttpd /home/aynabar/apps/aynabar-laravel
sudo find /home/aynabar/apps/aynabar-laravel/storage -type d -exec chmod 775 {} \;
sudo find /home/aynabar/apps/aynabar-laravel/bootstrap/cache -type d -exec chmod 775 {} \;
```

## 6) Migrate and Seed

```bash
cd /home/aynabar/apps/aynabar-laravel
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Start queue worker if you later enable queued jobs:

```bash
php artisan queue:work --daemon
```

## 7) OpenLiteSpeed Rewrites

In vHost rewrite rules (for Laravel front controller):

```txt
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

Restart OpenLiteSpeed from CyberPanel after changes.

## 8) Verify

1. Open `https://qr.yourdomain.com`
2. Login with seeded superadmin
3. Create dynamic QR/barcode
4. Open `https://qr.yourdomain.com/r/{slug}` to confirm redirect and scan logging

## 9) Update Workflow

```bash
cd /home/aynabar/apps/aynabar-laravel
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Also clear old caches before recache when troubleshooting:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 10) Security

- Change default admin password immediately.
- Keep `.env` out of version control.
- Use least-privilege DB credentials.
- Schedule backups for DB and app files.
