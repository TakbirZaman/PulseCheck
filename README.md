# PulseCheck

A lightweight uptime and endpoint monitoring app built with Laravel. Register endpoints, poll them on a schedule, log response times, and get emailed when something goes down.

## Features

- **User auth** — register, login, logout (session-based).
- **Endpoint CRUD** — name, URL, HTTP method, expected status code, timeout, check interval, active toggle.
- **Scheduled checks** — `pulse:check` runs every minute and dispatches a ping job for any active endpoint whose interval has elapsed.
- **Queued pings** — each check runs as `PingEndpointJob`, so slow targets don't block the scheduler.
- **Ping logging** — status code, response time (ms), success flag, and error message stored per check.
- **Down alerts** — on a failed check, every user gets an `EndpointDownAlert` email with a link to the dashboard.
- **Dashboard** — endpoint overview with latest status and recent history.

## Tech stack

| Layer | Choice |
| --- | --- |
| Framework | Laravel 9 (PHP ^8.0) |
| Database | MySQL (configurable) |
| Queue | `sync` by default, `database` driver supported |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js |
| Build | Laravel Mix 6 / Webpack |

## Requirements

- PHP 8.0+
- Composer
- Node.js 16+ and npm
- MySQL (or any Laravel-supported database)

## Installation

```bash
git clone https://github.com/TakbirZaman/PulseCheck.git
cd PulseCheck

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials, then:

```bash
php artisan migrate --seed
npm run dev
```

`db:seed` loads `EndpointSeeder` with a few sample endpoints.

## Running

Terminal 1 — the app:

```bash
php artisan serve
```

Terminal 2 — the scheduler (drives `pulse:check` every minute):

```bash
php artisan schedule:work
```

Terminal 3 — the queue worker, only needed if you switch to an async driver:

```bash
# in .env: QUEUE_CONNECTION=database
php artisan queue:work
```

With the default `QUEUE_CONNECTION=sync`, jobs run inline during the scheduler tick, so no worker is required.

Open http://localhost:8000.

## Routes

| Method | URI | Name | Notes |
| --- | --- | --- | --- |
| GET | `/` | — | Redirects to dashboard |
| GET/POST | `/login` | `login` | Guest |
| GET/POST | `/register` | `register` | Guest |
| POST | `/logout` | `logout` | Auth |
| GET | `/dashboard` | `dashboard` | Auth |
| GET | `/endpoints` | `endpoints.index` | Auth |
| GET | `/endpoints/create` | `endpoints.create` | Auth |
| POST | `/endpoints` | `endpoints.store` | Auth |
| GET | `/endpoints/{endpoint}` | `endpoints.show` | Auth |
| GET/PUT/PATCH/DELETE | `/endpoints/{endpoint}` | `endpoints.edit/update/destroy` | Auth |
| PATCH | `/endpoints/{endpoint}/toggle` | `endpoints.toggle` | Auth |

## How a check works

1. `app/Console/Kernel.php` schedules `pulse:check` every minute.
2. `MonitorEndpointsCommand` selects active endpoints where `last_pinged_at` is null or older than `interval_minutes`.
3. Each endpoint gets a `PingEndpointJob` dispatched.
4. The job sends the request with the endpoint's method and timeout, measures response time, and writes a `PingLog`.
5. If the status code doesn't match `expected_status_code` (or the request throws), `EndpointDownAlert` is mailed to all users.

## Project structure

```
app/
  Console/Commands/MonitorEndpointsCommand.php   # pulse:check
  Jobs/PingEndpointJob.php                       # single HTTP check
  Http/Controllers/                              # Auth, Dashboard, Endpoint
  Models/                                        # User, Endpoint, PingLog
  Notifications/EndpointDownAlert.php            # down email
database/migrations/                             # users, endpoints, ping_logs, jobs
resources/views/                                 # Blade templates (Tailwind)
routes/web.php                                   # all web routes
```

## Testing

```bash
php artisan test
```

## License

MIT.
