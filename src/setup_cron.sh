

#!/bin/bash

# Set path to current directory
CRON_FILE="cronjob.tmp"
CRON_LOG="cron_log.txt"
CRON_PATH=$(pwd)

# Remove old cron file
rm -f "$CRON_FILE"

# Create cron job entry
echo "0 0 * * * /usr/bin/php $CRON_PATH/cron.php >> $CRON_PATH/$CRON_LOG 2>&1" >> "$CRON_FILE"

# Register cron job
crontab "$CRON_FILE"
rm "$CRON_FILE"

echo "CRON job scheduled to run every 24 hours at midnight."
