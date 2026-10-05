# PulseCheck

Watches your URLs and emails you when one goes down. Small Laravel app, does one thing well.

What it does:
- Add endpoints with check frequency
- Logs status code + response time
- Sends email on failure

Stack: Laravel, MySQL, Scheduler

Run it:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
php artisan schedule:work
```
