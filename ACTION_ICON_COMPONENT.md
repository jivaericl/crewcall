# Action Icon Component

## Overview

The `<x-action-icon>` component provides a simple, consistent way to use LineIcons for action buttons throughout the application. It automatically maps action names to the correct icons from `config/icons.php`.

## Benefits

1. **Consistency**: All action buttons use the same icon system
2. **Maintainability**: Change an icon once in config, updates everywhere
3. **Simplicity**: One-line component instead of 5+ lines of SVG code
4. **Type Safety**: Action names are defined in config
5. **Dark Mode**: Automatically works with dark/light themes

## Usage

### Basic Usage

```blade
<x-action-icon action="edit" />
<x-action-icon action="delete" />
<x-action-icon action="view" />
```

### With Custom Size

```blade
<x-action-icon action="edit" size="w-5 h-5" />
<x-action-icon action="delete" size="w-6 h-6" />
```

### With Additional Classes

```blade
<x-action-icon action="edit" class="text-blue-600" />
<x-action-icon action="delete" class="text-red-600" />
```

### In Flux Buttons

```blade
<flux:button href="{{ route('events.edit', $event->id) }}" variant="ghost" size="sm" title="Edit">
    <x-action-icon action="edit" />
</flux:button>

<flux:button wire:click="delete" variant="danger" size="sm" title="Delete">
    <x-action-icon action="delete" />
</flux:button>
```

### In Regular Links/Buttons

```blade
<a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center">
    <x-action-icon action="view" class="mr-2" />
    View Event
</a>

<button wire:click="duplicate" class="inline-flex items-center">
    <x-action-icon action="duplicate" class="mr-2" />
    Duplicate
</button>
```

## Available Actions

All actions are defined in `config/icons.php` under the `actions` key:

### Standard Actions
- `view` - View/preview an item (eye icon)
- `edit` - Edit an item (pencil icon)
- `delete` - Delete an item (trash icon)
- `duplicate` - Duplicate an item (plus-circle icon)
- `add` - Add new item (plus-circle icon)
- `download` - Download file (download-circle icon)

### Navigation Actions
- `sessions` - Navigate to sessions (layout icon)
- `content-library` - Navigate to content (folder icon)
- `speakers` - Navigate to speakers (user icon)
- `show-calling` - Navigate to show calling (layout icon)
- `manage-users` - Manage users (multiple users icon)
- `segments` - Navigate to segments (layout icon)
- `cues` - Navigate to cues (clipboard icon)

### Special Actions
- `run-of-show` - Run of show view (map marker icon)
- `versions` - View versions (refresh icon)
- `make-admin` - Make user admin (shield icon)
- `deactivate` - Deactivate item (trash icon)
- `assign` - Assign user (user icon)
- `go-to` - Go to location (arrow icon)
- `location` - Location marker (map marker icon)
- `tag` - Tag/bookmark (bookmark icon)

## Component Implementation

### File Location
`resources/views/components/action-icon.blade.php`

### Code
```blade
@props(['action', 'size' => 'w-4 h-4'])

@php
    // Map action names to icon aliases from config
    $iconAlias = 'actions.' . $action;
@endphp

<x-lineicon :alias="$iconAlias" :size="$size" {{ $attributes }} />
```

## Configuration

Icons are mapped in `config/icons.php`:

```php
'actions' => [
    'view' => 'eye',
    'edit' => 'pencil-1',
    'delete' => 'trash-3',
    'duplicate' => 'plus-circle',
    // ... more actions
],
```

## Migration Guide

### Before (Hardcoded SVG)

```blade
<flux:button href="{{ route('events.edit', $event->id) }}" variant="ghost" size="sm" title="Edit">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
    </svg>
</flux:button>
```

### After (Action Icon Component)

```blade
<flux:button href="{{ route('events.edit', $event->id) }}" variant="ghost" size="sm" title="Edit">
    <x-action-icon action="edit" />
</flux:button>
```

**Result:**
- 5 lines → 1 line
- Hardcoded SVG → Configurable icon
- Manual maintenance → Centralized management

## Example Implementation

The Events index page (`resources/views/livewire/events/index.blade.php`) has been updated to use the action-icon component. This serves as a reference implementation.

### Before (42 lines of SVG code)
```blade
<div class="flex justify-end gap-1">
    <flux:button href="{{ route('events.sessions.index', $event->id) }}" variant="ghost" size="sm" title="Sessions">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </flux:button>
    <!-- 7 more buttons with similar SVG code... -->
</div>
```

