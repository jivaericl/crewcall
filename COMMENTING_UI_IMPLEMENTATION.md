# Commenting and Mentions UI Implementation Guide

## Overview

The complete UI for the commenting and user mentions system has been successfully implemented. This document provides comprehensive guidance on using the commenting features, integrating comment sections into views, and managing mentions through the activity feed.

## What Has Been Implemented

### 1. Comment Section Component

**Location**: `app/Livewire/Comments/CommentSection.php`  
**View**: `resources/views/livewire/comments/comment-section.blade.php`

A fully-featured Livewire component that provides:
- Comment form with @mention autocomplete
- Real-time user suggestions while typing @username
- Threaded reply support
- Edit and delete functionality for own comments
- Visual distinction between top-level comments and replies
- User avatars and timestamps
- Responsive design with dark mode support

**Key Features**:
- **@Mention Autocomplete**: As users type @, the system shows matching usernames from the event team
- **Threaded Conversations**: Replies are visually nested under parent comments
- **Edit/Delete**: Users can edit or delete their own comments; super admins can delete any comment
- **Real-time Updates**: Uses Livewire events to refresh comments after actions
- **Validation**: Ensures comments are not empty and under 5000 characters

### 2. Activity Feed Component

**Location**: `app/Livewire/ActivityFeed/Index.php`  
**View**: `resources/views/livewire/activity-feed/index.blade.php`  
**Route**: `/activity-feed`

A comprehensive activity feed showing all mentions for the current user with:
- List of all comments where user was mentioned
- Read/unread status indicators (blue border for unread)
- Filtering by status (all, unread, read)
- Time range filtering (7, 30, 90 days, or all time)
- Mark individual mentions as read
- Mark all mentions as read at once
- Direct links to view comments in context
- Comment preview and event/item context
- Pagination for large numbers of mentions

**Visual Indicators**:
- Unread mentions have a blue border and "New" badge
- Read mentions show when they were read
- User avatars for comment authors
- Event and item context below each mention

### 3. Navigation Integration

**Location**: `resources/views/navigation-menu.blade.php`

Added "Activity" link to main navigation with:
- Unread mention count badge (red circle with number)
- Badge only appears when there are unread mentions
- Active state highlighting when on activity feed page
- Responsive design matching existing navigation

### 4. Routes

Added to `routes/web.php`:
```php
Route::get('/activity-feed', App\Livewire\ActivityFeed\Index::class)->name('activity-feed.index');
```

## How to Use the Commenting System

### Adding Comments to a View

To add commenting functionality to any page, include the comment section component in your Blade view:

```blade
@if($model)
    @livewire('comments.comment-section', [
        'commentable' => $model,
        'eventId' => $eventId
    ])
@endif
```

**Parameters**:
- `commentable`: The model instance (Event, Session, Segment, Cue, or ContentFile)
- `eventId`: The ID of the event for context and permissions

**Example for Event Detail Page**:
```blade
<!-- At the bottom of the event detail view -->
<div class="mt-8">
    @livewire('comments.comment-section', [
        'commentable' => $event,
        'eventId' => $event->id
    ])
</div>
```

**Example for Cue Edit Page**:
```blade
<!-- After the cue form -->
@if($cue && $cue->exists)
    @livewire('comments.comment-section', [
        'commentable' => $cue,
        'eventId' => $segment->session->event_id
    ])
@endif
```

### Writing Comments with @Mentions

1. **Type a Comment**: Click in the comment textarea
2. **Mention Someone**: Type @ followed by the person's username
3. **Select from Suggestions**: A dropdown appears with matching team members
4. **Click to Complete**: Click a suggestion to complete the mention
5. **Post Comment**: Click "Post Comment" to submit

**Mention Syntax**:
- Type `@` to trigger autocomplete
- Continue typing to filter suggestions
- Suggestions show name and email
- Only shows users assigned to the event
- Excludes yourself from suggestions

### Replying to Comments

1. Click the "Reply" button under any comment
2. The comment form changes to "Reply" mode
3. Type your reply (can include @mentions)
4. Click "Post Reply" or "Cancel Reply"
5. Reply appears nested under the parent comment

### Editing Comments

