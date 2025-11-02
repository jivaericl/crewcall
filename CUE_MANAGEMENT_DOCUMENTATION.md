# Cue Management System Documentation

## Overview

The cue management system provides comprehensive show calling functionality within segments. Cues are individual triggers that production teams use to execute lighting, audio, video, presentations, and other production elements during live events. This system supports customizable cue types, status tracking, priority levels, operator assignments, and complete audit trails.

## ✅ System Features

### Core Functionality

**Complete CRUD Operations**
- Create new cues within segments
- View all cues with advanced filtering
- Edit existing cue details
- Delete cues (soft delete with recovery)
- Duplicate cues for quick creation
- Real-time status updates

**Cue Types**
- System-wide cue types (managed by super admin)
- Event-specific custom cue types
- 7 default types: Lighting, Audio, Video, Presentation, Furniture, Speaker Clock, Downstage Monitors
- Color-coded visual identification
- Extensible type system

**Status Management**
- Four status levels: Standby, Go, Complete, Skip
- Quick status updates from list view
- Visual status indicators
- Status-based filtering

**Priority System**
- Four priority levels: Low, Normal, High, Critical
- Color-coded priority badges
- Priority-based filtering
- Critical cue highlighting

**User Assignments**
- Operator assignment (event team members)
- Automatic user tracking (created by, updated by)
- User-based filtering and reporting

**Tagging System**
- Apply multiple tags to cues
- Color-coded visual indicators
- Shared tag system across all entities
- Maximum 10 tags per cue

**File Management**
- Filename field for audio/video content
- Preparation for content module integration
- File reference tracking

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

### Cue Types Table

```sql
CREATE TABLE cue_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    color VARCHAR(255) DEFAULT '#3B82F6',
    icon VARCHAR(255) NULL,
    is_system BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    event_id BIGINT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    
    INDEX idx_event_id (event_id),
    INDEX idx_is_active (is_active),
    INDEX idx_event_active (event_id, is_active)
);
```

### Cues Table

```sql
CREATE TABLE cues (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    segment_id BIGINT NOT NULL,
    cue_type_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    time TIME NULL,
    status ENUM('standby', 'go', 'complete', 'skip') DEFAULT 'standby',
    notes TEXT NULL,
    filename VARCHAR(255) NULL,
    operator_id BIGINT NULL,
    priority ENUM('low', 'normal', 'high', 'critical') DEFAULT 'normal',
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (segment_id) REFERENCES segments(id) ON DELETE CASCADE,
    FOREIGN KEY (cue_type_id) REFERENCES cue_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (operator_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_segment_id (segment_id),
    INDEX idx_cue_type_id (cue_type_id),
    INDEX idx_status (status),
    INDEX idx_time (time),
    INDEX idx_segment_sort (segment_id, sort_order),
    INDEX idx_segment_time (segment_id, time)
);
```

### Cue-Tag Pivot Table

```sql
CREATE TABLE cue_tag (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    cue_id BIGINT NOT NULL,
    tag_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (cue_id) REFERENCES cues(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_cue_tag (cue_id, tag_id)
);
```

## 🎯 Model Structure

### CueType Model

**Location**: `app/Models/CueType.php`

**Fillable Fields**
- `name` - Type name
- `slug` - URL-friendly identifier
- `color` - Hex color code
- `icon` - Icon identifier (future use)
- `is_system` - System type flag
- `is_active` - Active status
- `event_id` - Event association (null = system-wide)
- `sort_order` - Display order

**Relationships**
- `event()` - Belongs to Event
- `cues()` - Has many Cues

**Scopes**
- `active()` - Active types only
- `system()` - System-wide types only
- `forEvent($eventId)` - Types for specific event
- `ordered()` - Sorted by sort_order and name

### Cue Model

**Location**: `app/Models/Cue.php`

**Traits Used**
- `HasFactory` - Laravel factory support
- `SoftDeletes` - Soft delete functionality
- `Auditable` - Automatic audit logging

**Fillable Fields**
- `segment_id` - Parent segment
- `cue_type_id` - Cue type
- `name` - Cue name
- `code` - Optional identifier
- `description` - Detailed description
- `time` - Execution time
- `status` - Current status
- `notes` - Additional notes
- `filename` - Audio/video filename
- `operator_id` - Assigned operator
- `priority` - Priority level
- `sort_order` - Display order

