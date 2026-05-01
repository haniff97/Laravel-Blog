# Laravel Blog Platform

A self-hosted, production-grade blog platform built with Laravel 13, deployed on an Orange Pi 4 Pro homelab with GCP as a reverse proxy.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Blade, Tailwind CSS, Vite |
| Database | MariaDB |
| Cache/Session | Redis |
| Markdown | league/commonmark |
| Auth | Laravel Breeze |
| Infrastructure | Docker, Nginx, Orange Pi 4 Pro (ARM64) |
| Networking | GCP e2-micro (reverse proxy), Tailscale VPN |

## Architecture

Internet → GCP Nginx (reverse proxy) → Tailscale → Orange Pi :8085 → Docker (Nginx → PHP-FPM → Laravel)

## Features

- 📝 Create, edit, delete blog posts
- 📄 Markdown support (headings, bold, code blocks, lists)
- 🔐 Admin authentication (Laravel Breeze)
- 🌓 Dark mode support
- 📱 Responsive design
- 📦 Draft / Published post status
- 🗑️ Soft deletes (posts go to trash, not permanently deleted)
- ⚡ Redis caching on public routes
- 🔒 Authorization (users can only edit their own posts)
- 🗄️ Database indexes on slug, status, published_at, user_id
- 🔄 DB transactions on write operations
- 🚫 N+1 query prevention via eager loading

## Core Principles Applied

| Principle | Implementation |
|---|---|
| DRY | Shared Blade layout (`layouts/app.blade.php`) |
| SOLID | Single responsibility — PostController handles HTTP only |
| Security | CSRF, authorization checks, input validation |
| REST | Proper HTTP verbs (GET, POST, PUT, DELETE) |
| N+1 Prevention | Eager loading with `with('user')` |
| DB Indexing | Indexes on frequently queried columns |
| Caching | Redis cache on public post listing and single post |
| Transactions | DB::transaction() on store and update |
| Soft Deletes | SoftDeletes trait on Post model |

## Local Development Setup

### Prerequisites

- Docker & Docker Compose
- Git

### Steps

```bash
# 1. Clone the repo
git clone https://github.com/haniff97/Laravel-Blog.git
cd Laravel-Blog

# 2. Create env file and fill in values
cp src/.env.example src/.env

# 3. Create NVMe storage directories (or regular dirs for local dev)
mkdir -p storage/app/public
mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/logs

# 4. Build and start containers
docker compose up -d --build

# 5. Install dependencies
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app npm run build

# 6. Generate key and migrate
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
```

Visit `http://localhost:8085`

## Project Structure

.
├── docker-compose.yml
├── docker/
│   ├── php/Dockerfile
│   └── nginx/default.conf
└── src/                              # Laravel application
├── app/
│   ├── Http/Controllers/
│   │   └── PostController.php    # Blog + admin CRUD
│   └── Models/
│       └── Post.php              # SoftDeletes, markdown renderer, scopes
├── database/migrations/
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php         # Shared layout (DRY)
│   ├── blog/                     # Public blog views
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── admin/posts/              # Admin views
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
└── routes/web.php

## Deployment

Deployed on a self-hosted Orange Pi 4 Pro homelab:

- All persistent data stored on NVMe at `/mnt/nvme/`
- Exposed via GCP e2-micro reverse proxy over Tailscale VPN
- SSL via Let's Encrypt (Certbot)

## Author

**Haniff** — [GitHub](https://github.com/haniff97)


