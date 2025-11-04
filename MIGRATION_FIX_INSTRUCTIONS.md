# Migration Fix Instructions

## Problem

The Run of Show migrations have incorrect foreign key references. They reference `sessions` table instead of `event_sessions` table, causing this error:

```
SQLSTATE[HY000]: General error: 3780 Referencing column 'session_id' and referenced column 'id' in foreign key constraint 'session_states_session_id_foreign' are incompatible.
```

## Solution

Update two migration files to explicitly reference the `event_sessions` table.

---

## File 1: user_run_of_show_preferences_table.php

**File Location:**
```
database/migrations/2025_11_01_153646_create_user_run_of_show_preferences_table.php
```

**Line to Change:** Line 17

**BEFORE:**
```php
$table->foreignId('session_id')->constrained()->onDelete('cascade');
```

**AFTER:**
```php
$table->foreignId('session_id')->constrained('event_sessions')->onDelete('cascade');
```

**Complete File (for reference):**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_run_of_show_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('event_sessions')->onDelete('cascade');  // ← CHANGED THIS LINE
            $table->json('visible_columns')->default(json_encode([
                'order',
                'name',
                'start_time',
                'end_time',
                'duration',
                'type',
                'status',
                'notes'
            ]));
            $table->timestamps();

            // Unique constraint: one preference record per user per session
            $table->unique(['user_id', 'session_id']);
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_run_of_show_preferences');
    }
};
```

---

## File 2: session_states_table.php

**File Location:**
```
database/migrations/2025_11_01_153703_create_session_states_table.php
```

**Line to Change:** Line 16

**BEFORE:**
```php
$table->foreignId('session_id')->unique()->constrained()->onDelete('cascade');
```

**AFTER:**
```php
$table->foreignId('session_id')->unique()->constrained('event_sessions')->onDelete('cascade');
```

**Complete File (for reference):**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('session_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->unique()->constrained('event_sessions')->onDelete('cascade');  // ← CHANGED THIS LINE
            $table->foreignId('active_segment_id')->nullable()->constrained('segments')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index('active_segment_id');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_states');
    }
};
```

---

## Summary of Changes

Both files need the same type of change:

1. Find the line with `->constrained()->onDelete('cascade')`
2. Change it to `->constrained('event_sessions')->onDelete('cascade')`

The key addition is specifying `'event_sessions'` as the table name inside the `constrained()` method.

---

## Why This Fix Works

- The Session model in PLANNR uses the `event_sessions` table (not `sessions`)
- Laravel's `sessions` table is for storing user session data (cookies, auth)
- The `constrained()` method without parameters tries to guess the table name
- It guesses `sessions` from `session_id`, but we need `event_sessions`
- By explicitly specifying `'event_sessions'`, we tell Laravel the correct table to reference

---

## After Making Changes

Once you've updated both files, run:

```bash
# If you haven't run migrations yet:
php artisan migrate

# If you already tried and got the error:
php artisan migrate:rollback
php artisan migrate

# If rollback doesn't work:
php artisan tinker
>>> Schema::dropIfExists('session_states');
>>> Schema::dropIfExists('user_run_of_show_preferences');
>>> exit
php artisan migrate
```

---

**That's it!** Just add `'event_sessions'` to the `constrained()` method in both files.
