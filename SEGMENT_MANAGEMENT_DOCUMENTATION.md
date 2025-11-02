# Segment Management System Documentation

## Overview

The segment management system provides a way to organize sessions into individual time-based segments. Segments act as "folders" or containers that will eventually hold show calling cues. Each segment represents a distinct part of a session, such as walk-in, speaker presentations, breaks, or other activities.

## ✅ System Features

### Core Functionality

**Complete CRUD Operations**
- Create new segments within a session
- View all segments in chronological order
- Edit existing segment details
- Delete segments (soft delete with recovery)
- Duplicate segments for quick creation

**Time-Based Organization**
- Start and end times for each segment
- Automatic duration calculation
- Chronological sorting by time
- Visual timeline representation

**User Assignments**
- Client assignment (filtered to users with Client role)
- Producer assignment (any event team member)
- Automatic user tracking (created by, updated by)

**Tagging System**
- Apply multiple tags to segments
- Color-coded visual indicators
- Shared tag system across events, sessions, and segments
- Maximum 10 tags per segment

**Audit Logging**
- Complete change history tracking
- Who made changes and when
- Before/after value comparison
- IP address and user agent logging

**Soft Deletes**
- Safe deletion with recovery capability
- Data preservation for compliance
- Audit trail maintenance

## 📊 Database Structure

### Segments Table

```sql
CREATE TABLE segments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    session_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    producer_id BIGINT NULL,
    client_id BIGINT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (session_id) REFERENCES event_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (producer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_session_id (session_id),
    INDEX idx_start_time (start_time),
    INDEX idx_session_start (session_id, start_time),
    INDEX idx_sort_order (sort_order)
);
```

### Segment-Tag Pivot Table

```sql
CREATE TABLE segment_tag (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    segment_id BIGINT NOT NULL,
    tag_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (segment_id) REFERENCES segments(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_segment_tag (segment_id, tag_id)
);
```

## 🎯 Model Structure

### Segment Model

**Location**: `app/Models/Segment.php`

**Traits Used**
- `HasFactory` - Laravel factory support
- `SoftDeletes` - Soft delete functionality
- `Auditable` - Automatic audit logging

**Fillable Fields**
- `session_id` - Parent session
- `name` - Segment name
- `code` - Optional identifier
- `start_time` - Start time (HH:MM format)
- `end_time` - End time (HH:MM format)
- `producer_id` - Assigned producer
- `client_id` - Assigned client
- `sort_order` - Display order

**Relationships**
- `session()` - Belongs to Session
- `producer()` - Belongs to User (producer_id)
- `client()` - Belongs to User (client_id)
- `creator()` - Belongs to User (created_by)
- `updater()` - Belongs to User (updated_by)
- `tags()` - Many-to-many with Tag

**Scopes**
- `ordered()` - Sort by sort_order and start_time
- `forSession($sessionId)` - Filter by session

**Computed Attributes**
- `duration` - Calculated time duration

**Auto-Behaviors**
- Automatically sets `created_by` and `updated_by`
- Auto-increments `sort_order` if not provided
- Updates `updated_by` on every save

## 🎨 User Interface

### Segments List Page

**Access**: `/sessions/{sessionId}/segments`

**Features**
- Header showing session and event context
- "Back to Sessions" and "Add Segment" buttons
- Real-time search (name and code)
- Chronological table display
- Action buttons (Edit, Duplicate, Delete)
- Pagination (15 segments per page)
- Empty state with helpful message

**Table Columns**
1. **Time** - Start and end times with duration
2. **Segment** - Name, code badge, and tags
3. **Client** - Assigned client user
4. **Producer** - Assigned producer
5. **Actions** - Edit, duplicate, delete buttons

**Visual Elements**
- Color-coded tag badges
- Gray code badges
- Time range display (e.g., "9:00 AM to 10:00 AM")
- Hover effects on table rows
- Confirmation modal for deletion

### Segment Creation/Edit Form

