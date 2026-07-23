# Laravel Job Postings API

A Laravel REST API for managing job postings: create and search job opportunities, aggregate listings from an external job source, and let candidates subscribe to email alerts for new postings matching a search pattern.

## Features

- Job posting API — create and list job opportunities (`POST /api/jobs`, `GET /api/jobs`)
- Search results combine internally stored jobs with listings pulled live from an external source, normalizing its messier response format into a consistent shape
- Email alert subscriptions (`POST /api/subscriptions`) with an optional search-pattern filter, notifying subscribers when a matching job is posted
- No external database required — SQLite by default
- 36 automated tests (unit + feature); the external source and mail delivery are faked in the suite, so the full run finishes in under a second with no external dependencies

## Requirements

- PHP 7.4
- Composer
- Node.js (only needed if you want to run the external source service locally)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite database (no external DB server needed)
touch database/database.sqlite
php artisan migrate

# Gmail SMTP for real email delivery on job alerts
# Generate an App Password at https://myaccount.google.com/apppasswords
# then set MAIL_USERNAME and MAIL_PASSWORD in .env
```

## External source integration

Search results merge in listings from a companion service, expected at `EXTRA_SOURCE_URL` (default `http://localhost:8080`). Without it running, `GET /jobs` still works — external results are simply omitted, with the client failing gracefully and logging a warning.

## Serving the app

```bash
php artisan serve
```

- `GET /` — a minimal demo UI (search, post a job, subscribe to alerts)
- `POST /api/jobs`, `GET /api/jobs`, `POST /api/subscriptions` — the API itself

## Tests

```bash
php artisan test
```

36 tests, no external dependencies, runs in under a second.

## Note

The core implementation (job posting/search, external source aggregation, alerts, tests) is my own work. Some cosmetic edits after the fact — README wording, renaming a placeholder company name in test fixtures — were done with AI assistance.
