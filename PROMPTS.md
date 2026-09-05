# Portal Ujian — Development Guidelines

> Last updated: 2026-09-04

This file serves as a guide for future development, refactoring decisions, and coding standards. It captures lessons learned from the initial architecture review.

---

## 1. General Principles

### Do
- Keep controllers thin — extract business logic to service classes when a method exceeds ~50 lines
- Use Form Request classes (`StoreXxxRequest`, `UpdateXxxRequest`) for all validation
- Add eager loading (`with()`) whenever accessing relationships in views or loops
- Use route model binding (`Model $model`) instead of manual `Model::findOrFail($id)`
- Add return type hints on all Eloquent relationship methods
- Use `$fillable` only for fields that should be mass-assigned; guard sensitive fields
- Use `Hash::make()` for ALL password creation/updates (never store plain text)
- Use nullable relationship access (`$score->inputByAdmin?->name`) in Blade templates
- Group routes logically with prefix and name prefixes
- Keep Blade templates focused on presentation — no business logic

### Don't
- Don't put business logic directly in controllers beyond ~100 lines
- Don't use inline `$request->validate()` in controllers — use Form Requests
- Don't forget to eager load relationships accessed in Blade loops (N+1 risk)
- Don't store plain-text passwords — always hash with `Hash::make()`
- Don't hardcode `colspan` values in Blade templates — calculate dynamically or use a component
- Don't put inline JavaScript in Blade templates — extract to `resources/js/`
- Don't leave dead/unused code in the codebase
- Don't use the `AuditLog` model — it's unused and can be removed
- Don't add password reset infrastructure for admins via the web — use CLI commands

---

## 2. Adding a New Feature

### Step 1: Database

```
php artisan make:migration create_xxx_table
php artisan db:health           # Check DB integrity before migration
php artisan migrate
php artisan db:stats            # Verify tables created
```

### Step 2: Model
```
php artisan make:model Xxx
# Add fillable, casts, relationships with return type hints
# Add helper methods (e.g., isPassed, isVisible)
# Add scopes if needed
```

### Step 3: Form Requests
```
app/Http/Requests/Admin/StoreXxxRequest.php
app/Http/Requests/Admin/UpdateXxxRequest.php
```

### Step 4: Controller
```
app/Http/Controllers/Admin/XxxController.php
# Use Form Requests for validation
# Inject service classes for business logic
# Return views or JSON responses — keep methods focused
```

### Step 5: Routes
```php
Route::resource('xxxs', XxxController::class)->except(['show']);
```

### Step 6: Views
```
resources/views/admin/xxxs/index.blade.php
resources/views/admin/xxxs/create.blade.php
resources/views/admin/xxxs/edit.blade.php
# Keep JS in resources/js/admin/xxxs.js, import via Vite
```

### Step 7: Tests
```
tests/Feature/XxxControllerTest.php
tests/Unit/XxxModelTest.php
```

---

## 3. Import/Export Workflow

The current 3-step import pattern (preview → confirm → finalize) via session is the established pattern. When adding new imports:

1. **Preview** — validate file, parse rows, store in session, show preview table
2. **Confirm** — read from session, validate business rules, show confirmation
3. **Finalize** — read from session, process with try-catch per row, collect errors, clear session

**Always hash passwords** during import:
```php
// CORRECT
'password' => Hash::make($plainPassword),

// WRONG (never do this)
'password' => $plainPassword,
```

---

## 4. Score Publishing

Scores have a publish/unpublish workflow. Use the established pattern in `ScoreController`:
- Individual publish/unpublish per score
- Bulk publish/unpublish all
- Filter by subject or exam schedule

Always check `subject->passing_grade` when displaying pass/fail status.

---

## 5. Authentication Patterns

### Adding a new guard
1. Add to `config/auth.php`: guard + provider
2. Create middleware (or reuse `admin.auth` pattern)
3. Register middleware alias in `bootstrap/app.php`
4. Add route middleware to `routes/web.php`
5. Never use the same session guard for different user types

### Admin role checking
Current pattern: inline `$admin->isSuperadmin()` checks in controllers.
Future improvement: create a `RoleMiddleware` or `SuperadminMiddleware`.

---

## 6. Dashboard & Statistics

Dashboard statistics should be built in the controller using Eloquent aggregates, not in the view. Example:

