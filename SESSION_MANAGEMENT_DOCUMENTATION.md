# Session Management System Documentation

## Overview

The Session Management System provides comprehensive functionality for creating and managing time-based sessions within events. Each session includes core fields plus flexible custom fields that can be defined per event.

## Features

### Core Session Fields

Every session includes the following standard fields:

- **Name** (required): The session title or name
- **Code**: Optional identifier or session code (e.g., "S01", "KEYNOTE-1")
- **Description**: Detailed information about the session
- **Location**: Physical or virtual location where the session takes place
- **Start Date & Time** (required): When the session begins
- **End Date & Time** (required): When the session ends (must be after start date)
- **Client**: Assigned client user (selected from users with "Client" role)
- **Producer**: Assigned producer (selected from event team members)

### Custom Fields System

The custom fields feature allows you to extend sessions with event-specific data:

#### Supported Field Types

1. **Text**: Single-line text input
2. **Number**: Numeric values (integers or decimals)
3. **Date**: Date picker
4. **Select (Dropdown)**: Predefined options
5. **Checkbox**: Yes/No boolean value

#### Custom Field Properties

- **Name**: Descriptive field label
- **Type**: One of the five supported types
- **Required**: Whether the field must be filled
- **Options**: Available choices (for select fields only)
- **Sort Order**: Display order in forms

### Automatic Tracking

All sessions automatically track:

- **Created By**: User who created the session
- **Updated By**: User who last modified the session
- **Created At**: Timestamp of creation
- **Updated At**: Timestamp of last modification
- **Soft Deletes**: Deleted sessions are preserved for recovery

### Audit Logging

All session changes are logged via the Auditable trait:

- Session creation, updates, and deletions
- Custom field value changes
- User assignments
- Complete before/after value comparison

## Database Structure

### Tables

#### `event_sessions`
- Stores all session data
- Foreign keys to `events`, `users` (client, producer, created_by, updated_by)
- Indexed on `event_id`, `start_date` for performance
- Soft deletes enabled

#### `custom_fields`
- Defines custom field configurations per event
- Stores field type, options, requirements
- Ordered by `sort_order`

#### `session_custom_field_values`
- Stores actual custom field values for each session
- Unique constraint on session_id + custom_field_id
- Cascade deletes with sessions and fields

## User Interface

### Session List View

**Location**: `/events/{eventId}/sessions`

Features:
- Time-sorted list of all sessions
- Search by name, code, description, or location
- Displays start/end times, client, producer
- Quick actions: Edit, Duplicate, Delete
- Pagination for large session lists
- "Manage Custom Fields" button
- "Add Session" button

### Session Form

**Location**: 
- Create: `/events/{eventId}/sessions/create`
- Edit: `/events/{eventId}/sessions/{sessionId}/edit`

Features:
- All core fields with validation
- Real-time duration calculation
- Client dropdown (filtered by Client role)
- Producer dropdown (all event team members)
- Dynamic custom fields section
- Field-specific input types
- Required field indicators
- Cancel and Save buttons

### Custom Fields Management

**Location**: `/events/{eventId}/custom-fields`

Features:
- List all custom fields for the event
- Create, edit, and delete custom fields
- Visual type badges
- Required/Optional indicators
- Option count for select fields
- Sortable field order

### Custom Field Form

**Location**:
- Create: `/events/{eventId}/custom-fields/create`
- Edit: `/events/{eventId}/custom-fields/{fieldId}/edit`

Features:
- Field name and type selection
- Dynamic options textarea (for select type)
- Required checkbox
- Sort order input
- Contextual help text

## Usage Examples

### Creating a Session

1. Navigate to an event
2. Click "Sessions" button
3. Click "Add Session"
4. Fill in required fields (Name, Start/End dates)
5. Optionally assign Client and Producer
6. Fill in any required custom fields
7. Click "Create Session"

### Adding Custom Fields

**Example: Continuing Education Credits**

1. Go to event sessions page
2. Click "Manage Custom Fields"
3. Click "Add Custom Field"
4. Enter:
   - Name: "Continuing Education Credits"
   - Type: Number
   - Required: Yes
5. Click "Create Field"

The field will now appear on all session forms for this event.

**Example: Session Category**

1. Create custom field
2. Name: "Session Category"
3. Type: Select (Dropdown)
4. Options (one per line):
   ```
   Keynote
   Workshop
   Panel Discussion
   Networking
   Break
   ```
5. Required: Yes
6. Save

### Assigning Clients and Producers

**Prerequisites**:
- Users must be assigned to the event
- Clients must have the "Client" role
- Producers can be any event team member

**Steps**:
1. Edit or create a session
2. Select from Client dropdown (shows only Client role users)
3. Select from Producer dropdown (shows all event team)
4. Save session

## API / Model Usage

### Creating a Session Programmatically

