# Audit Logging System Documentation

## Overview
A comprehensive audit logging system has been successfully integrated into your Laravel application. This system automatically tracks all changes to models, recording who made the change, when it happened, what was changed, and the before/after values.

## Features

### Automatic Tracking
- **Who**: Records the user who made the change
- **When**: Timestamps every change with precise date and time
- **What**: Tracks the model type and ID that was changed
- **How**: Records the event type (created, updated, deleted, restored)
- **Changes**: Stores before and after values for all changes
- **Context**: Captures IP address and user agent for security

### Event Types
1. **Created**: When a new model is created
2. **Updated**: When an existing model is modified
3. **Deleted**: When a model is soft-deleted
4. **Restored**: When a soft-deleted model is restored

### Filtering and Search
- Filter by event type (created, updated, deleted, restored)
- Filter by model type (Event, Tag, etc.)
- Filter by user who made the change
- Search across all audit logs
- Paginated results (20 per page)

## Database Structure

### Audit Logs Table
```sql
- id (bigint, primary key)
- auditable_type (varchar) - Model class name
- auditable_id (bigint) - Model ID
- event (varchar) - created, updated, deleted, restored
- user_id (foreign key to users, nullable) - Who made the change
- old_values (json, nullable) - Before values
- new_values (json, nullable) - After values
- ip_address (varchar, nullable) - User's IP address
- user_agent (varchar, nullable) - User's browser/agent
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**:
- Composite index on (auditable_type, auditable_id)
- Index on user_id
- Index on event
- Index on created_at

## Implementation

### 1. AuditLog Model
**Location**: `/home/ubuntu/laravel-app/app/Models/AuditLog.php`

**Key Features**:
- Polymorphic relationship to any auditable model
- Belongs to User relationship
- JSON casting for old_values and new_values
- Computed attributes for description and changes

**Computed Attributes**:
- `description`: Human-readable description of the change
- `changes`: Formatted array of what changed

### 2. Auditable Trait
**Location**: `/home/ubuntu/laravel-app/app/Traits/Auditable.php`

This trait can be added to any model to enable automatic audit logging.

**How It Works**:
- Hooks into Laravel model events (created, updated, deleted, restored)
- Automatically captures old and new values
- Records user information and context
- Stores changes in the audit_logs table

**Customization**:
You can customize which attributes are audited by adding an `$auditable` property to your model:

```php
protected $auditable = ['name', 'description', 'status'];
```

By default, it audits all fillable attributes except:
- password
- remember_token
- created_at
- updated_at
- deleted_at

### 3. Livewire Component
**Class**: `/home/ubuntu/laravel-app/app/Livewire/AuditLogs/Index.php`
**View**: `/home/ubuntu/laravel-app/resources/views/livewire/audit-logs/index.blade.php`

**Features**:
- Paginated audit log listing
- Multiple filter options
- Real-time search
- Detailed view modal
- Color-coded event badges
- Responsive design with dark mode

## Usage

### Adding Audit Logging to a Model

To enable audit logging on any model, simply add the `Auditable` trait:

```php
<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'description', 'status'];
}
```

That's it! The model will now automatically log all changes.

### Customizing Auditable Attributes

If you want to audit only specific attributes:

```php
class YourModel extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'description', 'status', 'internal_notes'];
    
    // Only audit these attributes
    protected $auditable = ['name', 'description', 'status'];
    // internal_notes will not be audited
}
```

### Accessing Audit Logs for a Model

You can access audit logs for any model instance:

```php
$event = Event::find(1);
$auditLogs = $event->auditLogs; // Get all audit logs for this event
```

### Viewing Audit Logs in the UI

1. Navigate to `/audit-logs` in your application
2. Use filters to narrow down results:
   - Event type (created, updated, deleted, restored)
   - Model type (Event, Tag, etc.)
   - User who made the change
3. Search for specific changes
4. Click "View Details" to see full change information

## Currently Audited Models

The following models are currently being audited:

1. **Event** (`App\Models\Event`)
   - Tracks: name, description, start_date, end_date, timezone, created_by, updated_by
   
2. **Tag** (`App\Models\Tag`)
   - Tracks: name, color

## Audit Log Details Modal

When you click "View Details" on any audit log entry, you'll see:

### Basic Information
- Event type (created, updated, deleted, restored)
- Model type and ID
- User who made the change (name and email)
- Timestamp (formatted and relative)
- IP address
- User agent (browser information)

### Changes Section

**For Created Events**:
- Shows all initial values in green

**For Updated Events**:
- Shows field-by-field comparison
- Old values in red
- New values in green
- Only shows fields that actually changed

**For Deleted Events**:
- Shows all values at time of deletion in red

**For Restored Events**:
- Shows all values after restoration in green

## Security and Privacy

### Data Captured
- **User ID**: Links to the authenticated user
- **IP Address**: For security tracking
- **User Agent**: Browser/device information
- **Timestamps**: Precise timing of changes

### Data Protection
- Passwords are automatically excluded from auditing
- Remember tokens are excluded
- You can exclude any sensitive fields via the `$auditable` property

### Access Control
- Audit logs are only accessible to authenticated users
- Protected by Jetstream authentication middleware
- Requires email verification

## Performance Considerations

### Indexes
The audit_logs table has several indexes to ensure fast queries:
- Composite index on (auditable_type, auditable_id) for model lookups
- Index on user_id for user-based filtering
- Index on event for event type filtering
- Index on created_at for chronological sorting

### Pagination
- Results are paginated at 20 records per page
- Prevents memory issues with large datasets

### Query Optimization
- Uses eager loading for user and auditable relationships
- Efficient filtering with indexed columns

## Example Scenarios

### Scenario 1: Track Event Changes
```php
// Create an event
$event = Event::create([
    'name' => 'Annual Conference',
    'description' => 'Our yearly conference',
    'start_date' => '2025-12-01 09:00:00',
    'end_date' => '2025-12-01 17:00:00',
    'timezone' => 'America/New_York',
]);
// Audit log created: "John Doe created Event #1"

