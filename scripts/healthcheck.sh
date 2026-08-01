#!/bin/bash
# ==============================================================================
# Cora Workspace Platform health check script
# Validates live endpoints, wp-cli active status, and PHP error logs
# ==============================================================================

# Configuration
SSH_USER="u484406462"
SSH_IP="145.79.213.97"
SSH_PORT="65002"

MAIN_URL="https://app.heycora.in"
DEMO_URL="https://app.heycora.in/demo"

# Terminal formatting
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

FAILED=0

echo -e "=== ${YELLOW}Cora Workspace Platform Health Check${NC} ==="

check_http_endpoint() {
    local SITE_NAME=$1
    local URL=$2
    local EXPECTED_STRING=$3
    
    echo -n "Checking $SITE_NAME HTTP response... "
    
    # Fetch status code and body
    local TEMP_FILE
    TEMP_FILE=$(mktemp)
    local HTTP_CODE
    HTTP_CODE=$(curl -s -L -w "%{http_code}" -o "$TEMP_FILE" "$URL")
    
    if [ "$HTTP_CODE" -ne 200 ]; then
        echo -e "${RED}FAILED${NC} (HTTP $HTTP_CODE)"
        rm -f "$TEMP_FILE"
        FAILED=1
        return
    fi
    
    if ! grep -q "$EXPECTED_STRING" "$TEMP_FILE"; then
        echo -e "${RED}FAILED${NC} (Expected string '$EXPECTED_STRING' not found)"
        rm -f "$TEMP_FILE"
        FAILED=1
        return
    fi
    
    echo -e "${GREEN}PASSED${NC} (HTTP 200, string verified)"
    rm -f "$TEMP_FILE"
}

# 1. HTTP Endpoint checks
check_http_endpoint "Main Login Page" "$MAIN_URL/workspace/login" "<title>Cora — Login</title>"
check_http_endpoint "Demo Login Page" "$DEMO_URL/workspace/login" "<title>Cora — Login</title>"

# 2. Remote check of plugin active status via SSH/WP-CLI
echo -n "Checking Active plugins via SSH (Main)... "
MAIN_ACTIVE=$(ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no "$SSH_USER@$SSH_IP" \
  "cd ~/domains/heycora.in/public_html && wp plugin is-active cora-workspace --allow-root && echo 'yes' || echo 'no'")

if [ "$MAIN_ACTIVE" = "yes" ]; then
    echo -e "${GREEN}ACTIVE${NC}"
else
    echo -e "${RED}INACTIVE/MISSING${NC}"
    FAILED=1
fi

echo -n "Checking Active plugins via SSH (Demo)... "
DEMO_ACTIVE=$(ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no "$SSH_USER@$SSH_IP" \
  "cd ~/domains/heycora.in/public_html/demo && wp plugin is-active cora-workspace --allow-root && echo 'yes' || echo 'no'")

if [ "$DEMO_ACTIVE" = "yes" ]; then
    echo -e "${GREEN}ACTIVE${NC}"
else
    echo -e "${RED}INACTIVE/MISSING${NC}"
    FAILED=1
fi

# 3. Check for recent PHP Fatal errors
echo "Checking recent PHP Fatal errors (last 30 lines of server logs)..."
FATALS=$(ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no "$SSH_USER@$SSH_IP" \
  "tail -n 30 ~/domains/heycora.in/logs/error.log 2>/dev/null | grep -i 'Fatal error' || true")

if [ -n "$FATALS" ]; then
    echo -e "${RED}WARNING: Recent Fatal Errors Found!${NC}"
    echo "$FATALS"
    FAILED=1
else
    echo -e "${GREEN}No recent fatal errors found in PHP log.${NC}"
fi

echo "======================================="
if [ "$FAILED" -eq 0 ]; then
    echo -e "${GREEN}ALL HEALTH CHECKS PASSED${NC}"
    exit 0
else
    echo -e "${RED}HEALTH CHECKS FAILED${NC}"
    exit 1
fi
