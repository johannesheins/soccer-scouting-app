#!/bin/sh
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [ -f "$SCRIPT_DIR/.env" ]; then
    # shellcheck source=/dev/null
    . "$SCRIPT_DIR/.env"
fi

if [ -z "$IMAGE_REPO" ] || [ -z "$COMPOSE_FILE" ]; then
    echo "IMAGE_REPO and COMPOSE_FILE must be set. Copy scripts/.env.example to scripts/.env and fill it in." >&2
    exit 1
fi

docker compose -f "$COMPOSE_FILE" build --no-cache

docker tag soccer-scouting-app-php "$IMAGE_REPO/php:latest"
docker tag soccer-scouting-app-nginx "$IMAGE_REPO/nginx:latest"

docker push "$IMAGE_REPO/php:latest"
docker push "$IMAGE_REPO/nginx:latest"