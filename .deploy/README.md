# Deployment Scripts untuk Hostinger

Quick reference guide untuk deployment Laravel ke Hostinger menggunakan Git dan SSH.

## File Scripts

- **`config.sh`** - Konfigurasi deployment (WAJIB diisi)
- **`setup-ssh.sh`** - Setup SSH configuration (jalankan sekali)
- **`first-deploy.sh`** - Setup deployment pertama kali (jalankan sekali)
- **`deploy-local.sh`** - Deploy dari lokal ke server (untuk deployment rutin)
- **`deploy.sh`** - Script yang dijalankan di server (otomatis dipanggil)

## Quick Start

### 1. Setup Awal (Sekali)

```bash
# 1. Setup SSH
./.deploy/setup-ssh.sh

# 2. Edit konfigurasi
nano .deploy/config.sh
# Isi: SSH_HOST, SERVER_PATH, dll

# 3. First deployment
./.deploy/first-deploy.sh
```

### 2. Deployment Rutin

```bash
# Setelah push code ke Git
./.deploy/deploy-local.sh
```

## Konfigurasi

Edit file `.deploy/config.sh` dengan informasi server Anda:

```bash
SSH_HOST="hostinger"                    # Alias SSH atau hostname
SERVER_PATH="~/domains/yourdomain.com/public_html"  # Path server
BRANCH="main"                           # Branch untuk deploy
```

**Cara mendapatkan informasi:**
1. Login ke hPanel Hostinger
2. Buka **Advanced** → **SSH Access**
3. Catat: SSH Host, Port, Username
4. Untuk SERVER_PATH, biasanya: `~/domains/yourdomain.com/public_html`

## Workflow

### Setup Pertama Kali

```
1. ./setup-ssh.sh          → Setup SSH config & key
2. Edit config.sh           → Isi informasi server
3. ./first-deploy.sh        → Clone repo & setup awal
4. Edit .env di server      → Konfigurasi database, dll
5. ./deploy-local.sh        → Deploy pertama
```

### Deployment Rutin

```
1. git add .
2. git commit -m "Update"
3. git push origin main
4. ./.deploy/deploy-local.sh
```

## Troubleshooting

### SSH Connection Failed
```bash
# Test koneksi manual
ssh hostinger

# Atau dengan detail
ssh -p 65002 u123456789@ssh.hostinger.com
```

### Permission Denied
```bash
# Set permission untuk scripts
chmod +x .deploy/*.sh
```

### Composer/Node Not Found
- Pastikan Composer sudah terinstall di server
- Node.js mungkin tidak tersedia di shared hosting
- Build assets di lokal dan commit hasil build

### .env Not Found
```bash
# SSH ke server
ssh hostinger

# Copy .env.example
cd ~/domains/yourdomain.com/public_html
cp .env.example .env
nano .env  # Edit dengan konfigurasi yang benar
```

## Script Details

### setup-ssh.sh
- Generate SSH key (jika belum ada)
- Setup SSH config (~/.ssh/config)
- Test koneksi SSH

### first-deploy.sh
- Clone repository ke server
- Setup .env file
- Run deployment pertama

### deploy-local.sh
- Connect ke server via SSH
- Menjalankan deploy.sh di server
- Menampilkan output deployment

### deploy.sh (di server)
- Pull latest code dari Git
- Install dependencies (Composer & NPM)
- Build assets
- Run migrations
- Clear & cache config
- Handle maintenance mode

## Tips

1. **Selalu backup sebelum deploy** - Set `BACKUP_BEFORE_DEPLOY=true` di config.sh
2. **Test di staging dulu** - Gunakan subdomain untuk testing
3. **Monitor logs** - Cek `storage/logs/deploy_*.log` di server
4. **Build assets lokal** - Jika Node.js tidak tersedia di server, build di lokal dan commit

## Support

Jika mengalami masalah:
1. Cek error logs: `storage/logs/laravel.log` di server
2. Cek deployment logs: `storage/logs/deploy_*.log` di server
3. Test SSH connection manual
4. Hubungi support Hostinger jika masalah terkait server

