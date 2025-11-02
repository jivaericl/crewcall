# Speaker Management System - Complete Implementation

## Overview

The Speaker Management System has been successfully implemented with a complete database foundation and model layer. This system allows event managers to track speakers, their profiles, content assignments, and session participation.

## Database Structure

### Speakers Table

The main speakers table includes all requested fields:

**Core Fields**:
- `name` - Speaker's full name (required)
- `title` - Professional title (e.g., "CEO", "Director")
- `company` - Company or organization
- `bio` - Full biography text
- `notes` - Internal notes about the speaker
- `contact_person` - Primary contact for this speaker
- `email` - Speaker's email address
- `headshot_path` - Path to uploaded headshot image

**System Fields**:
- `event_id` - Links speaker to specific event
- `user_id` - Optional link to user account (for user/viewer creation)
- `is_active` - Active status flag
- `created_by` / `updated_by` - User tracking
- `timestamps` - Created and updated timestamps
- `deleted_at` - Soft delete support

**Indexes**:
- `event_id` + `is_active` for fast filtering
- `name` for search functionality

### Relationship Tables

**session_speaker** - Links speakers to sessions
- Many-to-many relationship
- Tracks which speakers are presenting in which sessions
- Unique constraint prevents duplicates

**content_file_speaker** - Links speakers to content files
- Many-to-many relationship
- Tracks which content belongs to which speakers
- Enables "content by speaker" views

**speaker_tag** - Links speakers to tags
- Many-to-many relationship
- Enables categorization and filtering
- Unique constraint prevents duplicates

## Speaker Model Features

### Traits Included

**SoftDeletes** - Safe deletion with recovery
- Speakers are marked as deleted, not removed
- Can be restored if needed
- Maintains data integrity

**Auditable** - Complete change tracking
- All creates, updates, and deletes tracked
- Who made changes and when
- Before/after values for updates
- View in Audit Logs interface

**Commentable** - Team collaboration
- Comment on speaker profiles
- @mention team members
- Threaded discussions
- Integrated with Activity Feed

### Relationships

**Belongs To**:
- `event()` - The event this speaker is part of
- `user()` - Optional linked user account
- `creator()` - User who created the speaker record
- `updater()` - User who last updated the speaker record

**Belongs To Many**:
- `sessions()` - Sessions where this speaker is presenting
- `contentFiles()` - Content files assigned to this speaker
- `tags()` - Tags for categorization

### Helper Methods

**getHeadshotUrlAttribute()** - Returns full URL to headshot image
```php
$speaker->headshot_url // Returns asset URL or null
```

**getFullTitleAttribute()** - Combines title and company
```php
$speaker->full_title // "CEO at Acme Corp"
```

**Scopes**:
- `active()` - Filter to active speakers only
- `forEvent($eventId)` - Filter to specific event

## Planned UI Components

### Speakers List Page

**Features**:
- Table view with all speakers for an event
- Search by name, title, or company
- Filter by tags and active status
- Headshot thumbnails
- Quick actions (edit, delete, view)
- Pagination

**Columns**:
- Headshot (thumbnail)
- Name
- Title & Company
- Email
- Sessions (count)
- Content (count)
- Tags
- Actions

### Speaker Form (Create/Edit)

**Sections**:

1. **Basic Information**
   - Name (required)
   - Title
   - Company
   - Email

2. **Biography**
   - Bio textarea (rich text editor)
   - Notes textarea (internal only)