**Relationships**
- `segment()` - Belongs to Segment
- `cueType()` - Belongs to CueType
- `operator()` - Belongs to User (operator_id)
- `creator()` - Belongs to User (created_by)
- `updater()` - Belongs to User (updated_by)
- `tags()` - Many-to-many with Tag

**Scopes**
- `ordered()` - Sort by sort_order and time
- `forSegment($segmentId)` - Filter by segment
- `byStatus($status)` - Filter by status
- `byPriority($priority)` - Filter by priority

**Helper Methods**
- `getStatusBadgeColorAttribute()` - Status color mapping
- `getPriorityBadgeColorAttribute()` - Priority color mapping

**Auto-Behaviors**
- Automatically sets `created_by` and `updated_by`
- Auto-increments `sort_order` if not provided
- Updates `updated_by` on every save

## 🎨 User Interface

### Cues List Page

**Access**: `/segments/{segmentId}/cues`

**Features**
- Header showing segment, session, and event context
- "Back to Segments" and "Add Cue" buttons
- Advanced filtering system
- Real-time search (name, code, description)
- Sortable table display
- Quick status updates
- Action buttons (Edit, Duplicate, Delete)
- Pagination (20 cues per page)
- Empty state with helpful message

**Filters**
1. **Search** - Text search across name, code, description
2. **Cue Type** - Filter by cue type
3. **Status** - Filter by status (Standby, Go, Complete, Skip)
4. **Priority** - Filter by priority (Low, Normal, High, Critical)

**Table Columns**
1. **Time** - Execution time
2. **Cue** - Name, code badge, filename, tags
3. **Type** - Color-coded cue type badge
4. **Status** - Dropdown for quick updates
5. **Priority** - Color-coded priority badge
6. **Operator** - Assigned operator name
7. **Actions** - Edit, duplicate, delete buttons

**Visual Elements**
- Color-coded cue type badges
- Status dropdown for quick updates
- Priority badges (red=critical, amber=high, blue=normal, gray=low)
- Tag badges with custom colors
- File icon for cues with filenames
- Hover effects on table rows
- Confirmation modal for deletion

### Cue Creation/Edit Form

**Access**: 
- Create: `/segments/{segmentId}/cues/create`
- Edit: `/segments/{segmentId}/cues/{cueId}/edit`

**Form Fields**

**Required Fields** (marked with *)
- **Cue Name** - Descriptive title (3-255 characters)
- **Cue Type** - Dropdown of available types
- **Status** - Standby, Go, Complete, Skip
- **Priority** - Low, Normal, High, Critical

**Optional Fields**
- **Cue Code** - Identifier (max 50 characters)
- **Description** - Detailed description (textarea)
- **Time** - Execution time (HH:MM format)
- **Filename** - Audio/video file reference
- **Operator** - Dropdown of event team members
- **Notes** - Additional notes (textarea)
- **Tags** - Checkbox grid (max 10 tags)

**Form Actions**
- Cancel (returns to cues list)
- Create/Update Cue (saves and redirects)

## 🚀 Usage Guide

### Creating a Cue

1. **Navigate to Segment**
   - Go to Events → Select Event → Sessions → Select Session → Segments
   - Click the cues icon (lightning bolt) next to a segment

2. **Start Creation**
   - Click "Add Cue" button
   - Form opens with default values

3. **Fill Required Fields**
   - Enter cue name (e.g., "Lights Up", "Play Video", "Speaker Intro")
   - Select cue type
   - Set status (usually "Standby" for new cues)
   - Set priority (usually "Normal")

4. **Add Optional Details**
   - Enter code if needed (e.g., "LX-01", "AUD-01", "VID-01")
   - Add description for detailed instructions
   - Set execution time
   - Enter filename for audio/video cues
   - Select operator
   - Add notes
   - Check relevant tags

5. **Save**
   - Click "Create Cue"
   - Success message appears
   - Redirected to cues list

### Managing Cue Status

**From List View**
1. Locate the cue in the table
2. Click the status dropdown in the Status column
3. Select new status (Standby, Go, Complete, Skip)
4. Status updates immediately