**Access**: 
- Create: `/sessions/{sessionId}/segments/create`
- Edit: `/sessions/{sessionId}/segments/{segmentId}/edit`

**Form Fields**

**Required Fields** (marked with *)
- **Segment Name** - Descriptive title (3-255 characters)
- **Start Time** - Time picker (HH:MM format)
- **End Time** - Time picker (HH:MM format, must be after start)

**Optional Fields**
- **Code** - Identifier (max 50 characters)
- **Client** - Dropdown of users with Client role
- **Producer** - Dropdown of event team members
- **Tags** - Checkbox grid (max 10 tags)

**Real-Time Features**
- Duration calculator (updates as times change)
- Instant validation feedback
- Character counters
- Tag limit enforcement

**Form Actions**
- Cancel (returns to segments list)
- Create/Update Segment (saves and redirects)

## 🚀 Usage Guide

### Creating a Segment

1. **Navigate to Session**
   - Go to Events → Select Event → Sessions
   - Click the segments icon next to a session

2. **Start Creation**
   - Click "Add Segment" button
   - Form opens with default times (9:00 AM - 10:00 AM)

3. **Fill Required Fields**
   - Enter segment name (e.g., "Walk-in", "Speaker 1", "Break")
   - Set start time
   - Set end time (must be after start time)

4. **Add Optional Details**
   - Enter code if needed (e.g., "SEG-01", "BREAK-1")
   - Select client from dropdown
   - Select producer from dropdown
   - Check relevant tags

5. **Review Duration**
   - Duration automatically calculates and displays
   - Verify times are correct

6. **Save**
   - Click "Create Segment"
   - Success message appears
   - Redirected to segments list

### Editing a Segment

1. Click the edit (pencil) icon next to any segment
2. Modify any fields as needed
3. Click "Update Segment"
4. Changes are saved and audit log created

### Duplicating a Segment

1. Click the duplicate (copy) icon next to any segment
2. New segment created with:
   - Same name + " (Copy)" suffix
   - Same times
   - Same client and producer
   - Same tags
3. Edit the duplicate to adjust details

### Deleting a Segment

1. Click the delete (trash) icon
2. Confirmation modal appears
3. Confirm deletion
4. Segment soft-deleted (can be recovered)

### Searching Segments

1. Use search bar at top of segments list
2. Searches across name and code fields
3. Results update in real-time (300ms debounce)
4. Clear search to see all segments

## 🔧 Technical Details

### Validation Rules

**Segment Name**
- Required
- String type
- Minimum 3 characters
- Maximum 255 characters

**Code**
- Optional (nullable)
- String type
- Maximum 50 characters

**Start Time**
- Required
- Time format (HH:MM)
- Must be valid time

**End Time**
- Required
- Time format (HH:MM)
- Must be after start time

**Client ID**
- Optional (nullable)
- Must exist in users table

**Producer ID**
- Optional (nullable)
- Must exist in users table

### Duration Calculation

The duration is calculated using Carbon's diff method:

```php
$start = Carbon::parse($this->start_time);
$end = Carbon::parse($this->end_time);
$diff = $start->diff($end);

// Format: "X hours, Y minutes"
```

### Sort Order

Segments are automatically assigned a sort_order when created:

```php
$maxOrder = Segment::where('session_id', $sessionId)->max('sort_order');
$newOrder = ($maxOrder ?? -1) + 1;
```

This allows for future drag-and-drop reordering functionality.

### Query Optimization

**Eager Loading**
```php
Segment::with(['client', 'producer', 'creator', 'updater', 'tags'])
    ->where('session_id', $sessionId)
    ->ordered()
    ->paginate(15);
```

**Indexes**
- `session_id` - Fast filtering by session
- `start_time` - Chronological sorting
- `(session_id, start_time)` - Composite index for common query
- `sort_order` - Future drag-and-drop support

## 📋 Routes

All segment routes are protected by authentication middleware.

**List Segments**
```
GET /sessions/{sessionId}/segments
Route name: sessions.segments.index
Component: App\Livewire\Segments\Index
```

