#!/usr/bin/env bash

set -euo pipefail

docker compose -f docker-compose.remote.yml run --rm wpcli wp "$@"