# Run of Show - Complete Documentation

## Overview

The Run of Show view is a professional show calling interface that displays all segments for a session in a customizable table format. It allows show callers to mark the currently active segment with a light green highlight that updates instantly for all users watching in real-time.

## Key Features

### 1. **Customizable Column Display**
- Per-user column preferences
- Choose which columns to show or hide
- Preferences saved to database
- Persists across sessions

### 2. **Real-Time Active Segment Highlighting**
- Show caller can mark any segment as "active"
- Active segment highlighted in light green
- Updates instantly for all connected users (2-second polling)
- Clear active segment button

### 3. **Professional Table View**
- Clean, responsive design
- Dark mode support
- Sortable by order and time
- Shows all segment details

### 4. **Available Columns**
- **Order** - Segment order number
- **Name** - Segment name
- **Start Time** - When segment starts
- **End Time** - When segment ends
- **Duration** - Length in minutes
- **Type** - Segment type
- **Status** - Pending, In Progress, Completed
- **Notes** - Segment notes/description
- **Cues** - Number of cues in segment
- **Created By** - Who created the segment
- **Last Updated** - When last modified

## Database Schema

### Tables Created

#### `user_run_of_show_preferences`
Stores per-user column preferences for each session.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users
- `session_id` - Foreign key to sessions
- `visible_columns` - JSON array of visible column names
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Unique composite index on (user_id, session_id)
- Index on user_id
- Index on session_id

#### `session_states`
Tracks the currently active segment for each session.

**Fields:**
- `id` - Primary key
- `session_id` - Foreign key to sessions (unique)
- `active_segment_id` - Foreign key to segments (nullable)
- `updated_by` - User who set the active segment
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Unique index on session_id
- Index on active_segment_id
- Index on updated_by

## Models

### UserRunOfShowPreference Model

**Location:** `app/Models/UserRunOfShowPreference.php`

**Key Methods:**
```php
// Get or create preference for a user and session
UserRunOfShowPreference::getOrCreate($userId, $sessionId)

// Get default columns
UserRunOfShowPreference::defaultColumns()

// Get all available columns
UserRunOfShowPreference::availableColumns()
```

**Relationships:**
- `user()` - Belongs to User
- `session()` - Belongs to Session

### SessionState Model

**Location:** `app/Models/SessionState.php`

**Key Methods:**
```php
// Set the active segment for a session
SessionState::setActiveSegment($sessionId, $segmentId, $userId)

// Get the active segment ID for a session
SessionState::getActiveSegmentId($sessionId)

// Clear the active segment
SessionState::clearActiveSegment($sessionId)
```

**Relationships:**
- `session()` - Belongs to Session
- `activeSegment()` - Belongs to Segment
- `updater()` - Belongs to User

## Livewire Component

### RunOfShow\Index Component

**Location:** `app/Livewire/RunOfShow/Index.php`

**Public Properties:**
- `$sessionId` - Current session ID
- `$session` - Session model instance
- `$visibleColumns` - Array of visible column names
- `$showColumnModal` - Boolean for modal visibility
- `$activeSegmentId` - Currently active segment ID

**Key Methods:**

#### `mount($sessionId)`
Initializes the component with session data and user preferences.

#### `loadUserPreferences()`
Loads the user's column preferences from the database.

#### `refreshActiveSegment()`
Refreshes the active segment ID from the database.

#### `setActiveSegment($segmentId)`
Sets a segment as active and broadcasts to all connected clients.

#### `clearActiveSegment()`
Clears the active segment and broadcasts to all clients.

#### `openColumnModal()` / `closeColumnModal()`
Controls the column customization modal.

#### `toggleColumn($column)`
Toggles a column's visibility in the preferences.

#### `saveColumnPreferences()`
Saves the current column preferences to the database.

#### `isColumnVisible($column)`
Checks if a column is currently visible.

#### `handleSegmentActivated($data)`
Listens for segment activation events from other users.

## Real-Time Updates

### How It Works

1. **Polling:** The view polls every 2 seconds using `wire:poll.2s="refreshActiveSegment"`
2. **Event Broadcasting:** When a user sets an active segment, a Livewire event is dispatched
3. **Event Listening:** All connected clients listen for the `segmentActivated` event
4. **Automatic Refresh:** When an event is received, the component refreshes the active segment