### After (8 lines of clean code)
```blade
<div class="flex justify-end gap-1">
    <flux:button href="{{ route('events.sessions.index', $event->id) }}" variant="ghost" size="sm" title="Sessions">
        <x-action-icon action="sessions" />
    </flux:button>
    <flux:button href="{{ route('events.content.index', $event->id) }}" variant="ghost" size="sm" title="Content Library">
        <x-action-icon action="content-library" />
    </flux:button>
    <flux:button href="{{ route('events.speakers.index', $event->id) }}" variant="ghost" size="sm" title="Speakers">
        <x-action-icon action="speakers" />
    </flux:button>
    <flux:button href="{{ route('events.users', $event->id) }}" variant="ghost" size="sm" title="Manage Users">
        <x-action-icon action="manage-users" />
    </flux:button>
    <flux:button href="{{ route('events.edit', $event->id) }}" variant="ghost" size="sm" title="Edit">
        <x-action-icon action="edit" />
    </flux:button>
    <flux:button wire:click="duplicateEvent({{ $event->id }})" variant="ghost" size="sm" title="Duplicate">
        <x-action-icon action="duplicate" />
    </flux:button>
    <flux:button wire:click="confirmDelete({{ $event->id }})" variant="danger" size="sm" title="Delete">
        <x-action-icon action="delete" />
    </flux:button>
</div>
```

## Adding New Actions

To add a new action icon:

1. **Add to config** (`config/icons.php`):
```php
'actions' => [
    // ... existing actions
    'my-new-action' => 'icon-name-from-lineicons',
],
```

2. **Use in views**:
```blade
<x-action-icon action="my-new-action" />
```

3. **Clear config cache**:
```bash
php artisan config:clear
```

## Pages to Update

The following pages still use hardcoded SVG icons and should be updated to use the action-icon component:

### High Priority (Frequently Used)
- ✅ `resources/views/livewire/events/index.blade.php` - **DONE** (Example implementation)
- ⏳ `resources/views/livewire/sessions/index.blade.php`
- ⏳ `resources/views/livewire/segments/index.blade.php`
- ⏳ `resources/views/livewire/content/index.blade.php`
- ⏳ `resources/views/livewire/content/show.blade.php`
- ⏳ `resources/views/livewire/speakers/index.blade.php`

### Medium Priority
- ⏳ `resources/views/livewire/custom-fields/index.blade.php`
- ⏳ `resources/views/livewire/content-categories/index.blade.php`
- ⏳ `resources/views/livewire/cue-types/index.blade.php`
- ⏳ `resources/views/livewire/calendar/index.blade.php`
- ⏳ `resources/views/livewire/calendar/show.blade.php`
- ⏳ `resources/views/livewire/calendar/calendar-view.blade.php`

### Low Priority
- ⏳ `resources/views/livewire/chat/index.blade.php`
- ⏳ `resources/views/livewire/chat/new-message.blade.php`
- ⏳ `resources/views/livewire/events/show.blade.php`
- ⏳ `resources/views/livewire/cues/index.blade.php`
- ⏳ `resources/views/livewire/tags/index.blade.php`

## Search and Replace Pattern

To update a page, use this pattern:

### Find:
```blade
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="[PATH_DATA]"></path>
</svg>
```

### Replace with:
```blade
<x-action-icon action="[ACTION_NAME]" />
```

Where `[ACTION_NAME]` is one of the actions defined in `config/icons.php`.

## Troubleshooting

### Icon Not Showing

1. **Check if action exists in config:**
```bash
php artisan tinker
>>> config('icons.actions.your-action-name')
```

2. **Check if icon file exists:**
```bash
ls public/vendor/lineicons/[icon-name].svg
```

3. **Clear caches:**
```bash
php artisan config:clear
php artisan view:clear
```

### Wrong Icon Displaying

1. **Update config** (`config/icons.php`):
```php
'actions' => [
    'your-action' => 'correct-icon-name',
],
```

2. **Clear config cache:**
```bash
php artisan config:clear
```

### Icon Size Issues

Pass custom size:
```blade
<x-action-icon action="edit" size="w-5 h-5" />
<x-action-icon action="delete" size="w-6 h-6" />
```

## Best Practices

1. **Use semantic action names**: `edit`, `delete`, `view` instead of `pencil`, `trash`, `eye`
2. **Keep icons consistent**: Use the same action name for the same purpose across the app
3. **Document new actions**: Add comments in `config/icons.php` for custom actions
4. **Test in dark mode**: Ensure icons work in both light and dark themes
5. **Group related actions**: Keep navigation actions separate from CRUD actions in config

## Future Enhancements

Potential improvements:

1. **Action button component**: Combine button + icon into one component
2. **Tooltip integration**: Auto-generate tooltips from action names
3. **Permission checking**: Hide actions based on user permissions
4. **Loading states**: Show loading indicator on action buttons
5. **Confirmation modals**: Auto-confirm for destructive actions

---

**Status:** Component created and tested ✅  
**Example implementation:** Events index page ✅  
**Ready for rollout:** Yes ✅
