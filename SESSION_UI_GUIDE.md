# Session Management User Interface Guide

## Overview

The session management interface is fully built and integrated into your Laravel application. This guide provides a complete walkthrough of all available features and how to use them.

## 🎯 Accessing Sessions

### From Events List

1. Navigate to **Events** in the main menu
2. Find the event you want to manage
3. Click the **clock icon** (Sessions button) in the actions column
4. You'll be taken to the sessions list for that event

### Direct URL

- Sessions List: `/events/{eventId}/sessions`
- Create Session: `/events/{eventId}/sessions/create`
- Edit Session: `/events/{eventId}/sessions/{sessionId}/edit`

## 📋 Sessions List Interface

### Layout

The sessions list page displays:

**Header Section**
- Event name and breadcrumb navigation
- "Back to Events" button
- "Add Session" button (primary action)
- "Manage Custom Fields" button

**Search and Filters**
- Search bar for finding sessions by name, code, description, or location
- Real-time search with debouncing (300ms delay)

**Sessions Table**
Columns displayed:
1. **Date/Time**: Start date and time range
2. **Session**: Name, code (badge), description, and tags
3. **Location**: Where the session takes place
4. **Client**: Assigned client user
5. **Producer**: Assigned producer
6. **Actions**: Edit, Duplicate, Delete buttons

**Pagination**
- 15 sessions per page
- Next/Previous navigation
- Page numbers

### Visual Features

**Session Display**
- Start date shown prominently (e.g., "Oct 31")
- Time range below date (e.g., "9:00 AM - 10:30 AM")
- Session name in bold
- Code displayed as a gray badge
- Description truncated to 60 characters
- Tags shown as color-coded badges

**Action Buttons**
- Edit (pencil icon) - Opens edit form
- Duplicate (copy icon) - Creates a copy of the session
- Delete (trash icon) - Shows confirmation modal

**Empty State**
- Displays when no sessions exist
- Helpful message encouraging session creation
- "Add Session" button

## ➕ Creating a Session

### Step-by-Step

1. **Navigate to Sessions**
   - From events list, click the clock icon for your event

2. **Click "Add Session"**
   - Button is in the top-right corner of the page

3. **Fill in Core Fields**

   **Required Fields** (marked with *)
   - **Session Name**: Descriptive title (3-255 characters)
   - **Start Date & Time**: When the session begins
   - **End Date & Time**: When the session ends (must be >= start date)

   **Optional Fields**
   - **Code**: Session identifier (e.g., "S01", "KEYNOTE-1")
   - **Location**: Physical or virtual location
   - **Client**: Select from users with Client role
   - **Producer**: Select from event team members
   - **Description**: Detailed information (up to 5000 characters)

4. **Select Tags**
   - Check boxes for relevant tags
   - Tags display with color-coded badges
   - Maximum 10 tags per session

5. **Fill Custom Fields** (if configured)
   - Any custom fields defined for the event appear here
   - Required custom fields marked with *
   - Field types: text, number, date, select, checkbox

6. **Save**
   - Click "Create Session" button
   - Success message appears
   - Redirected to sessions list

### Features While Creating

**Duration Calculator**
- Automatically calculates session length
- Updates in real-time as you change dates
- Displays in format: "X days, Y hours, Z minutes"

**Default Dates**
- Start date defaults to event start date
- End date defaults to 1 hour after start
- Saves time when creating multiple sessions

**Real-time Validation**
- Fields validate as you type
- Error messages appear immediately
- Clear, helpful error descriptions

## ✏️ Editing a Session

### How to Edit

1. **From Sessions List**
   - Click the edit (pencil) icon next to any session

2. **Make Changes**
   - All fields are editable
   - Previously selected tags are pre-checked
   - Custom field values are pre-filled

3. **Save Changes**
   - Click "Update Session" button
   - Changes are saved
   - Audit log entry created automatically

### What's Tracked

When you edit a session, the system automatically tracks:
- Who made the change (your user account)
- When the change was made (timestamp)
- What was changed (before/after values)
- Where the change came from (IP address, browser)

View this information in **Audit Logs**.

## 📋 Duplicating a Session

### Purpose

Quickly create a copy of an existing session with all its settings.

### How to Duplicate

1. Click the duplicate (copy) icon next to any session
2. A new session is created with:
   - Same name + " (Copy)" suffix
   - Same dates and times
   - Same location
   - Same client and producer
   - Same description
   - Same tags
   - Same custom field values

3. Edit the duplicated session to adjust details

### Use Cases