// Update the event
$event->update(['name' => 'Annual Conference 2025']);
// Audit log created: "John Doe updated Event #1"
// Changes: name: "Annual Conference" → "Annual Conference 2025"

// Delete the event
$event->delete();
// Audit log created: "John Doe deleted Event #1"

// Restore the event
$event->restore();
// Audit log created: "John Doe restored Event #1"
```

### Scenario 2: View Audit History
```php
// Get all changes to a specific event
$event = Event::find(1);
$history = $event->auditLogs()->with('user')->get();

foreach ($history as $log) {
    echo $log->description; // "John Doe updated Event #1"
    echo $log->created_at->diffForHumans(); // "2 hours ago"
    
    // See what changed
    foreach ($log->changes as $field => $change) {
        echo "{$field}: {$change['old']} → {$change['new']}";
    }
}
```

### Scenario 3: Find Who Changed Something
```php
// Find all changes by a specific user
$userId = 1;
$userChanges = AuditLog::where('user_id', $userId)
    ->with('auditable')
    ->orderBy('created_at', 'desc')
    ->get();

// Find all deletions
$deletions = AuditLog::where('event', 'deleted')
    ->with(['user', 'auditable'])
    ->get();
```

## Adding Audit Logging to New Models

When you create a new model that needs audit logging:

1. **Add the trait**:
```php
use App\Traits\Auditable;

class NewModel extends Model
{
    use Auditable;
}
```

2. **That's it!** The model will automatically:
   - Log all creates
   - Log all updates (with before/after values)
   - Log all deletes
   - Log all restores (if using SoftDeletes)

## Troubleshooting

### Audit Logs Not Being Created

**Check 1**: Ensure the model has the Auditable trait
```php
use App\Traits\Auditable;

