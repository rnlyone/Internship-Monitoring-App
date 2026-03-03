# 📋 Mieru Internship Log

> A comprehensive internship monitoring system for managing schedules, attendance, logbooks, approvals, and reporting — built with **Laravel 12** and the **Vuexy Bootstrap 5** UI template.

---

## ✨ Features

### 👩‍💼 For Interns

| Feature | Description |
|---|---|
| **Interactive Calendar** | View and manage personal shift schedules with Flatpickr date/time picker |
| **Presence Stamps** | Clock in (entry) and clock out (exit) per shift; system auto-detects late/absence status |
| **Shift Logbooks** | Write, edit, and delete per-shift activity logs |
| **Dashboard** | Countdown timers to upcoming shifts, weekly hours chart, attendance overview |
| **Kanban Board** | Drag-and-drop cards across columns (Backlog → To Do → In Progress → Review → Done) |
| **Profile Management** | Update name, email, and password |

### 🛠️ For Admins

| Feature | Description |
|---|---|
| **Schedule Approvals** | Approve or reject intern-submitted schedules; bulk approve/reject with filters |
| **Logbook Review** | Browse all intern logbook entries with search and filtering |
| **Internship Reports** | Per-intern summary with KPI tiles, donut chart, bar charts, approval breakdown, and **PDF export** |
| **Kanban Management** | Full CRUD on kanban cards (create, edit, delete); set color, priority, due date, assignee |
| **User Management** | Create, view, edit, and delete intern accounts |
| **App Settings** | Configure application-wide settings (internship dates, org name, etc.) |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | [Laravel 12](https://laravel.com) · PHP 8.2+ |
| **Auth** | Laravel Breeze (session-based, email verification) |
| **UI Framework** | [Vuexy v2.0](https://pixinvent.com/vuexy-bootstrap-html-admin-template/) Bootstrap 5 |
| **Database** | SQLite (default) — swappable to MySQL/PostgreSQL |
| **PDF Export** | [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) v3.1 |
| **Charts** | [Chart.js 4](https://www.chartjs.org/) (CDN) |
| **Drag & Drop** | [SortableJS](https://sortablejs.github.io/Sortable/) (vendored) |
| **Date Picker** | [Flatpickr](https://flatpickr.js.org/) (vendored) |
| **Icons** | Tabler Icons (`ti-*`) |
| **Frontend Build** | [Vite 7](https://vite.dev/) + `laravel-vite-plugin` |
| **Frontend JS** | [Alpine.js 3](https://alpinejs.dev/) · [Axios](https://axios-http.com/) |
| **Testing** | [Pest 4](https://pestphp.com/) + `pest-plugin-laravel` |
| **Dev Tools** | Laravel Sail, Pint, Pail |
| **Timezone** | `Asia/Singapore` |

---

## 📋 Requirements

- PHP **8.2** or higher
- [Composer](https://getcomposer.org/) 2.x
- [Node.js](https://nodejs.org/) 18+ with npm
- SQLite (bundled with PHP) — or a MySQL/PostgreSQL server if you change `DB_CONNECTION`

---

## 🚀 Installation

### Quick Setup (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/your-org/log.mieru.or.id.git
cd log.mieru.or.id

# 2. Copy environment file and configure
cp .env.example .env

# 3. Run the full setup script (install deps, generate key, migrate, build assets)
composer run setup

# 4. Start the development server
composer run dev
```

> `composer run setup` runs: `composer install` → `.env` copy → `artisan key:generate` → `artisan migrate` → `npm install` → `npm run build`

> `composer run dev` starts concurrently: `artisan serve`, `queue:listen`, `artisan pail` (log tailing), and `npm run dev` (Vite HMR)

---

### Manual Setup

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy and configure environment file
cp .env.example .env
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets (production)
npm run build
# OR watch for changes (development)
npm run dev

# Start the application
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000)

---

## ⚙️ Environment Configuration

Key variables in your `.env` file:

```env
APP_NAME="Mieru Internship Log"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Singapore

# Database (SQLite default — no extra config needed)
DB_CONNECTION=sqlite

# To use MySQL instead:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mieru_log
# DB_USERNAME=root
# DB_PASSWORD=

# Mail (for email verification)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@mieru.or.id"
```

---

## 🗄️ Database Schema

The application uses SQLite by default (`database/database.sqlite` — created automatically on first migrate).

### Tables

| Table | Description |
|---|---|
| `users` | Users with `role` column (`admin` \| `intern`) |
| `schedule_slots` | Shifts with UUID PK, `start_shift`, `end_shift`, `caption`, `status`, `approval_status` |
| `presence_stamps` | Entry/exit stamps (`type`: `entry` \| `exit`) linked to a schedule slot |
| `shift_logbooks` | Per-shift activity log entries linked to a schedule slot |
| `kanban_cards` | Kanban cards with `column_name`, `position`, `color`, `priority`, `due_date`, `assigned_to` |
| `settings` | Key–value store for app-wide configuration |
| `cache` | Laravel cache table |
| `jobs` | Laravel queue jobs table |

### Schedule Slot Statuses

| Status | Meaning |
|---|---|
| `not_yet` | Shift hasn't started yet |
| `ongoing` | Shift is currently in progress |
| `done` | Intern clocked in and out successfully |
| `late` | Intern clocked in after shift start time |
| `absence` | Shift ended without any presence stamp |

### Approval Statuses

| Status | Meaning |
|---|---|
| `pending` | Awaiting admin review |
| `approved` | Admin approved the schedule |
| `rejected` | Admin rejected the schedule |

---

## 🧭 Application Routes

### Public

| Method | URI | Description |
|---|---|---|
| `GET` | `/` | Welcome / landing page |

### Authenticated (All Roles)

| Method | URI | Description |
|---|---|---|
| `GET` | `/dashboard` | Main dashboard |
| `GET` | `/schedules` | Calendar view |
| `GET` | `/schedules/events` | Calendar events JSON |
| `POST` | `/schedules` | Create a new shift |
| `PUT` | `/schedules/{id}` | Update a shift |
| `DELETE` | `/schedules/{id}` | Delete a shift |
| `POST` | `/presence/{id}/entry` | Clock in |
| `POST` | `/presence/{id}/exit` | Clock out |
| `GET/POST/PUT/DELETE` | `/logbooks/{schedule}` | Shift logbook CRUD |
| `GET` | `/kanban` | Kanban board |
| `POST` | `/kanban/reorder` | Save drag-and-drop position |
| `GET/PUT/DELETE` | `/kanban/cards/{card}` | Card detail / edit / delete |
| `GET/PATCH/DELETE` | `/profile` | Profile management |

### Admin Only

| Method | URI | Description |
|---|---|---|
| `GET` | `/admin/approvals` | Schedule approval list |
| `POST` | `/admin/approvals/bulk-approve` | Bulk approve schedules |
| `POST` | `/admin/approvals/bulk-reject` | Bulk reject schedules |
| `GET` | `/admin/logbooks` | All intern logbooks |
| `GET` | `/admin/reports` | Internship reports list |
| `GET` | `/admin/reports/{intern}` | Single intern report (JSON) |
| `GET` | `/admin/reports/{intern}/pdf` | Export report as PDF |
| `GET/POST/PUT/DELETE` | `/admin/users` | User management CRUD |
| `GET/PUT` | `/settings` | App settings |

---

## 📊 Internship Reports & PDF Export

The Reports module provides a rich analytics view per intern:

**On-page Visualisation:**
- 12 KPI tiles (total shifts, done, late, absences, total/done/late/absent hours, logbook entries, approval counts, attendance rate)
- Donut chart — attendance breakdown (done / late / absence)
- Bar chart — weekly hours worked
- Approval status progress bar (approved / pending / rejected)
- Full schedule table with expandable logbook entries per shift

**PDF Export** (`/admin/reports/{intern}/pdf`) generates a comprehensive 2-page document:
- Intern identity card + KPI tiles
- Attendance breakdown bars + weekly hours table
- Kanban cards summary
- Full schedule detail with inline logbook entries
- Auto-named: `internship-report-{name}-{date}.pdf`

---

## 🗂️ Kanban Board

All authenticated users can view and drag cards between columns. Only **admins** can create, edit, or delete cards.

### Columns

`Backlog` → `To Do` → `In Progress` → `Review` → `Done`

### Card Fields

| Field | Details |
|---|---|
| **Title** | Required |
| **Description** | Free text textarea |
| **Color** | 8 preset swatches + custom hex input |
| **Priority** | Low / Medium / High (color badge) |
| **Due Date** | Flatpickr date picker |
| **Assigned To** | Any registered user |

Card positions persist immediately to the database on every drag via a `POST /kanban/reorder` call.

---

## 👥 User Roles

| Capability | Admin | Intern |
|---|---|---|
| View own dashboard | ✅ | ✅ |
| Manage own schedules | ✅ | ✅ |
| Clock in / clock out | ✅ | ✅ |
| Write shift logbooks | ✅ | ✅ |
| View & drag kanban board | ✅ | ✅ |
| Manage own profile | ✅ | ✅ |
| Create / edit / delete kanban cards | ✅ | ❌ |
| Approve / reject schedules | ✅ | ❌ |
| Review all logbooks | ✅ | ❌ |
| View internship reports + PDF export | ✅ | ❌ |
| Manage users | ✅ | ❌ |
| App settings | ✅ | ❌ |

---

## 📁 Directory Structure

```
app/
  Http/Controllers/         # All controllers (Auth, Admin, Kanban, etc.)
  Models/                   # User, ScheduleSlot, KanbanCard, Setting, ...
  View/Components/          # Blade view components
database/
  migrations/               # All database migrations
  seeders/                  # DatabaseSeeder
resources/
  views/
    layouts/                # App shell (Vuexy sidebar, navbar)
    auth/                   # Login, register pages
    components/             # Reusable Blade partials
    admin/                  # reports.blade.php, report-pdf.blade.php, ...
    kanban/                 # index.blade.php (full Kanban board)
    dashboard.blade.php
  css/                      # app.css + custom.css
  js/                       # app.js + bootstrap.js (Vite entry points)
routes/
  web.php                   # All application routes
  auth.php                  # Breeze auth routes
public/
  assets/vendor/            # Vendored libs (Vuexy, SortableJS, Flatpickr, ...)
  build/                    # Vite production output
```

---

## 🧪 Testing

This project uses [Pest](https://pestphp.com/):

```bash
# Run all tests
php artisan test

# Or directly with Pest
./vendor/bin/pest

# Run with coverage
./vendor/bin/pest --coverage
```

---

## 🧹 Code Style

[Laravel Pint](https://laravel.com/docs/pint) is included for PSR-12 code formatting:

```bash
./vendor/bin/pint
```

---

## 🙏 Credits

- [Laravel](https://laravel.com) — The PHP framework for web artisans
- [Vuexy](https://pixinvent.com/vuexy-bootstrap-html-admin-template/) — Bootstrap 5 Admin Template by Pixinvent
- [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) — PDF generation
- [SortableJS](https://sortablejs.github.io/Sortable/) — Drag-and-drop
- [Flatpickr](https://flatpickr.js.org/) — Lightweight date/time picker
- [Chart.js](https://www.chartjs.org/) — JavaScript charts
- [Pest](https://pestphp.com/) — Elegant PHP testing framework

---

## 📄 License

This project is proprietary software developed for **mieru.or.id**. All rights reserved.
