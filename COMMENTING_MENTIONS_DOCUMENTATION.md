# Commenting and User Mentions System Documentation

## Overview

The PLANNR commenting and user mentions system provides comprehensive collaboration capabilities across all modules. Users can add comments to events, sessions, segments, cues, and content files, tag other team members using @mentions, and receive notifications when mentioned. A unified activity feed shows all mentions with read/unread tracking.

## System Architecture

### Database Structure

The commenting system is built on three interconnected tables that provide flexible, polymorphic commenting capabilities.

**comments table**: Stores all comments with polymorphic relationships allowing comments on any model. Fields include event_id for event context, user_id for the commenter, commentable_type and commentable_id for polymorphic relationship, comment text, parent_id for threaded replies, timestamps, and soft deletes.

**comment_mentions table**: Tracks user mentions within comments. Fields include comment_id, mentioned_user_id, is_read flag, read_at timestamp, and timestamps. A unique constraint prevents duplicate mentions.

**notifications table**: Stores notifications for mentioned users (already implemented). Fields include event_id, user_id (recipient), type (mention, cue_change, etc.), title, message, action_url, data (JSON), is_read flag, read_at timestamp, and timestamps.

### Models and Relationships

**Comment Model** (`App\Models\Comment`): Implements polymorphic relationships to any commentable model, supports threaded replies with parent/child relationships, automatically processes @mentions on creation, creates notifications for mentioned users, and includes scopes for filtering and querying.

**CommentMention Model** (`App\Models\CommentMention`): Tracks individual mentions with read status, provides scopes for unread and recent mentions, includes helper method to mark as read, and relates to both comment and mentioned user.

**Commentable Trait** (`App\Traits\Commentable`): Added to Event, Session, Segment, Cue, and ContentFile models. Provides comments() relationship for top-level comments with replies, allComments() relationship for all comments, and commentCountAttribute for quick count access.

### Automatic Mention Processing