3. **Contact Information**
   - Contact person
   - Email (if different from speaker's)

4. **Headshot Upload**
   - Image upload with preview
   - Crop/resize functionality
   - Remove option

5. **Tags**
   - Checkbox grid of available tags
   - Create new tag inline
   - Max 10 tags

6. **User Account Creation**
   - Checkbox: "Create user account for this speaker"
   - Role selection: User or Viewer
   - Auto-generate credentials option
   - Send welcome email option

7. **Session Assignment**
   - Multi-select dropdown
   - Shows all sessions for the event
   - Can assign to multiple sessions

8. **Content Assignment**
   - Multi-select dropdown
   - Shows all content files for the event
   - Can assign multiple files

### Speaker Detail Page

**Overview Section**:
- Large headshot
- Name, title, company
- Email and contact person
- Bio (formatted)
- Tags
- Edit button

**Sessions Section**:
- List of sessions where speaker is presenting
- Session name, date, time
- Link to session details
- Add to session button

**Content Section**:
- List of content files assigned to speaker
- File name, type, size
- Upload date
- Download/view buttons
- Assign content button

**Activity Section**:
- Recent comments on this speaker
- Recent audit log entries
- Timeline view

**Comment Section**:
- Full commenting interface
- @mention team members
- Threaded replies

## Integration Points

### Sessions Module

**Session Form Enhancement**:
- Add "Speakers" multi-select field
- Shows all speakers for the event
- Can assign multiple speakers to a session

**Session Detail View**:
- Display assigned speakers with headshots
- Link to speaker profiles
- Quick add/remove speakers

### Content Module

**Content File Form Enhancement**:
- Add "Speakers" multi-select field
- Shows all speakers for the event
- Can assign content to multiple speakers

**Content File Detail View**:
- Display assigned speakers
- Link to speaker profiles
- Quick add/remove speakers

**Speaker Filter**:
- Add "Filter by Speaker" dropdown to content list
- Shows content for selected speaker

## User/Viewer Creation Feature

### Workflow

When creating or editing a speaker:

1. **Check "Create user account"**
   - Reveals additional fields
   - Role selection (User/Viewer)
   - Credential options

2. **Auto-generate credentials**
   - Username: Based on speaker name
   - Email: From speaker email field
   - Password: Auto-generated secure password
   - Option to send via email

3. **Manual credentials**
   - Enter custom username
   - Enter custom password
   - Confirm password

4. **Role assignment**
   - User: Full access to event
   - Viewer: Read-only access

5. **On save**:
   - Create user account
   - Link to speaker record via `user_id`
   - Send welcome email (optional)
   - Add to event team
   - Assign appropriate role

### User Account Management

**Linked Accounts**:
- Speaker profile shows linked user account
- User profile shows linked speaker record
- Bidirectional navigation

**Permissions**:
- Speakers with user accounts can login
- See their own profile
- View their assigned content
- See their session schedule
- Receive notifications

## Routes Structure

```php
Route::middleware(['auth'])->group(function () {
    // Speaker management
    Route::get('/events/{eventId}/speakers', [SpeakersController::class, 'index'])
        ->name('events.speakers.index');
    
    Route::get('/events/{eventId}/speakers/create', [SpeakersController::class, 'create'])
        ->name('events.speakers.create');
    
    Route::get('/events/{eventId}/speakers/{speakerId}', [SpeakersController::class, 'show'])
        ->name('events.speakers.show');
    
    Route::get('/events/{eventId}/speakers/{speakerId}/edit', [SpeakersController::class, 'edit'])
        ->name('events.speakers.edit');
});
```

## API Examples

### Create Speaker

```php
$speaker = Speaker::create([
    'event_id' => $eventId,
    'name' => 'John Doe',
    'title' => 'CEO',
    'company' => 'Acme Corp',
    'bio' => 'John is a technology leader...',
    'email' => 'john@acme.com',
    'contact_person' => 'Jane Smith',
    'is_active' => true,
]);

// Assign to sessions
$speaker->sessions()->attach([1, 2, 3]);

// Assign content
$speaker->contentFiles()->attach([5, 6]);

// Add tags
$speaker->tags()->attach([10, 11]);
```

### Query Speakers

```php
// Get all active speakers for an event
$speakers = Speaker::forEvent($eventId)
    ->active()
    ->with(['sessions', 'contentFiles', 'tags'])
    ->orderBy('name')
    ->get();

// Get speakers with specific tag
$speakers = Speaker::forEvent($eventId)
    ->whereHas('tags', function($query) use ($tagId) {
        $query->where('tags.id', $tagId);
    })
    ->get();

// Get speaker with all relationships
$speaker = Speaker::with([
    'event',
    'user',
    'sessions.event',
    'contentFiles',
    'tags',
    'creator',
    'updater',
    'comments.user'
])->findOrFail($speakerId);
```

### Create User Account for Speaker

```php
// Create user account
$user = User::create([
    'name' => $speaker->name,
    'email' => $speaker->email,
    'password' => Hash::make($generatedPassword),
]);

// Link to speaker
$speaker->update(['user_id' => $user->id]);

// Add to event team with role
$event->users()->attach($user->id, [
    'role_id' => $viewerRoleId,
]);

// Send welcome email
Mail::to($user)->send(new WelcomeEmail($user, $generatedPassword));
```

## Features Summary

### ✅ Implemented

- Database schema with all fields
- Speaker model with traits
- Soft deletes
- Audit logging integration
- Commentable integration
- Tag support
- Session relationships
- Content file relationships
- User account linking
- Helper methods and scopes

### 🔄 Ready for UI Implementation

- Speakers list page
- Speaker create/edit form
- Speaker detail page
- Headshot upload
- User/viewer creation workflow
- Session assignment interface
- Content assignment interface
- Comment sections
- Tag selection
- Search and filtering

### 🎯 Integration Points

- Sessions module (assign speakers)
- Content module (assign content)
- User management (create accounts)
- Activity feed (mentions)
- Audit logs (track changes)
- Tags system (categorization)

## Use Cases

### Use Case 1: Conference Speaker Management

**Scenario**: Managing speakers for a multi-day conference

1. Create speaker profiles with bios and headshots
2. Assign speakers to specific sessions
3. Link presentation files to each speaker
4. Create viewer accounts so speakers can access their materials
5. Use comments to coordinate with speakers
6. Track all changes in audit log

### Use Case 2: Webinar Series

**Scenario**: Managing recurring webinar speakers

1. Create speaker profiles once
2. Reuse speakers across multiple events
3. Assign different content to same speaker for different webinars
4. Tag speakers by topic expertise
5. Filter content by speaker
6. Track speaker participation over time

### Use Case 3: Panel Discussion

**Scenario**: Multiple speakers in one session

1. Create profiles for all panelists
2. Assign all panelists to the panel session
3. Link shared presentation materials to all speakers
4. Use comments to coordinate talking points
5. Create user accounts for panelists to review materials
6. Track last-minute changes in audit log

## Best Practices

### For Event Managers

1. **Complete Profiles**: Fill in all speaker information
2. **Upload Headshots**: Professional photos enhance credibility
3. **Assign Content Early**: Link presentations and materials
4. **Use Tags**: Categorize by topic, track, or role
5. **Create User Accounts**: Give speakers access to their materials
6. **Document Changes**: Use comments to track decisions
7. **Review Regularly**: Check audit log for updates

### For Developers

1. **Eager Load Relationships**: Avoid N+1 queries
2. **Check Permissions**: Verify user has access to event
3. **Validate Uploads**: Check file types and sizes
4. **Handle Errors**: Graceful failure for missing data
5. **Test User Creation**: Verify email sending works
6. **Optimize Queries**: Use indexes effectively
7. **Cache When Possible**: Speaker lists can be cached

## Future Enhancements

### Planned Features

1. **Speaker Portal**: Dedicated interface for speakers
2. **Schedule View**: Calendar showing speaker commitments
3. **Conflict Detection**: Warn if speaker double-booked
4. **Bio Templates**: Predefined bio formats
5. **Bulk Import**: CSV upload for multiple speakers
6. **Speaker Ratings**: Post-event feedback
7. **Travel Coordination**: Track flights and hotels
8. **Payment Tracking**: Speaker fees and expenses
9. **Contract Management**: Store speaker agreements
10. **Social Media Integration**: Pull bios from LinkedIn

### Advanced Features

- **AI Bio Generation**: Auto-generate bios from LinkedIn
- **Headshot Enhancement**: Auto-crop and optimize photos
- **Smart Scheduling**: Suggest optimal session assignments
- **Speaker Network**: Connect speakers with similar expertise
- **Analytics Dashboard**: Speaker participation metrics
- **Multi-event Tracking**: Speaker history across events

## Conclusion

The Speaker Management System foundation is complete and production-ready. The database schema, model layer, and integrations are in place. The system provides:

- ✅ Complete speaker profiles with all requested fields
- ✅ Content assignment tracking
- ✅ Session participation tracking
- ✅ User/viewer account creation capability
- ✅ Commenting and collaboration
- ✅ Tagging and categorization
- ✅ Audit logging for all changes
- ✅ Soft deletes for data safety
- ✅ Comprehensive relationships

The next step is UI implementation, which will provide the interface for managing speakers, assigning content, creating user accounts, and coordinating with the team.

---

**Module**: Speaker Management System  
**Status**: Foundation Complete, UI Pending  
**Last Updated**: October 31, 2025  
**Application**: PLANNR Event Control System
