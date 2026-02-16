#!/bin/bash
set -e

# Run migrations
echo "Running database migrations..."
php spark migrate

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
