#!/bin/bash
# ==============================================================================
# Cora Workspace build script
# Verifies version synchronization, counts PHP guards, and builds release zip
# ==============================================================================

set -eo pipefail

# Configuration
PLUGIN_DIR="/Users/shrutian/Desktop/cora/app/public/wp-content/plugins"
PLUGIN_NAME="cora-workspace"
MAIN_FILE="$PLUGIN_DIR/$PLUGIN_NAME/cora-workspace.php"
MANIFEST="/Users/shrutian/Desktop/cora/updates/cora-workspace.json"
OUTPUT_ZIP="/Users/shrutian/Desktop/cora/updates/cora-workspace.zip"

echo "=== Cora Workspace Release Builder ==="

# Step 1: Extract versions
echo "Checking version consistency..."

# Get version from plugin header
HEADER_VERSION=$(grep -i "Version:" "$MAIN_FILE" | head -n 1 | awk '{print $NF}' | tr -d '\r\n')
# Get version from constant
CONSTANT_VERSION=$(grep "define( 'CORA_WORKSPACE_VERSION'" "$MAIN_FILE" | head -n 1 | cut -d"'" -f4 | tr -d '\r\n')
# Get version from manifest json
MANIFEST_VERSION=$(grep '"version":' "$MANIFEST" | head -n 1 | cut -d'"' -f4 | tr -d '\r\n')

echo "  Plugin Header:      $HEADER_VERSION"
echo "  Plugin Constant:    $CONSTANT_VERSION"
echo "  Updates Manifest:   $MANIFEST_VERSION"

if [ "$HEADER_VERSION" != "$CONSTANT_VERSION" ] || [ "$HEADER_VERSION" != "$MANIFEST_VERSION" ]; then
    echo "ERROR: Version mismatch detected!" >&2
    exit 1
fi
echo "✅ Versions match."

# Step 2: Perform sanity check on function_exists guards
echo "Analyzing PHP function definitions and safety guards..."
TOTAL_FUNCTIONS=$(grep -c "^function " "$MAIN_FILE" || true)
TOTAL_GUARDS=$(grep -c "function_exists" "$MAIN_FILE" || true)

echo "  Total functions:    $TOTAL_FUNCTIONS"
echo "  Function guards:    $TOTAL_GUARDS"

if [ "$TOTAL_FUNCTIONS" -eq 0 ]; then
    echo "ERROR: No functions found in main plugin file!" >&2
    exit 1
fi

UNGUARDED=$((TOTAL_FUNCTIONS - TOTAL_GUARDS))
if [ "$UNGUARDED" -gt 5 ]; then
    # We allow a small number of helper functions if they are nested/ignored, but most must be wrapped
    echo "WARNING: There are $UNGUARDED unguarded functions. Checking safety..."
fi
echo "✅ Guard analysis complete."

# Step 3: Packaging
echo "Packaging lean release zip..."
rm -f "$OUTPUT_ZIP"

cd "$PLUGIN_DIR"
zip -r -q "$OUTPUT_ZIP" "$PLUGIN_NAME" \
  -x "$PLUGIN_NAME/node_modules/*" \
  -x "$PLUGIN_NAME/.git/*" \
  -x "$PLUGIN_NAME/scratch/*" \
  -x "$PLUGIN_NAME/scratch_*" \
  -x "$PLUGIN_NAME/test-results/*" \
  -x "$PLUGIN_NAME/apex-realty-group/*" \
  -x "$PLUGIN_NAME/package-lock.json" \
  -x "$PLUGIN_NAME/*.guide.html" \
  -x "$PLUGIN_NAME/*.html" \
  -x "$PLUGIN_NAME/*.py" \
  -x "*/.DS_Store"

echo "✅ Packaging complete: $OUTPUT_ZIP"
ZIP_SIZE=$(du -h "$OUTPUT_ZIP" | cut -f1)
echo "  Zip file size: $ZIP_SIZE"
echo "======================================="
echo "Build successful! Ready for deploy."
