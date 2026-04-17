#!/bin/bash

# Backup user data sebelum migrate:fresh atau seeding
# Usage: bash scripts/backup-users.sh

BACKUP_DIR="./backups/user-backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/users_backup_$TIMESTAMP.sql"

# Create backup directory if not exists
mkdir -p "$BACKUP_DIR"

# Check if database container is running
if ! docker ps | grep -q newproject_db; then
    echo "❌ Database container 'newproject_db' is not running."
    echo "Start it with: docker-compose up -d db"
    exit 1
fi

# Backup users table only
echo "📦 Backing up users table..."
docker exec newproject_db mysqldump \
    -uroot \
    -pp455w0rd \
    newproject users \
    > "$BACKUP_FILE"

if [ -f "$BACKUP_FILE" ]; then
    echo "✅ Backup saved: $BACKUP_FILE"
    echo "   Size: $(du -h "$BACKUP_FILE" | cut -f1)"
else
    echo "❌ Backup failed!"
    exit 1
fi

# Also backup as JSON for easier viewing
JSON_FILE="$BACKUP_DIR/users_backup_$TIMESTAMP.json"
echo "📄 Exporting as JSON..."
docker exec newproject_db mysql -uroot -pp455w0rd newproject \
    -e "SELECT * FROM users;" \
    --json > "$JSON_FILE" 2>/dev/null

if [ -f "$JSON_FILE" ]; then
    echo "✅ JSON export saved: $JSON_FILE"
fi

echo ""
echo "💾 Backup complete! You can now safely run:"
echo "   docker exec newproject_php php artisan migrate:fresh --seed"
echo ""
echo "To restore from backup later:"
echo "   docker exec -i newproject_db mysql -uroot -pp455w0rd newproject < $BACKUP_FILE"
