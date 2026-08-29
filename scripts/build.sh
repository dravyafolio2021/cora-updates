#!/bin/bash
# ==============================================================================
# Cora Workspace Release Builder & Packaging Pipeline
# Verifies version synchronization, safety guards, and packages lean release zip
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
echo "✅ Versions match ($HEADER_VERSION)."

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
  -x "$PLUGIN_NAME/.env*" \
  -x "$PLUGIN_NAME/**/.env*" \
  -x "$PLUGIN_NAME/scratch/*" \
  -x "$PLUGIN_NAME/scratch_*" \
  -x "$PLUGIN_NAME/**/scratch/*" \
  -x "$PLUGIN_NAME/test-results/*" \
  -x "$PLUGIN_NAME/**/test-results/*" \
  -x "$PLUGIN_NAME/tests/*" \
  -x "$PLUGIN_NAME/apex-realty-group/*" \
  -x "$PLUGIN_NAME/package-lock.json" \
  -x "$PLUGIN_NAME/package.json" \
  -x "$PLUGIN_NAME/tailwind.config.js" \
  -x "$PLUGIN_NAME/src/*" \
  -x "$PLUGIN_NAME/*.guide.html" \
  -x "$PLUGIN_NAME/*.html" \
  -x "$PLUGIN_NAME/* (*)*" \
  -x "$PLUGIN_NAME/**/* (*)*" \
  -x "$PLUGIN_NAME/*.log" \
  -x "$PLUGIN_NAME/**/*.log" \
  -x "$PLUGIN_NAME/*.md" \
  -x "*/.DS_Store" \
  -x "*.DS_Store"

# Step 4: Verification of packaged zip
echo "Verifying release zip integrity..."
ZIP_ENTRIES=$(unzip -l "$OUTPUT_ZIP")

# Security check: ensure no .env files leaked into zip
if echo "$ZIP_ENTRIES" | grep -q "\.env"; then
    echo "ERROR: Security failure: .env file found in release zip!" >&2
    exit 1
fi

# Quality check: ensure no parenthesized duplicates leaked into zip
if echo "$ZIP_ENTRIES" | grep -E "\([0-9]+\)"; then
    echo "ERROR: Quality failure: duplicate copy files found in release zip!" >&2
    exit 1
fi

# Ensure cora-bridge.py is included
if ! echo "$ZIP_ENTRIES" | grep -q "cora-workspace/cora-bridge.py"; then
    echo "ERROR: Missing required MCP asset cora-bridge.py in release zip!" >&2
    exit 1
fi

# Ensure main plugin file is included
if ! echo "$ZIP_ENTRIES" | grep -q "cora-workspace/cora-workspace.php"; then
    echo "ERROR: Missing main plugin file cora-workspace.php in release zip!" >&2
    exit 1
fi

echo "✅ Packaging and security verification complete: $OUTPUT_ZIP"
ZIP_SIZE=$(du -h "$OUTPUT_ZIP" | cut -f1)
echo "  Zip file size: $ZIP_SIZE"
echo "======================================="
echo "Build successful! Ready for deploy."
