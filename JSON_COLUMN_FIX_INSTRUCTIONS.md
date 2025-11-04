# JSON Column Default Value Fix

## Problem

MySQL doesn't allow default values for JSON columns, causing this error:

```
SQLSTATE[42000]: Syntax error or access violation: 1101 BLOB, TEXT, GEOMETRY or JSON column 'visible_columns' can't have a default value
```

## Solution

Remove the default value from the migration and handle it in the model instead.

---

## File 1: Migration File (Database Level)

**File Location:**
```
database/migrations/2025_11_01_153646_create_user_run_of_show_preferences_table.php
```

**Lines to Change:** Lines 18-27

**BEFORE:**
```php
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
```

**AFTER:**
```php
$table->json('visible_columns')->nullable();
```

**Complete Migration File:**
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
            $table->foreignId('session_id')->constrained('event_sessions')->onDelete('cascade');
            $table->json('visible_columns')->nullable();  // ← CHANGED: Removed default value
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

## File 2: Model File (Application Level)

**File Location:**
```
app/Models/UserRunOfShowPreference.php
```

**What to Add:** After the `$casts` property (around line 20), add these two sections:

**ADD THIS CODE:**
```php
/**
 * The model's default values for attributes.
 */
protected $attributes = [
    'visible_columns' => null,
];

/**
 * Boot the model.
 */
protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (is_null($model->visible_columns)) {
            $model->visible_columns = static::defaultColumns();
        }
    });
}
```

**Complete Model File:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRunOfShowPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'visible_columns',
    ];

    protected $casts = [
        'visible_columns' => 'array',
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'visible_columns' => null,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->visible_columns)) {
                $model->visible_columns = static::defaultColumns();
            }
        });
    }

    /**
     * Get the user that owns the preference.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session that this preference is for.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the default visible columns.
     */
    public static function defaultColumns(): array
    {
        return [
            'order',
            'name',
            'start_time',
            'end_time',
            'duration',
            'type',
            'status',
            'notes',
        ];
    }

    /**
     * Get all available columns.
     */
    public static function availableColumns(): array
    {
        return [
            'order' => 'Order',
            'name' => 'Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'duration' => 'Duration',
            'type' => 'Type',
            'status' => 'Status',
            'notes' => 'Notes',
            'cues_count' => 'Cues',
            'created_by' => 'Created By',
            'updated_at' => 'Last Updated',
        ];
    }

    /**
     * Get or create preference for a user and session.
     */
    public static function getOrCreate(int $userId, int $sessionId): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $sessionId,
            ],
            [
                'visible_columns' => static::defaultColumns(),
            ]
        );
    }
}
```

---

## Summary of Changes

### Migration File
- **Remove:** The entire `->default(json_encode([...]))` part
- **Replace with:** `->nullable()`

### Model File
- **Add:** `protected $attributes` property with `visible_columns => null`
- **Add:** `boot()` method that sets default columns when creating new records

---

## Why This Fix Works

**The Problem:**
- MySQL (especially strict mode) doesn't allow default values for JSON/TEXT/BLOB columns
- The migration was trying to set a default at the database level

**The Solution:**
- Remove the default from the database schema (make it nullable)
- Handle the default value at the application level (in the model)
- When a new record is created, the `boot()` method automatically sets the default columns
- This is a Laravel best practice for handling defaults on JSON columns

---

## How It Works

1. **Database:** Column is nullable, no default value
2. **Model Creation:** When you create a new preference record:
   - The `creating` event fires
   - The `boot()` method checks if `visible_columns` is null
   - If null, it automatically sets it to `defaultColumns()`
3. **Result:** Every new record gets the default columns without database-level defaults

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
>>> Schema::dropIfExists('user_run_of_show_preferences');
>>> exit
php artisan migrate
```

---

## Testing the Fix

After migration, test that defaults work:

```bash
php artisan tinker
```

```php
// Create a preference without specifying visible_columns
$pref = App\Models\UserRunOfShowPreference::create([
    'user_id' => 1,
    'session_id' => 1,
]);

// Check that visible_columns was automatically set
dd($pref->visible_columns);
// Should output: ["order", "name", "start_time", "end_time", "duration", "type", "status", "notes"]
```

---

**That's it!** The default value is now handled in the application code instead of the database schema.