class YourModel extends Model
{
    use Auditable; // Make sure this is present
}
```

**Check 2**: Ensure the user is authenticated
```php
// Audit logs require an authenticated user
if (auth()->check()) {
    // Changes will be logged
}
```

**Check 3**: Check database connection
```bash
php artisan migrate:status
```

### Audit Logs Not Showing in UI

**Check 1**: Verify route is accessible
```bash
php artisan route:list | grep audit
```

**Check 2**: Clear caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

**Check 3**: Rebuild assets
```bash
pnpm run build
```

### Changes Not Showing Correctly

**Check 1**: Ensure attributes are in `$fillable`
```php
protected $fillable = ['name', 'description']; // Must be fillable to be audited
```

**Check 2**: Check if attributes are excluded
```php
// These are excluded by default:
// password, remember_token, created_at, updated_at, deleted_at
```

## Best Practices

### 1. Exclude Sensitive Data
```php
class User extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'email', 'password'];
    
    // Don't audit password changes
    protected $auditable = ['name', 'email'];
}
```

### 2. Regular Cleanup
Consider implementing a cleanup strategy for old audit logs:
```php
// Delete audit logs older than 1 year
AuditLog::where('created_at', '<', now()->subYear())->delete();
```

### 3. Monitor Storage
Audit logs can grow large over time. Monitor your database size and implement archival strategies if needed.

### 4. Use Filters
When viewing audit logs, use filters to narrow down results:
- Filter by model type to see changes to specific entities
- Filter by user to track individual user actions
- Filter by event type to find specific types of changes

## Routes

```php
GET /audit-logs - View all audit logs (audit-logs.index)
```

## Navigation

The Audit Logs link appears in:
- **Desktop**: Top navigation bar
- **Mobile**: Responsive hamburger menu
- **Active State**: Highlights when viewing audit logs

## Future Enhancements

Potential features that could be added:

1. **Export Functionality**: Export audit logs to CSV/Excel
2. **Advanced Filtering**: Date range filters, multiple model types
3. **Audit Log Retention Policies**: Automatic cleanup of old logs
4. **Audit Log Archival**: Move old logs to archive storage
5. **Real-time Notifications**: Alert on specific types of changes
6. **Audit Log Comparison**: Compare two versions side-by-side
7. **Rollback Functionality**: Restore previous versions
8. **Audit Log Reports**: Generate summary reports
9. **API Access**: RESTful API for audit logs
10. **Webhook Integration**: Trigger webhooks on specific changes

## File Locations

### Backend Files
- **Migration**: `/home/ubuntu/laravel-app/database/migrations/2025_10_31_164013_create_audit_logs_table.php`
- **Model**: `/home/ubuntu/laravel-app/app/Models/AuditLog.php`
- **Trait**: `/home/ubuntu/laravel-app/app/Traits/Auditable.php`
- **Livewire Component**: `/home/ubuntu/laravel-app/app/Livewire/AuditLogs/Index.php`
- **Routes**: `/home/ubuntu/laravel-app/routes/web.php`

### Frontend Files
- **View**: `/home/ubuntu/laravel-app/resources/views/livewire/audit-logs/index.blade.php`
- **Navigation**: `/home/ubuntu/laravel-app/resources/views/navigation-menu.blade.php`

## Testing the System

### Manual Testing

1. **Start the server**: `php artisan serve`
2. **Login**: Authenticate with your account
3. **Create an event**: Go to Events → Create Event
4. **View audit logs**: Go to Audit Logs
5. **Verify the creation was logged**
6. **Edit the event**: Change some fields
7. **View audit logs**: See the update with before/after values
8. **Delete the event**: Delete it
9. **View audit logs**: See the deletion was logged

### Database Testing

```bash
php artisan tinker

# Check audit logs
>>> App\Models\AuditLog::count();
>>> App\Models\AuditLog::latest()->first();

# Check a specific model's audit logs
>>> $event = App\Models\Event::first();
>>> $event->auditLogs;
```

---

**Audit Logging System is fully operational!**

The system is now tracking all changes to Event and Tag models. You can easily add audit logging to any other model by simply adding the `Auditable` trait. The audit logs viewer provides a comprehensive interface for reviewing all changes made in your application.
