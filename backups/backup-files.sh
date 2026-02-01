#!/bin/bash

DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_PATH="/opt/backups/files"
SOURCE="/opt/library-api/storage"

echo "📦 Backing up uploaded files..."

tar -czf $BACKUP_PATH/files_$DATE.tar.gz $SOURCE

echo "✅ Files backup done: files_$DATE.tar.gz"
