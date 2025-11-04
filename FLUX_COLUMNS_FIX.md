# Fix: Flux Columns Component Missing

## Problem

```
Unable to locate a class or view for component [flux::columns].
```

This error appears when accessing the Speakers management page.

---

## Solution

Create the missing `flux::columns` component.

---

## Manual Fix

### Step 1: Create Directory

```bash
mkdir -p resources/views/flux/columns
```

### Step 2: Create Component File

**File:** `resources/views/flux/columns/index.blade.php`

```blade
@props([
    'sticky' => false,
])

@php
$classes = $sticky ? 'sticky top-0 z-20' : '';
@endphp

<thead {{ $attributes->class($classes) }} data-flux-columns>
    <tr>
        {{ $slot }}
    </tr>
</thead>
```

### Step 3: Clear Cache

```bash
php artisan view:clear
```

---

## Usage

The `flux::columns` component is used to wrap table column headers:

```blade
<flux:table>
    <flux:columns>
        <flux:column>Name</flux:column>
        <flux:column>Email</flux:column>
        <flux:column>Actions</flux:column>
    </flux:columns>
    
    <flux:rows>
        <!-- table rows -->
    </flux:rows>
</flux:table>
```

---

## Where It's Used

This component is used in:
- **Speakers Index** (`resources/views/livewire/speakers/index.blade.php`)
- Any other table views that use `<flux:columns>`

---

## Component Features

- **Sticky Headers:** Set `sticky="true"` to make headers stick to top when scrolling
- **Custom Classes:** Add additional classes via `class` attribute
- **Semantic HTML:** Renders as `<thead>` element
- **Data Attribute:** Includes `data-flux-columns` for JavaScript targeting

---

## Example with Sticky Headers

```blade
<flux:table>
    <flux:columns sticky="true">
        <flux:column>Column 1</flux:column>
        <flux:column>Column 2</flux:column>
    </flux:columns>
    
    <flux:rows>
        <!-- many rows that scroll -->
    </flux:rows>
</flux:table>
```

---

## Difference from flux::table.columns

Note: There are TWO column components:

1. **`flux::columns`** - Standalone component (this one)
   - Location: `resources/views/flux/columns/index.blade.php`
   - Usage: `<flux:columns>`

2. **`flux::table.columns`** - Table sub-component
   - Location: `resources/views/flux/table/columns.blade.php`
   - Usage: `<flux:table.columns>` (with dot notation)

Both work, but `flux::columns` is simpler and doesn't require the Flux facade.

---

## Troubleshooting

### Error Persists After Creating File

1. **Clear all caches:**
   ```bash
   php artisan view:clear
   php artisan cache:clear
   php artisan config:clear
   ```

2. **Verify file exists:**
   ```bash
   ls -la resources/views/flux/columns/index.blade.php
   ```

3. **Check file permissions:**
   ```bash
   chmod 644 resources/views/flux/columns/index.blade.php
   ```

### Component Not Found

Make sure the directory structure is correct:
```
resources/
└── views/
    └── flux/
        └── columns/
            └── index.blade.php
```

---

## Git Commit

The fix has been committed:
```
commit 49fa076
Fix: Add flux::columns component for speaker management
```

---

**That's it!** The columns component is now available and speaker management should work.
