# PLANNR - Production Deployment Guide

## Overview

This guide provides step-by-step instructions for deploying PLANNR to a production server. PLANNR is a Laravel 10 application with Livewire 3, Flux UI Pro, and requires PHP 8.1+, MySQL/MariaDB, Redis, and Node.js.

## Table of Contents

1. [Server Requirements](#server-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Deployment Options](#deployment-options)
4. [Manual Deployment Steps](#manual-deployment-steps)
5. [Post-Deployment Configuration](#post-deployment-configuration)
6. [Security Hardening](#security-hardening)
7. [Maintenance & Updates](#maintenance--updates)
8. [Troubleshooting](#troubleshooting)

---

## Server Requirements

### Minimum Requirements

- **Operating System:** Ubuntu 22.04 LTS or similar Linux distribution
- **PHP:** 8.1 or higher
- **Web Server:** Nginx or Apache
- **Database:** MySQL 8.0+ or MariaDB 10.3+
- **Cache/Queue:** Redis 6.0+
- **Node.js:** 18.x or higher
- **Memory:** 2GB RAM minimum (4GB recommended)
- **Storage:** 10GB minimum (20GB recommended)

### PHP Extensions Required

```bash
php8.1-cli
php8.1-fpm
php8.1-mysql
php8.1-redis
php8.1-mbstring
php8.1-xml
php8.1-bcmath
php8.1-curl
php8.1-zip
php8.1-gd
php8.1-intl
```

### Additional Software

- Composer 2.x
- Git
- Supervisor (for queue workers)
- Certbot (for SSL certificates)

---

## Pre-Deployment Checklist

### 1. Domain & DNS
- [ ] Domain name registered
- [ ] DNS A record pointing to server IP
- [ ] DNS propagation complete (check with `dig your-domain.com`)

### 2. Server Access
- [ ] SSH access configured
- [ ] Non-root user created with sudo privileges
- [ ] SSH key authentication enabled
- [ ] Firewall configured (UFW or similar)

### 3. Required Services
- [ ] MySQL/MariaDB installed and running
- [ ] Redis installed and running
- [ ] Nginx/Apache installed and configured
- [ ] PHP-FPM installed and running

### 4. Application Preparation
- [ ] All code committed to Git repository
- [ ] Frontend assets built (`npm run build`)
- [ ] Environment variables documented
- [ ] Database backup strategy planned

---

## Deployment Options

### Option 1: Forge (Recommended for Ease)

[Laravel Forge](https://forge.laravel.com) provides automated deployment with:
- One-click server provisioning
- Automatic SSL certificates
- Zero-downtime deployments
- Queue worker management
- Database backups

**Cost:** $12-19/month + server costs

### Option 2: Ploi

[Ploi](https://ploi.io) is similar to Forge with additional features:
- Server management
- Automated deployments
- Team collaboration
- Monitoring and alerts

**Cost:** $10-20/month + server costs

### Option 3: Manual Deployment

Full control with manual configuration (this guide focuses on this option).

**Cost:** Free (server costs only)

### Option 4: Docker

Containerized deployment using Docker and Docker Compose.

**Pros:** Consistent environments, easy scaling
**Cons:** Additional complexity

---

## Manual Deployment Steps

### Step 1: Prepare the Server

#### 1.1 Update System Packages

```bash
sudo apt update && sudo apt upgrade -y
```

#### 1.2 Install PHP 8.1

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-redis \
    php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl \
    php8.1-zip php8.1-gd php8.1-intl
```

#### 1.3 Install MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Create database and user:

```sql
CREATE DATABASE plannr_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'plannr_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON plannr_production.* TO 'plannr_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 1.4 Install Redis

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### 1.5 Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### 1.6 Install Node.js

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 1.7 Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"
```

#### 1.8 Install Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### Step 2: Deploy Application Code

#### 2.1 Create Application Directory

```bash
sudo mkdir -p /var/www/plannr
sudo chown -R $USER:$USER /var/www/plannr
cd /var/www/plannr
```

#### 2.2 Clone Repository or Upload Files

**Option A: Git Clone**
```bash
git clone https://github.com/your-username/plannr.git .
```

**Option B: Upload via SCP**
```bash
# From your local machine
scp -r /path/to/laravel-app/* user@server:/var/www/plannr/
```

**Option C: Upload via SFTP**
Use FileZilla, Cyberduck, or similar SFTP client.

#### 2.3 Install Dependencies

```bash
cd /var/www/plannr
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### Step 3: Configure Environment

#### 3.1 Create Production Environment File

```bash
cp .env.production .env
```

#### 3.2 Edit Environment Variables

```bash
nano .env
```

Update the following values:

```env
APP_NAME=PLANNR
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plannr_production
DB_USERNAME=plannr_user
DB_PASSWORD=your_strong_password_here

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="PLANNR"
```

#### 3.3 Generate Application Key

```bash
php artisan key:generate
```

#### 3.4 Run Migrations

```bash
php artisan migrate --force
```

#### 3.5 Create Storage Symlink

```bash
php artisan storage:link
```

#### 3.6 Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/plannr
sudo chmod -R 755 /var/www/plannr
sudo chmod -R 775 /var/www/plannr/storage
sudo chmod -R 775 /var/www/plannr/bootstrap/cache
```

### Step 4: Configure Nginx

#### 4.1 Create Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/plannr
```

Add the following configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/plannr/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

#### 4.2 Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/plannr /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: Configure SSL with Let's Encrypt

#### 5.1 Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

#### 5.2 Obtain SSL Certificate

```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Follow the prompts to:
- Enter email address
- Agree to terms
- Choose whether to redirect HTTP to HTTPS (recommended: Yes)

#### 5.3 Test Auto-Renewal

```bash
sudo certbot renew --dry-run
```

### Step 6: Configure Queue Workers

#### 6.1 Create Supervisor Configuration

```bash
sudo nano /etc/supervisor/conf.d/plannr-worker.conf
```

Add the following:

```ini
[program:plannr-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/plannr/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/plannr/storage/logs/worker.log
stopwaitsecs=3600
```

#### 6.2 Start Queue Workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start plannr-worker:*
```

### Step 7: Configure Scheduler (Cron)

```bash
sudo crontab -e -u www-data
```

Add the following line:

```cron
* * * * * cd /var/www/plannr && php artisan schedule:run >> /dev/null 2>&1
```

### Step 8: Optimize Application

```bash
cd /var/www/plannr
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

---

## Post-Deployment Configuration

### 1. Create Admin User

```bash
cd /var/www/plannr
php artisan tinker
```

In Tinker:

```php
$user = new App\Models\User();
$user->name = 'Admin User';
$user->email = 'admin@your-domain.com';
$user->password = Hash::make('secure_password_here');
$user->email_verified_at = now();
$user->save();
exit
```

### 2. Configure Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 3. Set Up Monitoring

Consider installing:
- **Laravel Telescope** (already included for debugging)
- **Laravel Horizon** (for queue monitoring)
- **Uptime monitoring** (UptimeRobot, Pingdom, etc.)
- **Server monitoring** (New Relic, DataDog, etc.)

### 4. Configure Backups

#### Database Backups

Create backup script:

```bash
sudo nano /usr/local/bin/backup-plannr-db.sh
```

Add:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/plannr"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR
mysqldump -u plannr_user -p'your_password' plannr_production | gzip > $BACKUP_DIR/plannr_$DATE.sql.gz
find $BACKUP_DIR -name "plannr_*.sql.gz" -mtime +7 -delete
```

Make executable and add to cron:

```bash
sudo chmod +x /usr/local/bin/backup-plannr-db.sh
sudo crontab -e
```

Add:

```cron
0 2 * * * /usr/local/bin/backup-plannr-db.sh
```

---

## Security Hardening

### 1. Disable Directory Listing

Already configured in Nginx config above.

### 2. Hide PHP Version

Edit `/etc/php/8.1/fpm/php.ini`:

```ini
expose_php = Off
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.1-fpm
```

### 3. Configure Security Headers

Add to Nginx config:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
```

### 4. Limit Request Size

Already configured in Nginx: `client_max_body_size 20M;`

### 5. Rate Limiting

Laravel includes rate limiting. Configure in `app/Http/Kernel.php` if needed.

### 6. Fail2Ban

Install and configure Fail2Ban to prevent brute force attacks:

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## Maintenance & Updates

### Deploying Updates

```bash
cd /var/www/plannr

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan event:cache

# Restart services
sudo supervisorctl restart plannr-worker:*
sudo systemctl reload php8.1-fpm
```

### Zero-Downtime Deployments

For zero-downtime deployments, consider:
- **Laravel Envoyer** (paid service)
- **Deployer** (free, open-source)
- **Custom deployment scripts** with symlinks

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Check logs:**
```bash
tail -f /var/www/plannr/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

**Common causes:**
- Incorrect file permissions
- Missing `.env` file
- Database connection issues
- Missing PHP extensions

### Issue: Queue Jobs Not Processing

**Check queue workers:**
```bash
sudo supervisorctl status plannr-worker:*
```

**Restart workers:**
```bash
sudo supervisorctl restart plannr-worker:*
```

### Issue: CSS/JS Not Loading

**Rebuild assets:**
```bash
cd /var/www/plannr
npm run build
```

**Check file permissions:**
```bash
sudo chown -R www-data:www-data /var/www/plannr/public
```

### Issue: Database Connection Failed

**Test connection:**
```bash
mysql -u plannr_user -p plannr_production
```

**Check credentials in `.env`**

### Issue: Redis Connection Failed

**Check Redis status:**
```bash
sudo systemctl status redis-server
redis-cli ping
```

---

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/10.x/deployment)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)

---

## Support

For issues specific to PLANNR, refer to:
- Application documentation in the repository
- Laravel community forums
- Stack Overflow with `laravel` tag

---

**Deployment Guide Version:** 1.0  
**Last Updated:** 2025  
**Application:** PLANNR Event Control & Production Management System