**Status Workflow**
- **Standby** → Cue is ready, waiting for execution
- **Go** → Cue is being executed
- **Complete** → Cue has been executed successfully
- **Skip** → Cue was intentionally skipped

### Using Filters

**Search**
- Type in search box to filter by name, code, or description
- Results update in real-time (300ms debounce)

**Cue Type Filter**
- Select a cue type to show only cues of that type
- "All Types" shows all cues

**Status Filter**
- Select a status to show only cues with that status
- Useful for finding pending cues or completed cues

**Priority Filter**
- Select a priority to show only cues at that level
- Useful for focusing on critical or high-priority cues

**Combining Filters**
- All filters work together
- Example: Show all "Critical" priority "Lighting" cues in "Standby" status

### Duplicating a Cue

1. Click the duplicate (copy) icon next to any cue
2. New cue created with:
   - Same name + " (Copy)" suffix
   - Same type, priority, operator
   - Same description, notes, filename
   - Same tags
   - Status reset to "Standby"
3. Edit the duplicate to adjust details

### Deleting a Cue

1. Click the delete (trash) icon
2. Confirmation modal appears
3. Confirm deletion
4. Cue soft-deleted (can be recovered from audit log)

## 🔧 Technical Details

### Validation Rules

**Cue Name**
- Required
- String type
- Minimum 3 characters
- Maximum 255 characters

**Cue Code**
- Optional (nullable)
- String type
- Maximum 50 characters

**Cue Type**
- Required
- Must exist in cue_types table

**Description**
- Optional (nullable)
- Text type

**Time**
- Optional (nullable)
- Time format (HH:MM)

**Status**
- Required
- Must be: standby, go, complete, or skip

**Priority**
- Required
- Must be: low, normal, high, or critical

**Filename**
- Optional (nullable)
- String type
- Maximum 255 characters

**Operator ID**
- Optional (nullable)
- Must exist in users table

**Tags**
- Optional (nullable)
- Array type
- Maximum 10 tags
- Each tag must exist in tags table

### Default Cue Types

The system includes 7 pre-configured cue types:

1. **Lighting** (Yellow #FBBF24)
2. **Audio** (Green #10B981)
3. **Video** (Blue #3B82F6)
4. **Presentation** (Purple #8B5CF6)
5. **Furniture** (Gray #6B7280)
6. **Speaker Clock** (Red #EF4444)
7. **Downstage Monitors** (Cyan #06B6D4)

### Query Optimization

**Eager Loading**
```php
Cue::with(['cueType', 'operator', 'tags', 'updater'])
    ->where('segment_id', $segmentId)
    ->ordered()
    ->paginate(20);
```

**Indexes**
- All foreign keys indexed
- Composite index on (segment_id, sort_order)
- Composite index on (segment_id, time)
- Status and priority indexed for filtering

**Pagination**
- 20 cues per page
- Reduces page load time
- Smooth navigation

## 📋 Routes

All cue routes are protected by authentication middleware.

**List Cues**
```
GET /segments/{segmentId}/cues
Route name: segments.cues.index
Component: App\Livewire\Cues\Index
```

**Create Cue**
```
GET /segments/{segmentId}/cues/create
Route name: segments.cues.create
Component: App\Livewire\Cues\Form
```

**Edit Cue**
```
GET /segments/{segmentId}/cues/{cueId}/edit
Route name: segments.cues.edit
Component: App\Livewire\Cues\Form
```

## 🔗 Integration Points

### With Segments

- Cues belong to segments (parent-child relationship)
- Accessed via cues icon in segments list
- Breadcrumb navigation shows segment context
- Cascade delete when segment is deleted

### With Cue Types

- Each cue must have a type
- Types can be system-wide or event-specific
- Color-coded visual consistency
- Restrict delete prevents orphaned cues

### With Users

- Operator assignment from event team members
- Automatic tracking of creator and updater
- User names displayed in cues list

### With Tags

- Shared tag system across all entities
- Color-coded visual consistency
- Tag selection via checkbox grid
- Maximum 10 tags per cue

### With Audit Logs

- All cue operations logged automatically
- View in Audit Logs interface
- Filter by model type "Cue"
- Complete before/after value tracking

## 💡 Use Cases

### Lighting Cues

**Session**: "Morning Keynote"
**Segment**: "Keynote Speaker"

**Cues**:
1. LX-01: House Lights Out (Lighting, Critical, 9:30 AM)
2. LX-02: Stage Wash Up (Lighting, Critical, 9:30:05 AM)
3. LX-03: Follow Spot On Speaker (Lighting, High, 9:30:10 AM)
4. LX-04: Transition to Q&A (Lighting, Normal, 10:25 AM)

### Audio/Video Cues

**Session**: "Product Launch"
**Segment**: "Opening Video"

**Cues**:
1. VID-01: Play Intro Video (Video, Critical, 2:00 PM, filename: intro-2024.mp4)
2. AUD-01: Fade Music In (Audio, High, 2:00 PM, filename: theme-music.mp3)
3. AUD-02: Fade Music Out (Audio, High, 2:03 PM)
4. VID-02: Stop Video (Video, Critical, 2:03:30 PM)

### Multi-Department Show

**Session**: "Awards Ceremony"
**Segment**: "Award Presentation 1"

**Cues**:
1. AUD-01: Walk-On Music (Audio, High, 7:00 PM)
2. LX-01: Stage Lights Up (Lighting, Critical, 7:00:05 PM)
3. VID-01: Show Nominee Reel (Video, High, 7:01 PM, filename: nominee-reel-1.mp4)
4. PRES-01: Display Winner Slide (Presentation, Normal, 7:03 PM)
5. LX-02: Winner Spotlight (Lighting, High, 7:03:10 PM)
6. AUD-02: Applause Track (Audio, Low, 7:03:15 PM)

### Technical Setup Cues

**Session**: "Conference Day 1"
**Segment**: "Pre-Show Setup"

**Cues**:
1. FURN-01: Set Podium Center Stage (Furniture, Normal, 8:00 AM)
2. MON-01: Enable Downstage Monitors (Downstage Monitors, High, 8:15 AM)
3. CLK-01: Start Speaker Clock (Speaker Clock, Normal, 8:20 AM)
4. AUD-01: Sound Check (Audio, High, 8:25 AM)

## 🎯 Best Practices

### Naming Conventions

**Be Specific**
- Use clear, action-oriented names
- Include what happens, not just the type
- Example: "Fade House Lights to 50%" not "Lights"

**Use Codes Consistently**
- Develop a coding system per department
- Examples: LX-01 (Lighting), AUD-01 (Audio), VID-01 (Video)
- Makes communication easier during show calling

**Include Details**
- Mention specific equipment or targets
- Example: "Follow Spot on Speaker 1" not "Follow Spot"

### Priority Management

**Critical**
- Must happen exactly on time
- Show-stopping if missed
- Examples: Curtain up, video playback start

**High**
- Important but has small timing window
- Noticeable if missed
- Examples: Lighting transitions, audio fades

**Normal**
- Standard cues
- Some flexibility in timing
- Examples: Monitor enables, clock starts

**Low**
- Nice to have
- Can be skipped if needed
- Examples: Ambient effects, background music

### Status Workflow

**During Rehearsal**
- Mark cues as "Complete" as you test them
- Use "Skip" for cues not needed in rehearsal
- Reset all to "Standby" before show

**During Show**
- Update to "Go" as you call each cue
- Mark "Complete" after execution
- Use "Skip" for intentionally skipped cues

**Post-Show**
- Review all cues for accuracy
- Update notes based on what happened
- Prepare for next performance

### Organization

**Logical Grouping**
- Group related cues with tags
- Use consistent naming patterns
- Maintain chronological order by time

**Operator Assignment**
- Assign early in planning process
- Ensure clear ownership
- Update as team changes

**Documentation**
- Use description field for detailed instructions
- Add notes for special considerations
- Include filename for media cues

### Timing

**Set Accurate Times**
- Use actual execution times
- Account for cue duration
- Build in buffer time

**Sequential Cues**
- Space cues appropriately
- Allow time for execution
- Consider operator reaction time

**Time-Critical Cues**
- Mark as high or critical priority
- Add notes about timing requirements
- Assign to experienced operators

## 🔮 Future Enhancements

The cue system is designed to support future features:

### Content Module Integration

- Direct file upload and management
- Preview audio/video content
- Version control for media files
- Automatic file validation

### Drag-and-Drop Reordering

The `sort_order` field is ready for:
- Visual reordering of cues
- Automatic time recalculation
- Conflict detection

### Cue Sheets

Generate formatted cue sheets:
- PDF export
- Print-friendly layouts
- Department-specific views
- Custom formatting options

### Real-Time Show Calling

Live show calling interface:
- Large, touch-friendly buttons
- Auto-advance to next cue
- Timer display
- Status indicators

### Cue Templates

Save cue configurations as templates:
- Reuse common cue sequences
- Apply to multiple segments
- Customize per event

### Advanced Filtering

Additional filter options:
- Filter by operator
- Filter by date range
- Filter by tags
- Saved filter presets

### Reporting

Generate reports on:
- Cue execution statistics
- Operator workload
- Type distribution
- Timeline analysis

## 🛠️ Troubleshooting

### Cue Not Appearing

**Possible Causes**
- Cue was deleted (soft-deleted)
- Search or filter active
- Wrong segment selected

**Solutions**
- Clear all filters
- Verify correct segment
- Check audit logs for deletion

### Can't Save Cue

**Common Issues**
- Required fields empty (name, type, status, priority)
- Invalid time format
- Too many tags selected (max 10)

**Solutions**
- Fill all required fields (marked with *)
- Use time picker for correct format
- Uncheck some tags if at maximum

### Status Not Updating

**Possible Causes**
- Network connection issue
- JavaScript error
- Permission issue

**Solutions**
- Refresh page
- Check browser console for errors
- Verify user permissions

### Cue Type Not Available

**Possible Causes**
- Type is inactive
- Type is event-specific for different event
- Type was deleted

**Solutions**
- Check cue type management
- Verify event association
- Create new type if needed

## 📊 Performance Considerations

### Database Optimization

**Indexes**
- All foreign keys indexed
- Composite indexes for common queries
- Status and priority indexed for filtering

**Eager Loading**
- Relationships loaded efficiently
- Prevents N+1 query problems
- Optimized for list views

**Pagination**
- 20 cues per page
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

**Quick Status Updates**
- Inline status changes
- No page reload required
- Immediate feedback

## 🔐 Security

### Authorization

- Authentication required for all routes
- User must be logged in
- Segment ownership verified

### Validation

**Server-Side**
- All inputs validated
- SQL injection prevention
- XSS protection

**Client-Side**
- Real-time feedback
- Required field checking
- Format validation

### Audit Trail

- Complete change history
- IP address logging
- User agent tracking
- Timestamp precision

## 📈 Analytics Potential

The cue system captures data for:

**Timing Analysis**
- Average cue execution time
- Most common cue times
- Time distribution across segments

**Type Distribution**
- Most used cue types
- Type usage patterns
- Department workload

**User Metrics**
- Operator workload
- Cue creation patterns
- Assignment distribution

**Status Tracking**
- Completion rates
- Skip frequency
- Status change patterns

**Priority Analysis**
- Critical cue count
- Priority distribution
- High-priority patterns

**Operational Insights**
- Cue creation patterns
- Edit frequency
- Deletion rates

## 📝 Summary

The cue management system provides:

✅ **Complete CRUD operations** for managing show calling cues  
✅ **Customizable cue types** with system-wide and event-specific options  
✅ **Status management** with four levels and quick updates  
✅ **Priority system** for critical cue identification  
✅ **Operator assignments** for clear ownership  
✅ **Tagging system** for categorization  
✅ **File references** for audio/video content  
✅ **Audit logging** for complete change tracking  
✅ **Soft deletes** for data safety  
✅ **Advanced filtering** for finding specific cues  
✅ **Intuitive UI** with Flux UI Pro components  
✅ **Search functionality** across multiple fields  
✅ **Responsive design** for all devices  
✅ **Dark mode** support throughout  
✅ **Future-ready** for content module and advanced features  

The system is production-ready and fully integrated with the existing event, session, segment, user, and tag management systems.

---

**Version**: 1.0  
**Last Updated**: October 31, 2025  
**Status**: Production Ready