```php
// In DashboardController
$totalStudents = User::count();
$totalScores = Score::where('is_published', true)->count();
$recentScores = Score::with(['user', 'subject'])
    ->where('is_published', true)
    ->latest()
    ->take(5)
    ->get();
```

---

## 7. Frontend (Blade + Tailwind)

- Extract all inline JavaScript from Blade templates to `resources/js/`
- Use Blade components (`<x-alert>`, `<x-modal>`) for reusable UI patterns
- Use `@error()` directive for field-level validation errors
- Dynamic colspan: `{{ $columns->count() + 1 }}` instead of hardcoded numbers
- Keep the admin layout (`layouts/admin.blade.php`) minimal — only layout logic, no feature-specific JS

---

## 8. Known Issues to Address

| Issue | Status | Notes |
|-------|--------|-------|
| Plain-text passwords in import | **TODO** | Hash passwords in `importFinalize()` |
| N+1: `Score::inputByAdmin` | **DONE** | Added to `with()` in `ScoreController::index()` |
| Dead code: `AuditLog` model | **TODO** | Remove model + migration, or implement logging |
| Dead code: `Subject::isPassed()` | **TODO** | Remove or use consistently |
| Dead code: `Admin::subjects()` etc. | **TODO** | Remove unused relationships |
| Inline JS in Blade templates | **TODO** | Extract to `resources/js/admin.js` |
| No rate limiting on login | **TODO** | Wire `FailedLoginAttempt` table to throttle |
| No Form Request classes | **DONE** | Form requests now used in Admin controllers |
| Notification system | **PLANNED** | See PRD.md for spec |
| Broadcast system | **PLANNED** | See PRD.md for spec |
| `.env` DB=sqlite for prod | **TODO** | Change to `mysql` for production |

---

## 9. Operational Commands

### Database Commands

| Command | Description | Use Case |
|---------|-------------|----------|
| `php artisan db:health` | Check DB connection, tables, indexes, FK integrity | Pre-deployment verification |
| `php artisan db:stats` | Show table row counts, key metrics, DB file size | Monitor data growth |
| `php artisan db:simulate` | Generate fake test students and scores | Load testing, development |
| `php artisan db:backup` | Backup SQLite database | Before risky operations |
| `php artisan db:seed` | Run database seeders | Fresh install |

### Admin Commands

| Command | Description |
|---------|-------------|
| `php artisan admin:create` | Create new admin account |
| `php artisan admin:list` | List all admins with last login |
| `php artisan admin:reset-password {email}` | Reset admin password |
| `php artisan logs:view` | View failed login attempts from CLI |

### Maintenance Commands

| Command | Description |
|---------|-------------|
| `php artisan down` | Put site in maintenance mode |
| `php artisan up` | Bring site back online |
| `php artisan cache:clear` | Clear all caches |
| `php artisan config:cache` | Cache config for production |
| `php artisan route:cache` | Cache routes for production |
| `php artisan view:cache` | Cache views for production |

### Monitoring

```bash
# Pre-deployment health check
php artisan db:health

# Check DB stats
php artisan db:stats

# Monitor failed logins
php artisan logs:view --type=web_failed --limit=10

# Generate test data for testing
php artisan db:simulate --students=50 --scores=100 --force
```

---

## 10. Activity Logging

All admin actions are automatically logged to `activity_logs` table:

- **Model events** — Created, updated, deleted (User, Subject, ExamSchedule, Score)
- **Score publishing** — Published, unpublished, bulk publish/unpublish
- **Import/Export** — Import results, export downloads
- **Auth** — Login, logout (admin only)

View logs at `/admin/logs` — two tabs: Activity (all actions) and Login Failed (failed attempts).

---

## 11. Deployment Checklist

See [DEPLOYMENT.md](DEPLOYMENT.md) for the full VPS deployment checklist.

Quick reference (pre-production):
- [ ] Run `php artisan db:health` — verify all tables and indexes
- [ ] Run `php artisan db:stats` — verify data integrity
- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Set `DB_CONNECTION=mysql` (or your managed DB)
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan storage:link`
- [ ] Run `npm install && npm run build`
- [ ] Delete root `.htaccess` (Laragon-specific)
- [ ] Point web server to `public/` as document root
- [ ] Set up queue worker via Supervisor (if jobs added)
- [ ] Set up cron for scheduler (if scheduled tasks added)