### Event: `segmentActivated`

**Payload:**
```php
[
    'sessionId' => $sessionId,
    'segmentId' => $segmentId,
]
```

**Triggered When:**
- Show caller clicks "Set Active" on a segment
- Show caller clicks "Clear Active" button

**Listeners:**
- All RunOfShow\Index components watching the same session

## User Interface

### Main View

**Route:** `/sessions/{sessionId}/run-of-show`

**Header:**
- Title: "Run of Show"
- Subtitle: Session name
- "Clear Active" button (visible when segment is active)
- "Columns" button (opens customization modal)
- "Edit Segments" button (links to segment management)

### Table

**Features:**
- Responsive design
- Horizontal scrolling for many columns
- Hover effects on rows
- Light green background for active segment
- Status badges with colors
- "Set Active" button for each segment
- "ACTIVE" badge for currently active segment

**Active Segment Highlighting:**
- Background: `bg-green-100` (light mode)
- Background: `bg-green-900/30` (dark mode)
- Automatically applied when segment is active
- Updates instantly across all users

### Column Customization Modal

**Features:**
- Grid layout of checkboxes
- All available columns listed
- Real-time toggle (no save required for preview)
- "Save Preferences" button to persist changes
- "Cancel" button to close without saving

**Behavior:**
- Checkboxes reflect current visibility
- Clicking toggles column on/off
- Changes are immediate in the modal
- Must click "Save" to persist to database
- Preferences are per-user, per-session

## Navigation

### Access Points

1. **From Sessions List:**
   - Blue "Run of Show" button (primary action)
   - First button in the actions column
   - Icon: Clipboard with checkmark

2. **From Segments List:**
   - Can navigate back to Run of Show
   - Link in breadcrumbs

3. **Direct URL:**
   - `/sessions/{sessionId}/run-of-show`

## Usage Guide

### For Show Callers

1. **Navigate to Run of Show:**
   - Go to Sessions list
   - Click the blue "Run of Show" button for your session

2. **Customize Columns (Optional):**
   - Click "Columns" button
   - Check/uncheck columns you want to see
   - Click "Save Preferences"

3. **Mark Active Segment:**
   - Find the segment that's currently happening
   - Click "Set Active" button next to it
   - The row will turn light green
   - All other users will see the highlight instantly

4. **Clear Active Segment:**
   - Click "Clear Active" button in the header
   - The highlight will be removed for all users

### For Team Members

1. **View Run of Show:**
   - Navigate to the Run of Show for your session
   - The view will automatically update every 2 seconds

2. **See Active Segment:**
   - The currently active segment is highlighted in light green
   - This is set by the show caller
   - Updates automatically without refreshing

3. **Customize Your View:**
   - Click "Columns" to choose which columns you want to see
   - Your preferences are saved and won't affect other users

## Technical Details

### Polling Interval

The view polls every 2 seconds to check for updates:
```blade
<div wire:poll.2s="refreshActiveSegment">
```

**Why 2 seconds?**
- Fast enough for real-time feel
- Doesn't overload the server
- Balances responsiveness and performance

**Can be adjusted:**
- Change `2s` to `1s` for faster updates
- Change to `5s` for less frequent updates
- Remove polling to disable automatic updates

### Performance Considerations

**Optimizations:**
- Eager loading of relationships (`creator`, `cues`)
- Indexed database queries
- Minimal data transfer (only segment ID)
- Efficient JSON storage for preferences

**Database Queries:**
- One query to load segments
- One query to load active segment ID
- One query to load/save preferences
- Cached per request

### Security

**Permissions:**
- Only authenticated users can access
- User must be assigned to the event
- Role-based access control applies
- User tracking for accountability

**Data Isolation:**
- Preferences are per-user
- Session state is per-session
- No cross-session data leakage

## Customization

### Default Columns

To change the default columns, edit `UserRunOfShowPreference::defaultColumns()`:

```php
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
```

### Available Columns

To add new columns, edit `UserRunOfShowPreference::availableColumns()`:

```php
public static function availableColumns(): array
{
    return [
        'order' => 'Order',
        'name' => 'Name',
        // Add new columns here
        'new_column' => 'New Column Label',
    ];
}
```

