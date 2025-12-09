# 🚀 Deployment Guide - Real-Time Features

## 📋 Pre-Deployment Checklist

- [ ] All tests passed (see TESTING_CHECKLIST.md)
- [ ] Code reviewed and approved
- [ ] Database backup created
- [ ] Environment variables prepared
- [ ] SSL certificates ready
- [ ] Server requirements met
- [ ] Monitoring tools configured

---

## 🖥️ Server Requirements

### Minimum Requirements:
- **PHP:** 8.2 or higher
- **Node.js:** 18.x or higher
- **Database:** MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
- **Redis:** 6.0+ (recommended for production)
- **Memory:** 2GB RAM minimum
- **Storage:** 10GB minimum

### Recommended:
- **PHP:** 8.3
- **Memory:** 4GB RAM
- **Redis:** 7.0+
- **Supervisor:** For process management
- **Nginx/Apache:** Web server
- **SSL:** Let's Encrypt or commercial certificate

---

## 🔧 Production Setup

### 1. Server Preparation

#### Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2+
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-common \
  php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring \
  php8.2-curl php8.2-xml php8.2-bcmath php8.2-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Redis
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install Supervisor
sudo apt install supervisor
```

---

### 2. Application Deployment

#### Clone & Setup
```bash
# Clone repository
cd /var/www
git clone <your-repo-url> absensi-sekolah
cd absensi-sekolah

# Set permissions
sudo chown -R www-data:www-data /var/www/absensi-sekolah
sudo chmod -R 755 /var/www/absensi-sekolah
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env for production
nano .env
```

**Production .env:**
```env
APP_NAME="Absensi Sekolah"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_sekolah
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST="your-domain.com"
REVERB_PORT=8080
REVERB_SCHEME=https

# Vite
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

#### Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### 3. Supervisor Configuration

Create supervisor configs for long-running processes:

#### Queue Worker
```bash
sudo nano /etc/supervisor/conf.d/absensi-queue.conf
```

```ini
[program:absensi-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/absensi-sekolah/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/absensi-sekolah/storage/logs/queue-worker.log
stopwaitsecs=3600
```

#### Reverb Server
```bash
sudo nano /etc/supervisor/conf.d/absensi-reverb.conf
```

```ini
[program:absensi-reverb]
process_name=%(program_name)s
command=php /var/www/absensi-sekolah/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/absensi-sekolah/storage/logs/reverb.log
```

#### Start Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

---

### 4. Web Server Configuration

#### Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/absensi-sekolah
```

```nginx
# HTTP to HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/absensi-sekolah/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # WebSocket (Reverb)
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/absensi-sekolah /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 5. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal (already configured by certbot)
sudo certbot renew --dry-run
```

---

### 6. Firewall Configuration

```bash
# Allow HTTP, HTTPS, SSH
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8080/tcp  # Reverb (if needed externally)
sudo ufw enable
sudo ufw status
```

---

## 🔄 Deployment Process

### Initial Deployment
```bash
cd /var/www/absensi-sekolah

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear & cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart services
sudo supervisorctl restart all
sudo systemctl reload nginx
```

### Zero-Downtime Deployment (Advanced)

```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Put app in maintenance mode
php artisan down --retry=60

# Run migrations
php artisan migrate --force

# Clear old cache
php artisan cache:clear
php artisan view:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers
php artisan queue:restart

# Restart supervisor services
sudo supervisorctl restart all

# Bring app back up
php artisan up

echo "✅ Deployment completed!"
```

---

## 📊 Monitoring & Logging

### Application Logs
```bash
# View logs
tail -f storage/logs/laravel.log

# Queue worker logs
tail -f storage/logs/queue-worker.log

# Reverb logs
tail -f storage/logs/reverb.log
```

### System Monitoring
```bash
# Check supervisor status
sudo supervisorctl status

# Check Nginx status
sudo systemctl status nginx

# Check Redis status
sudo systemctl status redis-server

# Check PHP-FPM status
sudo systemctl status php8.2-fpm
```

### Performance Monitoring

Install Laravel Telescope (optional):
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

---

## 🔐 Security Hardening

### 1. File Permissions
```bash
sudo chown -R www-data:www-data /var/www/absensi-sekolah
sudo chmod -R 755 /var/www/absensi-sekolah
sudo chmod -R 775 storage bootstrap/cache
sudo chmod 600 .env
```

### 2. Disable Directory Listing
Already configured in Nginx config above.

### 3. Rate Limiting
Add to `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 4. CORS Configuration
```bash
php artisan config:publish cors
```

### 5. Database Security
- Use strong passwords
- Limit database user permissions
- Enable SSL for database connections
- Regular backups

---

## 💾 Backup Strategy

### Automated Backup Script
```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/var/backups/absensi-sekolah"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u username -p'password' absensi_sekolah > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/absensi-sekolah \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/logs'

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

### Cron Job
```bash
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /path/to/backup.sh >> /var/log/backup.log 2>&1
```

---

## 🔄 Rollback Procedure

```bash
#!/bin/bash
# rollback.sh

# Put app in maintenance
php artisan down

# Restore database
mysql -u username -p'password' absensi_sekolah < /var/backups/db_backup.sql

# Restore files
cd /var/www
tar -xzf /var/backups/files_backup.tar.gz

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart services
sudo supervisorctl restart all

# Bring app back up
php artisan up
```

---

## 📈 Scaling Considerations

### Horizontal Scaling
- Use load balancer (Nginx/HAProxy)
- Shared Redis instance
- Centralized session storage
- CDN for static assets

### Vertical Scaling
- Increase server resources
- Optimize database queries
- Enable OPcache
- Use Redis for cache/session

### Database Optimization
```bash
# Enable query caching
php artisan config:cache

# Optimize tables
php artisan db:optimize
```

---

## ✅ Post-Deployment Checklist

- [ ] Application accessible via HTTPS
- [ ] WebSocket connection working
- [ ] Queue workers running
- [ ] Reverb server running
- [ ] Database migrations applied
- [ ] Cache cleared and rebuilt
- [ ] Logs rotating properly
- [ ] Backups configured
- [ ] Monitoring active
- [ ] SSL certificate valid
- [ ] Firewall configured
- [ ] Performance acceptable
- [ ] All tests passing

---

## 🆘 Troubleshooting

### Issue: 502 Bad Gateway
```bash
# Check PHP-FPM
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm

# Check Nginx
sudo nginx -t
sudo systemctl restart nginx
```

### Issue: WebSocket not connecting
```bash
# Check Reverb
sudo supervisorctl status absensi-reverb
sudo supervisorctl restart absensi-reverb

# Check firewall
sudo ufw status
sudo ufw allow 8080/tcp
```

### Issue: Queue not processing
```bash
# Check queue worker
sudo supervisorctl status absensi-queue
sudo supervisorctl restart absensi-queue

# Check Redis
redis-cli ping
```

### Issue: Permission denied
```bash
sudo chown -R www-data:www-data /var/www/absensi-sekolah
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📞 Support

For deployment issues:
1. Check logs: `storage/logs/laravel.log`
2. Check supervisor logs: `/var/log/supervisor/`
3. Check Nginx logs: `/var/log/nginx/error.log`
4. Review documentation: FITUR_REALTIME.md

---

**Deployment Guide Version:** 1.0  
**Last Updated:** December 6, 2025  
**Status:** Production Ready ✅
