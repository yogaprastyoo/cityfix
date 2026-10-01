#!/usr/bin/env bash
# CityFix - Backup database + foto (Tahap 4 §18)
# Pasang di cron harian, contoh (jam 01:30):
#   30 1 * * * cd /var/www/cityfix && bash CITYFIX/deploy/backup.sh >> /var/log/cityfix-backup.log 2>&1
# Kredensial DB dibaca dari ~/.my.cnf milik user cron (jangan tulis password di script):
#   [mysqldump]
#   user=cityfix_app
#   password=PASSWORD_KUAT
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/cityfix}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/cityfix}"
DB_NAME="${DB_NAME:-cityfix_production}"
KEEP_DAYS="${KEEP_DAYS:-7}"
STAMP=$(date +%Y%m%d_%H%M)

mkdir -p "$BACKUP_DIR"

mysqldump --single-transaction --routines "$DB_NAME" | gzip > "$BACKUP_DIR/cityfix_${STAMP}.sql.gz"
tar -czf "$BACKUP_DIR/cityfix_storage_${STAMP}.tar.gz" -C "$APP_DIR/storage/app" public

# Retensi harian lokal. Salinan mingguan/bulanan + off-site (NAS/object storage) diatur terpisah:
# backup di server yang sama bukan backup yang kuat.
find "$BACKUP_DIR" -name 'cityfix_*' -mtime +"$KEEP_DAYS" -delete

echo "Backup selesai: $BACKUP_DIR (${STAMP})"
