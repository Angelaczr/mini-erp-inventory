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

## 🌐 Live Demo Deployment (Render + Supabase — both free forever)

This repo includes a `Dockerfile` + `render.yaml` for deploying to [Render](https://render.com)'s permanent free web service tier, connected to a [Supabase](https://supabase.com) free PostgreSQL database.

**Why this combo:**
- Render web service: free forever, no credit card, sleeps after 15 min of inactivity (30–60s cold start on next visit) — fine for a portfolio demo
- Supabase Postgres: free forever, no credit card, **pauses after 7 days of zero database activity** (data is preserved, not deleted) — this repo includes a GitHub Action that pings the live demo daily to prevent that entirely

### Step 1 — Create a Supabase Project
1. Go to [supabase.com](https://supabase.com) → **Start your project** → sign in with GitHub
2. **New Project** → pick a name, generate a database password (save it!), choose a region close to you
3. Once created, go to **Project Settings → Database → Connection string** and copy:
   - **Host** (e.g. `aws-0-ap-southeast-1.pooler.supabase.com`)
   - **Port** → use **6543** (the connection pooler, recommended for apps like this)
   - **Database** → `postgres`
   - **User** → looks like `postgres.xxxxxxxxxxxx`
   - **Password** → what you set in step 2

### Step 2 — Push This Repo to GitHub
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/Angelaczr/mini-erp-inventory.git
git branch -M main
git push -u origin main
```

### Step 3 — Deploy to Render
1. [render.com](https://render.com) → **Login with GitHub** (no credit card required)
2. **New** → **Web Service** → select this repo → Render auto-detects the `Dockerfile`
3. Plan: **Free**
4. Under **Environment Variables**, add:
   ```
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=pgsql
   DB_HOST=<your Supabase pooler host>
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=<your Supabase user, e.g. postgres.xxxxxxxxxxxx>
   DB_PASSWORD=<your Supabase database password>
   ```
5. **Create Web Service** — Render builds and deploys. On first boot, the entrypoint script runs migrations and seeds demo data automatically.

### Step 4 — Prevent Supabase Auto-Pause (already included)
This repo ships with `.github/workflows/keep-alive.yml`, which pings your live demo once a day — keeping Supabase active without any manual work.

**Before it works, edit one line:**
1. Open `.github/workflows/keep-alive.yml`
2. Replace `YOUR-APP-NAME` with your actual Render service URL
3. Commit and push — GitHub Actions will handle the rest automatically

## 📄 License

MIT — feel free to use this as a learning reference.
