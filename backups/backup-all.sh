#!/bin/bash

echo "🚀 Starting full backup..."

/opt/library-api/backup-db.sh
/opt/library-api/backup-files.sh

echo "✅ Full backup completed."
