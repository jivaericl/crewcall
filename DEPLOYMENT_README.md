# PLANNR - Deployment Package

## Package Contents

This deployment package contains the complete PLANNR application ready for production deployment.

### What's Included

- Complete Laravel 10 application
- All PHP dependencies (via Composer)
- Compiled frontend assets (CSS, JavaScript)
- Database migrations
- Application configuration files
- Documentation and deployment guides

### What's NOT Included (You'll Need to Set Up)

- `.env` file (use `.env.production` as template)
- `node_modules` directory (install with `npm ci`)
- User-uploaded files (backup separately)
- Database (create and configure separately)

## Quick Start

### 1. Extract Package

```bash
tar -xzf plannr-deployment-YYYYMMDD.tar.gz
sudo mv laravel-app /var/www/plannr
cd /var/www/plannr
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 3. Configure Environment

```bash
cp .env.production .env
nano .env  # Edit with your settings
php artisan key:generate
```

### 4. Set Up Database

```bash
php artisan migrate --force
php artisan storage:link
```

### 5. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/plannr
sudo chmod -R 755 /var/www/plannr
sudo chmod -R 775 /var/www/plannr/storage
sudo chmod -R 775 /var/www/plannr/bootstrap/cache
```

### 6. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

## Full Documentation

For complete deployment instructions, see:

- **DEPLOYMENT_GUIDE.md** - Comprehensive step-by-step guide
- **DEPLOYMENT_CHECKLIST.md** - Deployment checklist
- **RUN_OF_SHOW_DOCUMENTATION.md** - Run of Show feature guide
- **SPEAKER_MANAGEMENT_DOCUMENTATION.md** - Speaker management guide

## System Requirements

- **PHP:** 8.1 or higher
- **Database:** MySQL 8.0+ or MariaDB 10.3+
- **Redis:** 6.0+
- **Node.js:** 18.x+
- **Web Server:** Nginx or Apache
- **Memory:** 2GB RAM minimum (4GB recommended)

## Support

For deployment assistance, refer to the comprehensive guides included in this package or consult the Laravel documentation at https://laravel.com/docs

## Application Features

PLANNR is a complete event control and production management system with:

- **Event Management** - Create and manage events
- **Session Management** - Organize sessions within events
- **Segment Management** - Break sessions into segments
- **Run of Show** - Real-time show calling with active segment highlighting
- **Speaker Management** - Manage speakers with profiles and assignments
- **Content Management** - Upload and organize event content
- **Cue Management** - Technical cue sheets for production
- **Custom Fields** - Flexible data collection
- **Comments & @Mentions** - Team collaboration
- **Tags** - Organize and categorize
- **Audit Logs** - Track all changes
- **Dark Mode** - Full dark mode support

## Recent Updates

### November 2025
- ✅ Run of Show view with real-time active segment highlighting
- ✅ Per-user customizable column preferences
- ✅ Speaker management module with full CRUD
- ✅ Speaker integration with sessions and content
- ✅ Production-ready deployment configuration

## Version

**Application Version:** 1.0  
**Laravel Version:** 10.49.1  
**Livewire Version:** 3.6.4  
**Package Date:** November 2, 2025

---

**Ready to deploy!** Follow the DEPLOYMENT_GUIDE.md for detailed instructions.