1. Click the pencil icon on your own comment
2. The comment text becomes editable
3. Make your changes
4. Click "Save" or "Cancel"
5. Edited comments update immediately

### Deleting Comments

1. Click the trash icon on your comment (or any comment if super admin)
2. Confirm the deletion
3. Comment is soft-deleted and removed from view
4. Replies to deleted comments are also removed

## Activity Feed Usage

### Accessing the Activity Feed

Click "Activity" in the main navigation to view all your mentions.

### Filtering Mentions

**By Status**:
- **All Mentions**: Shows every mention regardless of read status
- **Unread**: Shows only mentions you haven't marked as read
- **Read**: Shows mentions you've already viewed

**By Time Range**:
- **Last 7 days**: Recent mentions from the past week
- **Last 30 days**: Mentions from the past month (default)
- **Last 90 days**: Mentions from the past quarter
- **All time**: Every mention you've ever received

### Managing Mentions

**Mark Individual as Read**:
1. Find the mention in your feed
2. Click "Mark as Read" button
3. The blue border disappears and status updates

**Mark All as Read**:
1. Click "Mark All as Read" button at the top
2. All unread mentions are marked as read at once
3. Useful for clearing your notification backlog

**View Comment in Context**:
1. Click "View Comment" button on any mention
2. Navigates to the page containing the comment
3. See the full context of the discussion

### Understanding the Activity Feed

Each mention card shows:
- **Who mentioned you**: Name and avatar of the commenter
- **Where**: The type of item (Event, Session, Segment, Cue, Content File)
- **When**: How long ago the mention occurred
- **Comment Preview**: First few lines of the comment
- **Context**: Event name and item name
- **Status**: "New" badge for unread, or "Read X ago" for read mentions

## Integration Examples

### Events Module

Add to the event detail or edit page:

```blade
<!-- resources/views/livewire/events/form.blade.php -->
<!-- After the event form -->
@if($eventId)
    @php
        $event = \App\Models\Event::find($eventId);
    @endphp
    @if($event)
        @livewire('comments.comment-section', [
            'commentable' => $event,
            'eventId' => $event->id
        ])
    @endif
@endif
```

### Sessions Module

Add to session list or detail view:

```blade
<!-- Show comment count in session list -->
<div class="text-sm text-zinc-500">
    {{ $session->comment_count }} {{ Str::plural('comment', $session->comment_count) }}
</div>

<!-- Add comment section to session detail -->
@livewire('comments.comment-section', [
    'commentable' => $session,
    'eventId' => $session->event_id
])
```

### Segments Module

```blade
@livewire('comments.comment-section', [
    'commentable' => $segment,
    'eventId' => $segment->session->event_id
])
```

### Cues Module

```blade
@livewire('comments.comment-section', [
    'commentable' => $cue,
    'eventId' => $cue->segment->session->event_id
])
```

### Content Files Module

```blade
@livewire('comments.comment-section', [
    'commentable' => $contentFile,
    'eventId' => $contentFile->event_id
])
```

## Technical Details

### Component Methods

**CommentSection Component**:
- `addComment()`: Creates a new comment or reply
- `startReply($commentId)`: Initiates reply mode
- `cancelReply()`: Exits reply mode
- `startEdit($commentId, $currentText)`: Enters edit mode
- `saveEdit()`: Saves edited comment
- `cancelEdit()`: Exits edit mode
- `deleteComment($commentId)`: Soft deletes a comment
- `loadUserSuggestions()`: Fetches matching users for @mentions
- `selectUser($username)`: Completes @mention with selected user

**ActivityFeed Component**:
- `markAsRead($mentionId)`: Marks single mention as read
- `markAllAsRead()`: Marks all unread mentions as read
- `updatedFilter()`: Refreshes list when filter changes
- `updatedDays()`: Refreshes list when time range changes

### Livewire Events

The comment system uses Livewire events for real-time updates:
- `comment-added`: Fired when a new comment is posted
- `comment-updated`: Fired when a comment is edited
- `comment-deleted`: Fired when a comment is deleted
- `mentions-updated`: Fired when mentions are marked as read

Components listen for these events and refresh automatically.

### Permissions

**Comment Creation**:
- User must be authenticated
- User must be assigned to the event

