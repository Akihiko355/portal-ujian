# Portal Ujian — Product Requirements Document

> Version: 1.0
> Last updated: 2026-09-04
> Status: Draft

---

## 1. Product Overview

### 1.1 Purpose

**Portal Ujian** is a web-based exam management system for educational institutions. It enables administrators to manage exam schedules, student scores, and course materials, while students can view their exam details and published scores.

### 1.2 Users

| Role | Description |
|------|-------------|
| **Admin** | Institution staff who manage departments, subjects, exam schedules, scores, and student data |
| **Student (Mahasiswa)** | End users who view exam schedules and their published scores |

### 1.3 Problem Statement

Currently, communication between admin and students relies on:
- Manual announcement channels (WhatsApp, email, or offline)
- Students checking exam details manually
- No real-time notification system

This creates information gaps, especially when exam schedules change or new scores are published.

### 1.4 Goals

1. Provide in-app notification center for admins
2. Enable targeted broadcast messaging from admin to students
3. Improve information delivery speed and reliability

---

## 2. Existing Features

### 2.1 Admin Panel

- **Departments** — CRUD operations for institutional departments
- **Subjects** — CRUD operations for courses/subjects with passing grade
- **Students** — CRUD operations + Excel import/export
- **Exam Schedules** — CRUD with visibility settings (link/password reveal timing)
- **Scores** — Input scores with publish/unpublish workflow
- **Activity Logs** — Failed login attempt tracking

### 2.2 Student Portal

- **Dashboard** — View published exam schedules and scores
- **Profile** — Update personal information and password

### 2.3 Authentication

- Dual guard system: `web` (students) + `admin`
- Student self-registration
- Admin accounts created via CLI only
- Failed login attempt tracking

---

## 3. New Feature: Alert & Broadcast System

### 3.1 Feature A — In-App Notification Center (Admin)

**Objective:** Admins receive real-time in-app notifications for important system events.

#### Notification Types

| Event | Title | Message Template | Priority |
|-------|-------|------------------|----------|
| New student registered | Student Baru | "{name} ({email}) telah mendaftar" | Low |
| New score added | Nilai Baru | "Nilai {subject} untuk {student} telah diinput" | Medium |
| Score published | Nilai Dipublikasi | "Nilai {subject} ({count} mahasiswa) telah dipublikasi" | Medium |
| Exam schedule created | Jadwal Baru | "Jadwal ujian {subject} pada {date} telah dibuat" | High |
| Exam starting soon | Reminder Ujian | "Ujian {subject} akan dimulai dalam {hours} jam" | High |
| Bulk import completed | Import Selesai | "Import {count} mahasiswa dari {file} selesai" | Medium |

#### Priority Levels

| Level | Badge Color | Behavior |
|-------|------------|----------|
| **Low** | Slate (gray) | Regular display, no urgency |
| **Medium** | Amber | Regular display |
| **High** | Red | Stays at top of notification list |
| **Urgent** | Red + badge | Badge on bell icon, counts toward unread total |

#### UI Requirements

- **Bell icon** in admin sidebar with unread count badge
- **Dropdown panel** showing latest 20 notifications
- **"Lihat Semua"** link to full notification page
- **Mark as read** on click, or "Mark all read" button
- **Full page** (`/admin/notifications`) with filterable list
- **Timestamps** relative ("5 menit lalu", "Kemarin")

### 3.2 Feature D — Broadcast Message (Admin → Students)

**Objective:** Admins can send announcements to students with targeting options.

#### Broadcast Types

| Type | Description |
|------|-------------|
| **General** | All active students |
| **By Department** | Students in selected department(s) |
| **By Exam Schedule** | Students registered for specific exam |
| **Custom** | Filter by combination of dept + exam schedule |

#### Message Structure

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `title` | string | Yes | Max 100 chars |
| `content` | text | Yes | Max 2000 chars |
| `urgency` | enum | Yes | `info`, `warning`, `important` |
| `target_type` | enum | Yes | `all`, `department`, `exam_schedule`, `custom` |
| `target_ids` | JSON | Conditional | Array of dept/exam IDs |
| `exam_schedule_id` | int | Conditional | For "By Exam Schedule" |
| `send_at` | datetime | No | Schedule for future delivery |
| `expires_at` | datetime | No | Auto-hide after this time |

