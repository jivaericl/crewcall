# Speaker Management System - Complete Documentation

## Overview

The Speaker Management system is a comprehensive module within PLANNR that allows you to manage speaker profiles, assign speakers to sessions, track speaker content, and optionally create user accounts for speakers to access the system.

## Features

### Core Features

1. **Speaker Profiles**
   - Full name, title, company information
   - Biography and internal notes
   - Contact person and email
   - Headshot upload capability
   - Active/inactive status tracking
   - Tag categorization

2. **Session Assignment**
   - Many-to-many relationship with sessions
   - Speakers can be assigned to multiple sessions
   - Sessions can have multiple speakers
   - Easy selection interface in session forms

3. **Content Management Integration**
   - Track which content files are assigned to speakers
   - Assign content to speakers during upload
   - View all speaker content in one place

4. **User Account Creation**
   - Optional user account creation for speakers
   - Allows speakers to log in and access the system
   - Linked to speaker profile for easy management

5. **Full System Integration**
   - Comments and @mentions support
   - Audit logging for all changes
   - Tag system integration
   - Soft deletes (can restore deleted speakers)
   - User tracking (created by, updated by)
   - Dark mode support
   - Mobile responsive design

## Database Schema

### Tables Created

#### `speakers` Table
- `id` - Primary key
- `event_id` - Foreign key to events
- `name` - Speaker full name (required)
- `title` - Job title
- `company` - Company name
- `full_title` - Computed title (e.g., "CEO at Company")
- `bio` - Biography (text)
- `notes` - Internal notes (text)
- `contact_person` - Contact person name
- `email` - Speaker email
- `headshot_url` - URL to headshot image
- `user_id` - Optional link to user account
- `is_active` - Active status (default: true)
- `created_by` - User who created the record
- `updated_by` - User who last updated the record
- `created_at`, `updated_at`, `deleted_at` - Timestamps

#### `session_speaker` Pivot Table
- `id` - Primary key
- `session_id` - Foreign key to sessions
- `speaker_id` - Foreign key to speakers
- `created_at`, `updated_at` - Timestamps

#### `content_file_speaker` Pivot Table
- `id` - Primary key
- `content_file_id` - Foreign key to content_files
- `speaker_id` - Foreign key to speakers
- `created_at`, `updated_at` - Timestamps

## File Structure

### Backend (Livewire Components)

```
app/Livewire/Speakers/
├── Index.php       # Speaker list with search, filters, pagination
├── Form.php        # Create/edit speaker form with validation
└── Show.php        # Speaker detail view
```

### Frontend (Blade Views)

```
resources/views/livewire/speakers/
├── index.blade.php # Speaker list UI
├── form.blade.php  # Speaker form UI
└── show.blade.php  # Speaker detail UI
```

### Models

```
app/Models/Speaker.php
```

### Routes

```php
// In routes/web.php
Route::get('/events/{eventId}/speakers', App\Livewire\Speakers\Index::class)
    ->name('events.speakers.index');
Route::get('/events/{eventId}/speakers/create', App\Livewire\Speakers\Form::class)
    ->name('events.speakers.create');
Route::get('/events/{eventId}/speakers/{speakerId}/edit', App\Livewire\Speakers\Form::class)
    ->name('events.speakers.edit');
Route::get('/events/{eventId}/speakers/{speakerId}', App\Livewire\Speakers\Show::class)
    ->name('events.speakers.show');
```

## Model Relationships

### Speaker Model

```php
// Belongs to
- event() - The event this speaker belongs to
- user() - Optional user account linked to speaker
- creator() - User who created the speaker
- updater() - User who last updated the speaker

// Has many
- comments() - Comments on this speaker (polymorphic)

// Many to many
- sessions() - Sessions this speaker is assigned to
- contentFiles() - Content files assigned to this speaker
- tags() - Tags categorizing this speaker
```

### Integration with Other Models

**Session Model** - Added relationship:
```php
public function speakers()
{
    return $this->belongsToMany(Speaker::class, 'session_speaker')
        ->withTimestamps();
}
```

**ContentFile Model** - Added relationship:
```php
public function speakers()
{
    return $this->belongsToMany(Speaker::class, 'content_file_speaker')
        ->withTimestamps();
}
```

## User Interface

### Speaker List Page (`/events/{id}/speakers`)

