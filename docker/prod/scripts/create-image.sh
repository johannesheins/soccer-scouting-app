#!/bin/sh
set -e

# Usage: create-image.sh <version>
# Images are pushed as both :<version> and :latest, so an older release can
# be restored later by deploying :<version> instead of :latest.

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [ -f "$SCRIPT_DIR/.env" ]; then
    # shellcheck source=/dev/null
    . "$SCRIPT_DIR/.env"
fi

if [ -z "$IMAGE_REPO" ] || [ -z "$COMPOSE_FILE" ]; then
    echo "IMAGE_REPO and COMPOSE_FILE must be set. Copy scripts/.env.example to scripts/.env and fill it in." >&2
    exit 1
fi

VERSION="$1"
if [ -z "$VERSION" ]; then
    echo "Usage: $0 <version>" >&2
    exit 1
fi

if [ "$VERSION" = "latest" ]; then
    echo "Version must not be 'latest'." >&2
    exit 1
fi

if [ -n "$(git -C "$SCRIPT_DIR" status --porcelain --untracked-files=no 2>/dev/null)" ]; then
    echo "Warning: working tree has uncommitted changes; image $VERSION will not match commit exactly." >&2
fi

if [ -n "$GHCR_TOKEN" ]; then
    if [ -z "$GHCR_USER" ]; then
        echo "GHCR_USER must be set when GHCR_TOKEN is set." >&2
        exit 1
    fi
    printf '%s' "$GHCR_TOKEN" | docker login "${IMAGE_REPO%%/*}" -u "$GHCR_USER" --password-stdin
fi

for SERVICE in php nginx; do
    if docker manifest inspect "$IMAGE_REPO/$SERVICE:$VERSION" >/dev/null 2>&1; then
        echo "$IMAGE_REPO/$SERVICE:$VERSION already exists in the registry. Choose a different version." >&2
        exit 1
    fi
done

echo "Building version $VERSION"

docker compose -f "$COMPOSE_FILE" build --no-cache

for SERVICE in php nginx; do
    docker tag "soccer-scouting-app-$SERVICE" "$IMAGE_REPO/$SERVICE:$VERSION"
    docker tag "soccer-scouting-app-$SERVICE" "$IMAGE_REPO/$SERVICE:latest"

    docker push "$IMAGE_REPO/$SERVICE:$VERSION"
    docker push "$IMAGE_REPO/$SERVICE:latest"
done

echo "Pushed $IMAGE_REPO/{php,nginx}:$VERSION (also tagged :latest)"