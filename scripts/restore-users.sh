#!/bin/bash

# Restore users from backup
# Usage: bash scripts/restore-users.sh backups/user-backups/users_backup_20260417_202304.sql

if [ -z "$1" ]; then
    echo "❌ Usage: bash scripts/restore-users.sh <backup-file>"
    echo ""
    echo "Available backups:"
    ls -lh backups/user-backups/users_backup_*.sql 2>/dev/null || echo "No backups found"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "⚠️  WARNING: This will restore the users table from backup!"
echo "Database: newproject"
echo "File: $BACKUP_FILE"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 1
fi

echo "🔄 Restoring users table..."
docker exec -i newproject_db mysql -uroot -pp455w0rd newproject < "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Users restored successfully!"
    docker exec newproject_php php artisan cache:clear
    echo "✅ Cache cleared."
else
    echo "❌ Restore failed!"
    exit 1
fi
