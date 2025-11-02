# PLANNR - Production Deployment Checklist

## Pre-Deployment

### Server Setup
- [ ] Server provisioned (2GB RAM minimum, 4GB recommended)
- [ ] Ubuntu 22.04 LTS installed
- [ ] SSH access configured with key authentication
- [ ] Non-root user created with sudo privileges
- [ ] Firewall configured (ports 22, 80, 443 open)

### Domain & DNS
- [ ] Domain name registered
- [ ] DNS A record pointing to server IP address
- [ ] DNS propagation verified (`dig your-domain.com`)

### Required Software Installed
- [ ] PHP 8.1+ with required extensions
- [ ] MySQL 8.0+ or MariaDB 10.3+
- [ ] Redis 6.0+
- [ ] Nginx or Apache
- [ ] Node.js 18.x+
- [ ] Composer 2.x
- [ ] Git
- [ ] Supervisor
- [ ] Certbot (for SSL)

## Database Setup

- [ ] MySQL/MariaDB installed and running
- [ ] Database created: `plannr_production`
- [ ] Database user created with strong password
- [ ] User granted all privileges on database
- [ ] Database credentials documented securely

## Application Deployment

### Code Deployment
- [ ] Application code uploaded to `/var/www/plannr`
- [ ] Composer dependencies installed (`composer install --no-dev`)
- [ ] NPM dependencies installed (`npm ci`)
- [ ] Frontend assets built (`npm run build`)

### Environment Configuration
- [ ] `.env` file created from `.env.production`
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] `APP_ENV` set to `production`
- [ ] `APP_DEBUG` set to `false`
- [ ] `APP_URL` set to production domain
- [ ] Database credentials configured
- [ ] Redis connection configured
- [ ] Mail settings configured
- [ ] All sensitive credentials secured

### Database & Storage
- [ ] Migrations run (`php artisan migrate --force`)
- [ ] Storage symlink created (`php artisan storage:link`)
- [ ] File permissions set correctly (www-data:www-data)
- [ ] Storage directories writable (775)

## Web Server Configuration

### Nginx
- [ ] Nginx site configuration created
- [ ] Document root set to `/var/www/plannr/public`
- [ ] PHP-FPM configured
- [ ] Site enabled in `/etc/nginx/sites-enabled/`
- [ ] Nginx configuration tested (`nginx -t`)
- [ ] Nginx reloaded

### SSL Certificate
- [ ] Certbot installed
- [ ] SSL certificate obtained for domain
- [ ] HTTPS redirect enabled
- [ ] Auto-renewal tested (`certbot renew --dry-run`)

## Background Services

### Queue Workers
- [ ] Supervisor configuration created
- [ ] Queue workers configured (2+ processes)
- [ ] Supervisor reloaded and workers started
- [ ] Worker logs verified (`tail -f storage/logs/worker.log`)

### Scheduler (Cron)
- [ ] Cron job added for Laravel scheduler
- [ ] Cron running as www-data user
- [ ] Scheduler tested

## Optimization

### Laravel Optimization
- [ ] Config cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Events cached (`php artisan event:cache`)
- [ ] Composer autoloader optimized

### Performance
- [ ] OPcache enabled in PHP
- [ ] Redis configured for cache and sessions
- [ ] Queue workers running
- [ ] Static assets compressed (gzip/brotli)

## Security

### Application Security
- [ ] `APP_DEBUG` disabled in production
- [ ] Database credentials secured
- [ ] `.env` file not publicly accessible
- [ ] Directory listing disabled
- [ ] PHP version hidden (`expose_php = Off`)

### Server Security
- [ ] Firewall enabled (UFW)
- [ ] SSH password authentication disabled
- [ ] Fail2Ban installed and configured
- [ ] Security headers configured in Nginx
- [ ] Rate limiting configured

### SSL/TLS
- [ ] SSL certificate installed
- [ ] HTTPS enforced
- [ ] HTTP redirects to HTTPS
- [ ] SSL certificate auto-renewal working

