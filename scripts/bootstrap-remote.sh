#!/usr/bin/env bash

set -euo pipefail

if [ ! -f ".env" ]; then
  echo "Missing .env file."
  echo "Create it from .env.example and set secure remote values first."
  exit 1
fi

git pull
git submodule update --init --recursive

docker compose -f docker-compose.remote.yml up -d

echo "Waiting for WordPress to become available..."
sleep 10

docker compose -f docker-compose.remote.yml run --rm wpcli wp core install \
  --url="${REMOTE_WORDPRESS_URL}" \
  --title="${REMOTE_WORDPRESS_TITLE:-Casino Affiliate}" \
  --admin_user="${REMOTE_WORDPRESS_ADMIN_USER}" \
  --admin_password="${REMOTE_WORDPRESS_ADMIN_PASSWORD}" \
  --admin_email="${REMOTE_WORDPRESS_ADMIN_EMAIL}" \
  --skip-email || true

docker compose -f docker-compose.remote.yml run --rm wpcli wp theme activate "${WP_THEME_SLUG:-wp-casino-theme}"

docker compose -f docker-compose.remote.yml run --rm wpcli wp plugin activate "${WP_PLUGIN_SLUG:-wp-casino-plugin}" || true

docker compose -f docker-compose.remote.yml run --rm wpcli wp rewrite flush

echo ""
echo "Remote WordPress deployed:"
echo "  ${REMOTE_WORDPRESS_URL}"