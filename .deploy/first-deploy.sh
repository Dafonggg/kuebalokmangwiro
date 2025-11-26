#!/bin/bash

# ============================================
# First Deployment Script untuk Hostinger
# ============================================
# Script ini untuk setup awal deployment
# Hanya dijalankan SEKALI untuk setup pertama
# ============================================

set -e

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

echo -e "${CYAN}========================================${NC}"
echo -e "${CYAN}  First Deployment Setup${NC}"
echo -e "${CYAN}========================================${NC}\n"

# Check if config file exists
if [ ! -f "$CONFIG_FILE" ]; then
    error "Config file not found: $CONFIG_FILE"
    warning "Please create and configure $CONFIG_FILE first"
    exit 1
fi

# Load configuration
source "$CONFIG_FILE"

# Validate required variables
if [ -z "$SSH_HOST" ] || [ -z "$SERVER_PATH" ]; then
    error "Required configuration missing in $CONFIG_FILE"
    warning "Please set: SSH_HOST, SERVER_PATH"
    exit 1
fi

# Build SSH connection
SSH_USER="${SSH_USER:-}"
SSH_PORT="${SSH_PORT:-22}"

if [ -n "$SSH_USER" ]; then
    if [ "$SSH_PORT" != "22" ]; then
        SSH_CONNECTION="$SSH_USER@$SSH_HOST -p $SSH_PORT"
    else
        SSH_CONNECTION="$SSH_USER@$SSH_HOST"
    fi
else
    SSH_CONNECTION="$SSH_HOST"
fi

info "This script will help you setup the first deployment"
info "SSH: $SSH_CONNECTION"
info "Server Path: $SERVER_PATH"
echo ""

# Check SSH connection
info "Testing SSH connection..."
if ssh -o ConnectTimeout=10 -o BatchMode=yes "$SSH_CONNECTION" "echo 'OK'" &>/dev/null; then
    success "SSH connection successful"
else
    error "SSH connection failed!"
    warning "Please:"
    warning "  1. Run ./.deploy/setup-ssh.sh to setup SSH"
    warning "  2. Or check your SSH credentials in config.sh"
    exit 1
fi

# Check if directory exists on server
info "Checking server directory..."
if ssh "$SSH_CONNECTION" "test -d $SERVER_PATH"; then
    warning "Directory $SERVER_PATH already exists on server"
    read -p "Continue anyway? (y/n): " continue_anyway
    if [ "$continue_anyway" != "y" ] && [ "$continue_anyway" != "Y" ]; then
        info "Cancelled"
        exit 0
    fi
else
    info "Directory $SERVER_PATH does not exist"
    read -p "Create directory? (y/n): " create_dir
    if [ "$create_dir" = "y" ] || [ "$create_dir" = "Y" ]; then
        ssh "$SSH_CONNECTION" "mkdir -p $SERVER_PATH"
        success "Directory created"
    fi
fi

# Check if Git repository is already cloned
info "Checking if repository is already cloned..."
if ssh "$SSH_CONNECTION" "test -d $SERVER_PATH/.git"; then
    warning "Git repository already exists in $SERVER_PATH"
    read -p "Do you want to re-clone? (y/n): " re_clone
    if [ "$re_clone" = "y" ] || [ "$re_clone" = "Y" ]; then
        info "Removing existing repository..."
        ssh "$SSH_CONNECTION" "rm -rf $SERVER_PATH/* $SERVER_PATH/.*" 2>/dev/null || true
    else
        info "Skipping clone, using existing repository"
        CLONE_SKIP=true
    fi
fi

# Clone repository if needed
if [ "${CLONE_SKIP:-false}" != "true" ]; then
    if [ -z "$REPOSITORY_URL" ] || [ "$REPOSITORY_URL" = "https://github.com/username/repository.git" ]; then
        warning "REPOSITORY_URL not configured in config.sh"
        read -p "Enter Git repository URL: " repo_url
        if [ -n "$repo_url" ]; then
            REPOSITORY_URL="$repo_url"
        else
            error "Repository URL is required"
            exit 1
        fi
    fi
    
    info "Cloning repository..."
    BRANCH="${BRANCH:-main}"
    ssh "$SSH_CONNECTION" "cd $SERVER_PATH && git clone -b $BRANCH $REPOSITORY_URL ." || {
        error "Failed to clone repository"
        exit 1
    }
    success "Repository cloned"
fi

# Check if .env exists on server
info "Checking .env file..."
if ssh "$SSH_CONNECTION" "test -f $SERVER_PATH/.env"; then
    warning ".env file already exists on server"
    read -p "Do you want to create new .env from .env.example? (y/n): " create_env
    if [ "$create_env" = "y" ] || [ "$create_env" = "Y" ]; then
        ssh "$SSH_CONNECTION" "cd $SERVER_PATH && cp .env.example .env"
        success ".env file created from .env.example"
        warning "⚠️  IMPORTANT: Edit .env file on server with correct configuration!"
        warning "   Run: ssh $SSH_CONNECTION"
        warning "   Then: nano $SERVER_PATH/.env"
    fi
else
    if ssh "$SSH_CONNECTION" "test -f $SERVER_PATH/.env.example"; then
        info "Creating .env from .env.example..."
        ssh "$SSH_CONNECTION" "cd $SERVER_PATH && cp .env.example .env"
        success ".env file created"
        warning "⚠️  IMPORTANT: Edit .env file on server with correct configuration!"
        warning "   Run: ssh $SSH_CONNECTION"
        warning "   Then: nano $SERVER_PATH/.env"
    else
        warning ".env.example not found, you need to create .env manually"
    fi
fi

# Run first deployment
echo ""
read -p "Do you want to run first deployment now? (y/n): " run_deploy
if [ "$run_deploy" = "y" ] || [ "$run_deploy" = "Y" ]; then
    info "Running first deployment..."
    echo ""
    
    # Run deploy.sh on server
    ssh "$SSH_CONNECTION" bash << EOF
set -e
cd "$SERVER_PATH"

# Export configuration
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

# Run deployment
bash .deploy/deploy.sh
EOF

    if [ $? -eq 0 ]; then
        success "First deployment completed!"
    else
        error "First deployment failed"
        exit 1
    fi
else
    info "Skipping deployment. You can run it later with: ./.deploy/deploy-local.sh"
fi

echo ""
success "First deployment setup completed!"
echo ""
info "Next steps:"
info "  1. Edit .env file on server with correct configuration"
info "  2. Run: ./.deploy/deploy-local.sh (for regular deployments)"
echo ""

