#!/usr/bin/env bash
set -euo pipefail

# Package the plugin into a dist zip excluding dev files via .gitattributes export-ignore
# Usage: scripts/package.sh

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

PLUGIN_SLUG="msl-form-validator"
VERSION=$(sed -En 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*([^[:space:]]+).*$/\1/p' "${PLUGIN_SLUG}.php" | head -n1)

if [[ -z "$VERSION" ]]; then
  echo "Could not determine version from ${PLUGIN_SLUG}.php" >&2
  exit 1
fi

mkdir -p dist
OUTPUT="dist/${PLUGIN_SLUG}-${VERSION}.zip"

# Use worktree attributes so current .gitattributes export-ignore rules are applied
git archive --format=zip --worktree-attributes -o "$OUTPUT" HEAD

echo "Built: $OUTPUT"
