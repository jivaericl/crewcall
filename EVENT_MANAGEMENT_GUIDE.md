# Event Management System - User Guide

## Overview

The Event Management System provides a comprehensive interface for creating, managing, and organizing events with advanced features including tagging, user assignments, filtering, and audit logging.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Event Dashboard](#event-dashboard)
3. [Creating Events](#creating-events)
4. [Managing Events](#managing-events)
5. [User Assignments](#user-assignments)
6. [Filtering & Search](#filtering--search)
7. [Tags System](#tags-system)
8. [Best Practices](#best-practices)

---

## Getting Started

### Accessing the System

1. Navigate to your application URL
2. Login with your credentials
3. Click "Events" in the main navigation menu

### User Roles

- **Super Admin**: Full system access, can manage roles and all events
- **Event Admin**: Can manage specific events and assign users
- **Regular User**: Can view and interact with assigned events based on role permissions

---

## Event Dashboard

### Statistics Cards

The dashboard displays four key metrics:

**Total Events**
- Shows the total number of events in the system
- Click to view all events

**Upcoming Events** (Blue)
- Events that haven't started yet
- Click to filter to upcoming events only

**Ongoing Events** (Green)
- Events currently in progress
- Click to filter to ongoing events only

**Past Events** (Gray)
- Events that have concluded
- Click to filter to past events only

### Quick Actions

- **Create Event**: Primary button in the top-right corner
- **Filter Events**: Use the search bar and filter dropdowns
- **Sort Events**: Click column headers to sort

---

## Creating Events

### Step-by-Step Guide

1. **Click "Create Event"** button
2. **Fill in Event Details:**

#### Event Name *Required*
- Minimum 3 characters
- Maximum 255 characters
- Example: "Annual Conference 2025"
- Clear, descriptive names work best

#### Description *Optional*
- Up to 5,000 characters
- Include agenda, location, or relevant details
- Character counter shows remaining space

#### Date & Time *Required*

**Start Date & Time**
- Use the datetime picker
- Select date and time when event begins

**End Date & Time**
- Must be after or equal to start date
- System validates automatically

**Duration Display**
- Automatically calculated
- Shows in days, hours, and minutes
- Updates in real-time as you change dates

**Timezone**
- Auto-detected from your browser
- Can be changed manually
- All times displayed in selected timezone

#### Tags *Optional*
- Select up to 10 tags
- Create new tags inline
- Visual color-coded badges
- Click "Create New Tag" to add custom tags

3. **Save the Event**
   - Click "Create Event" button
   - System validates all fields
   - Redirects to event list on success

### Real-Time Validation

The form provides instant feedback:
- ✅ Valid inputs show no errors
- ❌ Invalid inputs show red error messages
- 🔵 Helper text provides guidance
- ⏱️ Duration updates automatically

---

## Managing Events

### Event List View

The event list displays:

**Event Information**
- Name and description preview
- Start date and time
- Associated tags
- Current status (Upcoming/Ongoing/Past)
- Last updated timestamp and user

**Status Indicators**
- **Blue Badge**: Upcoming event
- **Green Badge**: Ongoing event
- **Gray Badge**: Past event

### Available Actions

Each event has four action buttons:

**1. Users Button** (People Icon)
- Manage user assignments
- Assign roles to team members
- Set event admins
- Available to event admins and super admins

**2. Edit Button** (Pencil Icon)
- Modify event details
- Update dates, description, tags
- All fields are editable

**3. Duplicate Button** (Copy Icon)
- Creates a copy of the event
- Adds "(Copy)" to the name
- Copies all tags
- Useful for recurring events

**4. Delete Button** (Trash Icon)
- Soft deletes the event
- Shows confirmation modal
- Action can be recovered from database
- Logged in audit trail

---

## User Assignments

### Assigning Users to Events

1. **Navigate to Event List**
2. **Click "Users" button** for the event
3. **Click "Assign User"** button
4. **Select User** from dropdown
5. **Select Role** from active roles
6. **Check "Make Event Admin"** if needed
7. **Click "Assign User"**

### Managing Assignments

**View Assignments**
- Table shows all assigned users
- Displays user name, email, and role
- Shows event admin badge if applicable

**Toggle Admin Status**
- Click "Make Admin" or "Remove Admin"
- Event admins can manage other assignments
- Changes logged in audit trail

**Remove Assignments**
- Click "Remove" button
- User loses access to event
- Action is logged

### Event Admin Capabilities

Event admins can:
- Assign new users to the event
- Remove user assignments
- Promote/demote other users to event admin
- Cannot manage system-wide roles (super admin only)

---

## Filtering & Search

### Search Functionality

**Text Search**
- Search by event name or description
- Real-time results with 300ms debounce
- Case-insensitive matching
- Highlights active search term

### Filter Options

**By Tag**
- Dropdown shows all available tags
- Filter to events with specific tag
- Combines with other filters

**By Status**
- All Status (default)
- Upcoming: Future events
- Ongoing: Currently active
- Past: Completed events

**Active Filters Display**
- Shows all active filters as badges
- Click "Clear All" to reset
- Individual filters can be removed

### Sorting

**Sortable Columns:**
- Event Name (A-Z or Z-A)
- Start Date (Newest or Oldest)

**How to Sort:**
1. Click column header
2. First click: Ascending order
3. Second click: Descending order
4. Arrow indicator shows current sort

---

## Tags System

### Creating Tags

**From Event Form:**
1. Click "Create New Tag" button
2. Enter tag name (unique)
3. Select color using color picker
4. Click "Create Tag"
5. Tag automatically selected for current event

**Tag Properties:**
- **Name**: Unique identifier
- **Color**: Visual indicator (hex color)
- **Usage**: Can be applied to multiple events

### Using Tags

**Benefits:**
- Categorize events by type
- Quick visual identification
- Filter events by category
- Organize large event lists

**Best Practices:**
- Use consistent naming
- Choose distinct colors
- Limit to 5-10 main categories
- Examples: Conference, Workshop, Meeting, Training, Social

### Managing Tags

Tags are reusable across all events:
- Create once, use many times
- Color-coded for quick recognition
- Up to 10 tags per event
- Visible in event list and details

---

## Best Practices

### Event Naming

✅ **Good Examples:**
- "Q1 2025 Sales Conference"
- "Product Launch - Widget Pro"
- "Team Building Workshop - March"

❌ **Avoid:**
- "Event 1"
- "Meeting"
- "TBD"

### Description Writing

Include:
- Event purpose and objectives
- Location (physical or virtual)
- Expected attendees
- Agenda highlights
- Special requirements

### Date & Time Management

- Set accurate start/end times
- Consider setup and teardown time
- Account for timezone differences
- Add buffer time for overruns

### User Assignment Strategy

**Before Event:**
- Assign all team members early
- Set clear roles and responsibilities
- Designate at least one event admin
- Review permissions

**During Event:**
- Monitor user access
- Add temporary staff as needed
- Track who's making changes

**After Event:**
- Review audit logs
- Remove temporary assignments
- Document lessons learned

### Tag Organization

**Recommended Tags:**
- Event Type: Conference, Workshop, Meeting
- Department: Sales, Marketing, Engineering
- Priority: High, Medium, Low
- Status: Planning, Confirmed, Cancelled

### Performance Tips

- Use filters to narrow large lists
- Archive old events regularly
- Keep descriptions concise
- Limit tags to relevant categories

---

## Features Summary

### Event Creation
✅ Intuitive form with real-time validation
✅ Automatic timezone detection
✅ Duration calculator
✅ Rich text descriptions
✅ Multi-tag support
✅ Inline tag creation

### Event Management
✅ Advanced search and filtering
✅ Sortable columns
✅ Status indicators
✅ Duplicate functionality
✅ Soft delete with confirmation
✅ Last updated tracking

### User Assignments
✅ Role-based access control
✅ Event admin delegation
✅ Easy assignment interface
✅ User management per event
✅ Permission inheritance

### System Features
✅ Complete audit logging
✅ Dark mode support
✅ Mobile responsive design
✅ Real-time updates
✅ Pagination for large lists
✅ Export capabilities (future)

---

## Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Create Event | `C` (when on events page) |
| Search | `/` (focus search box) |
| Clear Filters | `Esc` (when filters active) |

---

## Troubleshooting

### Common Issues

**"End date must be after start date"**
- Ensure end date/time is later than start
- Check timezone settings
- Verify date format

**"Tag name already exists"**
- Choose a unique tag name
- Check existing tags list
- Consider using different wording

**"You do not have permission"**
- Verify you're assigned to the event
- Check your role permissions
- Contact event admin or super admin

**Events not appearing**
- Check active filters
- Verify search terms
- Ensure events exist in database
- Try clearing all filters

### Getting Help

1. Check audit logs for recent changes
2. Review user assignments
3. Verify role permissions
4. Contact system administrator

---

## Technical Details

### Database Tables

- **events**: Core event data
- **tags**: Reusable tag definitions
- **event_tag**: Many-to-many relationship
- **event_user**: User assignments with roles
- **audit_logs**: Complete change history

### Soft Deletes

Events use soft deletes:
- Not permanently removed
- Can be recovered if needed
- Maintains data integrity
- Preserves audit trail

### Audit Logging

All actions are logged:
- Event creation and updates
- User assignments
- Tag changes
- Deletion actions
- Who, what, when, and how

---

## Future Enhancements

Planned features:
- [ ] Calendar view
- [ ] Event templates
- [ ] Recurring events
- [ ] Email notifications
- [ ] Export to PDF/Excel
- [ ] Event check-in system
- [ ] Resource management
- [ ] Budget tracking

---

## Support

For additional help:
- Review audit logs at `/audit-logs`
- Check role permissions at `/roles`
- Contact your system administrator
- Refer to Laravel documentation

---

## Quick Reference

### Event Statuses
- **Upcoming**: Start date is in the future
- **Ongoing**: Current time is between start and end dates
- **Past**: End date has passed

### Permission Levels
- **View**: Can see event details
- **Add**: Can create new items
- **Edit**: Can modify existing items
- **Delete**: Can remove items

### Color Coding
- 🔵 **Blue**: Upcoming/Information
- 🟢 **Green**: Ongoing/Success
- ⚫ **Gray**: Past/Inactive
- 🔴 **Red**: Danger/Delete
- 🟣 **Purple**: Admin/Special

---

## Conclusion

The Event Management System provides a powerful, user-friendly interface for organizing and managing events of any size. With features like advanced filtering, role-based access control, and comprehensive audit logging, you have complete control over your event planning process.

For the best experience:
- Keep event information up to date
- Assign appropriate roles to team members
- Use tags consistently
- Review audit logs regularly
- Leverage filtering for large event lists

Happy event planning!