#### Urgency Levels

| Level | Badge Color | Student Experience |
|-------|------------|---------------------|
| **Info** | Blue | Regular display in dashboard banner |
| **Warning** | Amber | Highlighted banner, persistent until dismissed |
| **Important** | Red | Full-width banner, persistent, cannot dismiss |

#### Student UI

- **Dashboard banner** at top of student dashboard showing active broadcasts
- **Notification bell** with count (same as admin)
- **Full page** (`/notifications`) with history

#### Admin UI

- **Broadcast page** (`/admin/broadcasts`) — list of sent broadcasts
- **"Buat Broadcast"** button → modal or dedicated page
- **Filters** — by urgency, target type, date range
- **Stats** — delivery count, read count per broadcast

---

## 4. User Flows

### 4.1 Admin: View Notifications

```
Admin Dashboard
    → Klik bell icon (sidebar)
    → Dropdown: list notification (20 terbaru)
    → "Lihat Semua" → /admin/notifications
    → Full page: filter by priority, date, read status
    → Klik notifikasi → Mark as read + redirect to relevant page
```

### 4.2 Admin: Create Broadcast

```
Admin Dashboard
    → Menu: "Broadcast" atau tab di sidebar
    → Klik "Buat Broadcast Baru"
    → Form: title, content, urgency
    → Pilih target: All / Dept / Exam
    → (Optional) Schedule send time
    → Preview recipients count
    → Submit
    → Confirmation page with delivery stats
```

### 4.3 Student: View Broadcast

```
Student Dashboard
    → Banner appears at top (if active broadcast exists)
    → Dismiss info/warning banners
    → Click "Lihat" → /notifications page
    → Full list of all broadcasts (paginated)
    → Can filter by urgency
```

---

## 5. Data Models

### 5.1 Notification (Admin Alerts)

```
notifications
├── id (bigint, PK)
├── type (string) — event type identifier
├── title (string, max:100)
├── message (text)
├── priority (enum: low, medium, high, urgent)
├── data (json) — additional context {user_id, subject_id, etc.}
├── link (string, nullable) — redirect URL after click
├── read_at (timestamp, nullable)
├── created_at
└── updated_at
```

### 5.2 Broadcast

```
broadcasts
├── id (bigint, PK)
├── admin_id (FK → admins)
├── title (string, max:100)
├── content (text, max:2000)
├── urgency (enum: info, warning, important)
├── target_type (enum: all, department, exam_schedule, custom)
├── target_ids (json) — [dept_ids] or [exam_schedule_id]
├── send_at (timestamp)
├── expires_at (timestamp, nullable)
├── created_at
└── updated_at
```

### 5.3 Broadcast Delivery

```
broadcast_delivery
├── id (bigint, PK)
├── broadcast_id (FK → broadcasts)
├── user_id (FK → users)
├── read_at (timestamp, nullable)
├── created_at
└── updated_at
```

### 5.4 Broadcast Receipt (for student notifications)

```
broadcast_receipts
├── id (bigint, PK)
├── broadcast_id (FK → broadcasts)
├── user_id (FK → users)
├── dismissed (boolean, default: false)
├── dismissed_at (timestamp, nullable)
├── read_at (timestamp, nullable)
├── created_at
└── updated_at
```

---

## 6. API / Routes

### 6.1 Admin Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/admin/notifications` | `admin.notifications.index` | List all notifications |
| GET | `/admin/notifications/{id}` | `admin.notifications.show` | View single notification |
| PATCH | `/admin/notifications/{id}/read` | `admin.notifications.read` | Mark as read |
| POST | `/admin/notifications/read-all` | `admin.notifications.read-all` | Mark all as read |
| DELETE | `/admin/notifications` | `admin.notifications.destroy` | Delete notification |
| GET | `/admin/broadcasts` | `admin.broadcasts.index` | List broadcasts |
| GET | `/admin/broadcasts/create` | `admin.broadcasts.create` | Create broadcast form |
| POST | `/admin/broadcasts` | `admin.broadcasts.store` | Store broadcast |
| GET | `/admin/broadcasts/{id}` | `admin.broadcasts.show` | View broadcast + stats |
| DELETE | `/admin/broadcasts/{id}` | `admin.broadcasts.destroy` | Delete broadcast |