When a comment is created, the system automatically extracts @mentions using regex pattern matching, finds users by username, creates CommentMention records for each unique mention, generates Notification records for mentioned users, and excludes self-mentions (users can't mention themselves).

## Features Implemented

### Polymorphic Commenting

Comments can be added to any model that uses the Commentable trait. The system currently supports Events, Sessions, Segments, Cues, and Content Files. Each comment knows its context (which event it belongs to) and what it's commenting on (the commentable model).

### Threaded Replies

Comments support threaded conversations through the parent_id relationship. Top-level comments have parent_id = null, replies reference their parent comment, and replies are loaded recursively with user information. This creates natural conversation threads.

### User Mentions

Users can mention team members using @username syntax. The system detects mentions automatically, creates mention records, generates notifications, and provides read/unread tracking. Mentioned users receive notifications they can view and mark as read.

### Soft Deletes

Comments use soft deletes for safe data management. Deleted comments are hidden from normal views but preserved in the database. Replies to deleted comments are also soft-deleted through cascade. Comments can be permanently deleted if needed.

### Event Context

All comments are associated with an event, even when commenting on sessions, segments, or cues. This provides event-scoped filtering, ensures proper permissions, and maintains data organization.

## Implementation Guide

### Adding Comments to a Model

To enable commenting on a new model, add the Commentable trait to the model class, ensure the model has a name attribute for display, and optionally add comment count to list views.

Example:
```php
use App\Traits\Commentable;

class MyModel extends Model
{
    use Commentable;
    
    // Model code...
}
```

### Creating a Comment

To create a comment programmatically:
```php
$comment = Comment::create([
    'event_id' => $event->id,
    'user_id' => auth()->id(),
    'commentable_type' => get_class($model),
    'commentable_id' => $model->id,
    'comment' => 'This is a comment with @username mention',
]);
```

The system automatically processes mentions and creates notifications.

### Creating a Reply

To create a reply to an existing comment:
```php
$reply = Comment::create([
    'event_id' => $event->id,
    'user_id' => auth()->id(),
    'commentable_type' => $parentComment->commentable_type,
    'commentable_id' => $parentComment->commentable_id,
    'comment' => 'This is a reply',
    'parent_id' => $parentComment->id,
]);
```

### Retrieving Comments

Get all top-level comments for a model:
```php
$comments = $model->comments; // Includes user and replies
```

Get all comments (including replies):
```php
$allComments = $model->allComments;
```

Get comment count:
```php
$count = $model->comment_count;
```

### Retrieving Mentions

Get all mentions for a user:
```php
$mentions = CommentMention::forUser(auth()->id())
    ->with('comment.user', 'comment.commentable')
    ->latest()
    ->get();
```

Get unread mentions:
```php
$unreadMentions = CommentMention::forUser(auth()->id())
    ->unread()
    ->with('comment.user', 'comment.commentable')
    ->latest()
    ->get();
```

### Marking Mentions as Read

Mark a single mention as read:
```php
$mention->markAsRead();
```

Mark all mentions for a comment as read:
```php
$comment->mentions()->update([
    'is_read' => true,
    'read_at' => now(),
]);
```

## User Interface Components (To Be Implemented)

### Comment Component

A reusable Livewire component for displaying and adding comments would include a comment form with @mention autocomplete, display of existing comments with user avatars and timestamps, threaded reply display, edit and delete actions for own comments, and real-time updates when new comments are added.

**Suggested Location**: `app/Livewire/Comments/CommentSection.php`

**Usage**: Include on any page that displays a commentable model, pass the model as a parameter, and the component handles all comment operations.

### Activity Feed Component

A unified view of all mentions for the current user would include a list of all comments where user was mentioned, read/unread status indicators, links to the commented item, ability to mark as read, and filtering by date range and read status.

**Suggested Location**: `app/Livewire/ActivityFeed/Index.php`

**Route**: `/activity-feed` or `/mentions`

### Notification Badge

A notification indicator in the navigation showing unread mention count, dropdown with recent mentions, and quick access to activity feed.

**Suggested Location**: `resources/views/navigation-menu.blade.php`

## Integration Points

### Events Module

Comments can be added to events for general discussion, planning notes, and team coordination. The event detail page would include a comment section at the bottom showing all event-level comments.

### Sessions Module

Comments on sessions for session-specific notes, client feedback, and production notes. The session list or detail view would show comment counts and provide access to comment threads.

### Segments Module

Comments on segments for timing notes, technical requirements, and operator instructions. Each segment card or detail view would include comment functionality.

### Cues Module

Comments on cues for execution notes, troubleshooting information, and operator feedback. The cue detail or edit view would show associated comments.

### Content Files Module

Comments on content files for version notes, approval status, and revision requests. The content library would show comment counts and provide access to file-specific discussions.

### Show Calling Interface

While show calling is focused on execution, a comment indicator could show if a cue has comments, allowing quick access to important notes during the show.

## Mention Syntax

The system recognizes mentions using the @username format. Valid mentions include @john, @mary_smith, and @techdir. The system is case-sensitive and matches exact usernames.

**Best Practices**: Use actual usernames as they appear in the system, mention users who need to see the comment, avoid excessive mentions to prevent notification fatigue, and use mentions for actionable items or questions.

## Notification Flow

When a user is mentioned, the system creates a CommentMention record with is_read = false, creates a Notification record with type = 'mention', and sets action_url to link to the commented item. The mentioned user sees an unread notification count in the UI, can view the notification in their activity feed, clicks the notification to view the comment in context, and marks the mention as read when viewed.

## Security and Permissions

### Comment Permissions

Users must be authenticated to create comments and must be assigned to the event to comment on event-related items. Users can edit or delete their own comments, and event admins can delete any comment in their events. Super admins can delete any comment system-wide.

### Mention Permissions

Users can only mention other users assigned to the same event. The system validates that mentioned users exist and prevents self-mentions. Mentioned users receive notifications regardless of their role.

### Data Privacy

Comments are scoped to events for data isolation. Soft deletes preserve comment history, and cascade deletes maintain referential integrity. User data is protected through Laravel's authentication system.

## API Reference

### Comment Model Methods

- `processMentions()`: Automatically called on creation, extracts and processes @mentions
- `getActionUrl()`: Generates URL to view the commented item
- `getCommentableNameAttribute()`: Returns the name of the commented item
- `getCommentableTypeNameAttribute()`: Returns the type of the commented item (Event, Session, etc.)

### Comment Model Scopes

- `forEvent($eventId)`: Filter comments by event
- `topLevel()`: Get only top-level comments (no replies)
- `replies()`: Get only reply comments

### CommentMention Model Methods

- `markAsRead()`: Mark mention as read with timestamp

### CommentMention Model Scopes

- `forUser($userId)`: Filter mentions by mentioned user
- `unread()`: Get only unread mentions
- `recent($days)`: Get mentions from the last N days

### Commentable Trait Methods

- `comments()`: Relationship for top-level comments with replies
- `allComments()`: Relationship for all comments
- `getCommentCountAttribute()`: Computed attribute for comment count

## Database Queries

### Get Recent Activity for User

```php
$recentMentions = CommentMention::forUser(auth()->id())
    ->recent(7)
    ->with(['comment' => function($query) {
        $query->with('user', 'commentable');
    }])
    ->latest()
    ->paginate(20);
```

### Get Unread Count

```php
$unreadCount = CommentMention::forUser(auth()->id())
    ->unread()
    ->count();
```

### Get Comments for Event

```php
$eventComments = Comment::forEvent($eventId)
    ->topLevel()
    ->with('user', 'replies.user')
    ->latest()
    ->get();
```

## Testing

### Manual Testing Steps

1. Create a comment on an event mentioning another user
2. Verify the mentioned user receives a notification
3. Check that the mention appears in the user's activity feed
4. Mark the mention as read and verify the status changes
5. Create a reply to a comment
6. Verify threaded display works correctly
7. Delete a comment and verify soft delete
8. Test comment creation on sessions, segments, cues, and content files

### Test Cases

- Comment creation with valid mention
- Comment creation with invalid username
- Comment creation with self-mention (should be ignored)
- Comment creation with multiple mentions
- Reply creation
- Mention read/unread tracking
- Comment soft delete
- Notification generation
- Activity feed display

## Troubleshooting

### Mentions Not Working

If mentions aren't being detected, verify the username is exact (case-sensitive), check that the mentioned user exists, ensure the mentioned user is assigned to the event, and review the comment text for proper @username format.

### Notifications Not Appearing

If notifications aren't being created, check that the Comment model's booted() method is being called, verify the processMentions() method is executing, ensure the Notification model is accessible, and check database for notification records.

### Comments Not Displaying

If comments aren't showing up, verify the Commentable trait is added to the model, check that comments exist in the database, ensure proper eager loading of relationships, and verify the commentable_type matches the full class name.

## Future Enhancements

### Planned Features

**Rich Text Editor**: Support for formatted text, links, and embedded media in comments.

**File Attachments**: Allow attaching files to comments for additional context.

**Emoji Reactions**: Quick reactions to comments without writing a reply.

**Comment Search**: Search across all comments within an event.

**Mention Autocomplete**: Real-time username suggestions while typing @mentions.

**Email Notifications**: Send email when users are mentioned (configurable).

**Comment Templates**: Predefined comment templates for common scenarios.

**Comment Pinning**: Pin important comments to the top of the thread.

**Comment Editing History**: Track edits to comments with version history.

**Real-Time Updates**: Use WebSockets for instant comment updates.

## Best Practices

### For Users

**Be Specific**: Provide clear, actionable comments with relevant context.

**Use Mentions Wisely**: Only mention users who need to see the comment.

**Reply in Thread**: Use replies to keep conversations organized.

**Update Status**: Mark mentions as read after addressing them.

**Clean Up**: Delete outdated or resolved comments.

### For Developers

**Eager Load**: Always eager load user and replies relationships to avoid N+1 queries.

**Scope Queries**: Use event scoping to limit data access.

**Handle Permissions**: Check user permissions before allowing comment operations.

**Validate Input**: Sanitize comment text to prevent XSS attacks.

**Index Properly**: Ensure database indexes support common queries.

## Conclusion

The commenting and user mentions system provides a robust foundation for team collaboration within PLANNR. The polymorphic architecture allows comments on any model, threaded replies enable natural conversations, automatic mention detection and notifications keep teams informed, and read/unread tracking helps users manage their activity.

The database structure and models are complete and functional. Implementation of Livewire components and UI views will complete the system, providing a seamless commenting experience throughout the application.

---

**Module**: Commenting and User Mentions  
**Status**: Foundation Complete (UI Pending)  
**Last Updated**: October 31, 2025  
**Application**: PLANNR Event Control System
