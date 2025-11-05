# PLANNR - Quick Reference Guide

## 🚀 Quick Start (5 Minutes)

```bash
# 1. Extract
tar -xzf plannr-production-YYYYMMDD-HHMMSS.tar.gz && cd laravel-app

# 2. Install
composer install --no-dev --optimize-autoloader
pnpm install && pnpm run build

# 3. Configure
cp .env.example .env
# Edit .env with your database credentials
php artisan key:generate

# 4. Database
php artisan migrate
php artisan db:seed --class=RolesSeeder  # CRITICAL!

# 5. Optimize
php artisan optimize
chmod -R 775 storage bootstrap/cache
```

---

## 🐛 Common Issues & Solutions

### Issue: Event creation fails
**Error**: Foreign key constraint violation on event_user

**Solution**: Seed roles first!
```bash
php artisan db:seed --class=RolesSeeder
```

---

### Issue: Lists are empty (content/speakers/sessions)
**Cause**: User not assigned to event

**Solution**: Already fixed! EventScoped now includes created events.

**Verify**:
```sql
SELECT * FROM event_user WHERE user_id = YOUR_USER_ID;
```

---

### Issue: Audit logs error
**Error**: Undefined method event()

**Solution**: Already fixed in Segment/Cue models.

---

### Issue: Run of Show error
**Error**: Unknown column 'order'

**Solution**: Already fixed - uses 'sort_order' now.

---

### Issue: Speaker view button error
**Error**: Route [sessions.edit] not defined

**Solution**: Already fixed - uses 'events.sessions.edit'.

---

## 📋 Testing Checklist

```bash
# Quick smoke test
php artisan tinker

# Test 1: Create user
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);

# Test 2: Login
auth()->login($user);

# Test 3: Create event
$event = Event::create([
    'name' => 'Test Event',
    'start_date' => now(),
    'end_date' => now()->addDays(1),
]);

# Test 4: Verify auto-assignment worked
$event->assignedUsers()->count(); // Should be 1

# Test 5: Create speaker
$speaker = Speaker::create([
    'event_id' => $event->id,
    'first_name' => 'John',
    'last_name' => 'Doe',
]);

# Test 6: Verify name auto-populated
$speaker->name; // Should be "John Doe"

# All tests passed? You're good to go! ✅
```

---

## 🔑 Key Files Modified

**Models**:
- `app/Models/Speaker.php` - First/last name, full_name accessor
- `app/Models/Segment.php` - Added event() relationship
- `app/Models/Cue.php` - Removed EventScoped
- `app/Models/Contact.php` - Fixed type → contact_type
- `app/Models/EventUser.php` - NEW pivot model
- `app/Models/User.php` - Added eventUsers() relationship

**Livewire**:
- `app/Livewire/Events/Form.php` - Auto-assign creator
- `app/Livewire/Speakers/Form.php` - First/last name, autosuggest
- `app/Livewire/Contacts/Index.php` - Fixed type filter
- `app/Livewire/AuditLogs/Index.php` - Optional eventId
- `app/Livewire/RunOfShow/Index.php` - Fixed orderBy

**Views**:
- `resources/views/livewire/speakers/form.blade.php` - First/last name inputs, autosuggest
- `resources/views/livewire/speakers/show.blade.php` - Fixed route
- `resources/views/livewire/sessions/form.blade.php` - Fixed custom fields
- `resources/views/livewire/audit-logs/index.blade.php` - Fixed event filter

**Traits**:
- `app/Traits/EventScoped.php` - Include created events, qualify created_by

**Database**:
- `database/seeders/RolesSeeder.php` - NEW seeder

**Routes**:
- `routes/web.php` - Fixed dashboard, enabled tags

---

## 📊 What Was Fixed

| Issue | Status | Files Changed |
|-------|--------|---------------|
| Event creation error | ✅ Fixed | Events/Form.php |
| Contact creation error | ✅ Fixed | Contact.php, Contacts/Index.php |
| Lists empty | ✅ Fixed | EventScoped.php, Events/Form.php |
| User creation 404 | ✅ Fixed | web.php |
| Audit logs error | ✅ Fixed | AuditLogs/Index.php, index.blade.php |
| Custom fields broken | ✅ Fixed | sessions/form.blade.php |
| Tags missing | ✅ Fixed | web.php |
| Speaker name split | ✅ Fixed | Speaker.php, Speakers/Form.php, form.blade.php |
| Speaker sessions | ✅ Fixed | speakers/form.blade.php |
| Speaker view button | ✅ Fixed | speakers/show.blade.php |
| Speaker autosuggest | ✅ Fixed | Speakers/Form.php, form.blade.php |
| Segment event() | ✅ Fixed | Segment.php |
| Cue EventScoped | ✅ Fixed | Cue.php |
| EventUser missing | ✅ Fixed | EventUser.php (NEW) |
| User eventUsers() | ✅ Fixed | User.php |
| RunOfShow order | ✅ Fixed | RunOfShow/Index.php |

**Total**: 16 issues fixed across 20+ files

---

## 🎯 Production Checklist

Before going live:

- [ ] Database credentials in .env
- [ ] APP_ENV=production in .env
- [ ] APP_DEBUG=false in .env
- [ ] Run migrations
- [ ] **Seed roles** (critical!)
- [ ] Set file permissions
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL certificate
- [ ] Configure queue worker (if using)
- [ ] Set up scheduled tasks (if needed)
- [ ] Test all major features
- [ ] Create first super admin user
- [ ] Backup database

---

## 🔧 Useful Commands

```bash
# Clear everything
php artisan optimize:clear

# Rebuild everything
php artisan optimize

# Check routes
php artisan route:list | grep events

# Check migrations
php artisan migrate:status

# Check roles
php artisan tinker --execute="App\Models\Role::all()"

# Create super admin
php artisan tinker --execute="
\$user = App\Models\User::find(1);
\$user->is_super_admin = true;
\$user->save();
"
```

---

## 📞 Emergency Fixes

### If something breaks:

1. **Check logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Clear caches**:
   ```bash
   php artisan optimize:clear
   ```

3. **Check database**:
   ```sql
   -- Verify roles exist
   SELECT * FROM roles;
   
   -- Verify user assignments
   SELECT * FROM event_user;
   
   -- Check event creator
   SELECT id, name, created_by FROM events;
   ```

4. **Re-seed roles if needed**:
   ```bash
   php artisan db:seed --class=RolesSeeder
   ```

---

**Status**: ✅ Production Ready  
**Package Date**: November 4, 2025  
**Total Fixes**: 16 issues resolved
