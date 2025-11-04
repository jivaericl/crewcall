# Troubleshooting: Event Creation Not Working

## Problem

When trying to create an event, clicking the "Save" or "Create Event" button does nothing - no error, no success message, no redirect.

## Common Causes

1. **JavaScript not loaded** - Livewire scripts missing
2. **Form validation failing silently** - Required fields not filled
3. **Browser console errors** - JavaScript errors preventing submission
4. **CSRF token issues** - Security token expired or missing
5. **Database connection issues** - Can't save to database

---

## Troubleshooting Steps

### Step 1: Check Browser Console

**Open browser developer tools:**
- Chrome/Edge: Press `F12` or `Ctrl+Shift+I` (Windows) / `Cmd+Option+I` (Mac)
- Firefox: Press `F12` or `Ctrl+Shift+K` (Windows) / `Cmd+Option+K` (Mac)

**Look for errors in the Console tab:**

Common errors and solutions:

#### Error: "Livewire is not defined"
**Solution:** Livewire scripts not loaded
```blade
<!-- Check that layouts/app.blade.php has these: -->
@livewireStyles
@livewireScripts
```

#### Error: "419 Page Expired" or "CSRF token mismatch"
**Solution:** Refresh the page to get a new CSRF token
```bash
# Or clear application cache
php artisan cache:clear
```

#### Error: "fetch failed" or "NetworkError"
**Solution:** Development server might be down
```bash
# Restart the server
pkill -f "php artisan serve"
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 2: Check Required Fields

The form requires these fields to be filled:
- ✅ **Event Name** (minimum 3 characters)
- ✅ **Start Date**
- ✅ **End Date** (must be after or equal to start date)
- ✅ **Timezone**

**Try filling all fields before clicking Save.**

### Step 3: Check Laravel Logs

```bash
cd /home/ubuntu/laravel-app
tail -f storage/logs/laravel.log
```

Then try creating an event again and watch for errors.

Common log errors:

#### "SQLSTATE[HY000]: General error"
**Solution:** Database migration issue
```bash
php artisan migrate:fresh
# Warning: This deletes all data!
```

#### "Class 'App\Models\Event' not found"
**Solution:** Autoload issue
```bash
composer dump-autoload
```

### Step 4: Test Event Creation Manually

Test if event creation works at all:

```bash
cd /home/ubuntu/laravel-app
php artisan tinker
```

Then in Tinker:
```php
$user = App\Models\User::first();
auth()->login($user);

$event = App\Models\Event::create([
    'name' => 'Test Event',
    'description' => 'Test',
    'start_date' => now(),
    'end_date' => now()->addDay(),
    'timezone' => 'UTC',
]);

echo "Event created: " . $event->id;
exit
```

If this works, the problem is in the frontend/Livewire, not the model.

### Step 5: Check Livewire Component

Verify the Events Form component exists:

```bash
ls -la app/Livewire/Events/Form.php
```

Should show the file exists.

### Step 6: Clear All Caches

```bash
cd /home/ubuntu/laravel-app
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

Then restart the development server:
```bash
pkill -f "php artisan serve"
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 7: Check Network Tab

In browser developer tools, go to the **Network** tab:

1. Keep it open
2. Try creating an event
3. Look for requests to `/livewire/update`

**If you see a request:**
- Check the response status code
- 200 = Success (but maybe redirect not working)
- 419 = CSRF token issue
- 422 = Validation error
- 500 = Server error

**If you don't see any request:**
- JavaScript is not working
- Form submission is blocked
- Livewire not initialized

---

## Quick Fixes

### Fix 1: Hard Refresh Browser

Sometimes cached JavaScript causes issues:
- Windows/Linux: `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`

### Fix 2: Check Layout File

Verify `resources/views/layouts/app.blade.php` has Livewire scripts:

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- ... other head content ... -->
    @livewireStyles
</head>
<body>
    <!-- ... body content ... -->
    
    @livewireScripts
</body>
</html>
```

### Fix 3: Verify Route Exists

```bash
php artisan route:list | grep events
```

Should show:
```
POST   events/store  ... events.store
GET    events        ... events.index
```

### Fix 4: Check Database Connection

```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected!';"
```

Should output: "Database connected!"

---

## Testing Checklist

After applying fixes, test these:

- [ ] Browser console shows no errors
- [ ] All required fields are filled
- [ ] Click "Save" or "Create Event" button
- [ ] Network tab shows `/livewire/update` request
- [ ] Response status is 200
- [ ] Page redirects to events list
- [ ] Success message appears
- [ ] Event appears in the list

---

## Still Not Working?

### Check These Files

1. **Form Component:** `app/Livewire/Events/Form.php`
   - Has `save()` method
   - Validation rules correct
   - Returns redirect

2. **Form View:** `resources/views/livewire/events/form.blade.php`
   - Has `wire:submit.prevent="save"`
   - Submit button has `type="submit"`
   - All inputs have `wire:model`

3. **Event Model:** `app/Models/Event.php`
   - Has correct `$fillable` fields
   - Boot method sets `created_by`

4. **Routes:** `routes/web.php`
   - Events routes exist
   - Protected by auth middleware

### Enable Debug Mode

Edit `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Then try again and check for detailed error messages.

### Check Livewire Configuration

```bash
cat config/livewire.php | grep layout
```

Should show:
```
'layout' => 'layouts.app',
```

---

## Common Solutions Summary

| Problem | Solution |
|---------|----------|
| No error, nothing happens | Check browser console for JavaScript errors |
| 419 Page Expired | Refresh page or clear cache |
| Validation errors not showing | Check `@error` directives in form |
| Form submits but doesn't save | Check Laravel logs for database errors |
| Page doesn't redirect | Check `save()` method returns redirect |
| Success message not showing | Check session flash in index view |

---

## Need More Help?

1. **Check browser console** (most important!)
2. **Check Laravel logs** (`storage/logs/laravel.log`)
3. **Test with Tinker** (verify model works)
4. **Check Network tab** (verify request is sent)
5. **Enable debug mode** (see detailed errors)

---

**Most likely cause:** JavaScript error in browser console. Check there first!
