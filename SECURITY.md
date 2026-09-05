# Portal Ujian — Security Guide

> Last updated: 2026-09-04

---

## 1. Security Overview

Portal Ujian uses Laravel's built-in security features plus application-level controls. Here's what we have and what to verify before production.

### 1.1 What Laravel Handles Automatically

| Threat | Laravel Protection | Status |
|--------|-------------------|--------|
| SQL Injection | Eloquent ORM (parameterized queries) | ✅ Active |
| XSS (Cross-Site Scripting) | Blade `{{ }}` auto-escapes output | ✅ Active |
| CSRF (Cross-Site Request Forgery) | CSRF token on all POST/PUT/DELETE | ✅ Active |
| Password Hashing | Bcrypt (cost factor: 12) | ✅ Active |
| Session Hijacking | Session rotation on login | ⚠️ Verify |
| Mass Assignment | `$fillable` / `$guarded` on models | ⚠️ Verify per model |
| Clickjacking | `X-Frame-Options: SAMEORIGIN` | ⚠️ Add manually |

### 1.2 Application-Level Security

| Feature | Status | Notes |
|---------|--------|-------|
| Dual guard auth (admin/student) | ✅ Active | Guards properly separated |
| Failed login tracking | ✅ Active | DB table exists, check wiring |
| Rate limiting on login | ⚠️ Not yet | Add throttle middleware |
| Broadcast rate limiting | ⚠️ Planned | Alert feature includes this |
| Admin-only routes | ✅ Active | `auth:admin` middleware |
| Student-only routes | ✅ Active | `auth:web` middleware |
| HTTPS enforcement | ⚠️ VPS only | Configure in Nginx + `.env` |

---

## 2. Pre-Production Security Checklist

Run through this before deploying to any public-facing server.

### 2.1 Environment (.env)

```env
# PRODUCTION VALUES - NEVER COMMIT TO GIT
APP_ENV=production
APP_DEBUG=false                # CRITICAL: Must be false in prod
APP_KEY=base64:xxxxxxxxxxxx   # Run: php artisan key:generate
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PASSWORD=<strong-password>  # Min 16 chars, random

SESSION_DRIVER=file            # file > database for performance
SESSION_LIFETIME=120          # 2 hours
SESSION_ENCRYPT=true          # Encrypt session cookies
SESSION_SAME_SITE=lax         # CSRF + UX balance

CACHE_STORE=file              # file > database for performance

QUEUE_CONNECTION=database      # Or redis if available

# MAIL (if using)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io    # Use production SMTP
MAIL_PASSWORD=<secret>        # Use App Password for Gmail

# AWS / Cloud Storage (if using)
AWS_ACCESS_KEY_ID=<secret>
AWS_SECRET_ACCESS_KEY=<secret>
```

### 2.2 App Key

```bash
# Generate new key (never use the one from development)
php artisan key:generate

# Verify .env has APP_KEY filled
grep "APP_KEY=" .env
```

### 2.3 Nginx Security Headers

Add to your Nginx server block:

```nginx
# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;

# Prevent access to sensitive files
location ~ /\.(?!well-known) {
    deny all;
}

# Block access to .env
location ~ /\.env$ {
    deny all;
}

# Block access to storage (use signed URLs instead)
location /storage {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### 2.4 Permissions

```bash
# Correct ownership (Linux)
sudo chown -R www-data:www-data /var/www/portal-ujian/storage
sudo chown -R www-data:www-data /var/www/portal-ujian/bootstrap/cache

# Correct permissions
sudo chmod -R 775 /var/www/portal-ujian/storage
sudo chmod -R 775 /var/www/portal-ujian/bootstrap/cache

# Never 777
# Never let storage/ be publicly writable
```

---

## 3. Authentication Security

### 3.1 Login Rate Limiting

**Current state:** `FailedLoginAttempt` table exists but rate limiting is not wired up.

**Recommended implementation:**

```php
// routes/web.php — Add to login routes
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')  // 5 attempts per minute
    ->name('user.login.submit');
```

Or custom rate limiting based on email + IP:

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('login', function (Request $request) {
    $email = $request->input('email');
    return Limit::perMinute(5)->by($email . $request->ip());
});
```

### 3.2 Password Policy

- Minimum 8 characters (Laravel default)
- Bcrypt rounds: 12 (already set in `.env.example`)
- No password reset via web for admins (use CLI command)
- Student password reset: use Laravel's built-in `ForgotPasswordController`

### 3.3 Session Security

```env
# In .env for production
SESSION_DRIVER=file
SESSION_ENCRYPT=true
SESSION_SAME_SITE=lax    # 'strict' blocks legitimate cross-site navigation
SESSION_LIFETIME=120     # 2 hours max
```

### 3.4 Admin Account Security

- Admin accounts created only via CLI: `php artisan admin:create`
- No web-based admin registration
- `is_active` toggle prevents login without deletion
- `last_login_at` tracking for audit

---

## 4. API Security

### 4.1 Current State

Portal Ujian **does not have a public API**. All routes are web routes protected by session-based auth.

**This is a security strength** — no API attack surface.

### 4.2 If API is Added Later

```php
// routes/api.php (create if needed)
Route::middleware('auth:sanctum');  // Use Laravel Sanctum for API tokens

// NEVER expose session-based auth to API
// NEVER use web routes' session auth for mobile apps
```

