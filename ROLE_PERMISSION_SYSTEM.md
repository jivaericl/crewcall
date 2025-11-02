# Role-Based Permission System Documentation

## Overview

A comprehensive role-based permission system has been implemented that allows super admins to manage system-wide roles and event admins to assign users to events with specific roles. Each role has granular permissions (add, edit, view, delete) that can be configured.

---

## System Architecture

### Database Tables

**roles**
- Stores system-wide roles with permissions
- Fields: name, slug, description, is_system, is_active, can_add, can_edit, can_view, can_delete, sort_order
- All roles are tracked in audit logs

**event_user (Pivot Table)**
- Links users to events with specific roles
- Fields: event_id, user_id, role_id, is_admin
- Unique constraint prevents duplicate assignments

**users**
- Added `is_super_admin` boolean field
- Super admins have full system access

### Models

**Role Model** (`app/Models/Role.php`)
- Relationships: users(), events()
- Scopes: system(), active()
- Methods: hasPermission()
- Automatically generates slug from name
- Includes Auditable trait

**Event Model** (`app/Models/Event.php`)
- New relationships: assignedUsers(), assignedRoles()
- Methods: hasUser(), isAdmin()

**User Model** (`app/Models/User.php`)
- New relationships: assignedEvents(), eventRoles()
- Methods: isSuperAdmin(), isEventAdmin(), getRoleForEvent()

---

## Starter Roles

19 production-ready roles have been seeded with appropriate permissions:

| Role | Permissions | Description |
|------|------------|-------------|
| **Executive Sponsor** | View | High-level executive oversight |
| **Executive Producer** | View, Add, Edit, Delete | Full production control |
| **Content Producer** | View, Add, Edit | Content management |
| **Media Producer** | View, Add, Edit | Media asset management |
| **Technical Director** | View, Add, Edit | Technical oversight |
| **Graphic Designer** | View, Add, Edit | Graphics creation |
| **Art Director** | View, Add, Edit | Artistic direction |
| **Audio [A1]** | View, Add, Edit | Primary audio engineer |
| **Audio [A2]** | View, Edit | Secondary audio engineer |
| **Lighting Director** | View, Add, Edit | Lighting design |
| **Stage Hand** | View | Stage assistance |
| **Assistant Stage Manager** | View, Edit | Stage management support |
| **Stage Manager** | View, Add, Edit | Stage operations |
| **Show Caller** | View, Edit | Cue calling |
| **Client** | View | Client representative |
| **Projection/LED** | View, Add, Edit | Display management |
| **Playback** | View, Edit | Media playback |
| **Graphic Operator** | View, Edit | Graphics operation |
| **Production Assistant [PA]** | View | General assistance |

---

## User Roles

### Super Admin
- Full system access
- Can manage all roles (create, edit, activate/deactivate)
- Can assign/remove users from any event
- Can make other users super admins
- Visible "Roles" menu item

### Event Admin
- Can manage user assignments for their specific events
- Can assign/remove users and roles
- Can promote users to event admin status
- Cannot manage system-wide roles

### Regular User
- Can be assigned to multiple events with different roles
- Permissions determined by assigned role
- Can view events they're assigned to

---

## Features

### Role Management (Super Admin Only)

**Access:** `/roles`

**Capabilities:**
- View all system roles
- Create new custom roles
- Edit existing roles (name, description, permissions, sort order)
- Activate/deactivate roles
- Search and filter roles
- View permission badges (View, Add, Edit, Delete)

**Permissions Configuration:**
- **View**: Can view/read data
- **Add**: Can create new records
- **Edit**: Can modify existing records
- **Delete**: Can remove records

### Event User Assignment

**Access:** `/events/{id}/users`

**Capabilities:**
- View all users assigned to an event
- Assign new users with specific roles
- Remove user assignments
- Promote/demote event admin status
- See user details (name, email, role)

**Assignment Process:**
1. Click "Assign User" button
2. Select user from dropdown (shows available users)
3. Select role from active roles
4. Optionally check "Make Event Admin"
5. Submit assignment

**Event Admin Badge:**
- Purple badge indicates event admin status
- Event admins can manage other user assignments

---

## User Interface

### Navigation
- **Super Admins**: See "Roles" link in main navigation
- **All Users**: See "Events" link
- **Event List**: "Users" button for each event

### Role Management UI
- Clean table layout with Flux UI components
- Color-coded permission badges
- Active/Inactive status indicators
- Search functionality
- Filter by active status
- Responsive design with dark mode support

### Event User Management UI
- Modal-based user assignment
- Table view of current assignments
- Toggle admin status with one click
- Remove assignments easily
- Shows user email for clarity

---

## Technical Implementation

### Livewire Components

**Roles/Index** (`app/Livewire/Roles/Index.php`)
- Lists all roles with search and filtering
- Toggle active status
- Pagination support

**Roles/Form** (`app/Livewire/Roles/Form.php`)
- Create/edit roles
- Permission checkboxes
- Sort order management
- Super admin authorization check

**Events/ManageUsers** (`app/Livewire/Events/ManageUsers.php`)
- Assign users to events
- Manage role assignments
- Toggle event admin status
- Remove assignments
- Authorization checks

### Routes

```php
// Role management (super admin only)
Route::get('/roles', App\Livewire\Roles\Index::class)->name('roles.index');
Route::get('/roles/create', App\Livewire\Roles\Form::class)->name('roles.create');
Route::get('/roles/{roleId}/edit', App\Livewire\Roles\Form::class)->name('roles.edit');

// Event user management
Route::get('/events/{eventId}/users', App\Livewire\Events\ManageUsers::class)->name('events.users');
```