## User & Data Setup

### Admin User
- [ ] Admin user created via Tinker or seeder
- [ ] Admin credentials documented securely
- [ ] Email verification handled

### Initial Data
- [ ] Test event created (optional)
- [ ] Sample data loaded (optional)
- [ ] User roles configured

## Backups

### Database Backups
- [ ] Backup script created
- [ ] Automated daily backups configured
- [ ] Backup retention policy set (7 days)
- [ ] Backup restoration tested
- [ ] Off-site backup storage configured (optional)

### File Backups
- [ ] Application files backed up
- [ ] Uploaded files backed up
- [ ] Backup schedule documented

## Monitoring & Logging

### Application Monitoring
- [ ] Laravel logs configured
- [ ] Error logging tested
- [ ] Log rotation configured
- [ ] Telescope enabled for debugging (optional)

### Server Monitoring
- [ ] Uptime monitoring configured (UptimeRobot, Pingdom, etc.)
- [ ] Server resource monitoring (optional)
- [ ] Alert notifications configured

### Performance Monitoring
- [ ] Application performance baseline established
- [ ] Database query performance monitored
- [ ] Queue job processing monitored

## Testing

### Functionality Testing
- [ ] Homepage loads correctly
- [ ] User registration works
- [ ] User login works
- [ ] Events can be created
- [ ] Sessions can be created
- [ ] Segments can be created
- [ ] Run of Show view works
- [ ] Active segment highlighting works in real-time
- [ ] Speaker management works
- [ ] Content upload works
- [ ] File downloads work

### Performance Testing
- [ ] Page load times acceptable (<2s)
- [ ] Database queries optimized
- [ ] Redis caching working
- [ ] Queue jobs processing

### Security Testing
- [ ] HTTPS working
- [ ] SQL injection protection verified
- [ ] XSS protection verified
- [ ] CSRF protection verified
- [ ] File upload restrictions working

### Cross-Browser Testing
- [ ] Chrome/Edge tested
- [ ] Firefox tested
- [ ] Safari tested
- [ ] Mobile browsers tested

## Documentation

### Technical Documentation
- [ ] Deployment guide reviewed
- [ ] Server credentials documented
- [ ] Database credentials documented
- [ ] API keys documented (if applicable)
- [ ] Third-party service credentials documented

### User Documentation
- [ ] User guide available
- [ ] Admin guide available
- [ ] Feature documentation up to date

## Post-Deployment

### Immediate Actions
- [ ] Application accessible at production URL
- [ ] SSL certificate valid and working
- [ ] All core features tested
- [ ] Admin user can log in
- [ ] Email sending tested

### Within 24 Hours
- [ ] Monitor error logs
- [ ] Monitor server resources
- [ ] Verify backups completed
- [ ] Test queue job processing
- [ ] Verify cron jobs running

### Within 1 Week
- [ ] User feedback collected
- [ ] Performance metrics reviewed
- [ ] Security scan performed
- [ ] Backup restoration tested
- [ ] Documentation updated

## Rollback Plan

### If Issues Occur
- [ ] Rollback procedure documented
- [ ] Previous version backed up
- [ ] Database backup available
- [ ] DNS changes reversible
- [ ] Downtime communication plan ready

## Sign-Off

### Deployment Team
- [ ] Developer sign-off
- [ ] System administrator sign-off
- [ ] Project manager sign-off

### Stakeholders
- [ ] Client/stakeholder notified
- [ ] Go-live date confirmed
- [ ] Support plan communicated

---

## Quick Command Reference

### Deploy Updates
```bash
cd /var/www/plannr
git pull
composer install --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
sudo supervisorctl restart plannr-worker:*
```

### Check Status
```bash
# Application
php artisan about

# Services
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mysql
sudo systemctl status redis-server
sudo supervisorctl status

# Logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Emergency Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo supervisorctl restart all

# Check disk space
df -h

# Check memory
free -h
```

---

**Checklist Version:** 1.0  
**Last Updated:** 2025  
**Application:** PLANNR