```php
use App\Models\Session;

$session = Session::create([
    'event_id' => 1,
    'name' => 'Opening Keynote',
    'code' => 'KEY-01',
    'description' => 'Welcome and opening remarks',
    'location' => 'Main Hall',
    'start_date' => '2025-11-15 09:00:00',
    'end_date' => '2025-11-15 10:00:00',
    'client_id' => 5,
    'producer_id' => 3,
]);
```

### Setting Custom Field Values

```php
// Set a custom field value
$session->setCustomFieldValue($customFieldId, 'Value');

// Get a custom field value
$value = $session->getCustomFieldValue($customFieldId);
```

### Querying Sessions

```php
// Get all sessions for an event, ordered by start date
$sessions = Session::forEvent($eventId)->ordered()->get();

// Get sessions with relationships
$sessions = Session::with(['client', 'producer', 'customFieldValues'])
    ->where('event_id', $eventId)
    ->get();

// Search sessions
$sessions = Session::where('event_id', $eventId)
    ->where('name', 'like', '%keynote%')
    ->get();
```

### Creating Custom Fields

```php
use App\Models\CustomField;

$field = CustomField::create([
    'event_id' => 1,
    'name' => 'CE Credits',
    'field_type' => 'number',
    'is_required' => true,
    'sort_order' => 0,
]);

// For select fields
$field = CustomField::create([
    'event_id' => 1,
    'name' => 'Track',
    'field_type' => 'select',
    'options' => ['Technical', 'Business', 'Design'],
    'is_required' => false,
    'sort_order' => 1,
]);
```

## Validation Rules

### Session Validation

- **name**: Required, string, 3-255 characters
- **code**: Optional, string, max 50 characters
- **description**: Optional, string, max 5000 characters
- **location**: Optional, string, max 255 characters
- **start_date**: Required, valid date
- **end_date**: Required, valid date, must be >= start_date
- **client_id**: Optional, must exist in users table
- **producer_id**: Optional, must exist in users table

### Custom Field Validation

- **name**: Required, string, max 255 characters
- **field_type**: Required, one of: text, number, date, select, checkbox
- **options**: Required for select type, one option per line
- **is_required**: Boolean
- **sort_order**: Integer, minimum 0

## Best Practices

### Custom Field Design

1. **Keep field names clear and concise**
   - Good: "CE Credits", "Session Type"
   - Avoid: "The number of continuing education credits for this session"

2. **Use appropriate field types**
   - Numbers for quantities: CE Credits, Attendee Limit
   - Select for predefined categories: Track, Level, Format
   - Text for free-form: Special Notes, Requirements
   - Checkbox for yes/no: Recorded, Requires Registration

3. **Set required fields thoughtfully**
   - Only mark as required if truly necessary
   - Consider default values for common scenarios

4. **Order fields logically**
   - Most important fields first
   - Group related fields together

### Session Management

1. **Use session codes consistently**
   - Establish a naming convention (e.g., "DAY1-S01")
   - Makes sessions easier to reference

2. **Assign clients and producers early**
   - Helps with accountability
   - Enables role-based filtering

3. **Fill in descriptions**
   - Helps team members understand session purpose
   - Useful for program guides

4. **Duplicate similar sessions**
   - Saves time when creating multiple similar sessions
   - Remember to update times and specific details

## Troubleshooting

### Custom Field Not Showing

- **Issue**: Created a custom field but it doesn't appear on session form
- **Solution**: Refresh the page or clear browser cache

### Client/Producer Dropdown Empty

- **Issue**: No users appear in client or producer dropdown
- **Solution**: 
  - Ensure users are assigned to the event
  - For clients: Verify user has "Client" role assigned
  - Check event user assignments in "Manage Users"

### End Date Validation Error

- **Issue**: "End date must be after or equal to start date"
- **Solution**: Ensure end date/time is not before start date/time

### Required Custom Field Missing

- **Issue**: Can't save session, required field error
- **Solution**: Scroll down to custom fields section and fill in all required fields (marked with *)

## Future Enhancements

Potential features for future development:

1. **Drag-and-drop session reordering**
   - Visual timeline view
   - Automatic time recalculation

2. **Session templates**
   - Save common session configurations
   - Quick-create from templates

3. **Bulk operations**
   - Import sessions from CSV
   - Export session schedule
   - Bulk edit times

4. **Session dependencies**
   - Link related sessions
   - Track session sequences

5. **Resource management**
   - Assign equipment to sessions
   - Track room availability
   - Manage presenter assignments

6. **Conflict detection**
   - Overlapping sessions in same location
   - Double-booked producers/clients

## Support

For questions or issues with the session management system:

1. Check this documentation
2. Review audit logs for change history
3. Contact your system administrator
4. Submit feedback at https://help.manus.im

---

**Last Updated**: October 31, 2025
**Version**: 1.0
