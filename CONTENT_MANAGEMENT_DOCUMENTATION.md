# Content Management System Documentation

## Overview

The Content Management System provides comprehensive file upload, organization, and version control capabilities for the PLANNR application. This module allows users to manage media files, presentations, documents, and other content associated with events, with full integration into the cue system.

## System Features

### File Management
- **Upload Files**: Support for audio, video, presentations, documents, images, and other file types
- **File Organization**: Categorize files for easy discovery
- **Version Control**: Track multiple versions of files with change notes
- **Search & Filter**: Find files quickly by name, type, or category
- **File Metadata**: Automatic tracking of file size, type, and upload information
- **Download**: Direct download of current or previous versions

### Version Control
- **Automatic Versioning**: Each file upload creates a new version
- **Version History**: View all versions with upload date and user
- **Change Notes**: Document what changed in each version
- **Version Comparison**: See file size and metadata for each version
- **Current Version Tracking**: System always knows which version is current

### Integration Features
- **Event Association**: All files belong to specific events
- **Cue Integration**: Files can be referenced in cues via filename field
- **User Tracking**: Automatic tracking of who uploaded each file
- **Audit Logging**: All file operations are logged
- **Soft Deletes**: Safe deletion with recovery options

## Database Structure

### content_categories Table
- `id`: Primary key
- `event_id`: Foreign key to events (nullable for system categories)
- `name`: Category name
- `slug`: URL-friendly identifier
- `description`: Optional description
- `color`: Hex color code for visual identification
- `is_system`: Boolean for system-wide categories
- `is_active`: Boolean for active/inactive status
- `sort_order`: Display order
- `timestamps`: Created/updated timestamps

### content_files Table
- `id`: Primary key
- `event_id`: Foreign key to events
- `category_id`: Foreign key to content_categories (nullable)
- `name`: File display name
- `slug`: Unique URL-friendly identifier
- `description`: Optional file description
- `file_type`: Type (audio, video, presentation, document, image, other)
- `mime_type`: MIME type of current version
- `current_file_path`: Path to current version file
- `current_file_size`: Size in bytes of current version
- `current_version`: Current version number
- `metadata`: JSON field for additional data
- `is_active`: Boolean for active/inactive status
- `created_by`: Foreign key to users
- `updated_by`: Foreign key to users
- `timestamps`: Created/updated timestamps
- `deleted_at`: Soft delete timestamp

### content_file_versions Table
- `id`: Primary key
- `content_file_id`: Foreign key to content_files
- `version_number`: Version number (1, 2, 3, etc.)
- `file_path`: Path to this version's file
- `file_size`: Size in bytes
- `mime_type`: MIME type
- `metadata`: JSON field for version-specific data
- `change_notes`: Description of changes in this version
- `uploaded_by`: Foreign key to users
- `timestamps`: Created/updated timestamps

## Models

### ContentCategory Model
**Location**: `app/Models/ContentCategory.php`

**Relationships**:
- `event()`: Belongs to Event
- `contentFiles()`: Has many ContentFile

**Scopes**:
- `active()`: Filter active categories
- `system()`: Filter system-wide categories
- `forEvent($eventId)`: Get categories for specific event or system-wide
- `ordered()`: Order by sort_order and name

**Features**:
- Auto-generates slug from name
- Auto-increments sort_order

### ContentFile Model
**Location**: `app/Models/ContentFile.php`

**Traits**:
- `SoftDeletes`: Safe deletion
- `Auditable`: Change tracking

**Relationships**:
- `event()`: Belongs to Event
- `category()`: Belongs to ContentCategory
- `versions()`: Has many ContentFileVersion (ordered by version desc)
- `currentVersion()`: Has one ContentFileVersion (current version)
- `creator()`: Belongs to User (created_by)
- `updater()`: Belongs to User (updated_by)

**Scopes**:
- `active()`: Filter active files
- `forEvent($eventId)`: Filter by event
- `byType($type)`: Filter by file type
- `byCategory($categoryId)`: Filter by category
- `ordered()`: Order by created_at desc

