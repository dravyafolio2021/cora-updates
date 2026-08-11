#!/bin/bash
# ==============================================================================
# Cora Workspace deployment script
# Supports backup, upload, activation, verification, and automatic rollback
# ==============================================================================

set -eo pipefail

# Configuration
SSH_USER="u484406462"
SSH_IP="145.79.213.97"
SSH_PORT="65002"
LOCAL_ZIP="/Users/shrutian/Desktop/cora/updates/cora-workspace.zip"
REMOTE_TMP="/home/u484406462/cora-workspace-deploy.zip"

MAIN_PATH="/home/u484406462/domains/heycora.in/public_html"
DEMO_PATH="/home/u484406462/domains/heycora.in/public_html/demo"
STAGING_PATH="/home/u484406462/domains/heycora.in/public_html/stagging"

# Targets
TARGET=${1:-both}

echo "=== Cora Workspace Deployer ==="
echo "Target: $TARGET"

# Check if local zip exists
if [ ! -f "$LOCAL_ZIP" ]; then
    echo "ERROR: Local zip file not found at $LOCAL_ZIP. Run build.sh first!" >&2
    exit 1
fi

# Function to deploy to a specific path
deploy_site() {
    local SITE_NAME=$1
    local SITE_PATH=$2
    local TIMESTAMP
    TIMESTAMP=$(date +%s)
    local BACKUP_NAME="cora-workspace.bak-$TIMESTAMP"
    
    echo "----------------------------------------"
    echo "Deploying to $SITE_NAME ($SITE_PATH)..."
    echo "----------------------------------------"
    
    # 1. SCP Zip to remote server temp location
    echo "1. Uploading release zip to server..."
    scp -P "$SSH_PORT" -o StrictHostKeyChecking=no "$LOCAL_ZIP" "$SSH_USER@$SSH_IP:$REMOTE_TMP"
    
    # 2. SSH: Backup, Extract, Activate, Test, Rollback if failed
    echo "2. Executing remote update, verification & activation..."
    
    ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no "$SSH_USER@$SSH_IP" bash -s <<EOF
set -e

# Define directories
PLUGINS_DIR="$SITE_PATH/wp-content/plugins"
PLUGIN_DIR="\$PLUGINS_DIR/cora-workspace"
BACKUP_DIR="\$PLUGINS_DIR/$BACKUP_NAME"

echo "  Creating backup..."
if [ -d "\$PLUGIN_DIR" ]; then
    mv "\$PLUGIN_DIR" "\$BACKUP_DIR"
fi

# Ensure cleanup trap on failure
trap '
  echo "❌ CRITICAL: Activation or deployment failed! Reverting from backup..."
  if [ -d "\$BACKUP_DIR" ]; then
      rm -rf "\$PLUGIN_DIR"
      mv "\$BACKUP_DIR" "\$PLUGIN_DIR"
      echo "✅ Rollback completed successfully."
  else
      echo "ERROR: No backup found to rollback!"
  fi
  rm -f $REMOTE_TMP
  exit 1
' ERR

echo "  Extracting plugin..."
rm -rf "\$PLUGIN_DIR"
unzip -o -q $REMOTE_TMP -d "\$PLUGINS_DIR/"

echo "  Activating plugin..."
cd "$SITE_PATH"
wp plugin activate cora-workspace --allow-root

echo "  Flushing caches..."
wp cache flush --allow-root
wp rewrite flush --allow-root

# Run small sanity test
echo "  Verifying version info..."
VERSION_ACTIVE=\$(wp plugin get cora-workspace --field=version --allow-root)
echo "  Active version is: \$VERSION_ACTIVE"

# All checks passed, remove backup and temp files
echo "✅ Deployment successful. Cleaning up backup..."
rm -rf "\$BACKUP_DIR"
rm -f $REMOTE_TMP
EOF

}

# Run deployment based on target
if [ "$TARGET" = "main" ]; then
    deploy_site "Main Production" "$MAIN_PATH"
elif [ "$TARGET" = "demo" ]; then
    deploy_site "Demo Environment" "$DEMO_PATH"
elif [ "$TARGET" = "staging" ]; then
    deploy_site "Staging Environment" "$STAGING_PATH"
elif [ "$TARGET" = "both" ]; then
    deploy_site "Demo Environment" "$DEMO_PATH"
    deploy_site "Main Production" "$MAIN_PATH"
else
    echo "ERROR: Invalid target. Choose 'main', 'demo', 'staging', or 'both'." >&2
    exit 1
fi

echo "======================================="
echo "Deployment sequence finished successfully."
