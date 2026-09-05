# Portal Ujian — Architecture Documentation

> Last updated: 2026-09-04

---

## 1. Overview

**Portal Ujian** is a Laravel 11 exam management system for an educational institution. It handles student registration, exam scheduling, score management, and admin workflows. The system is built as a traditional Laravel monolith with a dual-authentication architecture separating the **Student Portal** from the **Admin Panel**.

- **Framework:** Laravel 11.31
- **PHP:** ^8.2
- **Frontend:** Tailwind CSS 3 + Vite 6 + Blade components + Inter font (2026 redesign)
- **Database:** SQLite (dev) / MySQL (prod) — Maatwebsite Excel for spreadsheet import/export
- **Database:** SQLite (dev) / MySQL (prod) — Maatwebsite Excel for spreadsheet import/export
- **Auth:** Dual guard — `web` (students) + `admin` (administrators)

---

## 2. Architecture Pattern

**Pure MVC** — no service layer, no repository pattern, no API routes.

```
┌─────────────────────────────────────────────────────────┐
│                    Routes (web.php)                      │
│  /login, /register, /dashboard    →  User routes       │
│  /admin/*                          →  Admin routes      │
└──────────┬─────────────────────────┬───────────────────┘
           │                         │
           ▼                         ▼
    User Controllers          Admin Controllers
    (User/ folder)            (Admin/ folder)
           │                         │
           └──────────┬──────────────┘
                      ▼
              Eloquent Models
              (9 models total)
                      │
                      ▼
               MySQL / SQLite
```

**Characteristics:**
- Flat MVC — business logic lives in controllers
- Session-based auth with dual guard (`web` + `admin`)
- Form Request classes for validation
- Blade components for reusable UI (page-header, badge, stat-card, input, select, empty-state, modal)
- No service layer, no API resources, no events/listeners (planned: Eloquent observers for notifications)
- File-based session and cache for production performance

---

## 3. Directory Structure

```
portal-ujian/
├── app/
│   ├── Console/Commands/          # 10 Artisan commands
│   │   ├── BackupDatabase.php     # SQLite backup
│   │   ├── CreateAdmin.php       # CLI admin creation
│   │   ├── DbHealth.php         # DB integrity check
│   │   ├── DbSimulate.php       # Fake data generator
│   │   ├── DbStats.php          # DB statistics
│   │   ├── Deploy.php           # Deployment automation
│   │   ├── ListAdmins.php       # List admin accounts
│   │   ├── ResetAdminPassword.php# CLI password reset
│   │   ├── Status.php           # System statistics
│   │   └── ViewLogs.php         # Login activity viewer
│   ├── Exports/                   # Excel export classes
│   │   └── MahasiswaExport.php   # Student Excel export
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php    # Abstract base (empty)
│   │   │   ├── Admin/            # Admin controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DepartmentController.php
│   │   │   │   ├── ExamScheduleController.php
│   │   │   │   ├── LogController.php
│   │   │   │   ├── ScoreController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   └── UserController.php
│   │   │   └── User/             # User (student) controllers
│   │   │       ├── AuthController.php
│   │   │       ├── DashboardController.php
│   │   │       └── ProfileController.php
│   │   └── Middleware/
│   │       └── AdminAuth.php     # Custom admin guard middleware
│   ├── Imports/
│   │   └── MahasiswaImport.php   # Student Excel import
│   ├── Models/
│   ├── Observers/
│   │   └── ModelObserver.php     # Auto-logs model events to ActivityLog
│   ├── Providers/
│   │   └── AppServiceProvider.php # Registers model observers
│   └── Models/
│       ├── ActivityLog.php        # Admin activity audit trail
│       ├── Admin.php
│       ├── Department.php
│       ├── ExamSchedule.php
│       ├── FailedLoginAttempt.php
│       ├── Score.php
│       ├── Subject.php
│       └── User.php
├── bootstrap/
│   └── app.php                   # Laravel 11 fluent config
├── config/
│   ├── app.php                   # Core app config
│   ├── auth.php                  # Dual guard config
│   ├── cache.php                 # Database cache driver
│   ├── database.php              # SQLite/MySQL config
│   ├── filesystems.php           # Local + S3 disks
│   ├── logging.php               # Monolog stack
│   ├── mail.php                  # SMTP (Mailtrap dev)
│   ├── queue.php                 # Database queue driver
│   ├── services.php              # Third-party stubs
│   └── session.php               # Database sessions
├── database/
│   ├── database.sqlite           # Dev SQLite database
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/               # 15+ migration files
│   └── seeders/
│       └── DatabaseSeeder.php    # Seeds: 1 admin, 3 departments, 5 subjects
├── resources/
│   ├── css/app.css              # Tailwind CSS source
│   ├── js/app.js                # Axios + bootstrap JS
│   └── views/
│       ├── admin/                # 21 admin Blade templates
│       ├── layouts/
│       │   ├── admin.blade.php  # Admin layout (sidebar + toasts)
│       │   └── user.blade.php   # User layout
│       ├── user/                 # User-facing templates
│       └── welcome.blade.php     # Public landing page
├── routes/
│   ├── web.php                   # All web routes (65 lines)
│   └── console.php               # Artisan inspire command
└── tests/                         # PHPUnit test suite
```

