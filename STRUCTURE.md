# Portal Ujian — Project Structure

> Last updated: 2026-08-31

This document describes the intended target structure for this project after cleanup. Use this as a guide when adding new files.

---

## Target Monorepo Structure

```
portal-ujian/
├── app/
│   ├── Console/Commands/
│   │   ├── BackupDatabase.php
│   │   ├── CreateAdmin.php
│   │   ├── Deploy.php
│   │   ├── ListAdmins.php
│   │   ├── ResetAdminPassword.php
│   │   ├── Status.php
│   │   └── ViewLogs.php
│   ├── Exports/
│   │   └── MahasiswaExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── Admin/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DepartmentController.php
│   │   │   │   ├── ExamScheduleController.php
│   │   │   │   ├── LogController.php
│   │   │   │   ├── ScoreController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   └── UserController.php
│   │   │   └── User/
│   │   │       ├── AuthController.php
│   │   │       ├── DashboardController.php
│   │   │       └── ProfileController.php
│   │   ├── Middleware/
│   │   │   └── AdminAuth.php
│   │   └── Requests/              # ← Add Form Request classes here
│   │       ├── Admin/
│   │       │   ├── StoreDepartmentRequest.php
│   │       │   ├── UpdateDepartmentRequest.php
│   │       │   ├── StoreSubjectRequest.php
│   │       │   ├── UpdateSubjectRequest.php
│   │       │   ├── StoreUserRequest.php
│   │       │   ├── UpdateUserRequest.php
│   │       │   ├── StoreExamScheduleRequest.php
│   │       │   ├── UpdateExamScheduleRequest.php
│   │       │   ├── StoreScoreRequest.php
│   │       │   └── UpdateScoreRequest.php
│   │       └── User/
│   │           ├── LoginRequest.php
│   │           └── RegisterRequest.php
│   ├── Imports/
│   │   └── MahasiswaImport.php
│   ├── Models/
│   │   ├── Admin.php
│   │   ├── Department.php
│   │   ├── ExamParticipant.php
│   │   ├── ExamSchedule.php
│   │   ├── FailedLoginAttempt.php
│   │   ├── Score.php
│   │   ├── Subject.php
│   │   └── User.php
│   └── Services/                   # ← Add service classes here
│       ├── FailedLoginAttemptService.php
│       └── ScorePublishService.php
├── bootstrap/
│   └── app.php
├── config/
├── database/
│   ├── database.sqlite
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── .htaccess
├── resources/
│   ├── css/
│   ├── js/                         # Extract inline JS here
│   │   └── admin.js               # Admin layout JS (from admin.blade.php)
│   └── views/
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
├── tests/
├── .env                            # Local env (not committed)
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
├── DEPLOYMENT.md                   # ← Deployment guide
├── ARCHITECTURE.md                 # ← This file's companion
└── PROMPTS.md                      # ← Development guidelines
```

---

## Naming Conventions

### Models
- Singular, PascalCase: `ExamSchedule`, `Score`, `FailedLoginAttempt`
- Relationship methods: plural for `hasMany`/`belongsToMany`, singular for `belongsTo`
- Always add return type hints on relationship methods:
  ```php
  public function scores(): HasMany { ... }
  public function subject(): BelongsTo { ... }
  ```

### Controllers
- Folder namespace separates domain: `Admin\` vs `User\`
- Controller name = Resource name: `DepartmentController`, `ScoreController`
- Methods: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` + custom actions

### Routes
- Use kebab-case for multi-word resources: `exam-schedules`, `admin.exam-schedules.*`
- Prefix user routes with `user.`, admin routes with `admin.`
- Use `except(['show'])` on resources only when a `show()` method exists

### Views
- Match controller names: `admin/departments/`, `admin/scores/`
- Layouts: `admin.blade.php`, `user.blade.php`
- Partials: `admin/_sidebar.blade.php`, `admin/_toast.blade.php`

### Form Request Classes
- `Store{Resource}Request.php` and `Update{Resource}Request.php`
- Grouped in `app/Http/Requests/{Admin|User}/`
- Never do inline `$request->validate()` in controllers

### Service Classes
- `{Domain}Service.php` — e.g., `ScorePublishService.php`
- Single responsibility — one service per workflow
- Injected via constructor or method injection
- Register in `AppServiceProvider`

### Artisan Commands
- `admin:create`, `admin:list`, `admin:reset-password`
- `backup:database`, `app:deploy`, `app:status`, `logs:view`

---

## File-to-Route Mapping

| File | Route Group |
|------|------------|
| `Admin/AuthController` | `/admin/login`, `/admin/logout` |
| `Admin/DashboardController` | `/admin/dashboard` |
| `Admin/DepartmentController` | `/departments` |
| `Admin/SubjectController` | `/subjects` |
| `Admin/UserController` | `/users`, `/users/import*`, `/users/export` |
| `Admin/ExamScheduleController` | `/exam-schedules` |
| `Admin/ScoreController` | `/scores`, `/scores/publish*` |
| `Admin/LogController` | `/logs` |
| `User/AuthController` | `/login`, `/register`, `/logout` |
| `User/DashboardController` | `/dashboard` |
| `User/ProfileController` | `/profile`, `/profile/password` |

---

## Database Schema

```
Table: admins               # Admin users
Table: users                # Student/mahasiswa users
Table: departments           # Academic departments
Table: subjects              # Exam subjects
Table: department_subject    # Pivot: departments ↔ subjects
Table: admin_subject         # Pivot: admins ↔ subjects
Table: exam_schedules        # Scheduled exams
Table: exam_participants     # Pivot: exam_schedules ↔ users
Table: scores                # Student exam scores
Table: audit_logs            # Admin action audit (unused)
Table: failed_login_attempts # Login attempt tracking
Table: admin_password_reset_tokens # Admin password reset (unused)
Table: sessions              # Laravel sessions
Table: cache                 # Laravel cache
Table: jobs                  # Laravel queue
```