- Creating multiple similar sessions
- Repeating sessions across different days
- Templates for common session types

## 🗑️ Deleting a Session

### Soft Delete Process

1. Click the delete (trash) icon
2. Confirmation modal appears
3. Confirm deletion
4. Session is soft-deleted (not permanently removed)

### What Happens

- Session disappears from the list
- `deleted_at` timestamp is set in database
- All data is preserved (tags, custom fields, audit logs)
- Can be restored by administrators if needed

### Safety Features

- Confirmation modal prevents accidental deletion
- Soft delete allows recovery
- Audit log records who deleted and when
- Related data is preserved

## 🏷️ Managing Tags

### Selecting Tags

**In Session Form**
- Tags section shows all available tags
- Checkboxes with color-coded labels
- Check multiple tags (max 10)
- Tags are saved with the session

**In Session List**
- Tags display as color-coded badges
- Appear below session description
- Visual categorization at a glance

### Creating New Tags

Tags are managed centrally (not within sessions):
1. Tags can be created when managing events
2. Once created, tags are available for all events and sessions
3. Same tag system across the application

## 🔧 Managing Custom Fields

### Accessing Custom Fields

From the sessions list page:
1. Click "Manage Custom Fields" button
2. View all custom fields for this event
3. Create, edit, or delete fields

### Custom Field Types

**Text**: Single-line text input
- Use for: Names, short descriptions, identifiers

**Number**: Numeric values (integers or decimals)
- Use for: CE Credits, attendee limits, scores

**Date**: Date picker
- Use for: Deadlines, milestones, registration dates

**Select (Dropdown)**: Predefined options
- Use for: Categories, tracks, levels, formats

**Checkbox**: Yes/No boolean
- Use for: Recorded, requires registration, approved

### Creating Custom Fields

1. Click "Add Custom Field"
2. Enter field details:
   - **Name**: Descriptive label
   - **Type**: Choose from 5 types
   - **Required**: Check if mandatory
   - **Options**: For select type, one per line
   - **Sort Order**: Display order (0 = first)
3. Save field
4. Field appears on all session forms

### Example Custom Fields

**Continuing Education Credits**
- Type: Number
- Required: Yes
- Use: Track CE credits for professional development

**Session Track**
- Type: Select
- Options: Technical, Business, Design, Leadership
- Required: Yes
- Use: Categorize sessions by track

**Recording Available**
- Type: Checkbox
- Required: No
- Use: Indicate if session will be recorded

## 🔍 Search and Filter

### Search Functionality

**Search Bar**
- Searches across: Name, Code, Description, Location
- Real-time search (updates as you type)
- Debounced (300ms delay for performance)
- Case-insensitive

**Search Tips**
- Use partial matches: "key" finds "Keynote"
- Search by code: "S01" finds sessions with that code
- Search by location: "Hall" finds "Main Hall", "Hall A", etc.

### Current Limitations

The following filters are not yet implemented but could be added:
- Filter by date range
- Filter by client or producer
- Filter by tags
- Filter by custom field values

## 📱 Responsive Design

### Desktop View
- Full table layout
- All columns visible
- Side-by-side action buttons
- Optimal for detailed management

### Mobile View
- Stacked layout
- Essential information prioritized
- Touch-friendly buttons
- Scrollable table on small screens

### Dark Mode
- Full dark mode support
- Automatically follows system preference
- Can be toggled in user settings
- All components support dark mode

## 🎨 Visual Design

### Color Coding

**Tags**
- Each tag has a custom color
- Background: 20% opacity of tag color
- Text: Full tag color
- Consistent across events and sessions

**Status Indicators**
- Success messages: Green
- Error messages: Red
- Info messages: Blue
- Warning messages: Yellow

**Action Buttons**
- Edit: Ghost (gray)
- Duplicate: Ghost (gray)
- Delete: Danger (red)
- Primary actions: Blue

### Typography

**Session Names**: Bold, larger font
**Codes**: Small badge, gray background
**Descriptions**: Regular weight, gray text
**Dates/Times**: Hierarchical sizing

## ⚡ Performance Features

### Optimization

**Eager Loading**
- Client, producer, creator, updater relationships loaded efficiently
- Tags loaded with sessions
- Prevents N+1 query problems

**Pagination**
- 15 sessions per page
- Reduces page load time
- Smooth navigation

**Debounced Search**
- 300ms delay before searching
- Reduces server requests
- Smooth typing experience

**Indexed Queries**
- Database indexes on event_id and start_date
- Fast filtering and sorting
- Optimized for large datasets

