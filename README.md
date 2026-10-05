# PulseCheck

PulseCheck watches a list of URLs and emails you when one of them stops responding. It's a Laravel app, and it doesn't try to be anything else.

You add an endpoint, tell it how often to check, and the scheduler takes it from there. Every ping gets logged with its status code and how long the response took. When a check fails you get an email about it, and so does everyone else on the app.

## What it does

- Register, login, logout. It's plain session auth, so there's no package sitting underneath it.
- Full CRUD on endpoints: URL, HTTP method, expected status code, timeout, how often to check, and an on/off toggle.
- `pulse:check` is the artisan command your scheduler fires every minute. It works out which endpoints are actually due, then queues a job for each one. Anything that isn't due gets skipped entirely.
- `PingEndpointJob` makes the request, times it, and writes a `PingLog` row with the status code, the response time in ms, whether it passed, and the error message if it threw.
- On a failed check it sends `EndpointDownAlert` to every user. That mail call sits inside a try/catch, so a botched SMTP config won't take the check down with it.
- A dashboard showing where each endpoint stands right now.

## The stack

Laravel 9 on PHP 8+, MySQL, and Blade views with Tailwind 3 and Alpine.js, built through Laravel Mix. The queue runs on `sync` out of the box, which is why you don't need a worker to get going.

## Getting it running

```bash
git clone https://github.com/TakbirZaman/PulseCheck.git
cd PulseCheck

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Drop your database credentials into `.env`, then run:

```bash
php artisan migrate --seed
npm run dev
```

The seeder gives you a handful of sample endpoints, so you've got something to look at before you add your own.

After that you'll want a couple of terminals open:

```bash
php artisan serve          # the app, http://localhost:8000
php artisan schedule:work  # drives pulse:check once a minute
php artisan queue:work     # only if you change the queue driver
```

You can skip that last one. With `QUEUE_CONNECTION=sync` your jobs run inline during the scheduler tick, and there's nothing for a worker to pick up. If you switch to `QUEUE_CONNECTION=database` in `.env`, the worker does become required, though you won't need to build the table yourself because there's already a `jobs` migration in here.

## Routes

| Method | URI | Name | Access |
| --- | --- | --- | --- |
| GET | `/` | none | redirects to dashboard |
| GET, POST | `/login` | `login` | guests |
| GET, POST | `/register` | `register` | guests |
| POST | `/logout` | `logout` | logged in |
| GET | `/dashboard` | `dashboard` | logged in |
| GET | `/endpoints` | `endpoints.index` | logged in |
| GET | `/endpoints/create` | `endpoints.create` | logged in |
| POST | `/endpoints` | `endpoints.store` | logged in |
| GET | `/endpoints/{endpoint}` | `endpoints.show` | logged in |
| GET, PUT, PATCH, DELETE | `/endpoints/{endpoint}` | `endpoints.edit`, `update`, `destroy` | logged in |
| PATCH | `/endpoints/{endpoint}/toggle` | `endpoints.toggle` | logged in |

## What happens when a check runs

1. `app/Console/Kernel.php` schedules `pulse:check` on a `->everyMinute()` cadence.
2. The command pulls the active endpoints whose `last_pinged_at` is either null or older than their `interval_minutes`.
3. Each one gets a `PingEndpointJob` dispatched, so you end up with one job per endpoint that's due.
4. The job sends the request with your endpoint's own method and timeout, measures the elapsed time, and writes the `PingLog`.
5. If the status code doesn't match `expected_status_code`, or the request threw, `EndpointDownAlert` goes out by mail.

## Where things live

```
app/Console/Commands/MonitorEndpointsCommand.php   pulse:check
app/Jobs/PingEndpointJob.php                       the single HTTP check
app/Http/Controllers/                              Auth, Dashboard, Endpoint
app/Models/                                        User, Endpoint, PingLog
app/Notifications/EndpointDownAlert.php            the down email
database/migrations/                               users, endpoints, ping_logs, jobs
resources/views/                                   Blade templates
routes/web.php                                     every web route
```

## Tests

```bash
php artisan test
```

Fair warning, it's still the stock example tests that come with a fresh Laravel app. I haven't written coverage for the ping logic yet, so don't lean on these.

## License

MIT.
