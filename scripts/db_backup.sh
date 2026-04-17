#!/bin/sh
# Backup MySQL database using mysqldump
set -e

DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-p455w0rd}
DB_NAME=${DB_NAME:-newproject}

BACKUP_DIR=/backups
TIMESTAMP=$(date +"%F_%H%M%S")
FILE="$BACKUP_DIR/${DB_NAME}_$TIMESTAMP.sql"

mkdir -p "$BACKUP_DIR"
echo "[backup] Starting backup $FILE"
mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$FILE"
if [ $? -eq 0 ]; then
  echo "[backup] Dump saved to $FILE"
  # keep latest symlink
  ln -sf "$FILE" "$BACKUP_DIR/${DB_NAME}_latest.sql"
else
  echo "[backup] Dump failed"
  exit 1
fi
