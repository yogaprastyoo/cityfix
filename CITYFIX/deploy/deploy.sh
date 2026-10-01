#!/usr/bin/env bash
# CityFix - Deployment Procedure (Tahap 4 §20)
# Jalankan di server production dari root project: bash CITYFIX/deploy/deploy.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/cityfix}"
cd "$APP_DIR"

echo "==> 1. Backup database + storage"
bash CITYFIX/deploy/backup.sh

echo "==> 2. Maintenance mode"
php artisan down --retry=60 || true
trap 'php artisan up' EXIT

echo "==> 3. Pull source code baru"
git pull --ff-only

echo "==> 4. Composer install (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 5. Build frontend"
npm ci
npm run build

echo "==> 6. Migration"
php artisan migrate --force

echo "==> 7. Storage link"
[ -L public/storage ] || php artisan storage:link

echo "==> 8. Optimize"
php artisan optimize

echo "==> 9. Restart queue worker (jika dipakai)"
php artisan queue:restart || true

echo "==> 10. Nonaktifkan maintenance mode"
php artisan up
trap - EXIT

echo "==> 11. Smoke test /up"
APP_URL=$(php artisan tinker --execute 'echo config("app.url");')
curl -fsS -o /dev/null -w "GET /up -> %{http_code}\n" "$APP_URL/up"

echo "==> 12. Lanjutkan smoke test manual (CITYFIX/Tahap4_Operasional.md §Smoke Test) dan pantau storage/logs"
