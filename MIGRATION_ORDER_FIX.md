# Migration Order Fix

## Issue

The `event_user` table migration was running before the `roles` table migration, causing foreign key constraint errors because `event_user` has a foreign key reference to the `roles` table.

## Problem

```
2025_10_31_100003_create_event_user_table.php    ← Was running FIRST (wrong!)
2025_10_31_100004_create_roles_table.php         ← Was running SECOND (wrong!)
```

The `event_user` table has this foreign key:
```php
$table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
```

This failed because the `roles` table didn't exist yet.

## Solution

Swapped the migration timestamps:

```
2025_10_31_100003_create_roles_table.php         ← Now runs FIRST (correct!)
2025_10_31_100004_create_event_user_table.php    ← Now runs SECOND (correct!)
```

## Correct Migration Order

The complete migration order is now:

1. `100000` - events
2. `100001` - tags
3. `100002` - event_tag (pivot)
4. **`100003` - roles** ✅ (FIXED - now before event_user)
5. **`100004` - event_user** ✅ (FIXED - now after roles)
6. `100005` - event_sessions
7. `100006` - session_tag (pivot)
8. `100007` - segments
9. `100008` - segment_tag (pivot)
10. `100009` - cue_types
11. `100010` - cues
12. `100011` - cue_tag (pivot)
13. `100012` - custom_fields
14. `100013` - session_custom_field_values
15. `100014` - content_categories
16. `100015` - content_files
17. `100016` - content_file_versions
18. `100017` - speakers
19. `100018` - speaker_tag (pivot)
20. `100019` - session_speaker (pivot)
21. `100020` - content_file_speaker (pivot)
22. `100021` - comments
23. `100022` - comment_mentions (pivot)
24. `100023` - chat_messages
25. `100024` - notifications
26. `100025` - user_presence
27. `100026` - audit_logs
28. `100027` - user_run_of_show_preferences
29. `100028` - session_states
30. `100029` - contacts
31. `100030` - update_sessions_table_for_contact_lookups

## Verification

To verify the order is correct:

```bash
cd database/migrations
ls -1 | grep "2025_10_31"
```

You should see `roles` before `event_user`.

## Fresh Installation

For fresh installations, migrations will now run in the correct order without errors:

```bash
php artisan migrate
```

## Existing Installations

If you've already run migrations with the old order:

1. **Option A: Fresh start (recommended for development)**
   ```bash
   php artisan migrate:fresh
   ```

2. **Option B: Manual fix (for production with data)**
   - The tables are already created, so no action needed
   - The order only matters for fresh installations

## Git Commit

This fix was committed as:
```
commit 307a024
fix: Correct migration order - roles table now migrates before event_user table
```

## Testing

Tested on fresh database:
```bash
php artisan migrate:fresh
```

Result: ✅ All migrations run successfully without foreign key errors.

## Updated Package

A new deployment package has been created with this fix:
- `plannr-complete-fixed-20251104.tar.gz`

This package includes the corrected migration order.
