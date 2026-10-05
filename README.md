# DeskFlow

DeskFlow is an IT service desk and ticket escalation application built with Laravel 10. It provides role-based ticket access, searchable ticket lists, Ajax-powered comments and private attachments, scheduled SLA escalation, and an authenticated metrics API.

## Features

- Employee, agent, and manager roles with ticket ownership checks.
- Ticket lifecycle and priority management.
- Searchable and filterable ticket list.
- Live comments with validated JPG, PNG, and PDF attachments.
- Private attachment storage and authenticated downloads.
- Artisan SLA escalation command (`sla:check`).
- Sanctum-protected metrics endpoint at `/api/v1/helpdesk/metrics`.

## Requirements

- PHP 8.2 or later with SQLite, OpenSSL, Fileinfo, Mbstring, and Zip extensions enabled.
- Composer.
- Node.js and npm.

## Local setup

```bash
git clone https://github.com/Error1904/deskflow.git
cd deskflow
composer install
copy .env.example .env
```

On macOS/Linux, use `cp .env.example .env` instead of `copy`.

Create the SQLite database file, generate the application key, and load the schema and demo data.
On Windows PowerShell:

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

On macOS/Linux:

```bash
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open <http://127.0.0.1:8000/login>.

### Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Employee | `employee@deskflow.test` | `password` |
| Agent | `agent@deskflow.test` | `password` |
| Manager | `manager@deskflow.test` | `password` |

These are development-only demo credentials. Do not use them in production.

## SLA escalation

```bash
php artisan sla:check
php artisan sla:check --hours=12
php artisan sla:check --dry-run
```

To run the check hourly, schedule `sla:check` in Laravel's scheduler and run `php artisan schedule:run` from your scheduler.

## Metrics API

The `GET /api/v1/helpdesk/metrics` endpoint requires a Sanctum bearer token and returns ticket counts and the resolution rate. It is rate-limited and cached for five minutes.

## Security

- Copy `.env.example` to `.env` and generate a unique application key. Never commit `.env` or real credentials.
- Demo passwords are for local development only.
- User-uploaded attachments are stored outside the public web root and are downloaded through an authenticated route.
- Configure production database credentials, HTTPS, and `APP_DEBUG=false` before deployment.