## 🚀 Keyboard Shortcuts

While not explicitly implemented, standard browser shortcuts work:
- **Tab**: Navigate between fields
- **Enter**: Submit forms
- **Esc**: Close modals
- **Ctrl/Cmd + Click**: Open in new tab

## 📊 Data Display

### Session List Sorting

Currently sorted by:
1. Start date (ascending)
2. Start time (ascending)

This creates a chronological schedule view.

### Time Display

**Format**: 12-hour with AM/PM
- Example: "9:00 AM - 10:30 AM"

**Date Format**: Month abbreviation + day
- Example: "Oct 31"

### Truncation

**Description**: Limited to 60 characters in list view
- Full description visible in edit form
- Prevents table overflow

## 🔐 Security Features

### Authorization

- Only authenticated users can access
- Must be logged in to view/edit sessions
- User tracking on all changes

### Validation

**Server-side**
- All inputs validated on server
- SQL injection prevention
- XSS protection

**Client-side**
- Real-time validation feedback
- Required field checking
- Date range validation

### Audit Trail

- Complete change history
- IP address logging
- User agent tracking
- Timestamp precision

## 💡 Best Practices

### Creating Sessions

1. **Use descriptive names**: Clear, concise session titles
2. **Add codes**: Helps with quick reference and communication
3. **Fill descriptions**: Provides context for team members
4. **Assign early**: Set client and producer when creating
5. **Tag appropriately**: Use relevant tags for organization
6. **Check times**: Verify start/end dates are correct

### Managing Sessions

1. **Regular review**: Check session list regularly
2. **Update promptly**: Make changes as soon as needed
3. **Use duplication**: Save time with similar sessions
4. **Verify assignments**: Ensure correct client/producer
5. **Tag consistently**: Use same tags for similar sessions

### Custom Fields

1. **Plan ahead**: Define custom fields before creating sessions
2. **Keep it simple**: Only add fields you'll actually use
3. **Clear names**: Use descriptive field labels
4. **Appropriate types**: Choose the right field type for data
5. **Required wisely**: Only mark truly essential fields as required

## 🎯 Common Workflows

### Creating a Multi-Day Conference Schedule

1. Create first session with all details
2. Duplicate for similar sessions
3. Edit each duplicate to adjust:
   - Times
   - Specific details
   - Assignments
4. Use tags to categorize by track or type
5. Review chronological list

### Managing Session Assignments

1. Filter/search for unassigned sessions
2. Edit each session
3. Assign client and producer
4. Save changes
5. Verify in list view

### Tracking Continuing Education

1. Create custom field: "CE Credits" (Number, Required)
2. Add field value when creating/editing sessions
3. Use for reporting and compliance
4. Export data as needed

## 🛠️ Troubleshooting

### Session Not Appearing

**Possible causes:**
- Session was deleted (check if soft-deleted)
- Wrong event selected
- Search filter active

**Solutions:**
- Clear search
- Verify correct event
- Check audit logs for deletion

### Can't Save Session

**Common issues:**
- End date before start date
- Required fields empty
- Required custom fields missing

**Solutions:**
- Check validation errors
- Fill all required fields (marked with *)
- Ensure end date >= start date

### Tags Not Showing

**Possible causes:**
- No tags selected
- Tags not saved
- Display issue

**Solutions:**
- Edit session and verify tags are checked
- Save session again
- Refresh page

### Custom Fields Not Appearing

**Possible causes:**
- No custom fields defined for event
- Wrong event

**Solutions:**
- Click "Manage Custom Fields"
- Create custom fields
- Refresh session form

## 📈 Future Enhancement Ideas

Potential improvements to consider:

1. **Calendar View**: Visual timeline of sessions
2. **Drag-and-Drop**: Reorder sessions visually
3. **Bulk Operations**: Edit multiple sessions at once
4. **Export**: Download session schedule as CSV/PDF
5. **Import**: Upload sessions from spreadsheet
6. **Filtering**: Filter by tags, date range, assignments
7. **Conflict Detection**: Warn about overlapping sessions
8. **Session Templates**: Save and reuse session configurations
9. **Presenter Management**: Assign presenters to sessions
10. **Resource Booking**: Link equipment/rooms to sessions

## 📞 Support

For questions or issues:

1. Check this documentation
2. Review the Session Management Documentation
3. Check Audit Logs for change history
4. Contact your system administrator

---

**Last Updated**: October 31, 2025  
**Version**: 2.0  
**Status**: Production Ready
