#!/usr/bin/env bash

set -e

COMPOSE_FILE="docker-compose.local.yml"
WP_PATH="/var/www/html"

run_wp() {
  docker compose -f "$COMPOSE_FILE" run --rm wpcli wp "$@" --path="$WP_PATH"
}

echo "Seeding WordPress content..."

run_wp post create \
  --post_type=page \
  --post_title="About" \
  --post_name="about" \
  --post_status=publish \
  --post_content="This is the About page for the WP Casino project."

run_wp post create \
  --post_type=post \
  --post_title="First Blog Post" \
  --post_name="first-blog-post" \
  --post_status=publish \
  --post_content="This is a standard WordPress blog post."

run_wp post create \
  --post_type=casino \
  --post_title="Royal Vegas Casino" \
  --post_name="royal-vegas-casino" \
  --post_status=publish \
  --post_content="Royal Vegas Casino review content."

run_wp post create \
  --post_type=casino \
  --post_title="Maple Spins Casino" \
  --post_name="maple-spins-casino" \
  --post_status=publish \
  --post_content="Maple Spins Casino review content."

run_wp rewrite flush

echo "Seed complete."
echo "Open:"
echo "http://localhost:8080/about/"
echo "http://localhost:8080/first-blog-post/"
echo "http://localhost:8080/casinos/"
echo "http://localhost:8080/casinos/royal-vegas-casino/"