**Comment Editing**:
- User can only edit their own comments

**Comment Deletion**:
- Users can delete their own comments
- Super admins can delete any comment

**Mention Viewing**:
- Users can only view their own mentions
- Mentions are filtered by `mentioned_user_id`

## Styling and Customization

### Dark Mode Support

All commenting UI components support dark mode:
- Light backgrounds: `bg-white`, `bg-zinc-50`
- Dark backgrounds: `dark:bg-zinc-800`, `dark:bg-zinc-900`
- Text colors: `text-zinc-900 dark:text-white`
- Border colors: `border-zinc-200 dark:border-zinc-700`

### Color Coding

- **User Avatars**: Blue for comment authors, Green for reply authors
- **Unread Mentions**: Blue border (`border-blue-500`)
- **Read Mentions**: Gray border (`border-zinc-200`)
- **Notification Badge**: Red background (`bg-red-500`)
- **New Badge**: Blue badge (`color="blue"`)

### Responsive Design

The commenting UI is fully responsive:
- Mobile: Stacked layout with full-width components
- Tablet: Optimized spacing and font sizes
- Desktop: Full feature set with optimal spacing

## Best Practices

### For Users

1. **Be Specific**: Provide clear context in comments
2. **Use @Mentions**: Tag people who need to see the comment
3. **Reply in Thread**: Keep conversations organized
4. **Check Activity Feed**: Review mentions regularly
5. **Mark as Read**: Keep your feed organized

### For Developers

1. **Always Pass Event ID**: Required for permissions and context
2. **Check Model Exists**: Verify model is loaded before showing comments
3. **Eager Load**: Use `with('comments')` to avoid N+1 queries
4. **Handle Permissions**: Check user access before showing comment sections
5. **Test @Mentions**: Verify autocomplete works with your user data

## Troubleshooting

### Comments Not Appearing

**Check**:
- Model has Commentable trait
- Event ID is correct
- User is assigned to the event
- Comments exist in database

**Solution**:
```php
// Verify trait is added
use App\Traits\Commentable;

class MyModel extends Model
{
    use Commentable;
}

// Check comments exist
$model->comments()->count();
```

### @Mentions Not Working

**Check**:
- Users are assigned to the event
- Usernames match exactly
- JavaScript is enabled
- Livewire is functioning

**Solution**:
```php
// Verify users are assigned
$event->users()->get();

// Check mention detection
preg_match_all('/@(\w+)/', $comment, $matches);
```

### Notification Badge Not Showing

**Check**:
- Unread mentions exist
- Navigation menu is rendering
- CSS is compiled

**Solution**:
```php
// Check unread count
\App\Models\CommentMention::forUser(auth()->id())->unread()->count();

// Rebuild assets
pnpm run build
```

## Future Enhancements

### Planned Features

1. **Rich Text Editor**: Support for formatted text and links
2. **File Attachments**: Attach files to comments
3. **Emoji Reactions**: Quick reactions without replies
4. **Comment Search**: Search across all comments
5. **Email Notifications**: Send email when mentioned (optional)
6. **Real-Time Updates**: Use WebSockets for instant updates
7. **Comment Templates**: Predefined comment templates
8. **Comment Pinning**: Pin important comments to top
9. **Edit History**: Track comment edits with versions
10. **Mention Groups**: Mention entire teams or roles

### Enhancement Ideas

- **Comment Analytics**: Track comment activity per event
- **Notification Preferences**: Customize notification settings
- **Comment Export**: Export comments to PDF or CSV
- **Comment Moderation**: Flag and review comments
- **Comment Voting**: Upvote/downvote comments
- **Comment Sorting**: Sort by date, popularity, or relevance

## Conclusion

The commenting and user mentions system is now fully functional with a complete UI. Users can collaborate on any aspect of an event, mention team members to bring attention to specific items, and manage their mentions through a unified activity feed.

The system integrates seamlessly with all existing modules and provides a professional, intuitive interface for team communication throughout the event production process.

---

**Module**: Commenting and User Mentions UI  
**Status**: Complete and Production-Ready  
**Last Updated**: October 31, 2025  
**Application**: PLANNR Event Control System
