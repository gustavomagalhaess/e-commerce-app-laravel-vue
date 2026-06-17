# E-Commerce

A customer-to-customer e-commerce platform where any registered user can both sell and buy products. Built with Laravel 13 (API) and Vue 3 (SPA).

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 · PHP 8.3 · Sanctum (bearer token auth) |
| Frontend | Vue 3 · Vite 8 · Pinia · Vue Router 4 · Tailwind CSS 4 |
| Database | MySQL 8.4 |
| Cache / Queue | Redis 7 · Laravel Horizon |
| Infrastructure | Docker · Nginx · PHP-FPM |

---

## Architecture

```
e-commerce/
  app/
    Domains/
      Auth/       # Login, register, profile, password
      Cart/       # Cart CRUD
      Catalog/    # Products and categories
      Orders/     # Checkout, order history, sales
      Admin/      # Admin-only category, product, and user management
    Providers/
      AppServiceProvider.php   # Interface → concrete repository bindings
  resources/
    js/
      api/        # Axios wrappers per domain
      stores/     # Pinia stores (auth, cart)
      router/     # Vue Router with auth/guest/admin guards
      views/      # Page components
      components/ # Shared UI components
```

**Backend pattern:** thin controllers → services (business logic) → repositories (data access).
All repositories are injected via interfaces (`*RepositoryInterface`) bound in `AppServiceProvider`.

**Frontend pattern:** Axios instance with bearer token interceptor → Pinia stores → Vue views.

---

## Roles

| Role | Capabilities |
|---|---|
| Guest | Browse products and categories |
| Customer | Everything a guest can do, plus: cart, checkout, order history, sales history, manage own products, edit profile |
| Admin | Everything a customer can do, plus: manage all products, categories, and users |

---

## Getting Started

### Requirements

- Docker and Docker Compose

### Install

```bash
make install
```

This builds all Docker images, installs PHP and Node dependencies, runs migrations with seeders, and compiles front-end assets.

### Run

```bash
make up        # start the stack
make dev       # start with Vite dev server (hot reload on port 5173)
```

Visit [http://localhost:8080](http://localhost:8080).

**Default test account:** `test@example.com` / `password`

---

## Common Commands

```bash
make test           # run PHPUnit test suite
make pint           # run Laravel Pint code style check
make pint c="--fix" # auto-fix code style violations
make fresh          # drop all tables, re-migrate, and re-seed
make shell          # open a shell inside the PHP container
make artisan c="…"  # run any artisan command
make logs           # tail logs from all containers
make horizon        # tail Horizon worker logs
```

---

## API

Base URL: `/api/v1` — all endpoints return JSON, authentication via `Authorization: Bearer <token>`.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/register` | — | Register a new customer |
| POST | `/login` | — | Login, returns token |
| POST | `/logout` | ✓ | Revoke current token |
| GET | `/user` | ✓ | Authenticated user profile |
| PUT | `/user/profile` | ✓ | Update name / email |
| PUT | `/user/password` | ✓ | Change password |
| GET | `/products` | — | Paginated product list (`?search=&category[]=&page=`) |
| GET | `/products/{id}` | — | Single product detail |
| POST | `/products` | ✓ | Create product (multipart) |
| POST | `/products/{id}` | ✓ | Update product (`_method=PUT`, multipart) |
| DELETE | `/products/{id}` | ✓ | Delete own product |
| GET | `/my-products` | ✓ | Seller's own product list |
| GET | `/categories` | — | All categories |
| GET | `/cart` | ✓ | View cart |
| POST | `/cart` | ✓ | Add item |
| PUT | `/cart/{id}` | ✓ | Update item quantity |
| DELETE | `/cart/{id}` | ✓ | Remove item |
| DELETE | `/cart` | ✓ | Clear cart |
| POST | `/orders` | ✓ | Place order (async via Horizon) |
| GET | `/orders` | ✓ | Buyer's order history |
| GET | `/orders/{id}` | ✓ | Order detail |
| GET | `/my-sales` | ✓ | Seller's sales history |
| GET | `/admin/categories` | admin | List categories |
| POST | `/admin/categories` | admin | Create category |
| PUT | `/admin/categories/{id}` | admin | Update category |
| DELETE | `/admin/categories/{id}` | admin | Delete category |
| GET | `/admin/products` | admin | All products |
| DELETE | `/admin/products/{id}` | admin | Delete any product |
| GET | `/admin/users` | admin | All users |
| PUT | `/admin/users/{id}/role` | admin | Change user role |

---

## CI

Two GitHub Actions workflows run on every pull request:

- **Pint** — checks code style (`vendor/bin/pint --test`)
- **Tests** — runs the full PHPUnit suite against SQLite in-memory