**Features:**
- Search by name, title, company, email
- Filter by active/inactive status
- Filter by tags
- Pagination (15 per page)
- Quick actions: View, Edit, Delete
- Create new speaker button
- Statistics: Total speakers, active speakers, speakers with user accounts

**Columns Displayed:**
- Headshot (thumbnail or initials)
- Name
- Title & Company
- Email
- Sessions count
- Content files count
- Tags
- Status (active/inactive badge)
- Actions

### Speaker Form Page (Create/Edit)

**Sections:**

1. **Basic Information**
   - Name (required, min 3 chars)
   - Title
   - Company
   - Email (validated format)
   - Contact person

2. **Headshot Upload**
   - File upload field
   - Accepts images (jpg, png, gif, webp)
   - Max size: 2MB
   - Preview after upload

3. **Biography & Notes**
   - Biography (public-facing, max 5000 chars)
   - Internal notes (private, max 5000 chars)

4. **User Account Creation**
   - Checkbox to create user account
   - Conditional fields:
     - Password (required if creating account)
     - Password confirmation
   - Links speaker to user account

5. **Categorization**
   - Tag selection (multi-select checkboxes)
   - Active/inactive toggle

6. **Actions**
   - Save button
   - Cancel button (returns to list)

### Speaker Detail Page (`/events/{id}/speakers/{speakerId}`)

**Layout:** Two-column layout

**Left Column:**
- Headshot (large)
- Name and title
- Email and contact person
- Status badges (inactive, has account)
- Tags
- Biography section
- Internal notes section

**Right Column:**
- Sessions list (with links to edit)
- Content files list (with download links)
- Activity timeline (created, updated)
- Comments section with @mentions

## Integration Points

### 1. Events List Page

Added "Speakers" button to event actions:
- Icon: User profile icon
- Links to: `/events/{id}/speakers`
- Located between "Content Library" and "Show Calling" buttons

### 2. Session Form

Added "Speakers" section:
- Multi-select checkboxes for all active speakers
- Shows speaker name and title
- Link to create new speaker if none exist
- Saves speaker assignments on form submit

### 3. Content Upload Modal

Added "Assign to Speakers" section:
- Multi-select checkboxes for all active speakers
- Assigns content to speakers on upload
- Helps track which content belongs to which speaker

## Validation Rules

### Speaker Form Validation

```php
'name' => 'required|string|max:255|min:3'
'title' => 'nullable|string|max:255'
'company' => 'nullable|string|max:255'
'email' => 'nullable|email|max:255'
'contact_person' => 'nullable|string|max:255'
'bio' => 'nullable|string|max:5000'
'notes' => 'nullable|string|max:5000'
'headshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
'user_id' => 'nullable|exists:users,id'
'is_active' => 'boolean'
```

### User Account Creation Validation

```php
'create_user' => 'boolean'
'user_password' => 'required_if:create_user,true|min:8'
'user_password_confirmation' => 'required_if:create_user,true|same:user_password'
```

## Traits Used

The Speaker model uses the following traits:

1. **SoftDeletes** - Enables soft deletion (can restore deleted speakers)
2. **Auditable** - Automatically logs all changes to audit_logs table
3. **Commentable** - Enables commenting and @mentions on speakers

## Permissions

The system uses role-based permissions. Relevant permissions for speakers:

- `view_speakers` - View speaker list and details
- `create_speakers` - Create new speakers
- `edit_speakers` - Edit existing speakers
- `delete_speakers` - Delete speakers
- `manage_speakers` - Full speaker management access

## Usage Examples

### Creating a Speaker

1. Navigate to Events list
2. Click "Speakers" button for the desired event
3. Click "Create Speaker" button
4. Fill in required fields (name at minimum)
5. Optionally upload headshot
6. Optionally create user account
7. Select tags for categorization
8. Click "Save"

### Assigning Speakers to Sessions

1. Edit a session
2. Scroll to "Speakers" section
3. Check boxes for speakers to assign
4. Save the session
5. Speakers will appear on session detail page

### Assigning Content to Speakers

1. Go to Content Library
2. Click "Upload File"
3. Fill in file details
4. Scroll to "Assign to Speakers"
5. Check boxes for relevant speakers
6. Upload the file
7. Content will appear on speaker detail page

### Creating User Account for Speaker