**Create Segment**
```
GET /sessions/{sessionId}/segments/create
Route name: sessions.segments.create
Component: App\Livewire\Segments\Form
```

**Edit Segment**
```
GET /sessions/{sessionId}/segments/{segmentId}/edit
Route name: sessions.segments.edit
Component: App\Livewire\Segments\Form
```

## 🔗 Integration Points

### With Sessions

- Segments belong to sessions (parent-child relationship)
- Accessed via segments icon in sessions list
- Breadcrumb navigation shows session context
- Cascade delete when session is deleted

### With Users

- Client dropdown filtered to users with Client role
- Producer dropdown shows all event team members
- Automatic tracking of creator and updater
- User names displayed in segments list

### With Tags

- Shared tag system across events, sessions, and segments
- Color-coded visual consistency
- Tag selection via checkbox grid
- Maximum 10 tags per segment

### With Audit Logs

- All segment operations logged automatically
- View in Audit Logs interface
- Filter by model type "Segment"
- Complete before/after value tracking

## 💡 Use Cases

### Conference Session Breakdown

**Session**: "Morning Keynote - 9:00 AM to 12:00 PM"

**Segments**:
1. Walk-in (9:00 AM - 9:15 AM)
2. Opening Remarks (9:15 AM - 9:30 AM)
3. Keynote Speaker (9:30 AM - 10:30 AM)
4. Break (10:30 AM - 10:45 AM)
5. Panel Discussion (10:45 AM - 11:45 AM)
6. Q&A (11:45 AM - 12:00 PM)

### Live Event Production

**Session**: "Product Launch Event"

**Segments**:
1. Pre-Show (Code: PRE-01, Tags: Technical, Setup)
2. Opening Video (Code: VID-01, Tags: Media, Playback)
3. CEO Welcome (Code: LIVE-01, Tags: Presentation, Executive)
4. Product Demo (Code: DEMO-01, Tags: Interactive, Technical)
5. Closing Remarks (Code: LIVE-02, Tags: Presentation)
6. Post-Show (Code: POST-01, Tags: Wrap-up)

### Broadcast Show

**Session**: "Evening News - 6:00 PM to 7:00 PM"

**Segments**:
1. Opening Titles (6:00 PM - 6:01 PM, Code: OPEN)
2. Segment A (6:01 PM - 6:15 PM, Code: SEG-A)
3. Commercial Break 1 (6:15 PM - 6:18 PM, Code: COM-1)
4. Segment B (6:18 PM - 6:30 PM, Code: SEG-B)
5. Commercial Break 2 (6:30 PM - 6:33 PM, Code: COM-2)
6. Segment C (6:33 PM - 6:55 PM, Code: SEG-C)
7. Closing (6:55 PM - 7:00 PM, Code: CLOSE)

## 🎯 Best Practices

### Naming Conventions

**Be Descriptive**
- Use clear, concise names
- Include speaker names or content type
- Example: "Speaker 1: John Doe - Marketing Trends"

**Use Codes Consistently**
- Develop a coding system
- Examples: SEG-01, BREAK-1, VID-01, LIVE-01
- Makes communication easier

**Tag Appropriately**
- Use tags to categorize segment types
- Examples: Technical, Presentation, Break, Media
- Helps with filtering and reporting

### Time Management

**Avoid Overlaps**
- Ensure end time of one segment matches start of next
- Use duration calculator to verify lengths
- Plan buffer time between segments

**Be Realistic**
- Account for setup and teardown time
- Include transition time between segments
- Build in contingency for delays

**Use Consistent Increments**
- Round to 5 or 15-minute intervals
- Makes scheduling cleaner
- Easier to communicate times

### Organization

**Logical Grouping**
- Group related segments with tags
- Use consistent naming patterns
- Maintain chronological order

**Client/Producer Assignment**
- Assign early in planning process
- Ensure clear ownership
- Update as team changes

**Documentation**
- Use segment names to describe content
- Add codes for quick reference
- Tag for categorization

