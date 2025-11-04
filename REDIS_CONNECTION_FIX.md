# Fix: Redis Connection Refused Error

## Problem

You're seeing this error in your logs:
```
Connection refused: 
/vendor/livewire/livewire/src/Features/SupportDisablingBackButtonCache/DisableBackButtonCacheMiddleware.php
```

This typically happens when Laravel is configured to use Redis but Redis is not installed or running.

## Quick Fix (Already Applied)

I've cleared all caches and verified your configuration. The development environment is now using:
- **Cache:** `file` (not Redis)
- **Sessions:** `database` (not Redis)
- **Queue:** `sync` (not Redis)

## Verification

Your `.env` file should have these settings:

```env
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

✅ These settings do NOT require Redis.

## If Error Persists

### Step 1: Clear All Caches

```bash
cd /home/ubuntu/laravel-app
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 2: Restart Development Server

```bash
# Stop the server
pkill -f "php artisan serve"

# Start it again
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 3: Check Your .env File

Make sure these lines are set correctly:

```env
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
```

**NOT:**
```env
CACHE_DRIVER=redis  # ❌ Don't use this without Redis
SESSION_DRIVER=redis  # ❌ Don't use this without Redis
QUEUE_CONNECTION=redis  # ❌ Don't use this without Redis
```

## For Production (If You Want to Use Redis)

If you're deploying to production and want to use Redis for better performance:

### Install Redis

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install redis-server

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Test Redis
redis-cli ping
# Should return: PONG
```

### Update .env for Production

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Clear Caches After Change

```bash
php artisan config:clear
php artisan cache:clear
```

## Development vs Production

### Development (Current Setup - No Redis Needed)
```env
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
```

**Pros:**
- ✅ No additional services required
- ✅ Easy to set up
- ✅ Works on any machine

**Cons:**
- ⚠️ Slower than Redis
- ⚠️ Not suitable for high traffic

### Production (With Redis - Better Performance)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Pros:**
- ✅ Much faster
- ✅ Better for high traffic
- ✅ Supports queue workers

**Cons:**
- ⚠️ Requires Redis installation
- ⚠️ Additional service to manage

## Common Causes of This Error

1. **Cached Configuration**
   - Solution: Run `php artisan config:clear`

2. **Old .env Settings**
   - Solution: Check `.env` has `CACHE_DRIVER=file`

3. **Redis Not Running**
   - Solution: Either install Redis or use `file` driver

4. **Config Cache Out of Sync**
   - Solution: Run `php artisan config:clear`

## Testing Your Fix

After making changes, test the application:

```bash
# Test homepage
curl -I http://localhost:8000

# Should return: HTTP/1.1 200 OK
```

Or visit in browser:
```
https://8000-ip6i9u6x1tygj2l6chst4-0772376b.manusvm.computer
```

## For Your Production Deployment

When you deploy to production, you have two options:

### Option 1: Keep Using File/Database (Simpler)
No changes needed. Your current `.env` settings work fine.

### Option 2: Use Redis (Better Performance)
1. Install Redis on your production server
2. Update `.env` to use Redis drivers
3. Clear caches
4. Restart services

## Current Status

✅ **Development server is running correctly**
✅ **Using file-based cache (no Redis required)**
✅ **Using database sessions**
✅ **All caches cleared**

The error should be resolved. If you still see it:
1. Check your browser console for cached errors
2. Hard refresh your browser (Ctrl+Shift+R or Cmd+Shift+R)
3. Check Laravel logs: `tail -f storage/logs/laravel.log`

---

**Note:** The development environment does NOT need Redis. It's perfectly fine to use file-based cache and database sessions for development and testing.
