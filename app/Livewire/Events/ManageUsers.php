<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageUsers extends Component
{
    public $eventId;
    public $event;
    public $selectedUserId;
    public $selectedRoleId;
    public $isAdmin = false;
    public $showAddModal = false;
    public $showCreateModal = false;
    
    // New user creation fields
    public $newUserName;
    public $newUserEmail;
    public $newUserPassword;
    public $newUserRole;
    public $newUserIsAdmin = false;

    protected $rules = [
        'selectedUserId' => 'required|exists:users,id',
        'selectedRoleId' => 'required|exists:roles,id',
        'isAdmin' => 'boolean',
    ];
    
    protected $createRules = [
        'newUserName' => 'required|string|max:255',
        'newUserEmail' => 'required|email|unique:users,email',
        'newUserPassword' => 'required|string|min:8',
        'newUserRole' => 'required|exists:roles,id',
        'newUserIsAdmin' => 'boolean',
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::with(['assignedUsers.eventRoles' => function ($query) use ($eventId) {
            $query->wherePivot('event_id', $eventId);
        }])->findOrFail($eventId);

        // Check if user has permission to manage users
        if (!auth()->user()->isSuperAdmin() && !$this->event->isAdmin(auth()->user())) {
            abort(403, 'You do not have permission to manage users for this event.');
        }
    }

    public function openAddModal()
    {
        $this->showAddModal = true;
        $this->selectedUserId = '';
        $this->selectedRoleId = '';
        $this->isAdmin = false;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->reset(['selectedUserId', 'selectedRoleId', 'isAdmin']);
    }

    public function assignUser()
    {
        $this->validate();

        // Check if assignment already exists
        $exists = DB::table('event_user')
            ->where('event_id', $this->eventId)
            ->where('user_id', $this->selectedUserId)
            ->where('role_id', $this->selectedRoleId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'This user is already assigned to this event with this role.');
            return;
        }

        DB::table('event_user')->insert([
            'event_id' => $this->eventId,
            'user_id' => $this->selectedUserId,
            'role_id' => $this->selectedRoleId,
            'is_admin' => $this->isAdmin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::find($this->selectedUserId);
        $role = Role::find($this->selectedRoleId);

        session()->flash('message', "{$user->name} has been assigned as {$role->name}.");
        
        $this->closeAddModal();
        $this->mount($this->eventId); // Refresh data
    }

    public function removeAssignment($assignmentId)
    {
        DB::table('event_user')->where('id', $assignmentId)->delete();
        
        session()->flash('message', 'User assignment removed.');
        $this->mount($this->eventId); // Refresh data
    }

    public function toggleAdmin($assignmentId)
    {
        $assignment = DB::table('event_user')->where('id', $assignmentId)->first();
        
        DB::table('event_user')
            ->where('id', $assignmentId)
            ->update([
                'is_admin' => !$assignment->is_admin,
                'updated_at' => now(),
            ]);

        session()->flash('message', 'Admin status updated.');
        $this->mount($this->eventId); // Refresh data
    }
    
    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->reset(['newUserName', 'newUserEmail', 'newUserPassword', 'newUserRole', 'newUserIsAdmin']);
    }
    
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['newUserName', 'newUserEmail', 'newUserPassword', 'newUserRole', 'newUserIsAdmin']);
    }
    
    public function createAndAssignUser()
    {
        $this->validate($this->createRules);
        
        // Create the new user
        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => bcrypt($this->newUserPassword),
        ]);
        
        // Assign to event
        DB::table('event_user')->insert([
            'event_id' => $this->eventId,
            'user_id' => $user->id,
            'role_id' => $this->newUserRole,
            'is_admin' => $this->newUserIsAdmin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $role = Role::find($this->newUserRole);
        session()->flash('message', "{$user->name} has been created and assigned as {$role->name}.");
        
        $this->closeCreateModal();
        $this->mount($this->eventId); // Refresh data
    }

    public function render()
    {
        $availableUsers = User::whereNotIn('id', function ($query) {
            $query->select('user_id')
                ->from('event_user')
                ->where('event_id', $this->eventId);
        })->orderBy('name')->get();

        $activeRoles = Role::active()->orderBy('sort_order')->get();

        // Get assigned users with their roles
        $assignments = DB::table('event_user')
            ->where('event_id', $this->eventId)
            ->join('users', 'event_user.user_id', '=', 'users.id')
            ->join('roles', 'event_user.role_id', '=', 'roles.id')
            ->select('event_user.*', 'users.name as user_name', 'users.email as user_email', 'roles.name as role_name')
            ->orderBy('users.name')
            ->get();

        return view('livewire.events.manage-users', [
            'availableUsers' => $availableUsers,
            'activeRoles' => $activeRoles,
            'assignments' => $assignments,
        ]);
    }
}
