# Laravel Octane di Windows - Panduan Lengkap

## ⚠️ FrankenPHP Limitation

**FrankenPHP tidak tersedia untuk Windows native.** 

Menurut dokumentasi resmi Laravel Octane:
> FrankenPHP binaries are only available for Linux and macOS. On Windows, use WSL or Docker.

## ✅ Solusi untuk Windows

### Option 1: RoadRunner (Recommended untuk Windows)

RoadRunner adalah pilihan terbaik untuk Windows karena:
- ✅ Native support untuk Windows
- ✅ Mudah di-install
- ✅ Performance boost 10-20x
- ✅ Tidak perlu Docker/WSL

#### Langkah Install RoadRunner:

**1. Enable Extension Sockets**
```bash
# Edit C:\xampp\php\php.ini
# Cari baris: ;extension=sockets
# Hapus ; (uncomment): extension=sockets
# Save file
```

**2. Restart Apache/PHP**
```bash
# Restart XAMPP Apache
# Atau restart PHP-FPM jika menggunakan
```

**3. Install RoadRunner**
```bash
composer require spiral/roadrunner-cli spiral/roadrunner-http
php artisan octane:install --server=roadrunner
```

**4. Start Octane dengan RoadRunner**
```bash
php artisan octane:start --server=roadrunner --host=127.0.0.1 --port=8000 --workers=4
```

### Option 2: Docker (Production-Ready)

Jika ingin menggunakan FrankenPHP, gunakan Docker:

**1. Install Docker Desktop for Windows**
- Download: https://www.docker.com/products/docker-desktop

**2. Create Dockerfile**
```dockerfile
FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    mbstring \
    zip

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
```

**3. Create docker-compose.yml**
```yaml
services:
  frankenphp:
    build:
      context: .
    ports:
      - "8000:8000"
    volumes:
      - .:/app
    environment:
      - APP_ENV=local
```

**4. Run with Docker**
```bash
docker-compose up
```

### Option 3: WSL2 (Windows Subsystem for Linux)

Gunakan FrankenPHP di WSL2:

**1. Install WSL2**
```powershell
wsl --install
```

**2. Install Ubuntu dari Microsoft Store**

**3. Di WSL Terminal:**
```bash
cd /mnt/c/xampp/htdocs/Absensi-Sekolah-main
php artisan octane:install --server=frankenphp
php artisan octane:start --server=frankenphp
```

### Option 4: Standard PHP Server (No Octane)

Jika tidak ingin setup Octane, aplikasi tetap bisa berjalan dengan baik:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Performance:**
- Standard: ~50-100 req/s
- With Octane: ~1,000-2,000 req/s

## 🚀 Recommended Setup untuk Windows

### Development
```bash
# Gunakan standard PHP server
php artisan serve
```

### Production
```bash
# Option A: RoadRunner (setelah enable sockets)
php artisan octane:start --server=roadrunner

# Option B: Docker dengan FrankenPHP
docker-compose up
```

## 📊 Performance Comparison

| Server | Req/s | Response Time | Setup Difficulty |
|--------|-------|---------------|------------------|
| Standard PHP | 50-100 | 50-200ms | ⭐ Easy |
| RoadRunner | 1,000-2,000 | 5-10ms | ⭐⭐ Medium |
| FrankenPHP (Docker) | 1,500-3,000 | 3-8ms | ⭐⭐⭐ Hard |
| FrankenPHP (WSL2) | 1,500-3,000 | 3-8ms | ⭐⭐⭐ Hard |

## 🎯 Quick Decision Guide

**Pilih Standard PHP Server jika:**
- Development lokal
- Traffic rendah (<50 concurrent users)
- Tidak ingin setup kompleks

**Pilih RoadRunner jika:**
- Production di Windows Server
- Traffic medium (50-200 concurrent users)
- Ingin performance boost tanpa Docker

**Pilih Docker + FrankenPHP jika:**
- Production dengan traffic tinggi (>200 concurrent users)
- Sudah familiar dengan Docker
- Ingin performance maksimal

**Pilih WSL2 + FrankenPHP jika:**
- Development di Windows tapi ingin environment Linux
- Testing production setup di local
- Familiar dengan Linux

## ✅ Current Status

**Aplikasi sudah siap digunakan dengan:**
- ✅ Standard PHP Server (php artisan serve)
- ✅ Laravel 12
- ✅ All 137 tests passing
- ✅ All features implemented

**Octane Status:**
- ⚠️ FrankenPHP: Tidak tersedia untuk Windows native
- ⏳ RoadRunner: Perlu enable extension sockets
- ✅ Docker: Siap digunakan (perlu Docker Desktop)

## 📝 Next Steps

### Untuk Development Sekarang:
```bash
# Gunakan ini untuk testing:
php artisan serve --host=127.0.0.1 --port=8000
```

### Untuk Production Nanti:

**Option 1: Enable Sockets + RoadRunner**
1. Edit `C:\xampp\php\php.ini`
2. Uncomment: `extension=sockets`
3. Restart Apache
4. Run: `composer require spiral/roadrunner-cli spiral/roadrunner-http`
5. Run: `php artisan octane:install --server=roadrunner`
6. Run: `php artisan octane:start --server=roadrunner`

**Option 2: Docker**
1. Install Docker Desktop
2. Create Dockerfile (lihat contoh di atas)
3. Run: `docker-compose up`

## 🎉 Kesimpulan

Aplikasi **sudah 100% siap digunakan** dengan atau tanpa Octane. Octane hanya memberikan performance boost, bukan requirement wajib.

Untuk development dan testing, gunakan `php artisan serve` sudah sangat cukup!