---

## 4. Model Relationships

```
Admin ──────────< admin_subject (pivot)
  │                     │
  │                     └───< Subject ─────< department_subject (pivot)
  │                                        │
  │                                        └───< Department ─────< User
  │                                                              │
  │                                                              │
  │                                      ┌───────────────────────┘
  │                                      │
  │                                      ▼
  │                              ExamSchedule
  │                                      │
  │                                      └───< ExamParticipant >── User
  │                                      │
  │                                      └───< Score >───────── User
  │                                                    │
  │                                                    └─── Subject
  │
  └───< AuditLog (unused)
  └───< Score (input_by_admin_id)
```

### Model Summary

| Model | Relationships | Key Method |
|-------|--------------|------------|
| `User` | belongsTo Department; hasMany ExamParticipant, Score | — |
| `Admin` | belongsToMany Subject; hasMany AuditLog, Score | `isSuperadmin()` |
| `Department` | hasMany User, ExamSchedule; belongsToMany Subject | — |
| `Subject` | belongsToMany Department, Admin; hasMany ExamSchedule, Score | `isPassed($score)` |
| `ExamSchedule` | belongsTo Subject, Department; hasMany ExamParticipant, Score | `isLinkVisible()`, `isPasswordVisible()` |
| `ExamParticipant` | belongsTo ExamSchedule, User | — |
| `Score` | belongsTo User, Subject, ExamSchedule, Admin | `isPassed()` |
| `AuditLog` | belongsTo Admin, User, Subject | — (unused) |
| `FailedLoginAttempt` | — | — |

### Database Constraints

- `scores`: unique `(user_id, subject_id)`
- `exam_participants`: unique `(exam_schedule_id, user_id)` + unique `(exam_schedule_id, participant_number)`
- `exam_schedules`: unique `(subject_id, department_id)`
- `failed_login_attempts`: indexed `(email, guard_type, attempted_at)` + `(ip_address, guard_type, attempted_at)`

---

## 5. Authentication Architecture

### Dual Guard System

| Guard | Guard Name | Model | Provider | Purpose |
|-------|-----------|-------|---------|---------|
| `web` | session | `User` | `users` | Student/mahasiswa login + registration |
| `admin` | session | `Admin` | `admins` | Administrator login |

### User (Student) Auth

- Registration enabled — students self-register
- Fields: name, email, phone, password, department_id, institution_address, is_active, nomor_ujian
- `is_active` toggle — inactive users are logged out immediately
- Failed login tracking → `failed_login_attempts` with `guard_type = web_failed`

### Admin Auth