### 6.2 Student Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/notifications` | `user.notifications.index` | List student notifications |
| PATCH | `/notifications/{id}/read` | `user.notifications.read` | Mark as read |
| POST | `/notifications/dismiss` | `user.notifications.dismiss` | Dismiss banner |

---

## 7. Acceptance Criteria

### 7.1 Notification Center (Admin)

- [ ] Bell icon shows unread count badge
- [ ] Dropdown shows latest 20 notifications
- [ ] Full page with pagination and filters
- [ ] Notifications created automatically on system events
- [ ] Clicking notification marks as read and redirects
- [ ] High/Urgent priority notifications appear at top

### 7.2 Broadcast (Admin → Students)

- [ ] Admin can create broadcast with title, content, urgency
- [ ] Admin can target: all, by department, by exam schedule
- [ ] Preview shows estimated recipient count before sending
- [ ] Broadcast appears as banner on student dashboard
- [ ] Students can dismiss info/warning banners
- [ ] Important banners persist until expired
- [ ] Students can view full broadcast history
- [ ] Admin can view delivery + read stats per broadcast

### 7.3 Security

- [ ] Broadcast can only be created by admin (not student)
- [ ] Students cannot access admin notification routes
- [ ] Rate limiting on broadcast creation (prevent spam)
- [ ] Broadcast content sanitized (XSS prevention)

---

## 8. Out of Scope (This Version)

- Email notifications (Option B) — future enhancement
- Push notifications / mobile app
- Real-time WebSocket updates (polling is acceptable for v1)
- Student reply/response to broadcasts
- Scheduled/subscription-based notification preferences

---

## 9. Technical Approach

### 9.1 Architecture

- **Database-driven notifications** — Polling every 30 seconds via AJAX
- **Eloquent observers** — Trigger notifications on model events
- **Queue jobs** — For broadcast delivery (batch insert to `broadcast_receipts`)
- **No WebSocket/SSE** — Keep it simple; polling is sufficient for v1

### 9.2 Notification Trigger Points

```php
// On new user registration
User::created() → Notification::create(['type' => 'student_registered', ...])

// On score created
Score::created() → Notification::create(['type' => 'score_added', ...])

// On score published
Score::published() → Notification::create(['type' => 'score_published', ...])

// On exam schedule created
ExamSchedule::created() → Notification::create(['type' => 'schedule_created', ...])
```

### 9.3 Broadcast Delivery Flow

```
1. Admin submits broadcast form
2. Validate + save to `broadcasts` table
3. Dispatch Queue Job: "DeliverBroadcastJob"
4. Job: INSERT INTO broadcast_receipts for each target user
5. Admin sees "Delivering..." state
6. Job complete → update delivery count stats
```

### 9.4 Polling Strategy

```javascript
// Every 30 seconds (admin), 60 seconds (student)
setInterval(fetchNotifications, 30000);

async function fetchNotifications() {
    const res = await axios.get('/api/notifications/recent');
    updateBadge(res.data.unread_count);
    if (res.data.unread_count > 0) {
        showToast(res.data.latest?.title);
    }
}
```

---

## 10. Security Considerations

- **Authorization**: All admin routes protected by `auth:admin` middleware
- **Broadcast spam**: Rate limit broadcast creation (max 10/day per admin)
- **XSS**: All broadcast content escaped in Blade templates (`{{ }}`)
- **CSRF**: Laravel's built-in CSRF protection on all POST/PUT/DELETE routes
- **SQL Injection**: Eloquent ORM parameter binding
- **Session**: Session-based auth, not JWT (more secure for traditional web apps)
- **Rate Limiting**: Throttle login attempts (existing `FailedLoginAttempt` table)
- **Password**: Bcrypt hashing, no plain-text storage

---

## 11. Open Questions

- [ ] Should notifications be deletable by admins?
- [ ] Maximum broadcast recipients per send? (pagination or batch)
- [ ] Should broadcasts be editable after sending?
- [ ] Notification retention period? (auto-delete old notifications?)
