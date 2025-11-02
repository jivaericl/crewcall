# Speaker Management - Quick Reference Guide

## Routes

```php
// List all speakers for an event
route('events.speakers.index', $eventId)

// Create new speaker
route('events.speakers.create', $eventId)

// Edit speaker
route('events.speakers.edit', ['eventId' => $eventId, 'speakerId' => $speakerId])

// View speaker details
route('events.speakers.show', ['eventId' => $eventId, 'speakerId' => $speakerId])
```

## Model Usage

### Get all speakers for an event
```php
$speakers = Speaker::where('event_id', $eventId)
    ->where('is_active', true)
    ->orderBy('name')
    ->get();
```

### Get speaker with relationships
```php
$speaker = Speaker::with([
    'sessions',
    'contentFiles',
    'tags',
    'user',
    'creator',
    'updater'
])->findOrFail($speakerId);
```

### Create a speaker
```php
$speaker = Speaker::create([
    'event_id' => $eventId,
    'name' => 'John Doe',
    'title' => 'CEO',
    'company' => 'Acme Corp',
    'email' => 'john@example.com',
    'bio' => 'Biography text...',
    'is_active' => true,
]);
```

### Assign speaker to session
```php
$session->speakers()->attach($speakerId);
// or sync multiple
$session->speakers()->sync([1, 2, 3]);
```

### Assign content to speaker
```php
$contentFile->speakers()->attach($speakerId);
// or sync multiple
$contentFile->speakers()->sync([1, 2, 3]);
```

### Get sessions for a speaker
```php
$sessions = $speaker->sessions;
```

### Get content files for a speaker
```php
$contentFiles = $speaker->contentFiles;
```

## Database Queries

### Count speakers per event
```php
$count = Speaker::where('event_id', $eventId)->count();
```

### Get active speakers only
```php
$speakers = Speaker::where('event_id', $eventId)
    ->where('is_active', true)
    ->get();
```

### Search speakers
```php
$speakers = Speaker::where('event_id', $eventId)
    ->where(function($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('title', 'like', "%{$search}%")
          ->orWhere('company', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%");
    })
    ->get();
```

### Get speakers with specific tag
```php
$speakers = Speaker::whereHas('tags', function($q) use ($tagId) {
    $q->where('tags.id', $tagId);
})->get();
```

### Get speakers with user accounts
```php
$speakers = Speaker::whereNotNull('user_id')->get();
```

## Livewire Component Properties

### Index Component
```php
public $eventId;        // Current event ID
public $search = '';    // Search query
public $statusFilter = ''; // 'active' or 'inactive'
public $tagFilter = ''; // Tag ID to filter by
public $deleteId;       // Speaker ID to delete
```

### Form Component
```php
public $eventId;
public $speakerId;      // null for create, ID for edit
public $name = '';
public $title = '';
public $company = '';
public $email = '';
public $contact_person = '';
public $bio = '';
public $notes = '';
public $headshot;       // File upload
public $is_active = true;
public $create_user = false;
public $user_password = '';
public $user_password_confirmation = '';
public $selectedTags = [];
```

### Show Component
```php
public $eventId;
public $speakerId;
public $event;
public $speaker;
```

## Blade Directives

### Display speaker name with fallback
```blade
{{ $speaker->name ?? 'Unknown Speaker' }}
```

### Display full title
```blade
@if($speaker->full_title)
    <p>{{ $speaker->full_title }}</p>
@endif
```

### Display headshot with fallback
```blade
@if($speaker->headshot_url)
    <img src="{{ $speaker->headshot_url }}" alt="{{ $speaker->name }}">
@else
    <div class="avatar-placeholder">
        {{ substr($speaker->name, 0, 1) }}
    </div>
@endif
```

### Display active badge
```blade
@if($speaker->is_active)
    <span class="badge badge-success">Active</span>
@else
    <span class="badge badge-gray">Inactive</span>
@endif
```

### Display user account badge
```blade
@if($speaker->user_id)
    <span class="badge badge-blue">Has Account</span>
@endif
```

