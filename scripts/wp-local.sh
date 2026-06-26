#!/usr/bin/env bash

set -euo pipefail

docker compose -f docker-compose.local.yml run --rm wpcli wp "$@"