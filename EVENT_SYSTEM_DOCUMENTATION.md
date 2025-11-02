# Event Management System Documentation

## Overview
A complete event management system has been successfully integrated into your Laravel application. This system allows authenticated users to create, edit, view, and delete events with comprehensive features including date/time management, timezone support, and a flexible tagging system.

## Features Implemented

### Event Properties
Each event includes the following fields:

1. **Name** (required) - The title of the event
2. **Description** (optional) - Detailed description of the event
3. **Start Date & Time** (required) - When the event begins, with date picker
4. **End Date & Time** (required) - When the event ends, with date picker
5. **Timezone** (required) - Timezone for the event (defaults to user's detected timezone)
6. **Tags** (optional) - Multiple tags can be assigned to each event
7. **Created By** - Automatically tracked user who created the event
8. **Updated By** - Automatically tracked user who last updated the event
9. **Timestamps** - Created at and updated at timestamps
10. **Soft Deletes** - Events are soft-deleted, allowing recovery if needed

### Tag System
- **Reusable Tags**: Create tags once and use them across multiple events
- **Color Coding**: Each tag has a customizable color for visual organization
- **Inline Creation**: Create new tags directly from the event form
- **Visual Display**: Tags are displayed with their colors in the event list

### User Tracking
- **Automatic Tracking**: The system automatically records who created and last updated each event
- **Display in UI**: The event list shows when events were last updated and by whom
- **Authentication Required**: All event operations require user authentication

### Soft Deletes
- **Safe Deletion**: Deleted events are marked as deleted but not removed from the database
- **Recovery Option**: Deleted events can be recovered if needed (via database or custom admin interface)
- **Data Integrity**: Maintains referential integrity while allowing data recovery

## Database Structure

### Events Table
```sql
- id (bigint, primary key)
- name (varchar)
- description (text, nullable)
- start_date (datetime)
- end_date (datetime)
- timezone (varchar, default: 'UTC')
- created_by (foreign key to users, nullable)
- updated_by (foreign key to users, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

### Tags Table
```sql
- id (bigint, primary key)
- name (varchar, unique)
- color (varchar, default: '#3b82f6')
- created_at (timestamp)
- updated_at (timestamp)
```

### Event_Tag Pivot Table
```sql
- id (bigint, primary key)
- event_id (foreign key to events)
- tag_id (foreign key to tags)
- created_at (timestamp)
- updated_at (timestamp)
- unique constraint on (event_id, tag_id)
```

## Models and Relationships

### Event Model
**Location**: `/home/ubuntu/laravel-app/app/Models/Event.php`

**Relationships**:
- `creator()` - BelongsTo User (created_by)
- `updater()` - BelongsTo User (updated_by)
- `tags()` - BelongsToMany Tag

**Features**:
- Uses `SoftDeletes` trait
- Automatic user tracking via model events
- Date casting for start_date and end_date

### Tag Model
**Location**: `/home/ubuntu/laravel-app/app/Models/Tag.php`

**Relationships**:
- `events()` - BelongsToMany Event

## Livewire Components

### Events Index Component
**Class**: `/home/ubuntu/laravel-app/app/Livewire/Events/Index.php`
**View**: `/home/ubuntu/laravel-app/resources/views/livewire/events/index.blade.php`

**Features**:
- Paginated event listing (10 per page)
- Real-time search functionality
- Display events with tags, dates, and user information
- Delete confirmation modal
- Responsive table design with dark mode support

**Public Properties**:
- `$search` - Search query string
- `$showDeleteModal` - Modal visibility state
- `$eventToDelete` - ID of event to be deleted

**Public Methods**:
- `confirmDelete($eventId)` - Show delete confirmation modal
- `deleteEvent()` - Perform soft delete
- `cancelDelete()` - Cancel delete operation

### Events Form Component
**Class**: `/home/ubuntu/laravel-app/app/Livewire/Events/Form.php`
**View**: `/home/ubuntu/laravel-app/resources/views/livewire/events/form.blade.php`

**Features**:
- Create and edit events in a single form
- Date/time pickers for start and end dates
- Timezone selector with all available timezones
- Tag selection with checkboxes
- Inline tag creation
- Automatic timezone detection from browser
- Form validation with error messages

**Public Properties**:
- `$eventId` - Event ID for editing (null for create)
- `$name` - Event name
- `$description` - Event description
- `$start_date` - Start date and time
- `$end_date` - End date and time
- `$timezone` - Event timezone
- `$selectedTags` - Array of selected tag IDs
- `$newTagName` - Name for new tag
- `$newTagColor` - Color for new tag

**Public Methods**:
- `mount($eventId = null)` - Initialize component
- `setTimezone($timezone)` - Set timezone from JavaScript
- `createTag()` - Create a new tag
- `save()` - Save or update event

## Routes

All event routes are protected by authentication middleware and require email verification.

### Available Routes
```php
GET  /events                    - List all events (events.index)
GET  /events/create             - Create new event form (events.create)
GET  /events/{eventId}/edit     - Edit event form (events.edit)
```

## User Interface

### Navigation
- **Desktop**: Events link appears in the top navigation bar
- **Mobile**: Events link appears in the responsive hamburger menu
- **Active State**: Navigation link highlights when on event pages

### Event List Page
**URL**: `/events`

**Features**:
- Search bar for filtering events by name or description
- "Create Event" button (top right)
- Responsive table showing:
  - Event name and description preview
  - Start date with timezone
  - End date
  - Tags with colors
  - Last updated time and user
  - Edit and Delete buttons
- Pagination controls
- Empty state with "Create Your First Event" button
- Success message display after create/update/delete

### Event Form Page
**URLs**: `/events/create` or `/events/{id}/edit`

**Features**:
- Event name input (required)
- Description textarea (optional)
- Start date & time picker (required)
- End date & time picker (required)
- Timezone dropdown (auto-detected, all timezones available)
- Tag selection with visual checkboxes
- Inline tag creation section with:
  - Tag name input
  - Color picker
  - "Add Tag" button
- Cancel and Save/Update buttons
- Real-time validation errors
- Success messages

## Timezone Handling

### Automatic Detection
The system automatically detects the user's timezone using JavaScript:
```javascript
const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
```

### Timezone Storage
- Timezones are stored as strings (e.g., "America/New_York", "Europe/London")
- All available PHP timezones are available in the dropdown
- Default timezone is UTC if detection fails

### Display
- Event dates are stored in the database with their specified timezone
- The timezone is displayed alongside the start date in the event list

## Validation Rules

### Event Validation
- **Name**: Required, string, max 255 characters
- **Description**: Optional, string
- **Start Date**: Required, valid date format
- **End Date**: Required, valid date format, must be after or equal to start date
- **Timezone**: Required, string
- **Selected Tags**: Optional, must be array

### Tag Validation
- **Name**: Required, string, max 255 characters, must be unique
- **Color**: Required, string (hex color format)

## Usage Examples

### Creating an Event
1. Navigate to `/events`
2. Click "Create Event" button
3. Fill in event details:
   - Name: "Annual Conference 2025"
   - Description: "Our yearly conference for all team members"
   - Start Date: Select date and time
   - End Date: Select date and time
   - Timezone: Auto-detected or manually selected
   - Tags: Select existing tags or create new ones
4. Click "Create Event"
5. Redirected to event list with success message

### Editing an Event
1. From event list, click "Edit" button on desired event
2. Modify any fields
3. Click "Update Event"
4. Redirected to event list with success message

### Deleting an Event
1. From event list, click "Delete" button
2. Confirm deletion in modal
3. Event is soft-deleted
4. Success message displayed

### Creating Tags
1. While creating/editing an event, scroll to Tags section
2. Enter tag name in "Create New Tag" section
3. Choose a color using the color picker
4. Click "Add Tag"
5. Tag is created and automatically selected for the event

## Dark Mode Support

All event management pages fully support dark mode:
- Automatic color scheme switching
- Dark-friendly form inputs
- Proper contrast for readability
- Tag colors remain visible in both modes

## Security Features

### Authentication
- All event routes require authentication
- Email verification required
- Sanctum session management

### Authorization
- Users can only access events while authenticated
- User tracking prevents anonymous modifications
- Soft deletes protect against accidental data loss

### Input Sanitization
- All inputs are validated
- XSS protection via Blade templating
- SQL injection protection via Eloquent ORM

## Future Enhancement Possibilities

### Potential Features
1. **Event Permissions**: Team-based access control
2. **Event Calendar View**: Visual calendar interface
3. **Event Reminders**: Email notifications before events
4. **Recurring Events**: Support for repeating events
5. **Event Attachments**: Upload files to events
6. **Event Comments**: Discussion threads per event
7. **Event Export**: Export to iCal, Google Calendar
8. **Advanced Search**: Filter by tags, date ranges, timezone
9. **Event Templates**: Reusable event templates
10. **Restore Deleted Events**: UI for recovering soft-deleted events

### Tag Enhancements
1. **Tag Categories**: Organize tags into categories
2. **Tag Permissions**: Team-specific tags
3. **Tag Analytics**: Most used tags, tag trends
4. **Tag Suggestions**: Auto-suggest tags based on event name/description

## Troubleshooting

### Events Not Displaying
- Ensure you're logged in
- Check database connection
- Verify migrations have run: `php artisan migrate:status`

### Timezone Not Auto-Detecting
- Ensure JavaScript is enabled in browser
- Check browser console for errors
- Manually select timezone from dropdown

### Tags Not Saving
- Check for duplicate tag names
- Verify tag color is in valid hex format
- Check validation errors in UI

### Date Picker Not Working
- Ensure browser supports `datetime-local` input type
- Use modern browser (Chrome, Firefox, Safari, Edge)
- Check for JavaScript errors

## File Locations

### Backend Files
- **Migrations**: `/home/ubuntu/laravel-app/database/migrations/`
  - `2025_10_31_163313_create_events_table.php`
  - `2025_10_31_163316_create_tags_table.php`
  - `2025_10_31_163319_create_event_tag_table.php`
- **Models**: `/home/ubuntu/laravel-app/app/Models/`
  - `Event.php`
  - `Tag.php`
- **Livewire Components**: `/home/ubuntu/laravel-app/app/Livewire/Events/`
  - `Index.php`
  - `Form.php`
- **Routes**: `/home/ubuntu/laravel-app/routes/web.php`

### Frontend Files
- **Views**: `/home/ubuntu/laravel-app/resources/views/livewire/events/`
  - `index.blade.php`
  - `form.blade.php`
- **Navigation**: `/home/ubuntu/laravel-app/resources/views/navigation-menu.blade.php`

## Testing the System

### Manual Testing Steps
1. **Start the server**: `php artisan serve`
2. **Register/Login**: Create an account or login
3. **Navigate to Events**: Click "Events" in navigation
4. **Create Event**: Test event creation with all fields
5. **Create Tags**: Test tag creation from event form
6. **Edit Event**: Test updating an existing event
7. **Search**: Test search functionality
8. **Delete**: Test delete with confirmation
9. **Dark Mode**: Toggle dark mode and verify appearance

### Database Verification
```bash
# Check events
php artisan tinker
>>> App\Models\Event::with('tags', 'creator', 'updater')->get();

# Check tags
>>> App\Models\Tag::with('events')->get();

# Check soft deletes
>>> App\Models\Event::withTrashed()->get();
```

## Maintenance

### Clearing Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuilding Assets
```bash
pnpm run build
```

### Running Migrations
```bash
php artisan migrate
```

---

**Event Management System is fully operational and ready for use!**

All features have been implemented, tested, and documented. The system integrates seamlessly with your existing Laravel Jetstream application and supports all requested functionality including date pickers, timezone detection, tag management, user tracking, and soft deletes.
