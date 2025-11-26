#!/bin/bash

# ============================================
# SSH Setup Helper untuk Hostinger
# ============================================
# Script ini membantu setup SSH configuration
# ============================================

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== SSH Setup untuk Hostinger ===${NC}\n"

# Check if SSH config exists
SSH_CONFIG="$HOME/.ssh/config"
SSH_DIR="$HOME/.ssh"

# Create .ssh directory if it doesn't exist
if [ ! -d "$SSH_DIR" ]; then
    echo -e "${YELLOW}Membuat directory .ssh...${NC}"
    mkdir -p "$SSH_DIR"
    chmod 700 "$SSH_DIR"
fi

# Check if SSH key exists
SSH_KEY="$HOME/.ssh/id_rsa"
SSH_PUB_KEY="$HOME/.ssh/id_rsa.pub"

if [ ! -f "$SSH_KEY" ]; then
    echo -e "${YELLOW}SSH key tidak ditemukan.${NC}"
    read -p "Apakah Anda ingin membuat SSH key baru? (y/n): " create_key
    
    if [ "$create_key" = "y" ] || [ "$create_key" = "Y" ]; then
        read -p "Masukkan email untuk SSH key: " email
        ssh-keygen -t rsa -b 4096 -C "$email" -f "$SSH_KEY"
        echo -e "${GREEN}SSH key berhasil dibuat!${NC}"
    else
        echo -e "${YELLOW}Anda bisa membuat SSH key nanti dengan: ssh-keygen -t rsa -b 4096${NC}"
    fi
else
    echo -e "${GREEN}SSH key sudah ada di: $SSH_KEY${NC}"
fi

# Display public key if exists
if [ -f "$SSH_PUB_KEY" ]; then
    echo -e "\n${BLUE}=== Public Key Anda ===${NC}"
    echo -e "${YELLOW}Copy key berikut dan paste ke Hostinger hPanel > Advanced > SSH Access > Manage SSH Keys:${NC}\n"
    cat "$SSH_PUB_KEY"
    echo -e "\n"
fi

# Get SSH information
echo -e "${BLUE}=== Konfigurasi SSH Hostinger ===${NC}\n"
read -p "SSH Host (default: ssh.hostinger.com): " ssh_host
ssh_host=${ssh_host:-ssh.hostinger.com}

read -p "SSH Port (default: 65002): " ssh_port
ssh_port=${ssh_port:-65002}

read -p "SSH Username (format: u123456789): " ssh_user

if [ -z "$ssh_user" ]; then
    echo -e "${RED}Username SSH wajib diisi!${NC}"
    exit 1
fi

# Create or update SSH config
echo -e "\n${YELLOW}Menambahkan konfigurasi ke ~/.ssh/config...${NC}"

# Backup existing config if exists
if [ -f "$SSH_CONFIG" ]; then
    cp "$SSH_CONFIG" "$SSH_CONFIG.backup.$(date +%Y%m%d_%H%M%S)"
    echo -e "${GREEN}Backup config lama dibuat.${NC}"
fi

# Check if hostinger entry already exists
if grep -q "Host hostinger" "$SSH_CONFIG" 2>/dev/null; then
    echo -e "${YELLOW}Entry 'hostinger' sudah ada di config.${NC}"
    read -p "Apakah Anda ingin mengupdate? (y/n): " update_config
    if [ "$update_config" = "y" ] || [ "$update_config" = "Y" ]; then
        # Remove old entry
        sed -i.bak '/^Host hostinger$/,/^$/d' "$SSH_CONFIG" 2>/dev/null || \
        sed -i '' '/^Host hostinger$/,/^$/d' "$SSH_CONFIG" 2>/dev/null
    else
        echo -e "${YELLOW}Konfigurasi tidak diubah.${NC}"
        exit 0
    fi
fi

# Append new configuration
cat >> "$SSH_CONFIG" << EOF

Host hostinger
    HostName $ssh_host
    User $ssh_user
    Port $ssh_port
    IdentityFile ~/.ssh/id_rsa
    ServerAliveInterval 60
    ServerAliveCountMax 3
    StrictHostKeyChecking no
    UserKnownHostsFile ~/.ssh/known_hosts
EOF

# Set correct permissions
chmod 600 "$SSH_CONFIG"
chmod 600 "$SSH_KEY" 2>/dev/null || true
chmod 644 "$SSH_PUB_KEY" 2>/dev/null || true

echo -e "${GREEN}Konfigurasi SSH berhasil ditambahkan!${NC}\n"

# Test connection
echo -e "${BLUE}=== Test Koneksi SSH ===${NC}"
read -p "Apakah Anda ingin test koneksi SSH sekarang? (y/n): " test_conn

if [ "$test_conn" = "y" ] || [ "$test_conn" = "Y" ]; then
    echo -e "${YELLOW}Mencoba koneksi ke server...${NC}"
    ssh -o ConnectTimeout=10 hostinger "echo 'Koneksi SSH berhasil!'" 2>&1
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Koneksi SSH berhasil!${NC}"
    else
        echo -e "${RED}✗ Koneksi SSH gagal.${NC}"
        echo -e "${YELLOW}Pastikan:${NC}"
        echo -e "  1. SSH access sudah diaktifkan di hPanel Hostinger"
        echo -e "  2. SSH key sudah ditambahkan ke hPanel (jika menggunakan key)"
        echo -e "  3. Username, host, dan port sudah benar"
        echo -e "  4. Anda bisa coba dengan password: ssh hostinger"
    fi
fi

echo -e "\n${GREEN}=== Setup SSH Selesai ===${NC}"
echo -e "Anda sekarang bisa connect dengan: ${BLUE}ssh hostinger${NC}\n"

