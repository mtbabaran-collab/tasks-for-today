# Tasks for Today Management System (Laravel)

IT0049 Web System Technologies · Technical Summative Assessment 4
**Full CRUD, Breeze authentication, and an email notification when a task is completed.**

- **Live site:** https://tasks-for-today-u5ov.onrender.com (free hosting: the first visit after a quiet period can take about a minute to wake up)
- **GitHub:** https://github.com/mtbabaran-collab/tasks-for-today
- **Demo login:** `demo@tasksfortoday.test` / `password` (created by the seeder)

---

## What it does

| Requirement | Where it is |
|---|---|
| Create Task form, validated | `TaskController@create/store`, `app/Http/Requests/StoreTaskRequest.php`, `resources/views/tasks/create.blade.php` |
| Edit / update page, validated | `TaskController@edit/update`, `app/Http/Requests/UpdateTaskRequest.php`, `resources/views/tasks/edit.blade.php` |
| Soft delete with an `is_archived` column | `TaskController@destroy` sets `is_archived = true`; `Task::active()` hides archived tasks; Archived page + Restore |
| Breeze authentication | `laravel/breeze` (Blade). Login, register, password reset, account settings at `/account` |
| Public pages | Welcome `/`, Task List `/tasks`, task details `/tasks/{id}`, Profile `/profile`, About `/about` |
| Login required | Create, edit, update, delete (archive), archived list and restore are inside `Route::middleware('auth')` in `routes/web.php` |
| Mailable + Blade email view | `app/Mail/TaskCompleted.php`, `resources/views/emails/task-completed.blade.php` |
| Email when status changes to done | `TaskController@update`: sends only when the **old** status was not `done` and the new one is `done` |
| Not resent for a task already done | Saving a task that is already `done` does not send again (tested) |

### Task fields
`title` (required, 3–120 characters), `description` (optional, up to 1000), `status` (pending / in progress / done), `priority` (low / medium / high), `due_date` (optional; cannot be in the past when creating), `is_archived`, `completed_at`, `user_id` (who created it).

### Validation rules
- Empty or spaces-only titles are rejected ("Please give the task a title.").
- Status and priority must be one of the allowed values.
- The due date must be a real date and, for a new task, today or later.
- Errors show under each field and the form keeps what was typed.

---

## Run it on your computer

Requirements: **PHP 8.3+** (8.4 recommended), **Composer**, **Node.js 20.19+** (22 recommended).

```bash
git clone https://github.com/mtbabaran-collab/tasks-for-today.git
cd tasks-for-today
composer install
npm install
npm run build
cp .env.example .env          # on Windows: copy .env.example .env
php artisan key:generate
touch database/database.sqlite   # on Windows: type nul > database\database.sqlite
php artisan migrate --seed
php artisan serve
```

Open http://127.0.0.1:8000 and log in with `demo@tasksfortoday.test` / `password`, or register a new account.

### Using MySQL (XAMPP) instead of SQLite
Create a database named `tasks_for_today` in phpMyAdmin, then in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasks_for_today
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`. A ready-made MySQL export is also in `database/tasks_for_today.sql`.

---

## Mail configuration

The "task completed" email goes to the address in `TASK_NOTIFY_EMAIL`:

```env
TASK_NOTIFY_EMAIL="tasks-admin@example.com"
MAIL_FROM_ADDRESS="no-reply@tasksfortoday.test"
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 1: log driver (default, no inbox needed)

```env
MAIL_MAILER=log
```

The whole email (headers + HTML) is written to `storage/logs/laravel.log`. On the hosted site the log goes to the server logs (`LOG_CHANNEL=stderr`).

### Option 2: Mailtrap (safe test inbox)

Create a free inbox at mailtrap.io and copy its SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

Run `php artisan config:clear` after changing `.env`.

### How to check the email
1. Log in, open a task that is **Pending**, click **Edit task**.
2. Change Status to **Done** and save. The message says a completion email was sent.
3. Look in `storage/logs/laravel.log` (or the Mailtrap inbox): you will see `Subject: Task completed: <task title>`.
4. Edit the same task again and save without changing the status. **No second email** is sent.

---

## Tests

```bash
php artisan test
```

41 tests (Breeze's auth tests plus this project's tests), including:
- `tests/Feature/TaskManagementTest.php`: public pages, guests blocked from create/edit/delete, validation, update, archive and restore, odd search values, and the seeder.
- `tests/Feature/TaskCompletedEmailTest.php`: email sent once when a task becomes done, not resent while it stays done, not sent for other status changes.

---

## Project structure (MVC)

```
app/Models/Task.php                     Model: fields, casts, active()/archived() scopes
app/Http/Controllers/TaskController.php Controller: CRUD, archive/restore, sends the email
app/Http/Requests/*TaskRequest.php      Validation rules
app/Mail/TaskCompleted.php              Mailable
resources/views/tasks/*                 Task pages (list, details, create, edit, archived)
resources/views/emails/task-completed.blade.php   Email view
resources/views/pages/*                 Profile and About pages
database/migrations/*_create_tasks_table.php      tasks table (is_archived soft delete)
database/seeders/                       Demo user + sample tasks
routes/web.php                          Public routes and the auth-protected group
```

---

## Hosting

The repository includes a `Dockerfile` and `render.yaml`, so it can be deployed on Render as a free web service:
Render > New > Blueprint > pick this repository > Apply. On first start the container creates the SQLite
database, runs the migrations and seeds the demo data (`docker/start.sh`). Emails use the log driver and
appear in Render > Logs.

Note: on Render's free plan the service sleeps after about 15 minutes without visitors (the first visit can take
about a minute), and each time it wakes up or is redeployed the SQLite database starts again from the demo data.
Tasks you add on the live site are kept only while the service is awake.