- Registration **disabled** — created via CLI (`php artisan admin:create`) or seeder
- Roles: `superadmin` vs `admin` (enum in DB)
- `is_active` toggle — inactive admins cannot log in
- `last_login_at` tracking on every successful login
- No web-based password reset (use `php artisan admin:reset-password`)

### Middleware

| Middleware | Usage |
|-----------|-------|
| `guest` | User login/register pages |
| `auth:web` | User authenticated routes |
| `guest:admin` | Admin login page |
| `admin.auth` (custom) | Admin authenticated routes |

---

## 6. Routes

**Single file:** `routes/web.php` (65 lines)

### User Routes (prefix: `/`, name: `user.`)

| Method | URI | Name | Middleware |
|--------|-----|------|-----------|
| GET | `/login` | `user.login` | guest |
| POST | `/login` | `user.login.submit` | guest |
| GET | `/register` | `user.register` | guest |
| POST | `/register` | `user.register.submit` | guest |
| POST | `/logout` | `user.logout` | auth:web |
| GET | `/dashboard` | `user.dashboard` | auth:web |
| GET | `/profile` | `user.profile.edit` | auth:web |
| PUT | `/profile` | `user.profile.update` | auth:web |
| PUT | `/profile/password` | `user.profile.password` | auth:web |

### Admin Routes (prefix: `/admin`, name: `admin.`)

| Resource | Controller | Except |
|----------|-----------|--------|
| `departments` | DepartmentController | show |
| `subjects` | SubjectController | show |
| `users` | UserController | show |
| `exam-schedules` | ExamScheduleController | show |
| `scores` | ScoreController | show |

Additional admin routes: login, logout, dashboard, export, import (3-step), publish/unpublish actions, logs.

---

## 7. Business Logic Locations

| Feature | Location |
|---------|----------|
| Score publish/unpublish (8 actions) | `Admin/ScoreController.php` |
| Multi-step user import (preview → confirm → finalize) | `Admin/UserController.php` |
| Excel export | `Exports/MahasiswaExport.php` + `Admin/UserController::export()` |
| Dashboard statistics | `Admin/DashboardController.php` |
| Student dashboard (published scores) | `User/DashboardController.php` |
| Login attempt tracking | `Admin/AuthController.php` + `User/AuthController.php` |
| Visibility logic (exam link/password reveal) | `ExamSchedule.php` model methods |

---

## 8. Configuration

### Environment Defaults (`.env`)

```
APP_NAME="Portal Ujian"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite              # Use mysql for production
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=dync
FILESYSTEM_DISK=local
MAIL_MAILER=smtp
```

### Runtime Requirements

| Requirement | Details |
|------------|---------|
| PHP | 8.2+ |
| Database | MySQL 8.0+ (or SQLite for dev) |
| Node.js | For asset build only (`npm run build`) |
| Composer | For dependency installation |
| Queue worker | `php artisan queue:work` (if jobs are added) |
| Scheduler | `php artisan schedule:run` in cron (if scheduled tasks added) |
| Storage link | `php artisan storage:link` (one-time) |

---

## 9. Strengths & Weaknesses

### Strengths

- Clean, consistent controller naming and organization
- Well-defined relational schema with proper constraints and indexes
- Dual-auth architecture cleanly separates concerns
- Session-based import workflow (preview → confirm → finalize) is UX-friendly
- Comprehensive failed-login tracking infrastructure
- Laravel 11 minimal structure — no legacy cruft

### Weaknesses

- **No service layer** — business logic embedded in controllers
- **No Form Request classes** — all validation is inline
- **No API routes** — web-only, no mobile/headless support
- **Dead code** — `AuditLog` model, unused migrations, orphaned relationships
- **No rate limiting** — despite `FailedLoginAttempt` infrastructure (planned)
- **Inline JS in Blade templates** — ~150 lines in admin layout
- **No role middleware** — `isSuperadmin()` checks inline in controllers
- **Plain-text password storage** — importFinalize() stores passwords unhashed
