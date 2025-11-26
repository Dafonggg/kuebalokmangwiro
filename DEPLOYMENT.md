# Panduan Deployment Laravel ke Hostinger

Panduan lengkap untuk deploy aplikasi Laravel ke Hostinger menggunakan Git dan SSH.

## Quick Start

Untuk memulai deployment dengan cepat, gunakan script helper yang sudah disediakan:

```bash
# 1. Setup SSH (sekali)
./.deploy/setup-ssh.sh

# 2. Edit konfigurasi
nano .deploy/config.sh

# 3. First deployment (sekali)
./.deploy/first-deploy.sh

# 4. Deployment rutin
./.deploy/deploy-local.sh
```

Lihat [`.deploy/README.md`](.deploy/README.md) untuk quick reference guide.

## Daftar Isi

1. [Persiapan SSH](#persiapan-ssh)
2. [Setup SSH Key](#setup-ssh-key)
3. [Konfigurasi SSH](#konfigurasi-ssh)
4. [Setup Server Hostinger](#setup-server-hostinger)
5. [Deployment Workflow](#deployment-workflow)
6. [Troubleshooting](#troubleshooting)

---

## Persiapan SSH

### 1. Mendapatkan SSH Credentials dari Hostinger

1. Login ke **hPanel Hostinger** Anda
2. Buka **Advanced** → **SSH Access**
3. Catat informasi berikut:
   - **SSH Host**: `ssh.hostinger.com` (atau sesuai yang diberikan)
   - **SSH Port**: Biasanya `65002` (atau sesuai yang diberikan)
   - **SSH Username**: Biasanya `u123456789` (format: u + nomor akun)
   - **SSH Password**: Password yang Anda set di hPanel

### 2. Verifikasi SSH Access

Pastikan SSH access sudah diaktifkan di hPanel Hostinger Anda.

---

## Setup SSH Key

### Opsi 1: Menggunakan Password (Simple)

Anda bisa langsung menggunakan password untuk koneksi SSH, tapi kurang aman.

### Opsi 2: Menggunakan SSH Key (Recommended)

#### Langkah 1: Generate SSH Key (jika belum ada)

```bash
# Cek apakah sudah ada SSH key
ls -la ~/.ssh

# Jika belum ada, generate SSH key baru
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

# Tekan Enter untuk menggunakan default location (~/.ssh/id_rsa)
# Masukkan passphrase (opsional, tapi recommended)
```

#### Langkah 2: Copy Public Key ke Hostinger

```bash
# Tampilkan public key
cat ~/.ssh/id_rsa.pub

# Copy seluruh output (mulai dari ssh-rsa sampai email)
```

#### Langkah 3: Setup SSH Key di Hostinger

1. Login ke hPanel Hostinger
2. Buka **Advanced** → **SSH Access**
3. Klik **Manage SSH Keys**
4. Klik **Add New SSH Key**
5. Paste public key Anda
6. Beri nama untuk key tersebut
7. Klik **Add**

#### Langkah 4: Test Koneksi SSH

```bash
# Test koneksi dengan password
ssh -p 65002 u123456789@ssh.hostinger.com

# Atau jika menggunakan SSH key
ssh -p 65002 -i ~/.ssh/id_rsa u123456789@ssh.hostinger.com
```

---

## Konfigurasi SSH

### Opsi 1: Menggunakan Script Helper (Recommended)

Gunakan script helper yang sudah disediakan:

```bash
./.deploy/setup-ssh.sh
```

Script ini akan:
- Generate SSH key jika belum ada
- Setup SSH config secara otomatis
- Menampilkan public key untuk ditambahkan ke Hostinger
- Test koneksi SSH

### Opsi 2: Setup Manual SSH Config File

Buat atau edit file `~/.ssh/config` untuk memudahkan koneksi:

```bash
# Edit atau buat file config
nano ~/.ssh/config
```

Tambahkan konfigurasi berikut:

```
Host hostinger
    HostName ssh.hostinger.com
    User u123456789
    Port 65002
    IdentityFile ~/.ssh/id_rsa
    ServerAliveInterval 60
    ServerAliveCountMax 3
```

**Ganti `u123456789` dengan username SSH Anda yang sebenarnya.**

Setelah itu, Anda bisa connect dengan perintah sederhana:

```bash
ssh hostinger
```

### Set Permission yang Benar

```bash
chmod 600 ~/.ssh/config
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub
```

---

## Setup Server Hostinger

### 1. Koneksi ke Server

```bash
ssh hostinger
# atau
ssh -p 65002 u123456789@ssh.hostinger.com
```

### 2. Cek PHP Version

```bash
php -v
# Pastikan PHP >= 8.2 untuk Laravel 12
```

### 3. Cek Composer

```bash
composer --version
# Jika belum ada, install Composer
```

### 4. Tentukan Lokasi Project

Biasanya di Hostinger, file website ada di:
- `~/domains/yourdomain.com/public_html/` (untuk domain utama)
- `~/domains/yourdomain.com/subdomain/public_html/` (untuk subdomain)

**Catat path lengkap ini**, akan digunakan di deployment config.

### 5. Setup Git di Server

```bash
# Cek apakah Git sudah terinstall
git --version

# Konfigurasi Git (jika belum)
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### 6. Clone Repository (Pertama Kali)

```bash
# Masuk ke directory public_html
cd ~/domains/yourdomain.com/public_html/

# Clone repository (gunakan HTTPS atau SSH)
git clone https://github.com/username/repository.git .

# Atau jika repository sudah ada, cukup pull
git pull origin main
```

### 7. Setup Environment File

```bash
# Copy .env.example ke .env
cp .env.example .env

# Edit .env file
nano .env
```

**Konfigurasi penting di `.env`:**

```env
APP_NAME="Kue Balok"
APP_ENV=production
APP_KEY=base64:... # Generate dengan: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_database
DB_USERNAME=u123456789_user
DB_PASSWORD=your_password

# Dapatkan info database dari hPanel → Databases
```

### 8. Install Dependencies

```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies (jika ada Node.js di server)
npm install

# Build assets
npm run build
```

### 9. Setup Laravel

```bash
# Generate application key (jika belum)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Setup storage link
php artisan storage:link

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Set Permissions

```bash
# Set permission untuk storage dan bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R u123456789:u123456789 storage bootstrap/cache
```

---

## Deployment Workflow

### Menggunakan Deployment Script

#### Setup Awal (Pertama Kali)

**Langkah 1: Setup SSH**

Jalankan script helper untuk setup SSH:

```bash
./.deploy/setup-ssh.sh
```

Script ini akan:
- Generate SSH key (jika belum ada)
- Setup SSH config di `~/.ssh/config`
- Menampilkan public key untuk ditambahkan ke Hostinger
- Test koneksi SSH

**Langkah 2: Konfigurasi Deployment**

Edit file `.deploy/config.sh` dan isi dengan informasi server Anda:

```bash
# SSH Configuration
SSH_HOST="hostinger"                    # Alias dari SSH config
SSH_USER=""                             # Kosongkan jika pakai alias
SSH_PORT="65002"                        # Port SSH
SERVER_PATH="~/domains/yourdomain.com/public_html"  # ⚠️ WAJIB DIISI
BRANCH="main"                           # Branch untuk deploy
```

**Langkah 3: First Deployment**

Jalankan script untuk setup pertama kali:

```bash
./.deploy/first-deploy.sh
```

Script ini akan:
- Clone repository ke server
- Setup .env file dari .env.example
- Run deployment pertama

**Langkah 4: Konfigurasi .env di Server**

SSH ke server dan edit .env:

```bash
ssh hostinger
cd ~/domains/yourdomain.com/public_html
nano .env
```

Isi dengan konfigurasi yang benar (database, APP_URL, dll).

#### Deployment Rutin

Setelah setup awal, untuk deployment rutin:

```bash
# 1. Push code ke Git
git add .
git commit -m "Update"
git push origin main

# 2. Deploy ke server
./.deploy/deploy-local.sh
```

Script `deploy-local.sh` akan:
- Connect ke server via SSH
- Menjalankan `deploy.sh` di server
- Pull latest code dari Git
- Install dependencies (Composer & NPM)
- Build assets
- Run migrations
- Clear dan cache config
- Handle maintenance mode (opsional)

#### Deploy Manual di Server

Jika Anda sudah login ke server:

#### Deploy Manual di Server

Jika Anda sudah login ke server:

```bash
cd ~/domains/yourdomain.com/public_html
./.deploy/deploy.sh
```

### Manual Deployment

Jika tidak menggunakan script, lakukan langkah berikut di server:

```bash
# 1. Masuk ke directory project
cd ~/domains/yourdomain.com/public_html

# 2. Pull latest code
git pull origin main

# 3. Install/update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 6. Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

### Masalah: Permission Denied saat SSH

**Solusi:**
- Pastikan SSH access sudah diaktifkan di hPanel
- Cek username dan port yang benar
- Pastikan SSH key sudah di-setup dengan benar

### Masalah: Composer Command Not Found

**Solusi:**
```bash
# Install Composer secara global
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### Masalah: NPM/Node Not Found

**Solusi:**
- Hostinger mungkin tidak menyediakan Node.js di shared hosting
- Gunakan build lokal dan commit file yang sudah di-build
- Atau gunakan VPS plan yang support Node.js

### Masalah: Database Connection Error

**Solusi:**
- Pastikan database credentials di `.env` benar
- Cek apakah database sudah dibuat di hPanel
- Pastikan `DB_HOST` menggunakan `localhost` atau sesuai yang diberikan Hostinger

### Masalah: 500 Internal Server Error

**Solusi:**
```bash
# Cek error log
tail -f storage/logs/laravel.log

# Pastikan permission benar
chmod -R 775 storage bootstrap/cache

# Pastikan .env sudah di-setup dengan benar
php artisan config:clear
php artisan config:cache
```

### Masalah: Assets Tidak Muncul

**Solusi:**
```bash
# Rebuild assets
npm run build

# Pastikan storage link sudah dibuat
php artisan storage:link

# Clear view cache
php artisan view:clear
```

---

## Checklist Deployment

Gunakan checklist ini untuk memastikan semua langkah sudah dilakukan:

### Pre-Deployment
- [ ] SSH access sudah diaktifkan di hPanel
- [ ] SSH key sudah di-setup (recommended)
- [ ] SSH config sudah dikonfigurasi
- [ ] Koneksi SSH sudah ditest
- [ ] Git repository sudah di-clone di server
- [ ] Database sudah dibuat di hPanel
- [ ] File `.env` sudah dikonfigurasi dengan benar

### Deployment
- [ ] Dependencies sudah di-install (Composer & NPM)
- [ ] Assets sudah di-build
- [ ] Migrations sudah di-run
- [ ] Storage link sudah dibuat
- [ ] Permissions sudah di-set dengan benar
- [ ] Cache sudah di-clear dan re-cache

### Post-Deployment
- [ ] Website sudah bisa diakses
- [ ] Database connection berhasil
- [ ] Assets (CSS/JS) sudah muncul
- [ ] Form dan fitur utama sudah berfungsi
- [ ] Error log tidak ada error baru

---

## Tips & Best Practices

1. **Selalu backup sebelum deploy:**
   ```bash
   # Backup database
   php artisan backup:run
   
   # Atau manual backup via hPanel
   ```

2. **Gunakan branch untuk testing:**
   - `main` untuk production
   - `staging` untuk testing di subdomain

3. **Monitor error logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Keep dependencies updated:**
   ```bash
   composer update
   npm update
   ```

5. **Optimize untuk production:**
   - Set `APP_DEBUG=false` di `.env`
   - Enable cache (config, route, view)
   - Use `--optimize-autoloader` untuk Composer

---

## Support

Jika mengalami masalah:
1. Cek error logs: `storage/logs/laravel.log`
2. Cek Hostinger documentation
3. Hubungi support Hostinger jika masalah terkait server

---

**Selamat! Aplikasi Laravel Anda sudah siap di-deploy ke Hostinger! 🚀**

