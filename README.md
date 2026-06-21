# Mini ERP — Inventory & Production Tracking

A simplified ERP module for tracking inventory and production stages across multiple warehouses, built with **Laravel 12** and **PostgreSQL**. Built as a portfolio piece, inspired by real production-tracking systems I've built for manufacturing clients — using fully dummy/sample data, no real business data included.

## ✨ Features

- **Multi-warehouse inventory tracking** — current stock is calculated in real time from movement history (no stale "stock" column to go out of sync).
- **Production stage tracking** — stock movements tagged by stage (e.g., sewing, dyeing, finishing), reflecting a garment manufacturing workflow, including specialized repair logs for reject items (Bikin Bagus).
- **Low-stock alerts** — dashboard flags items at or below their reorder level using strict SQL aggregation (`SUM`, `GROUP BY`, `HAVING`).
- **Form Request validation** — including custom business-rule validation (e.g., preventing OUT movements that would push stock negative).
- **REST API** — JSON endpoints for items and stock summaries, ready for a frontend SPA or mobile app to consume.

## 🛠️ Tech Stack

`PHP 8.2` · `Laravel 12` · `PostgreSQL` · `Eloquent ORM` · `Blade` · `Bootstrap 5` · `REST API` · `Docker`

## 🚀 Getting Started

This package contains the custom application code (models, controllers, migrations, views, routes). To run it, drop it into a fresh Laravel installation:

```bash
# 1. Create a fresh Laravel project
composer create-project laravel/laravel mini-erp-inventory
cd mini-erp-inventory

# 2. Copy the contents of this package into the project,
#    overwriting routes/web.php and merging the app/ and database/ folders

# 3. Configure your database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mini_erp
DB_USERNAME=postgres
DB_PASSWORD=

# 4. Run migrations with seed data
php artisan migrate --seed

# 5. Start the dev server
php artisan serve