### Loop through speaker sessions
```blade
@forelse($speaker->sessions as $session)
    <div>{{ $session->name }}</div>
@empty
    <p>No sessions assigned</p>
@endforelse
```

## Validation Rules

```php
// Speaker form validation
[
    'name' => 'required|string|max:255|min:3',
    'title' => 'nullable|string|max:255',
    'company' => 'nullable|string|max:255',
    'email' => 'nullable|email|max:255',
    'contact_person' => 'nullable|string|max:255',
    'bio' => 'nullable|string|max:5000',
    'notes' => 'nullable|string|max:5000',
    'headshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    'is_active' => 'boolean',
]

// User creation validation
[
    'create_user' => 'boolean',
    'user_password' => 'required_if:create_user,true|min:8',
    'user_password_confirmation' => 'required_if:create_user,true|same:user_password',
]
```

## Common Tasks

### Add speaker navigation to a page
```blade
<flux:button href="{{ route('events.speakers.index', $eventId) }}" icon="user">
    Speakers
</flux:button>
```

### Display speaker count
```blade
{{ $event->speakers()->count() }} speakers
```

### Check if speaker has sessions
```blade
@if($speaker->sessions->count() > 0)
    <p>This speaker has {{ $speaker->sessions->count() }} sessions</p>
@endif
```

### Get speakers for a specific session
```blade
@foreach($session->speakers as $speaker)
    <div>{{ $speaker->name }}</div>
@endforeach
```

### Add comment to speaker
```blade
@livewire('comments.comment-section', [
    'commentableType' => 'App\\Models\\Speaker',
    'commentableId' => $speaker->id,
    'eventId' => $eventId
])
```

## Permissions Check

```php
// Check if user can view speakers
if (auth()->user()->can('view_speakers')) {
    // Show speakers
}

// Check if user can create speakers
if (auth()->user()->can('create_speakers')) {
    // Show create button
}

// Check if user can edit speakers
if (auth()->user()->can('edit_speakers')) {
    // Show edit button
}

// Check if user can delete speakers
if (auth()->user()->can('delete_speakers')) {
    // Show delete button
}
```

## File Paths

```
Backend:
- app/Models/Speaker.php
- app/Livewire/Speakers/Index.php
- app/Livewire/Speakers/Form.php
- app/Livewire/Speakers/Show.php

Frontend:
- resources/views/livewire/speakers/index.blade.php
- resources/views/livewire/speakers/form.blade.php
- resources/views/livewire/speakers/show.blade.php

Migrations:
- database/migrations/*_create_speakers_table.php
- database/migrations/*_create_session_speaker_table.php
- database/migrations/*_create_content_file_speaker_table.php

Storage:
- storage/app/public/speakers/{event_id}/ (headshots)
```

## Artisan Commands

```bash
# Create storage symlink (required for headshots)
php artisan storage:link

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate Livewire component (if needed)
php artisan make:livewire Speakers/ComponentName
```

## Testing URLs

```
List: http://localhost:8000/events/1/speakers
Create: http://localhost:8000/events/1/speakers/create
Edit: http://localhost:8000/events/1/speakers/1/edit
Show: http://localhost:8000/events/1/speakers/1
```

## Database Tables

```sql
-- Main table
speakers

-- Pivot tables
session_speaker
content_file_speaker

-- Related tables (existing)
events
users
sessions
content_files
tags
taggables
comments
audit_logs
```

## Key Features Checklist

- [x] CRUD operations (Create, Read, Update, Delete)
- [x] Soft deletes
- [x] Search and filtering
- [x] Pagination
- [x] Headshot upload
- [x] User account creation
- [x] Session assignment
- [x] Content file assignment
- [x] Tag categorization
- [x] Comments and @mentions
- [x] Audit logging
- [x] User tracking (created_by, updated_by)
- [x] Dark mode support
- [x] Mobile responsive
- [x] Validation
- [x] Error handling

---

**Quick Tip:** Use `php artisan tinker` to interact with Speaker model directly for testing.
