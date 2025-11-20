<?php

namespace App\Livewire\TeamRoles;

use App\Models\Event;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $eventId;
    public $event;
    public $roles;
    
    // Form properties
    public $showCreateModal = false;
    public $editingRoleId = null;
    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $sort_order = 0;
    
    // User assignment properties
    public $showAssignModal = false;
    public $assigningRoleId = null;
    public $assigningRole = null;
    public $availableUsers = [];
    public $assignedUsers = [];
    public $selectedUsers = [];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $this->roles = TeamRole::where('event_id', $this->eventId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount('users')
            ->get();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'description', 'color', 'sort_order', 'editingRoleId']);
        $this->color = '#3B82F6';
        $this->showCreateModal = true;
    }

    public function openEditModal($roleId)
    {
        $role = TeamRole::findOrFail($roleId);
        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->description = $role->description ?? '';
        $this->color = $role->color;
        $this->sort_order = $role->sort_order;
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
        ]);

        if ($this->editingRoleId) {
            // Update existing role
            $role = TeamRole::findOrFail($this->editingRoleId);
            $role->update([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
            ]);
            session()->flash('message', 'Team role updated successfully.');
        } else {
            // Create new role
            TeamRole::create([
                'event_id' => $this->eventId,
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
            ]);
            session()->flash('message', 'Team role created successfully.');
        }

        $this->showCreateModal = false;
        $this->loadRoles();
    }

    public function delete($roleId)
    {
        $role = TeamRole::findOrFail($roleId);
        $role->delete();
        session()->flash('message', 'Team role deleted successfully.');
        $this->loadRoles();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }
    
    public function openAssignModal($roleId)
    {
        $this->assigningRoleId = $roleId;
        $this->assigningRole = TeamRole::with('users')->findOrFail($roleId);
        
        // Get all users assigned to this event
        $this->availableUsers = $this->event->users()->orderBy('name')->get();
        
        // Get currently assigned users for this role
        $this->assignedUsers = $this->assigningRole->users->pluck('id')->toArray();
        $this->selectedUsers = $this->assignedUsers;
        
        $this->showAssignModal = true;
    }
    
    public function saveAssignments()
    {
        $role = TeamRole::findOrFail($this->assigningRoleId);
        
        // Remove existing assignments
        DB::table('event_user_roles')
            ->where('event_id', $this->eventId)
            ->where('team_role_id', $this->assigningRoleId)
            ->delete();
        
        // Add new assignments
        foreach ($this->selectedUsers as $userId) {
            DB::table('event_user_roles')->insert([
                'event_id' => $this->eventId,
                'user_id' => $userId,
                'team_role_id' => $this->assigningRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        session()->flash('message', 'Role assignments updated successfully.');
        $this->showAssignModal = false;
        $this->loadRoles();
    }
    
    public function closeAssignModal()
    {
        $this->showAssignModal = false;
    }

    public function render()
    {
        return view('livewire.team-roles.index');
    }
}
