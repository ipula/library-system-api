#!/bin/bash

DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_PATH="/opt/backups/db"

DB_CONTAINER="library-mysql"
DB_NAME="library-api"
DB_USER="root"
DB_PASS="rootsecret"

echo "📦 Backing up database..."

docker exec $DB_CONTAINER \
  mysqldump -u$DB_USER -p$DB_PASS $DB_NAME \
  > $BACKUP_PATH/db_$DATE.sql

gzip $BACKUP_PATH/db_$DATE.sql

echo "✅ DB backup done: db_$DATE.sql.gz"