### Authorization

**Super Admin Check:**
```php
auth()->user()->isSuperAdmin()
```

**Event Admin Check:**
```php
$event->isAdmin(auth()->user())
```

**User Assignment Check:**
```php
$event->hasUser(auth()->user())
```

---

## Usage Examples

### Making a User Super Admin

1. **Via Database:**
```sql
UPDATE users SET is_super_admin = 1 WHERE email = 'admin@example.com';
```

2. **Via Tinker:**
```php
php artisan tinker
$user = User::where('email', 'admin@example.com')->first();
$user->is_super_admin = true;
$user->save();
```

### Creating a Custom Role

1. Login as super admin
2. Navigate to "Roles" → "Create New Role"
3. Enter role details:
   - Name: "Video Engineer"
   - Description: "Manages video systems"
   - Permissions: Check View, Add, Edit
   - Sort Order: 20
4. Click "Create Role"

### Assigning Users to Events

1. Go to Events list
2. Click "Users" button for an event
3. Click "Assign User"
4. Select user and role from dropdowns
5. Check "Make Event Admin" if needed
6. Click "Assign User"

### Checking User Permissions

```php
// Get user's role for an event
$role = $user->getRoleForEvent($event);

// Check specific permission
if ($role && $role->hasPermission('edit')) {
    // User can edit
}

// Check if user is event admin
if ($user->isEventAdmin($event)) {
    // User is event admin
}
```

---

## Audit Logging

All role and assignment changes are automatically tracked:

- **Role Creation**: Logged with all permissions
- **Role Updates**: Shows before/after values for changed fields
- **Role Activation/Deactivation**: Status changes tracked
- **User Assignments**: Creation and removal logged
- **Admin Status Changes**: Tracked with user and event context

View audit logs at `/audit-logs` to see complete change history.

---

## Security Features

1. **Super Admin Protection**: Role management restricted to super admins only
2. **Event Admin Authorization**: User assignment restricted to event admins and super admins
3. **Unique Constraints**: Prevents duplicate user/role assignments
4. **Cascade Deletes**: Removes assignments when events, users, or roles are deleted
5. **Active Status**: Inactive roles cannot be assigned to new events
6. **Audit Trail**: Complete history of all permission changes

---

## Best Practices

### Role Management
- Keep role names clear and descriptive
- Use sort order to group related roles
- Deactivate unused roles instead of deleting
- Document custom roles in descriptions
- Review permissions regularly

### Event Assignments
- Assign at least one event admin per event
- Use appropriate roles for each team member
- Remove users when they leave the project
- Review assignments before major events

### Permission Design
- **View Only**: For observers and clients
- **View + Edit**: For operators and technicians
- **View + Edit + Add**: For coordinators and managers
- **Full Permissions**: For producers and directors

---

## Future Enhancements

Potential additions to the system:

1. **Role Templates**: Quick-apply permission sets
2. **Bulk Assignment**: Assign multiple users at once
3. **Role Hierarchy**: Parent-child role relationships
4. **Custom Permissions**: Beyond add/edit/view/delete
5. **Time-Based Access**: Temporary role assignments
6. **Notification System**: Alert users when assigned to events
7. **Role Analytics**: Track role usage and permissions
8. **Export/Import**: Share role configurations between projects

---

## Troubleshooting

### "Only super admins can manage roles"
- Verify user has `is_super_admin = 1` in database
- Check authentication status
- Clear browser cache

### "You do not have permission to manage users"
- Verify user is event admin or super admin
- Check event assignment in `event_user` table
- Ensure `is_admin` is set to true

### Role not appearing in dropdown
- Check if role is active (`is_active = 1`)
- Verify role exists in database
- Clear application cache

### Duplicate assignment error
- User already has this role for this event
- Check existing assignments first
- Remove old assignment before reassigning

---

## Database Queries

### Get all users for an event with their roles
```php
$assignments = DB::table('event_user')
    ->where('event_id', $eventId)
    ->join('users', 'event_user.user_id', '=', 'users.id')
    ->join('roles', 'event_user.role_id', '=', 'roles.id')
    ->select('users.name', 'users.email', 'roles.name as role', 'event_user.is_admin')
    ->get();
```

### Get all events a user is assigned to
```php
$events = $user->assignedEvents()
    ->with('assignedRoles')
    ->get();
```

### Get all active roles
```php
$roles = Role::active()->orderBy('sort_order')->get();
```

---

## Testing Checklist

- [ ] Super admin can access role management
- [ ] Regular users cannot access role management
- [ ] Roles can be created with all permissions
- [ ] Roles can be edited and updated
- [ ] Roles can be activated/deactivated
- [ ] Event admins can assign users
- [ ] Event admins can remove assignments
- [ ] Event admins can toggle admin status
- [ ] Users cannot be assigned duplicate roles
- [ ] Inactive roles don't appear in assignment dropdown
- [ ] All changes are logged in audit logs
- [ ] Navigation shows/hides based on permissions
- [ ] Dark mode works correctly
- [ ] Mobile responsive design works

---

## Summary

The role-based permission system provides a complete solution for managing user access and permissions across events. With 19 starter roles, granular permissions, and flexible assignment options, it's ready for production use in event management applications.

**Key Benefits:**
- ✅ Flexible role management
- ✅ Granular permissions (add, edit, view, delete)
- ✅ Event-specific assignments
- ✅ Event admin delegation
- ✅ Complete audit trail
- ✅ User-friendly interface
- ✅ Production-ready starter roles
- ✅ Dark mode support
- ✅ Mobile responsive

For questions or support, refer to the Laravel documentation or contact your system administrator.