## 🔮 Future Enhancements

The segment system is designed to support future features:

### Drag-and-Drop Reordering

The `sort_order` field is already in place to support drag-and-drop functionality:
- Visual reordering of segments
- Automatic time recalculation
- Conflict detection

### Show Calling Cues

Segments will act as containers for cues:
- Lighting cues
- Audio cues
- Video playback cues
- Presentation cues

### Timeline Visualization

Visual timeline view of segments:
- Gantt chart style display
- Color-coded by tag
- Interactive time editing

### Conflict Detection

Automatic detection of:
- Overlapping segment times
- Resource conflicts
- Assignment conflicts

### Templates

Save segment configurations as templates:
- Reuse common segment structures
- Apply to multiple sessions
- Customize per event

### Reporting

Generate reports on:
- Segment durations
- User assignments
- Tag usage
- Timeline analysis

## 🛠️ Troubleshooting

### Segment Not Appearing

**Possible Causes**
- Segment was deleted (soft-deleted)
- Search filter active
- Wrong session selected

**Solutions**
- Clear search field
- Verify correct session
- Check audit logs for deletion

### Can't Save Segment

**Common Issues**
- End time before or equal to start time
- Required fields empty (name, times)
- Invalid time format

**Solutions**
- Ensure end time is after start time
- Fill all required fields (marked with *)
- Use time picker to ensure correct format

### Tags Not Showing

**Possible Causes**
- Tags not selected when saving
- Maximum 10 tags reached
- Tags not saved properly

**Solutions**
- Edit segment and verify tags are checked
- Uncheck some tags if at maximum
- Save segment again

### Duration Not Calculating

**Possible Causes**
- Invalid time format
- End time before start time
- JavaScript error

**Solutions**
- Use time picker instead of typing
- Verify end time is after start time
- Refresh page and try again

## 📊 Performance Considerations

### Database Optimization

**Indexes**
- All foreign keys indexed
- Composite index on (session_id, start_time)
- Sort_order indexed for future drag-and-drop

**Eager Loading**
- Relationships loaded efficiently
- Prevents N+1 query problems
- Optimized for list views

**Pagination**
- 15 segments per page
- Reduces page load time
- Smooth navigation

### UI Performance

**Debounced Search**
- 300ms delay before searching
- Reduces server requests
- Smooth typing experience

**Livewire Optimization**
- Real-time validation
- Minimal re-renders
- Efficient wire:model usage

## 🔐 Security

### Authorization

- Authentication required for all routes
- User must be logged in
- Session ownership verified

### Validation

**Server-Side**
- All inputs validated
- SQL injection prevention
- XSS protection

**Client-Side**
- Real-time feedback
- Required field checking
- Time range validation

### Audit Trail

- Complete change history
- IP address logging
- User agent tracking
- Timestamp precision

## 📈 Analytics Potential

The segment system captures data for:

**Time Analysis**
- Average segment duration
- Most common segment types
- Time distribution across sessions

**User Metrics**
- Producer workload
- Client engagement
- Assignment patterns

**Tag Analytics**
- Most used tags
- Tag combinations
- Category distribution

**Operational Insights**
- Segment creation patterns
- Edit frequency
- Deletion rates

## 📝 Summary

The segment management system provides:

✅ **Complete CRUD operations** for managing segments  
✅ **Time-based organization** with automatic duration calculation  
✅ **User assignments** for clients and producers  
✅ **Tagging system** for categorization  
✅ **Audit logging** for complete change tracking  
✅ **Soft deletes** for data safety  
✅ **Intuitive UI** with Flux UI Pro components  
✅ **Search and filter** capabilities  
✅ **Responsive design** for all devices  
✅ **Dark mode** support throughout  
✅ **Future-ready** for cues and advanced features  

The system is production-ready and fully integrated with the existing event, session, user, and tag management systems.

---

**Version**: 1.0  
**Last Updated**: October 31, 2025  
**Status**: Production Ready
