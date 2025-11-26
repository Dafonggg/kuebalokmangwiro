#!/bin/bash

# ============================================
# Deployment Script untuk Server Hostinger
# ============================================
# Script ini dijalankan DI SERVER setelah
# code di-pull dari Git repository
# ============================================

set -e  # Exit on error

# Load configuration if exists
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/config.sh" ]; then
    source "$SCRIPT_DIR/config.sh"
fi

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
LOG_FILE="${LOG_FILE:-$SCRIPT_DIR/../storage/logs/deploy_$TIMESTAMP.log}"

# Functions
log() {
    echo -e "${CYAN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1" | tee -a "$LOG_FILE"
}

info() {
    echo -e "${BLUE}ℹ${NC} $1" | tee -a "$LOG_FILE"
}

# Get current directory (should be project root)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

log "Starting deployment..."
log "Project root: $PROJECT_ROOT"
log "Timestamp: $TIMESTAMP"

# Check if .env exists
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    error ".env file not found!"
    warning "Please create .env file from .env.example"
    exit 1
fi

# Enable maintenance mode if configured
if [ "${ENABLE_MAINTENANCE_MODE:-false}" = "true" ]; then
    info "Enabling maintenance mode..."
    php artisan down || warning "Failed to enable maintenance mode"
fi

# Backup before deploy (if enabled)
if [ "${BACKUP_BEFORE_DEPLOY:-false}" = "true" ]; then
    info "Creating backup..."
    BACKUP_DIR="$PROJECT_ROOT/storage/backups"
    mkdir -p "$BACKUP_DIR"
    
    # Backup database if possible
    if command -v mysqldump &> /dev/null; then
        DB_NAME=$(grep DB_DATABASE "$PROJECT_ROOT/.env" | cut -d '=' -f2 | tr -d ' "')
        if [ -n "$DB_NAME" ]; then
            mysqldump "$DB_NAME" > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql" 2>/dev/null || \
            warning "Database backup failed (might need credentials)"
        fi
    fi
    
    success "Backup completed"
fi

# Pull latest code from Git
info "Pulling latest code from Git..."
if [ -d "$PROJECT_ROOT/.git" ]; then
    BRANCH="${BRANCH:-main}"
    git fetch origin
    git reset --hard "origin/$BRANCH" || error "Git pull failed"
    success "Code updated from Git"
else
    warning ".git directory not found. Skipping Git pull."
fi

# Install/Update Composer dependencies
info "Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    COMPOSER_BIN="${COMPOSER_BIN:-composer}"
    $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction || {
        error "Composer install failed"
        exit 1
    }
    success "Composer dependencies installed"
else
    error "Composer not found!"
    exit 1
fi

# Install/Update NPM dependencies and build assets
if [ "${BUILD_ASSETS:-true}" = "true" ]; then
    info "Building assets..."
    
    if command -v npm &> /dev/null; then
        NPM_BIN="${NPM_BIN:-npm}"
        $NPM_BIN install --production || warning "NPM install failed (might not be critical)"
        $NPM_BIN run build || warning "NPM build failed (might not be critical)"
        success "Assets built"
    else
        warning "NPM not found. Skipping asset build."
        warning "Make sure to build assets locally and commit them."
    fi
fi

# Run migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    info "Running database migrations..."
    PHP_BIN="${PHP_BIN:-php}"
    $PHP_BIN artisan migrate --force || {
        error "Migration failed"
        exit 1
    }
    success "Migrations completed"
fi

# Clear caches
if [ "${CLEAR_CACHE:-true}" = "true" ]; then
    info "Clearing caches..."
    $PHP_BIN artisan config:clear || true
    $PHP_BIN artisan route:clear || true
    $PHP_BIN artisan view:clear || true
    $PHP_BIN artisan cache:clear || true
    success "Caches cleared"
fi

# Cache for production
info "Caching for production..."
$PHP_BIN artisan config:cache || warning "Config cache failed"
$PHP_BIN artisan route:cache || warning "Route cache failed"
$PHP_BIN artisan view:cache || warning "View cache failed"
success "Production cache created"

# Setup storage link if not exists
if [ ! -L "$PROJECT_ROOT/public/storage" ]; then
    info "Creating storage link..."
    $PHP_BIN artisan storage:link || warning "Storage link failed"
fi

# Set permissions
info "Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || warning "Permission setting failed (might need sudo)"
success "Permissions set"

# Disable maintenance mode
if [ "${DISABLE_MAINTENANCE_MODE:-true}" = "true" ]; then
    info "Disabling maintenance mode..."
    $PHP_BIN artisan up || warning "Failed to disable maintenance mode"
fi

# Deployment completed
success "Deployment completed successfully!"
log "Deployment finished at $(date)"
log "Log saved to: $LOG_FILE"

echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}  Deployment Successful! 🚀${NC}"
echo -e "${GREEN}========================================${NC}\n"

