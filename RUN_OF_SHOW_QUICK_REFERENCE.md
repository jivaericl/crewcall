# Run of Show - Quick Reference

## Access

**URL:** `/sessions/{sessionId}/run-of-show`

**Route Name:** `sessions.run-of-show`

**Navigation:** Sessions list → Blue "Run of Show" button

## Quick Actions

### Set Active Segment
```php
wire:click="setActiveSegment({{ $segmentId }})"
```

### Clear Active Segment
```php
wire:click="clearActiveSegment()"
```

### Open Column Modal
```php
wire:click="openColumnModal()"
```

### Save Preferences
```php
wire:click="saveColumnPreferences()"
```

## Model Methods

### Get Active Segment
```php
$activeSegmentId = SessionState::getActiveSegmentId($sessionId);
```

### Set Active Segment
```php
SessionState::setActiveSegment($sessionId, $segmentId, auth()->id());
```

### Clear Active
```php
SessionState::clearActiveSegment($sessionId);
```

### Get User Preferences
```php
$preference = UserRunOfShowPreference::getOrCreate(auth()->id(), $sessionId);
$columns = $preference->visible_columns;
```

## Available Columns

```php
[
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
]
```

## Default Columns

```php
[
    'order',
    'name',
    'start_time',
    'end_time',
    'duration',
    'type',
    'status',
    'notes',
]
```

## Blade Snippets

### Check if Column is Visible
```blade
@if($this->isColumnVisible('column_name'))
    <!-- Column content -->
@endif
```

### Active Segment Highlight
```blade
<tr class="{{ $segment->id == $activeSegmentId ? 'bg-green-100 dark:bg-green-900/30' : '' }}">
```

### Polling
```blade
<div wire:poll.2s="refreshActiveSegment">
    <!-- Content that updates every 2 seconds -->
</div>
```

## Database Queries

### Get Segments for Run of Show
```php
$segments = Segment::where('session_id', $sessionId)
    ->with(['creator', 'cues'])
    ->orderBy('order')
    ->orderBy('start_time')
    ->get();
```

### Check if Segment is Active
```php
$isActive = SessionState::where('session_id', $sessionId)
    ->where('active_segment_id', $segmentId)
    ->exists();
```

## Events

### Dispatch Segment Activated
```php
$this->dispatch('segmentActivated', [
    'sessionId' => $sessionId,
    'segmentId' => $segmentId,
]);
```

### Listen for Segment Activated
```php
#[On('segmentActivated')]
public function handleSegmentActivated($data)
{
    if ($data['sessionId'] == $this->sessionId) {
        $this->refreshActiveSegment();
    }
}
```

## CSS Classes

### Active Segment (Light Mode)
```css
bg-green-100
```

### Active Segment (Dark Mode)
```css
bg-green-900/30
```

### Table Hover
```css
hover:bg-gray-50 dark:hover:bg-gray-700
```

## Configuration

### Change Polling Interval
```blade
<!-- Change from 2s to 5s -->
<div wire:poll.5s="refreshActiveSegment">
```

### Change Highlight Color
```blade
<!-- Change from green to blue -->
<tr class="{{ $segment->id == $activeSegmentId ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
```

## Testing

### Test Real-Time Updates
1. Open Run of Show in two browser windows
2. Set active segment in one window
3. Verify it updates in the other window within 2 seconds

### Test Column Preferences
1. Toggle some columns off
2. Save preferences
3. Reload page
4. Verify columns are still hidden

### Test Dark Mode
1. Enable dark mode
2. Set active segment
3. Verify green highlight is visible

## File Locations

```
Backend:
- app/Livewire/RunOfShow/Index.php
- app/Models/SessionState.php
- app/Models/UserRunOfShowPreference.php

Frontend:
- resources/views/livewire/run-of-show/index.blade.php

Migrations:
- database/migrations/*_create_session_states_table.php
- database/migrations/*_create_user_run_of_show_preferences_table.php

Routes:
- routes/web.php (sessions.run-of-show)
```

## Common Tasks

### Add a New Column

1. Add to `availableColumns()` in model:
```php
public static function availableColumns(): array
{
    return [
        // ...existing columns
        'new_column' => 'New Column Label',
    ];
}
```

2. Add column header in view:
```blade
@if($this->isColumnVisible('new_column'))
    <th>New Column Label</th>
@endif
```

3. Add column data in view:
```blade
@if($this->isColumnVisible('new_column'))
    <td>{{ $segment->new_column }}</td>
@endif
```

### Change Default Columns

Edit `defaultColumns()` in `UserRunOfShowPreference` model:
```php
public static function defaultColumns(): array
{
    return [
        'order',
        'name',
        'start_time',
        // Add or remove columns here
    ];
}
```

### Add Custom Styling to Active Segment

Edit the table row class in the view:
```blade
<tr class="
    {{ $segment->id == $activeSegmentId ? 'bg-green-100 dark:bg-green-900/30 font-bold border-l-4 border-green-500' : '' }}
">
```

## Keyboard Shortcuts (Future)

Not yet implemented, but planned:

- `↑` / `↓` - Navigate segments
- `Space` - Set active segment
- `Esc` - Clear active segment
- `C` - Open columns modal

## Troubleshooting

### Active segment not updating
```bash
# Check Livewire is working
php artisan livewire:discover

# Clear cache
php artisan cache:clear
php artisan view:clear
```

### Preferences not saving
```bash
# Run migrations
php artisan migrate

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Highlight not visible
```bash
# Rebuild Tailwind CSS
npm run build

# Clear browser cache
Ctrl+Shift+R (Windows/Linux)
Cmd+Shift+R (Mac)
```

---

**Quick Tip:** Use `wire:poll.2s` for real-time updates without WebSockets!
