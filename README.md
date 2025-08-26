# PHP Laravel Todo API with BigQuery (Starter)

This is a starter project for a Todo list API using Laravel and (optionally) Google BigQuery.

## Features

- RESTful API for Todo items
- Laravel 11 structure
- Ready for BigQuery or PostgreSQL backend
- Unique title validation
- Support for PATCH updates

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/todos | List todos |
| POST   | /api/todos | Create todo |
| GET    | /api/todos/{id} | Show one |
| PUT    | /api/todos/{id} | Full update |
| PATCH  | /api/todos/{id} | Partial update |
| DELETE | /api/todos/{id} | Delete todo |