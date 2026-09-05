# Portal Ujian — Deployment Guide

> Last updated: 2026-08-31

---

## Option 1: VPS (Recommended)

This app is a traditional Laravel monolith. VPS is the natural deployment target — it works almost out of the box with minimal changes.

### Requirements

| Component | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.2+ | Runtime |
| PHP Extensions | pdo_mysql, mbstring, xml, curl, zip, gd | Laravel + Maatwebsite Excel |
| MySQL | 8.0+ | Database |
| Composer | latest | Dependency management |
| Node.js | 18+ | Asset build (dev/build only) |
| Nginx or Apache | latest | Web server |
| Supervisor | latest | Queue worker management |

### Step-by-Step Deployment

#### 1. Server Setup

```bash
# Install PHP 8.2+
sudo apt install php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Install MySQL
sudo apt install mysql-server

# Install Nginx
sudo apt install nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs
```

#### 2. Clone & Install

```bash
cd /var/www
git clone <your-repo> portal-ujian
cd portal-ujian
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

#### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

Update `.env`:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_ujian
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Portal Ujian"
```

#### 4. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/portal-ujian/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ^~ /storage {
        alias /var/www/portal-ujian/storage/app/public;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/portal-ujian /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 5. Permissions

```bash
sudo chown -R www-data:www-data /var/www/portal-ujian/storage
sudo chown -R www-data:www-data /var/www/portal-ujian/bootstrap/cache
sudo chmod -R 775 /var/www/portal-ujian/storage
sudo chmod -R 775 /var/www/portal-ujian/bootstrap/cache
```

#### 6. Queue Worker (Supervisor)

```bash
sudo apt install supervisor
```

Create `/etc/supervisor/conf.d/portal-ujian.conf`:
```ini
[program:portal-ujian-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/portal-ujian/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/portal-ujian/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start portal-ujian-worker:*
```

#### 7. Scheduler (Cron)

```bash
crontab -u www-data -e
```

Add:
```
* * * * * php /var/www/portal-ujian/artisan schedule:run >> /dev/null 2>&1
```

#### 8. SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

### Clean Up Before Deploy

**IMPORTANT:** Delete the root `.htaccess` file before deployment — it's Laragon-specific and will break on a real server:

```bash
rm .htaccess
```

---

## Option 2: Vercel (Not Recommended)

Vercel is not an ideal fit for this project, but it can work with significant changes.

### Why Vercel is Difficult

- Laravel requires a persistent PHP process (queue workers, scheduler)
- SQLite is ephemeral on Vercel (wiped on cold start)
- Database session driver works with external DB, but adds latency
- No `php artisan` CLI access for migrations or queue workers
- File uploads require S3-compatible storage

### What Would Need to Change

| Area | Change |
|------|--------|
| Database | Replace SQLite with external MySQL (PlanetScale, Supabase, Railway) |
| Storage | Switch `FILESYSTEM_DISK` to `s3` |
| `.htaccess` | Delete root `.htaccess` |
| Environment | Move all secrets to Vercel dashboard |
| Assets | Add `npm run build` to Vercel build command |
| Queue | Set `QUEUE_CONNECTION=sync` (no background workers) |
| Sessions | Keep `SESSION_DRIVER=database` with external DB |

### Vercel Configuration (if you proceed)

Create `vercel.json`:
```json
{
  "builds": [
    {
      "src": "vercel.json",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/public/build/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ]
}
```

Create `vercel.php` at project root:
```php
<?php
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}
require_once __DIR__.'/public/index.php';
```

---

## Quick Comparison

| Factor | VPS | Vercel |
|--------|-----|--------|
| Setup complexity | Medium | High |
| Database | Local MySQL | External managed DB |
| Storage | Local disk | S3 required |
| Queue workers | Supervisor | Not available |
| SSL | Let's Encrypt | Automatic |
| Cost | Server rental (~$5-20/mo) | Free tier available |
| Cold starts | None | Yes |
| Custom domain | DNS + server config | Dashboard only |
| Laravel version support | Any | Limited (serverless constraints) |
| **Recommendation** | **Use this** | Avoid |

---

## Deployment Command

The project includes a custom Artisan command that automates the deployment process:

```bash
php artisan app:deploy
```

It performs: cache clear, config cache, route cache, view cache, asset publish, and storage link.

For production, extend this command or use a CI/CD pipeline (GitHub Actions, Deployer, etc.).