### 4.3 Vite / Frontend Assets

```javascript
// vite.config.js — Already configured correctly
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
    })],
    // API base URL for frontend
    define: {
        'process.env.VITE_API_URL': JSON.stringify(process.env.VITE_API_URL || '/'),
    },
});
```

- **No sensitive secrets** should be exposed to frontend via Vite
- Environment variables prefixed with `VITE_` are public
- Keep `APP_KEY`, `DB_*`, `MAIL_*` server-side only

---

## 5. Data Protection

### 5.1 Sensitive Data in Database

| Data | Protection | Notes |
|------|-----------|-------|
| User passwords | Bcrypt hashed | Never stored in plain text |
| Admin passwords | Bcrypt hashed | Same as users |
| Student emails | Plain text | Consider encrypting if GDPR applies |
| Failed login attempts | Plain text email/IP | Used for security monitoring |
| Exam passwords | Plain text | Encrypted at rest recommended for production |

### 5.2 Encryption at Rest

For production with sensitive exam data, add Laravel's encryption:

```php
// app/Models/ExamSchedule.php
protected $casts = [
    'exam_password' => 'encrypted',
];
```

### 5.3 File Upload Security

Excel files are imported via Maatwebsite Excel. Security measures:

- File extension validation: `.xlsx`, `.xls`
- File size limit: 5MB
- Uploaded files stored in `storage/app/imports/` (not public)
- Files deleted after import completion
- No executable file execution

---

## 6. Network Security

### 6.1 VPS Firewall (UFW)

```bash
# Allow SSH (change port from 22 if possible)
sudo ufw allow 22/tcp

# Allow HTTP + HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Deny all other inbound
sudo ufw default deny incoming

# Allow outbound (important for mail, etc.)
sudo ufw default allow outgoing
```

### 6.2 SSH Security

```bash
# Use SSH key authentication (disable password auth)
# Edit /etc/ssh/sshd_config:
PasswordAuthentication no
PubkeyAuthentication yes

# Change default SSH port
Port 2222

# Restart SSH
sudo systemctl restart sshd
```

### 6.3 SSL/TLS

```bash
# Use Let's Encrypt (free)
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot --quiet renew
```

---

## 7. Monitoring & Logging

### 7.1 Login Attempt Monitoring

```bash
# View failed login attempts
php artisan view:logs

# Or query directly
php artisan tinker
>>> App\Models\FailedLoginAttempt::where('guard_type', 'web_failed')
    ->where('attempted_at', '>', now()->subDay())
    ->count();
```

### 7.2 Laravel Log

```env
# .env
LOG_CHANNEL=stack
LOG_LEVEL=debug   # Change to 'warning' in production
```

Logs stored at `storage/logs/laravel.log`. Monitor for:
- Failed authentication attempts
- Database errors
- Queue job failures

### 7.3 External Monitoring (Optional)

For production:
- **Sentry** — Application error tracking
- **Uptime Robot** — Server uptime monitoring
- **New Relic** — Performance APM

---

## 8. Common Vulnerabilities Checklist

Run through this before any deployment:

```
[ ] APP_DEBUG=false in production
[ ] APP_KEY is set and not the default
[ ] .env is NOT committed to git (check .gitignore)
[ ] database.sqlite is NOT committed to git
[ ] storage/logs/*.log are NOT committed to git
[ ] HTTPS is enforced (no HTTP redirect)
[ ] SSL certificate is valid
[ ] Admin passwords are strong (not default/123456)
[ ] SESSION_DRIVER=file (not database for speed)
[ ] CACHE_STORE=file (not database for speed)
[ ] LOG_LEVEL=warning (not debug)
[ ] Nginx security headers added
[ ] File permissions set correctly (775 on storage/bootstrap/cache)
[ ] Firewall configured (UFW)
[ ] SSH key authentication enabled
[ ] Rate limiting on login routes enabled
[ ] No test/debug code left in production
```

---

## 9. Incident Response

If a security incident occurs:

1. **Immediately** set `APP_DEBUG=true` temporarily to see errors, then turn off
2. **Rotate** all credentials (DB password, mail password, APP_KEY)
3. **Check** `failed_login_attempts` table for unauthorized access
4. **Check** Laravel logs for suspicious activity
5. **Restore** from last known-good backup if data is compromised
6. **Notify** affected users if personal data was exposed

### Backup Command

```bash
# Built-in SQLite backup (dev)
php artisan backup:database

# For MySQL production
mysqldump -u root -p portal_ujian > backup_$(date +%Y%m%d).sql
```

---

## 10. Quick Security Wins

These take < 5 minutes and provide immediate security improvement:

```bash
# 1. Generate new app key
php artisan key:generate

# 2. Enable strict cookies
# Add to .env:
SESSION_SECURE_COOKIE=true   # Only if HTTPS is configured

# 3. Add rate limiting to login (in routes/web.php)
# Add ->middleware('throttle:5,1') to login routes

# 4. Disable directory listing (already handled by Laravel public/index.php)
# But verify Nginx doesn't have autoindex on

# 5. Set correct permissions
chmod 640 .env
chmod -R 775 storage bootstrap/cache
```
