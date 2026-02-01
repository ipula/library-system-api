#!/bin/bash

DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_PATH="/opt/backups/files"
SOURCE="/opt/library-api/storage"

# Create backup directory if it doesn't exist
if ! mkdir -p $BACKUP_PATH 2>/dev/null; then
  echo "❌ Error: No permission to create $BACKUP_PATH"
  echo "   Try running with sudo or check directory permissions"
  exit 1
fi

echo "📦 Backing up uploaded files..."

tar -czf $BACKUP_PATH/files_$DATE.tar.gz $SOURCE || {
  echo "❌ Error: Files backup failed"
  exit 1
}

echo "✅ Files backup done: files_$DATE.tar.gz"
