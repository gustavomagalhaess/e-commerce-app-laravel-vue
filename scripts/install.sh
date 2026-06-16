#!/usr/bin/env bash
# Bootstraps the already-scaffolded Laravel application for a fresh checkout.
#
# The Laravel skeleton, custom DDD code, configs, migrations, seeders, Inertia
# pages, and lockfiles all live under ./e-commerce and are committed to git.
# This script only does the steps that aren't captured by git:
#
#   1. Build the Docker images
#   2. composer install   (populate e-commerce/vendor/)
#   3. Generate APP_KEY if missing
#   4. Create gitignored storage runtime dirs + storage:link
#   5. Bring up MySQL/Redis, wait for MySQL
#   6. migrate:fresh --seed
#   7. npm ci + npm run build
#
# Re-runnable: every step is idempotent and non-destructive to committed files
# (no vendor:publish --force, no overlays, no re-scaffolding).
set -euo pipefail

cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
    cp .env.example .env
fi

# Source .env, but skip lines that try to overwrite bash's read-only specials
# (UID/GID/EUID/PPID/SHLVL/etc.) so older .env files don't break the script.
# shellcheck source=/dev/null
set -a
. <(grep -Ev '^(UID|GID|EUID|PPID|SHLVL|BASHPID|RANDOM|SECONDS|LINENO|HISTCMD|FUNCNAME|GROUPS|DIRSTACK|PIPESTATUS|BASH_[A-Z_]+|COMP_[A-Z_]+)=' .env || true)
set +a

DC="docker compose"

echo "==> Ensuring images are built"
$DC build

# Compose service that has php + composer + node available.
# --no-deps for steps that don't touch the DB; APP_RUN_DEPS brings up deps.
APP_RUN="$DC run --rm --no-deps app"
APP_RUN_DEPS="$DC run --rm app"

if [ ! -f e-commerce/artisan ]; then
    echo "ERROR: e-commerce/artisan is missing — this script expects the Laravel"
    echo "       app to already be scaffolded under ./e-commerce (it is committed"
    echo "       to git). Did the clone finish? Are you in the project root?"
    exit 1
fi

echo "==> Installing PHP dependencies (composer install)"
$APP_RUN composer install --no-interaction --no-progress --prefer-dist

echo "==> Creating e-commerce/.env from .env.example (if missing)"
$APP_RUN sh -lc 'test -f .env || cp .env.example .env'

echo "==> Generating APP_KEY (if missing)"
$APP_RUN sh -lc 'grep -q "^APP_KEY=base64:" .env || php artisan key:generate'

echo "==> Creating gitignored storage runtime dirs"
# These four are .gitignored, so a fresh clone has no copies of them.
# Laravel's session/cache/view/log drivers expect the directories to exist.
$APP_RUN sh -lc '
    mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache || true
    chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true
'

echo "==> Linking storage (public/storage -> ../storage/app/public)"
$APP_RUN php artisan storage:link || true

echo "==> Bringing up DB + Redis"
$DC up -d db redis

echo "==> Waiting for MySQL to be healthy..."
for i in $(seq 1 30); do
    if $DC exec -T db mysqladmin ping -h 127.0.0.1 -uroot -p"${DB_ROOT_PASSWORD:-root}" --silent >/dev/null 2>&1; then
        echo "    MySQL is ready."
        break
    fi
    sleep 2
done

echo "==> Running migrations + seeders (migrate:fresh --seed)"
$APP_RUN_DEPS php artisan migrate:fresh --seed --force

echo "==> Installing & building front-end assets"
# npm ci is deterministic (reads package-lock.json, which is committed).
$APP_RUN sh -lc 'npm ci && npm run build'

echo
echo "============================================================"
echo "  E-commerce app is installed."
echo "  Run \`make up\` and visit http://localhost:${APP_PORT:-8080}"
echo "============================================================"