**Helper Methods**:
- `getFileTypeIconAttribute()`: Returns emoji icon for file type
- `getFormattedFileSizeAttribute()`: Returns human-readable file size
- `getDownloadUrlAttribute()`: Returns public download URL
- `createNewVersion($filePath, $fileSize, $mimeType, $metadata, $changeNotes)`: Creates new version

### ContentFileVersion Model
**Location**: `app/Models/ContentFileVersion.php`

**Relationships**:
- `contentFile()`: Belongs to ContentFile
- `uploader()`: Belongs to User (uploaded_by)

**Helper Methods**:
- `getFormattedFileSizeAttribute()`: Returns human-readable file size
- `getDownloadUrlAttribute()`: Returns public download URL

## User Interface

### Content Library Page
**Route**: `/events/{eventId}/content`  
**Component**: `App\Livewire\Content\Index`

**Features**:
- Grid view of all files with visual cards
- Search by name or description
- Filter by file type (audio, video, presentation, etc.)
- Filter by category
- Upload new files via modal
- View version history
- Download files
- Delete files with confirmation

**File Card Display**:
- File type icon
- File name
- File size
- Description (if provided)
- File type badge
- Category badge (if assigned)
- Version number badge
- Upload information (user and date)
- Action buttons (Download, Versions, Delete)

### Upload Modal
**Features**:
- File selection (max 500MB)
- Name input (required)
- File type selection (required)
- Category selection (optional)
- Description textarea (optional)
- Real-time validation
- Progress indication during upload

### Version History Modal
**Features**:
- List of all versions (newest first)
- Current version indicator
- Version number, file size, uploader, date
- Change notes display
- Download button for each version

## File Types

The system supports six file type categories:

1. **Audio** 🎵
   - Music tracks
   - Sound effects
   - Voice recordings
   - Audio cues

2. **Video** 🎬
   - Video playback files
   - Presentations with video
   - Background loops
   - Video cues

3. **Presentation** 📊
   - PowerPoint files
   - Keynote files
   - PDF presentations
   - Slide decks

4. **Document** 📄
   - Scripts
   - Rundowns
   - PDFs
   - Text documents

5. **Image** 🖼️
   - Graphics
   - Photos
   - Logos
   - Background images

6. **Other** 📁
   - Any other file types

## Usage Guide

### Uploading a File

1. Navigate to Events → Click Content Library icon for an event
2. Click "Upload File" button
3. Select file from your computer
4. Enter a descriptive name
5. Select the file type
6. Optionally select a category
7. Optionally add a description
8. Click "Upload"

The system will:
- Upload the file to secure storage
- Create a content file record
- Create version 1 record
- Track who uploaded it and when

### Uploading a New Version

To upload a new version of an existing file:
1. Delete the old file or keep it for reference
2. Upload the new file with the same name
3. The system will create a new content file entry

**Note**: Future enhancement will add "Upload New Version" functionality to update existing files.

### Viewing Version History

1. Find the file in the Content Library
2. Click the clock icon (Versions button)
3. View all versions with details
4. Download any previous version if needed
5. See who uploaded each version and when

### Downloading Files

- Click the download icon on any file card
- Or click "Download" in the version history modal
- File will download directly to your browser

### Deleting Files

1. Click the delete icon (trash can)
2. Confirm deletion in the modal
3. File and all versions are soft-deleted
4. Physical files are removed from storage

**Warning**: Deletion removes all versions and cannot be undone.

### Searching and Filtering

**Search**:
- Type in the search box
- Searches file name and description
- Updates results in real-time

**Filter by Type**:
- Select from dropdown: All Types, Audio, Video, etc.
- Shows only files of selected type

**Filter by Category**:
- Select from dropdown
- Shows only files in selected category

**Clear Filters**:
- Change dropdown to "All Types" or "All Categories"
- Or clear the search box

## Integration with Cues

The content management system integrates with the cue system through the `filename` field:

