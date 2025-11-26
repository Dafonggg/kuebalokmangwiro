#!/bin/bash

# ============================================
# Deployment Configuration untuk Hostinger
# ============================================
# Edit file ini dengan informasi server Anda
# 
# CARA MENDAPATKAN INFORMASI:
# 1. Login ke hPanel Hostinger
# 2. Buka Advanced > SSH Access
# 3. Catat: SSH Host, Port, Username
# 4. Untuk SERVER_PATH, biasanya: ~/domains/yourdomain.com/public_html
# ============================================

# SSH Configuration
# Opsi 1: Gunakan alias dari ~/.ssh/config (recommended)
#   - Setup dengan: ./.deploy/setup-ssh.sh
#   - Kemudian gunakan: SSH_HOST="hostinger"
#
# Opsi 2: Langsung isi detail SSH
#   - SSH_HOST="ssh.hostinger.com"
#   - SSH_USER="u123456789" (format: u + nomor akun)
#   - SSH_PORT="65002" (biasanya 65002 untuk Hostinger)
SSH_HOST="hostinger"                    # Alias dari SSH config (recommended), atau "ssh.hostinger.com"
SSH_USER=""                             # Kosongkan jika pakai alias, atau isi username SSH
SSH_PORT="65002"                        # Port SSH (biasanya 65002 untuk Hostinger, bisa dikosongkan jika pakai alias)
SSH_KEY_PATH=""                         # Path ke SSH private key (kosongkan untuk pakai default ~/.ssh/id_rsa)

# Server Paths
# Path lengkap ke directory public_html di server Hostinger
# Format: ~/domains/yourdomain.com/public_html
# Untuk subdomain: ~/domains/yourdomain.com/subdomain/public_html
# 
# Cara menemukan path:
# 1. SSH ke server: ssh hostinger
# 2. Jalankan: pwd (setelah cd ke directory project)
SERVER_PATH="~/domains/yourdomain.com/public_html"  # ⚠️ WAJIB DIISI: Path server Anda
REPOSITORY_URL="https://github.com/username/repository.git"  # URL repository Git (untuk reference)
BRANCH="main"                           # Branch yang akan di-deploy (default: main)

# Deployment Options
BACKUP_BEFORE_DEPLOY=false              # Buat backup sebelum deploy (default: false, set true untuk production)
RUN_MIGRATIONS=true                     # Jalankan migrations setelah deploy (default: true)
CLEAR_CACHE=true                        # Clear cache setelah deploy (default: true)
BUILD_ASSETS=true                       # Build assets (npm run build) - set false jika build di lokal

# PHP Configuration
# Biasanya cukup "php", "composer", "npm"
# Jika perlu path lengkap, contoh: PHP_BIN="/usr/bin/php"
PHP_BIN="php"                           # Path ke PHP binary (default: "php")
COMPOSER_BIN="composer"                 # Path ke Composer (default: "composer")
NPM_BIN="npm"                           # Path ke NPM (default: "npm", mungkin tidak tersedia di shared hosting)

# Maintenance Mode
# Maintenance mode akan menampilkan halaman "Under Maintenance" selama deploy
ENABLE_MAINTENANCE_MODE=false           # Enable maintenance mode selama deploy (default: false)
DISABLE_MAINTENANCE_MODE=true           # Disable maintenance mode setelah deploy (default: true)

# Notification (Opsional - belum diimplementasi)
# Fitur untuk mengirim notifikasi setelah deploy
SEND_NOTIFICATION=false
NOTIFICATION_EMAIL="your-email@example.com"

# ============================================
# Jangan edit di bawah ini kecuali Anda tahu
# apa yang Anda lakukan
# ============================================

# Colors untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Export variables untuk digunakan di script lain
export SSH_HOST SSH_USER SSH_PORT SSH_KEY_PATH
export SERVER_PATH REPOSITORY_URL BRANCH
export BACKUP_BEFORE_DEPLOY RUN_MIGRATIONS CLEAR_CACHE BUILD_ASSETS
export PHP_BIN COMPOSER_BIN NPM_BIN
export ENABLE_MAINTENANCE_MODE DISABLE_MAINTENANCE_MODE
export TIMESTAMP RED GREEN YELLOW BLUE NC

