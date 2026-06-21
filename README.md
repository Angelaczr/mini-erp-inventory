# Mini ERP — Inventory & Production Tracking

A simplified ERP module for tracking inventory and production stages across multiple warehouses, built with **Laravel** and **MySQL**. Built as a portfolio piece, inspired by real production-tracking systems I've built for manufacturing clients — using fully dummy/sample data, no real business data included.

## ✨ Features

- **Multi-warehouse inventory tracking** — current stock is calculated in real time from movement history (no stale "stock" column to go out of sync)
- **Production stage tracking** — stock movements tagged by stage (cutting, sewing, dyeing, finishing), reflecting a garment manufacturing workflow
- **Low-stock alerts** — dashboard flags items at or below their reorder level using SQL aggregation (`SUM`, `GROUP BY`, `HAVING`)
- **Form Request validation** — including custom business-rule validation (e.g. preventing OUT movements that would push stock negative)
- **REST API** — JSON endpoints for items and stock summaries, ready for a frontend SPA or mobile app to consume

## 🛠️ Tech Stack

`PHP` · `Laravel` · `MySQL` · `Eloquent ORM` · `Blade` · `Bootstrap 5` · `REST API`

## 🚀 Getting Started

This package contains the custom application code (models, controllers, migrations, views, routes). To run it, drop it into a fresh Laravel installation:

```bash
# 1. Create a fresh Laravel project
composer create-project laravel/laravel mini-erp-inventory
cd mini-erp-inventory

# 2. Copy the contents of this package into the project,
#    overwriting routes/web.php and merging the app/ and database/ folders

# 3. Configure your database in .env
DB_CONNECTION=mysql
DB_DATABASE=mini_erp
DB_USERNAME=root
DB_PASSWORD=

# 4. Run migrations with seed data
php artisan migrate --seed

# 5. Start the dev server
php artisan serve
```

Visit `http://localhost:8000` for the dashboard, or try the API:

```bash
curl http://localhost:8000/api/items
curl http://localhost:8000/api/stock-summary
curl http://localhost:8000/api/stock-summary/low-stock
```

## 🗄️ Database Design

- `warehouses` — physical storage locations
- `categories` — item classification (Raw Material, WIP, Finished Goods, Packaging)
- `items` — master data per SKU, with a reorder level for low-stock alerts
- `stock_movements` — append-only ledger of IN / OUT / ADJUSTMENT events per item per warehouse, optionally tagged with a production stage

Current stock is **never stored directly** — it's always derived from the movement ledger. This avoids data drift between a "stock count" field and the actual movement history, which is a common source of bugs in inventory systems.

## 📚 Design Notes

- `Item::currentStock()` calculates stock on demand from the movement ledger (optionally scoped to one warehouse)
- `Item::scopeLowStock()` demonstrates a raw SQL aggregation query via Eloquent's query builder (`SUM` + `HAVING`)
- Validation logic for preventing over-withdrawal lives in `StoreStockMovementRequest`, keeping controllers thin
- API controllers are separated from web controllers (`App\Http\Controllers\Api`) to keep concerns isolated

## 📄 License

MIT — feel free to use this as a learning reference.
