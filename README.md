# Khidma.ma Backend

<p align="center">
  <strong>Laravel API for the Khidma.ma services marketplace.</strong>
</p>

## Overview

This backend provides the core business logic and API for Khidma.ma. It handles:

- Authentication and role-based access
- Services, categories, requests, and reviews
- Professional dashboards and analytics
- Admin moderation and platform insights
- Chat conversations and messaging

## Stack

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- MySQL
- PHPUnit
- Laravel Pint

## Architecture

The application follows a layered structure:

- `app/Http/Controllers`: HTTP endpoints
- `app/Models`: Eloquent models and relationships
- `app/Services`: business logic grouped by domain
- `routes/api.php`: API route definitions
- `database/`: migrations, factories, and seeders

Service domains currently include:

- `Admin`
- `Auth`
- `Category`
- `Chat`
- `Professional`
- `Request`
- `Review`
- `Service`

## Authentication And Roles

Authentication is managed with Laravel Sanctum.

Main roles used by the API:

- `admin`
- `professional`
- `client`

Protected routes are grouped with middleware such as:

- `auth:sanctum`
- `active`
- `role:admin`
- `role:professional`
- `role:client,professional`

## Important API Areas

- Auth: register, login, logout
- Categories: public listing and admin management
- Services: browse, view, create, update, soft delete, restore
- Requests: client booking flow and professional request handling
- Reviews: service feedback and ratings
- Chat: direct conversations and messages
- Admin: user moderation, service moderation, analytics

## Environment Setup

Copy the example environment and update the database credentials:

```bash
copy .env.example .env
```

Important variables from `.env.example`:

```env
APP_NAME=Khidma.ma
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khidma
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
```

## Local Setup

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Default local URL:

```text
http://localhost:8000
```

## Composer Scripts

- `composer run setup`: install dependencies, prepare `.env`, generate key, migrate, install frontend dependencies, and build assets
- `composer run dev`: run local development services together
- `composer run test`: clear config and execute tests

## Testing And Quality

```bash
php artisan test
```

Optional formatting:

```bash
./vendor/bin/pint
```

## Notes

- API routes are defined in [`routes/api.php`](./routes/api.php)
- Business logic is intentionally moved into `app/Services`
- The project expects the frontend app in `../Frontend` to consume this API

## Related Docs

- Root project guide: [`../README.md`](../README.md)
- Frontend app guide: [`../Frontend/README.md`](../Frontend/README.md)