**Option 1: During speaker creation**
1. Check "Create user account for this speaker"
2. Enter password and confirmation
3. Save speaker
4. User account is automatically created and linked

**Option 2: After speaker exists**
1. Edit the speaker
2. Check "Create user account for this speaker"
3. Enter password and confirmation
4. Save speaker
5. User account is created and linked

## API Endpoints (Livewire Actions)

### Index Component

- `updatingSearch()` - Resets pagination when searching
- `updatingStatusFilter()` - Resets pagination when filtering
- `updatingTagFilter()` - Resets pagination when filtering
- `confirmDelete($id)` - Shows delete confirmation
- `delete()` - Soft deletes the speaker

### Form Component

- `mount($eventId, $speakerId = null)` - Initializes form
- `updated($propertyName)` - Real-time validation
- `save()` - Saves speaker (create or update)
- `updatedHeadshot()` - Handles headshot upload

### Show Component

- `mount($eventId, $speakerId)` - Loads speaker details

## Technical Notes

### Headshot Storage

- Stored in: `storage/app/public/speakers/{event_id}/`
- Filename format: `{slug}-{timestamp}.{extension}`
- Accessible via: `storage/speakers/{event_id}/{filename}`
- Symlink required: `php artisan storage:link`

### User Account Creation

When creating a user account for a speaker:
1. User is created with speaker's email
2. User name is set to speaker's name
3. Password is hashed using bcrypt
4. User is automatically assigned to the event
5. Default role can be configured (typically "Speaker" role)
6. Speaker record is updated with `user_id`

### Full Title Computation

The `full_title` field is automatically computed:
- If title and company exist: "{title} at {company}"
- If only title: "{title}"
- If only company: "at {company}"
- If neither: null

### Performance Considerations

- Speaker list uses pagination (15 per page)
- Eager loading used for relationships (sessions, content, tags)
- Indexes on: `event_id`, `email`, `is_active`, `user_id`
- Soft deletes indexed for query performance

## Future Enhancements

Potential features for future development:

1. **Speaker Availability Calendar**
   - Mark dates when speakers are available/unavailable
   - Conflict detection when scheduling sessions

2. **Speaker Portal**
   - Dedicated dashboard for speakers with user accounts
   - View assigned sessions and content
   - Update own profile information

3. **Speaker Import/Export**
   - Bulk import speakers from CSV/Excel
   - Export speaker list with all details

4. **Speaker Communication**
   - Send emails to speakers directly from the system
   - Track communication history

5. **Speaker Documents**
   - Attach contracts, agreements, W9 forms
   - Track document status (pending, signed, etc.)

6. **Speaker Travel & Accommodation**
   - Track flight details, hotel bookings
   - Manage speaker expenses

7. **Speaker Ratings & Feedback**
   - Collect feedback after events
   - Rate speaker performance

## Troubleshooting

### Common Issues

**Issue:** Headshot not displaying
- **Solution:** Run `php artisan storage:link` to create symlink

**Issue:** User account creation fails
- **Solution:** Check that email is unique and password meets requirements

**Issue:** Speakers not appearing in session form
- **Solution:** Ensure speakers are marked as active (`is_active = true`)

**Issue:** Cannot delete speaker
- **Solution:** Check permissions and ensure user has `delete_speakers` permission

**Issue:** Tags not saving
- **Solution:** Verify tags exist and are not exceeding the limit (10 tags max)

## Testing Checklist

- [ ] Create a new speaker with all fields
- [ ] Create a speaker with minimal fields (name only)
- [ ] Upload a headshot
- [ ] Create user account for speaker
- [ ] Edit existing speaker
- [ ] Assign speaker to session
- [ ] Assign content to speaker
- [ ] Add tags to speaker
- [ ] Add comment to speaker with @mention
- [ ] Search for speaker by name
- [ ] Filter speakers by status
- [ ] Filter speakers by tag
- [ ] View speaker detail page
- [ ] Delete speaker (soft delete)
- [ ] Verify audit log entries
- [ ] Test dark mode display
- [ ] Test mobile responsive layout

## Conclusion

The Speaker Management system is now fully integrated into PLANNR, providing comprehensive speaker profile management, session assignment, content tracking, and optional user account creation. The system follows all established patterns in the application and integrates seamlessly with existing modules.

---

**Version:** 1.0  
**Last Updated:** 2025  
**Author:** PLANNR Development Team
