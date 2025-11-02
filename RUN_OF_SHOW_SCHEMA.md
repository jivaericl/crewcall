# Run of Show - Database Schema Design

## Overview

The Run of Show view requires two new database tables to support:
1. Per-user column preferences
2. Real-time active segment tracking

## Tables

### 1. `user_run_of_show_preferences` Table

Stores which columns each user wants to see in the Run of Show table.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users table
- `session_id` - Foreign key to sessions table (preferences per session)
- `visible_columns` - JSON array of column names to display
- `created_at`, `updated_at` - Timestamps

**Default Columns:**
```json
[
    "order",
    "name",
    "start_time",
    "end_time",
    "duration",
    "type",
    "status",
    "notes"
]
```

**Available Columns:**
- order - Segment order number
- name - Segment name
- start_time - Start time
- end_time - End time
- duration - Duration
- type - Segment type
- status - Status (pending, in_progress, completed)
- notes - Notes/description
- cues_count - Number of cues
- created_by - Creator name
- updated_at - Last updated

**Indexes:**
- `user_id, session_id` (unique composite)

### 2. `session_states` Table

Tracks the current active segment for each session in real-time.

**Fields:**
- `id` - Primary key
- `session_id` - Foreign key to sessions table (unique)
- `active_segment_id` - Foreign key to segments table (nullable)
- `updated_by` - User who set the active segment
- `updated_at` - When it was set active
- `created_at` - Timestamp

**Indexes:**
- `session_id` (unique)
- `active_segment_id`

**Purpose:**
- Single source of truth for which segment is currently active
- All users watching the same session see the same active segment
- Updates trigger Livewire events to refresh all connected clients

## Livewire Events

### Broadcasting Events

**Event: `segment-activated`**
- Payload: `{ sessionId, segmentId, updatedBy }`
- Triggers: When show caller marks a segment as active
- Listeners: All Run of Show components watching that session

**Event: `segment-completed`**
- Payload: `{ sessionId, segmentId }`
- Triggers: When show caller marks segment as completed
- Listeners: All Run of Show components watching that session

## Real-Time Update Flow

1. Show caller clicks "Set Active" on a segment
2. Backend updates `session_states` table
3. Livewire dispatches `segment-activated` event
4. All connected clients receive event via Livewire polling
5. Each client's component refreshes the active segment highlight
6. Light green highlight appears instantly on all screens

## Column Preference Flow

1. User clicks "Columns" button in Run of Show view
2. Modal shows checkboxes for all available columns
3. User toggles columns on/off
4. Preferences saved to `user_run_of_show_preferences` table
5. Table refreshes with new column configuration
6. Preferences persist across sessions

## Migration Order

1. Create `user_run_of_show_preferences` table
2. Create `session_states` table
3. Add indexes for performance

## Model Relationships

### User Model
```php
public function runOfShowPreferences()
{
    return $this->hasMany(UserRunOfShowPreference::class);
}
```

### Session Model
```php
public function state()
{
    return $this->hasOne(SessionState::class);
}

public function activeSegment()
{
    return $this->hasOneThrough(Segment::class, SessionState::class, 'session_id', 'id', 'id', 'active_segment_id');
}
```

### Segment Model
```php
public function isActive()
{
    return SessionState::where('active_segment_id', $this->id)->exists();
}
```

## Performance Considerations

- Index on `session_id` for fast lookups
- Index on `active_segment_id` for quick active segment checks
- JSON column for flexible preference storage
- Livewire polling interval: 2 seconds (configurable)
- Cache active segment per session to reduce queries

## Security

- Only users with `manage_show_call` permission can set active segment
- All users with `view_show_call` permission can see Run of Show
- User preferences are isolated per user
- Session state changes are logged in audit_logs

---

**Status:** Design Complete
**Next Step:** Create migrations
