#!/bin/bash

# Load environment variables from .env
if [ -f "/opt/library-api/.env" ]; then
  export $(grep -v '^#' /opt/library-api/.env | xargs)
else
  echo "❌ Error: .env file not found at /opt/library-api/.env"
  exit 1
fi

DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_PATH="/opt/backups/db"

# Create backup directory if it doesn't exist
if ! mkdir -p $BACKUP_PATH 2>/dev/null; then
  echo "❌ Error: No permission to create $BACKUP_PATH"
  echo "   Try running with sudo or check directory permissions"
  exit 1
fi

DB_CONTAINER="library-mysql"

echo "📦 Backing up database..."

docker exec $DB_CONTAINER \
  mysqldump --no-tablespaces -u$DB_USERNAME -p$DB_PASSWORD $DB_DATABASE \
  > $BACKUP_PATH/db_$DATE.sql || {
    echo "❌ Error: Database backup failed"
    echo "   If you see 'Access denied for PROCESS privilege', run:"
    echo "   docker exec library-mysql mysql -uroot -p<root_password> -e \"GRANT PROCESS ON *.* TO '$DB_USERNAME'@'%'; FLUSH PRIVILEGES;\""
    exit 1
  }

gzip $BACKUP_PATH/db_$DATE.sql

echo "✅ DB backup done: db_$DATE.sql.gz"