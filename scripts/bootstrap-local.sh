#!/usr/bin/env bash

set -euo pipefail

if [ ! -f ".env" ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi

echo "Initializing submodules..."
git submodule update --init --recursive

echo "Updating submodules to latest remote commits..."
git submodule update --remote --merge

docker compose -f docker-compose.local.yml up -d

echo "Waiting for WordPress to become available..."
sleep 10

docker compose -f docker-compose.local.yml run --rm wpcli wp core install \
  --url="${LOCAL_WORDPRESS_URL:-http://localhost:8080}" \
  --title="${LOCAL_WORDPRESS_TITLE:-Casino Affiliate Local}" \
  --admin_user="${LOCAL_WORDPRESS_ADMIN_USER:-admin}" \
  --admin_password="${LOCAL_WORDPRESS_ADMIN_PASSWORD:-admin}" \
  --admin_email="${LOCAL_WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
  --skip-email || true

docker compose -f docker-compose.local.yml run --rm wpcli wp theme activate "${WP_THEME_SLUG:-wp-casino-theme}"

docker compose -f docker-compose.local.yml run --rm wpcli wp plugin activate "${WP_PLUGIN_SLUG:-wp-casino-plugin}" || true

docker compose -f docker-compose.local.yml run --rm wpcli wp rewrite flush || true

echo ""
echo "Local WordPress is ready:"
echo "  ${LOCAL_WORDPRESS_URL:-http://localhost:8080}"
echo ""
echo "Admin:"
echo "  User: ${LOCAL_WORDPRESS_ADMIN_USER:-admin}"
echo "  Pass: ${LOCAL_WORDPRESS_ADMIN_PASSWORD:-admin}"