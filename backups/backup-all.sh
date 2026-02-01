#!/bin/bash

LOG_FILE="/opt/backups/backup-$(date +%Y-%m-%d_%H-%M-%S).log"
mkdir -p /opt/backups

echo "🚀 Starting full backup..." | tee -a $LOG_FILE

echo "📝 Checking app container status..." | tee -a $LOG_FILE
if ! docker exec library-app php -v > /dev/null 2>&1; then
  echo "❌ Error: App container is not running or not responding" | tee -a $LOG_FILE
  echo "   Try: docker compose -f docker/docker-compose.prod.yml logs app" | tee -a $LOG_FILE
  exit 1
fi

echo "📝 Starting database backup..." | tee -a $LOG_FILE
if bash /opt/library-api/backups/backup-db.sh 2>&1 | tee -a $LOG_FILE; then
  echo "✅ Database backup succeeded" | tee -a $LOG_FILE
else
  echo "❌ Database backup failed" | tee -a $LOG_FILE
  exit 1
fi

echo "📝 Starting files backup..." | tee -a $LOG_FILE
if bash /opt/library-api/backups/backup-files.sh 2>&1 | tee -a $LOG_FILE; then
  echo "✅ Files backup succeeded" | tee -a $LOG_FILE
else
  echo "❌ Files backup failed" | tee -a $LOG_FILE
  exit 1
fi

echo "✅ Full backup completed." | tee -a $LOG_FILE
echo "📋 Logs saved to: $LOG_FILE" | tee -a $LOG_FILE