Then add the column display logic in the Blade view.

### Polling Interval

To change the polling interval, edit the Blade view:

```blade
<!-- Change 2s to desired interval -->
<div wire:poll.5s="refreshActiveSegment">
```

### Highlight Color

To change the active segment highlight color, edit the Blade view:

```blade
<!-- Change bg-green-100 to desired color -->
<tr class="{{ $segment->id == $activeSegmentId ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
```

## Troubleshooting

### Issue: Active segment not updating

**Possible Causes:**
1. Polling is disabled
2. JavaScript errors in console
3. Database connection issues

**Solutions:**
1. Check that `wire:poll.2s` is present in the view
2. Open browser console and check for errors
3. Verify database is accessible

### Issue: Column preferences not saving

**Possible Causes:**
1. Database migration not run
2. User not authenticated
3. Session ID invalid

**Solutions:**
1. Run `php artisan migrate`
2. Ensure user is logged in
3. Verify session exists

### Issue: Highlight not visible

**Possible Causes:**
1. Dark mode CSS not applied
2. Custom theme overriding colors
3. Browser cache

**Solutions:**
1. Check dark mode classes are present
2. Inspect element and verify CSS
3. Clear browser cache and reload

## API Reference

### Component Methods

```php
// Public methods available via wire:click

wire:click="setActiveSegment($segmentId)"
// Sets the active segment and broadcasts to all users

wire:click="clearActiveSegment()"
// Clears the active segment

wire:click="openColumnModal()"
// Opens the column customization modal

wire:click="closeColumnModal()"
// Closes the modal

wire:click="toggleColumn('column_name')"
// Toggles a column's visibility

wire:click="saveColumnPreferences()"
// Saves current preferences to database
```

### Model Methods

```php
// UserRunOfShowPreference

UserRunOfShowPreference::getOrCreate($userId, $sessionId)
// Returns: UserRunOfShowPreference instance

UserRunOfShowPreference::defaultColumns()
// Returns: array of default column names

UserRunOfShowPreference::availableColumns()
// Returns: associative array of column_key => Column Label

// SessionState

SessionState::setActiveSegment($sessionId, $segmentId, $userId)
// Returns: SessionState instance

SessionState::getActiveSegmentId($sessionId)
// Returns: int|null (segment ID or null)

SessionState::clearActiveSegment($sessionId)
// Returns: void
```

## Testing Checklist

- [ ] Create a session with multiple segments
- [ ] Navigate to Run of Show
- [ ] Verify all default columns are visible
- [ ] Click "Columns" and toggle some columns off
- [ ] Save preferences and verify they persist
- [ ] Reload page and verify preferences are still applied
- [ ] Click "Set Active" on a segment
- [ ] Verify segment highlights in light green
- [ ] Open Run of Show in another browser/tab
- [ ] Verify active segment is highlighted in both
- [ ] Change active segment in one browser
- [ ] Verify it updates in the other browser within 2 seconds
- [ ] Click "Clear Active" and verify highlight is removed
- [ ] Test in dark mode
- [ ] Test on mobile device
- [ ] Test with different user accounts
- [ ] Verify each user has independent column preferences

## Future Enhancements

Potential features for future development:

1. **Keyboard Shortcuts**
   - Arrow keys to navigate segments
   - Space bar to set active
   - Escape to clear active

2. **Auto-Advance**
   - Automatically advance to next segment based on time
   - Optional feature with enable/disable toggle

3. **Segment Timer**
   - Show countdown timer for active segment
   - Visual progress bar

4. **Notes Panel**
   - Expandable notes section for active segment
   - Show full notes without truncation

5. **Cue Preview**
   - Show cues for active segment
   - Quick access to cue details

6. **Export Run of Show**
   - Export to PDF
   - Export to Excel
   - Print-friendly view

7. **WebSocket Support**
   - Replace polling with WebSockets
   - Instant updates without delay
   - Reduced server load

## Conclusion

The Run of Show view provides a professional, real-time show calling interface with customizable columns and instant updates across all users. It's designed for live event production where multiple team members need to stay synchronized on which segment is currently active.

---

**Version:** 1.0  
**Last Updated:** 2025  
**Module:** Run of Show