### Current Integration
- Cues have a `filename` text field
- Users manually enter the filename from the content library
- Filename references the content file

### Future Enhancements
- Dropdown selector showing available files filtered by type
- For Audio cues: show only audio files
- For Video cues: show only video files
- Direct link from cue to content file
- Preview files directly from cue form
- Auto-populate filename when selecting from dropdown

## Storage Configuration

### File Storage Location
- Files are stored in `storage/app/public/content/{eventId}/`
- Each event has its own subdirectory
- Files are organized by event for easy management

### Public Access
- Files are accessible via the `public/storage` symlink
- URLs are generated automatically
- Direct download links are provided

### File Size Limits
- Maximum upload size: 500MB per file
- Configurable in `app/Livewire/Content/Index.php`
- Adjust based on server capabilities

## Best Practices

### File Naming
- Use descriptive names that identify the content
- Include version info in the name if helpful
- Example: "Opening Video v2", "Keynote Audio Final"

### File Organization
- Create categories for different content types
- Use consistent naming conventions
- Add descriptions to help team members find files

### Version Control
- Upload new versions when content changes
- Add change notes to document what changed
- Keep previous versions for rollback if needed

### File Management
- Regularly review and archive old files
- Delete unused files to save storage space
- Keep file sizes optimized for performance

## Troubleshooting

### Upload Fails
- **File too large**: Reduce file size or increase limit
- **Invalid file type**: Check MIME type restrictions
- **Permission error**: Verify storage directory permissions
- **Timeout**: Increase PHP max_execution_time for large files

### File Not Found
- **Broken link**: File may have been deleted
- **Storage link missing**: Run `php artisan storage:link`
- **Wrong path**: Check file_path in database

### Version Not Showing
- **Database issue**: Check content_file_versions table
- **Relationship problem**: Verify foreign keys are correct

## Security Considerations

### Access Control
- All routes require authentication
- Users must be logged in to upload/download
- Event-based access control (users see only their event files)

### File Validation
- MIME type checking on upload
- File size limits enforced
- Malicious file detection (future enhancement)

### Storage Security
- Files stored outside public directory
- Accessed via Laravel storage system
- Symlink provides controlled access

## Future Enhancements

### Planned Features
1. **Bulk Upload**: Upload multiple files at once
2. **Drag & Drop**: Drag files directly into browser
3. **File Preview**: Preview images, PDFs, videos in browser
4. **Advanced Search**: Search by metadata, tags, date ranges
5. **Folder Structure**: Organize files in folders/subfolders
6. **File Sharing**: Share files with specific users or teams
7. **CDN Integration**: Serve files from CDN for better performance
8. **Compression**: Automatic file compression for storage efficiency
9. **Thumbnails**: Generate thumbnails for images and videos
10. **Direct Cue Integration**: Select files directly when creating cues

### Version Control Enhancements
1. **Update Existing File**: Upload new version to existing file record
2. **Restore Previous Version**: Make an old version current
3. **Compare Versions**: Side-by-side comparison of versions
4. **Version Branching**: Create alternative versions

## Technical Reference

### Routes
```php
Route::get('/events/{eventId}/content', App\Livewire\Content\Index::class)->name('content.index');
```

### Livewire Components
- `App\Livewire\Content\Index`: Main content library interface

### Models
- `App\Models\ContentFile`
- `App\Models\ContentFileVersion`
- `App\Models\ContentCategory`

### Migrations
- `2025_10_31_192520_create_content_categories_table`
- `2025_10_31_192520_create_content_files_table`
- `2025_10_31_192520_create_content_file_versions_table`

### Storage Paths
- Physical files: `storage/app/public/content/{eventId}/`
- Public access: `public/storage/content/{eventId}/`

## Support

For issues or questions about the content management system:
1. Check this documentation
2. Review the troubleshooting section
3. Check audit logs for file operations
4. Contact system administrator

---

**Last Updated**: October 31, 2025  
**Version**: 1.0  
**Module**: Content Management System
