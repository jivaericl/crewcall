<?php

namespace App\Livewire\TeamRoles;

use App\Models\Event;
use App\Models\TeamRole;
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

    public function render()
    {
        return view('livewire.team-roles.index');
    }
}
