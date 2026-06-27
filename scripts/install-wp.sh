#!/usr/bin/env bash

set -e

COMPOSE_FILE="docker-compose.local.yml"
WP_PATH="/var/www/html"

if [ -f .env ]; then
  set -a
  source .env
  set +a
fi

run_wp() {
  docker compose -f "$COMPOSE_FILE" run --rm wpcli wp "$@" --path="$WP_PATH"
}

docker compose -f "$COMPOSE_FILE" up -d --build

if ! run_wp config path > /dev/null 2>&1; then
  run_wp config create \
    --dbname="${MYSQL_DATABASE:-wordpress}" \
    --dbuser="${MYSQL_USER:-wordpress}" \
    --dbpass="${MYSQL_PASSWORD:-wordpress}" \
    --dbhost="db:3306" \
    --skip-check
fi

if ! run_wp core is-installed > /dev/null 2>&1; then
  run_wp core install \
    --url="${LOCAL_WORDPRESS_URL:-http://localhost:8080}" \
    --title="${LOCAL_WORDPRESS_TITLE:-Casino Affiliate Local}" \
    --admin_user="${LOCAL_WORDPRESS_ADMIN_USER:-admin}" \
    --admin_password="${LOCAL_WORDPRESS_ADMIN_PASSWORD:-admin}" \
    --admin_email="${LOCAL_WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
fi

run_wp option update home "${LOCAL_WORDPRESS_URL:-http://localhost:8080}"
run_wp option update siteurl "${LOCAL_WORDPRESS_URL:-http://localhost:8080}"

run_wp theme activate wp-casino-theme
run_wp plugin activate wp-casino-plugin

run_wp rewrite structure '/%postname%/'
run_wp rewrite flush

./scripts/seed-wp.sh

echo "WordPress ready."
echo "Site: ${LOCAL_WORDPRESS_URL:-http://localhost:8080}"
echo "Admin: ${LOCAL_WORDPRESS_URL:-http://localhost:8080}/wp-admin/"