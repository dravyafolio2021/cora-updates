#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_ZIP="${DIR}/cora-chatgpt-plugin.zip"

echo "📦 Packaging Cora ChatGPT Plugin..."
cd "${DIR}"

# Remove existing zip if present
rm -f "${OUTPUT_ZIP}"

# Create zip bundle
zip -r "${OUTPUT_ZIP}" manifest.json ai-plugin.json mcp.json openapi.json skills assets README.md

echo "✅ Created: ${OUTPUT_ZIP}"
echo "Ready for upload to OpenAI Plugin Submission portal!"
