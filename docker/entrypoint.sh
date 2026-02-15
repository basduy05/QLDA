#!/usr/bin/env bash
set -e

if [ "${RUN_MIGRATIONS}" = "true" ]; then
  echo "Running migrations..."
  for i in {1..10}; do
    if php artisan migrate --force; then
      break
    fi
    echo "Database not ready, retrying in 5s..."
    sleep 5
  done

  if [ "${RUN_SEED}" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
  fi
fi

exec apache2-foreground
