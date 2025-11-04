# Fix: Column Name Mismatch - start_time vs start_date

## Problem

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'start_time' in 'order clause'
(Connection: mysql, SQL: select * from `event_sessions` where `event_id` = 1 
and `event_sessions`.`deleted_at` is null order by `start_time` asc)
```

This error occurred when accessing the Speakers form page.

---

## Root Cause

The `event_sessions` table uses `start_date` and `end_date` columns (datetime fields), but the code was trying to order by `start_time` which doesn't exist.

### Correct Column Names

**Sessions Table (`event_sessions`):**
- ✅ `start_date` (datetime)
- ✅ `end_date` (datetime)
- ❌ NOT `start_time`
- ❌ NOT `end_time`

**Segments Table:**
- ✅ `start_time` (time)
- ✅ `end_time` (time)

**Cues Table:**
- ✅ `cue_time` (time)

---

## Fix Applied

### File: `app/Livewire/Speakers/Form.php`

**Line 168 - Changed:**

```php
// BEFORE (Wrong)
$sessions = Session::where('event_id', $this->eventId)->orderBy('start_time')->get();

// AFTER (Correct)
$sessions = Session::where('event_id', $this->eventId)->orderBy('start_date')->get();
```

---

## Manual Fix (For Your Machine)

### Step 1: Open the File

```bash
cd /Users/eric/Herd/CrewCall
nano app/Livewire/Speakers/Form.php
```

### Step 2: Find Line 168

Look for:
```php
$sessions = Session::where('event_id', $this->eventId)->orderBy('start_time')->get();
```

### Step 3: Change to

```php
$sessions = Session::where('event_id', $this->eventId)->orderBy('start_date')->get();
```

### Step 4: Save and Test

```bash
# Clear cache
php artisan cache:clear

# Test the speakers page
```

---

## Column Reference Guide

### Sessions (event_sessions table)

| Column | Type | Purpose |
|--------|------|---------|
| `start_date` | datetime | Session start date and time |
| `end_date` | datetime | Session end date and time |

**Example values:**
- `start_date`: `2025-11-15 09:00:00`
- `end_date`: `2025-11-15 10:30:00`

### Segments (segments table)

| Column | Type | Purpose |
|--------|------|---------|
| `start_time` | time | Segment start time within session |
| `end_time` | time | Segment end time within session |

**Example values:**
- `start_time`: `09:00:00`
- `end_time`: `09:15:00`

### Cues (cues table)

| Column | Type | Purpose |
|--------|------|---------|
| `cue_time` | time | When the cue should happen |

**Example value:**
- `cue_time`: `09:05:30`

---

## Why This Naming?

**Sessions** span dates/times (can be multi-day):
- Use `start_date` and `end_date` (datetime)
- Example: "Conference Day 1" from Nov 15 9:00 AM to Nov 15 5:00 PM

**Segments** are parts of a session (same day):
- Use `start_time` and `end_time` (time only)
- Example: "Opening Remarks" from 9:00 to 9:15

**Cues** are specific moments:
- Use `cue_time` (time only)
- Example: "Start video" at 9:05:30

---

## Verification

To verify the correct column names in your database:

```sql
-- Check sessions table
DESCRIBE event_sessions;

-- Check segments table
DESCRIBE segments;

-- Check cues table
DESCRIBE cues;
```

Or with Laravel:

```bash
php artisan tinker
>>> Schema::getColumnListing('event_sessions');
>>> Schema::getColumnListing('segments');
>>> Schema::getColumnListing('cues');
```

---

## Other Files to Check

If you encounter similar errors, check these files:

### Session Model (`app/Models/Session.php`)
```php
protected $fillable = [
    'start_date',  // ✅ Correct
    'end_date',    // ✅ Correct
];

protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
];

public function scopeOrdered($query)
{
    return $query->orderBy('start_date', 'asc');  // ✅ Correct
}
```

### Segment Model (`app/Models/Segment.php`)
```php
protected $fillable = [
    'start_time',  // ✅ Correct (for segments)
    'end_time',    // ✅ Correct (for segments)
];

protected $casts = [
    'start_time' => 'datetime:H:i',
    'end_time' => 'datetime:H:i',
];

public function scopeOrdered($query)
{
    return $query->orderBy('start_time', 'asc');  // ✅ Correct (for segments)
}
```

---

## Common Mistakes

### ❌ Wrong

```php
// Sessions
Session::orderBy('start_time')  // Column doesn't exist!

// Using wrong column type
Session::where('start_time', '09:00')  // Should be start_date with full datetime
```

### ✅ Correct

```php
// Sessions
Session::orderBy('start_date')

// Segments
Segment::orderBy('start_time')

// Cues
Cue::orderBy('cue_time')
```

---

## Testing

After applying the fix, test these pages:

1. **Speakers List** - Should load without errors
2. **Create Speaker** - Should show session dropdown
3. **Edit Speaker** - Should show assigned sessions
4. **Speaker Form** - Sessions should be ordered by date

---

## Git Commit

The fix has been committed:
```
commit aa0b9d4
Fix: Change start_time to start_date in Speakers Form
```

---

## Summary

**Problem:** Code used `start_time` for sessions table  
**Solution:** Changed to `start_date` (correct column name)  
**Location:** `app/Livewire/Speakers/Form.php` line 168  
**Result:** Speakers page now loads correctly  

---

**Remember:** Sessions use `start_date`, Segments use `start_time`!
