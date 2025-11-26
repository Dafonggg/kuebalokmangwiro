#!/bin/bash

# ============================================
# Local Deployment Script untuk Hostinger
# ============================================
# Script ini dijalankan DARI LOKAL untuk
# deploy ke server via SSH
# ============================================

set -e  # Exit on error

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="$SCRIPT_DIR/config.sh"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Functions
error() {
    echo -e "${RED}✗${NC} $1" >&2
}

success() {
    echo -e "${GREEN}✓${NC} $1"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

# Check if config file exists
if [ ! -f "$CONFIG_FILE" ]; then
    error "Config file not found: $CONFIG_FILE"
    warning "Please copy and edit .deploy/config.sh with your server information"
    exit 1
fi

# Load configuration
source "$CONFIG_FILE"

# Validate required variables
if [ -z "$SSH_HOST" ] || [ -z "$SERVER_PATH" ]; then
    error "Required configuration missing!"
    warning "Please check $CONFIG_FILE"
    warning "Required: SSH_HOST, SERVER_PATH"
    exit 1
fi

# Build SSH command
SSH_USER="${SSH_USER:-}"
SSH_PORT="${SSH_PORT:-22}"

# Determine SSH connection method
if [ -n "$SSH_KEY_PATH" ] && [ "$SSH_KEY_PATH" != "~/.ssh/id_rsa" ]; then
    SSH_KEY_OPT="-i $SSH_KEY_PATH"
else
    # Try to use SSH config alias if SSH_HOST is an alias
    SSH_KEY_OPT=""
fi

# Build SSH connection string
if [ -n "$SSH_USER" ]; then
    if [ "$SSH_PORT" != "22" ]; then
        SSH_CONNECTION="$SSH_USER@$SSH_HOST -p $SSH_PORT"
    else
        SSH_CONNECTION="$SSH_USER@$SSH_HOST"
    fi
else
    # Assume SSH_HOST is an alias from ~/.ssh/config
    SSH_CONNECTION="$SSH_HOST"
fi

info "Connecting to server..."
info "SSH: $SSH_CONNECTION"
info "Server Path: $SERVER_PATH"

# Test SSH connection
info "Testing SSH connection..."
if ssh $SSH_KEY_OPT -o ConnectTimeout=10 -o BatchMode=yes "$SSH_CONNECTION" "echo 'Connection successful'" &>/dev/null; then
    success "SSH connection successful"
else
    warning "SSH connection test failed, but continuing..."
    warning "You might be prompted for password"
fi

# Check if deploy.sh exists on server
info "Checking deployment script on server..."
if ssh $SSH_KEY_OPT "$SSH_CONNECTION" "test -f $SERVER_PATH/.deploy/deploy.sh"; then
    success "Deployment script found on server"
else
    error "Deployment script not found on server: $SERVER_PATH/.deploy/deploy.sh"
    warning "Please make sure:"
    warning "  1. Repository is cloned to $SERVER_PATH"
    warning "  2. .deploy/deploy.sh exists in the repository"
    exit 1
fi

# Show what will be deployed
echo -e "\n${CYAN}========================================${NC}"
echo -e "${CYAN}  Deployment Information${NC}"
echo -e "${CYAN}========================================${NC}"
echo -e "Server: ${BLUE}$SSH_CONNECTION${NC}"
echo -e "Path: ${BLUE}$SERVER_PATH${NC}"
echo -e "Branch: ${BLUE}${BRANCH:-main}${NC}"
echo -e "Timestamp: ${BLUE}$(date +'%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${CYAN}========================================${NC}\n"

# Ask for confirmation
read -p "Continue with deployment? (y/n): " confirm
if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
    info "Deployment cancelled"
    exit 0
fi

# Execute deployment on server
info "Starting deployment on server..."
echo -e "\n${CYAN}--- Server Output ---${NC}\n"

# Run deploy.sh on server with environment variables
ssh $SSH_KEY_OPT "$SSH_CONNECTION" bash << EOF
set -e
cd "$SERVER_PATH"

# Export configuration variables
export BACKUP_BEFORE_DEPLOY="${BACKUP_BEFORE_DEPLOY:-false}"
export RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
export CLEAR_CACHE="${CLEAR_CACHE:-true}"
export BUILD_ASSETS="${BUILD_ASSETS:-true}"
export ENABLE_MAINTENANCE_MODE="${ENABLE_MAINTENANCE_MODE:-false}"
export DISABLE_MAINTENANCE_MODE="${DISABLE_MAINTENANCE_MODE:-true}"
export PHP_BIN="${PHP_BIN:-php}"
export COMPOSER_BIN="${COMPOSER_BIN:-composer}"
export NPM_BIN="${NPM_BIN:-npm}"
export BRANCH="${BRANCH:-main}"

# Run deployment script
bash .deploy/deploy.sh
EOF

DEPLOY_EXIT_CODE=$?

echo -e "\n${CYAN}--- End Server Output ---${NC}\n"

if [ $DEPLOY_EXIT_CODE -eq 0 ]; then
    success "Deployment completed successfully!"
    echo -e "\n${GREEN}========================================${NC}"
    echo -e "${GREEN}  🚀 Deployment Successful!${NC}"
    echo -e "${GREEN}========================================${NC}\n"
    exit 0
else
    error "Deployment failed with exit code: $DEPLOY_EXIT_CODE"
    warning "Please check the server logs for details"
    exit $DEPLOY_EXIT_CODE
fi

