<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;

class Form extends Component
{
    public $roleId;
    public $name;
    public $description;
    public $is_active = true;
    public $can_add = false;
    public $can_edit = false;
    public $can_view = true;
    public $can_delete = false;
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_view' => 'boolean',
        'can_delete' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function mount($roleId = null)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can manage roles.');
        }

        if ($roleId) {
            $role = Role::findOrFail($roleId);
            
            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->description = $role->description;
            $this->is_active = $role->is_active;
            $this->can_add = $role->can_add;
            $this->can_edit = $role->can_edit;
            $this->can_view = $role->can_view;
            $this->can_delete = $role->can_delete;
            $this->sort_order = $role->sort_order;
        }
    }

    public function save()
    {
        if (!auth()->user()->isSuperAdmin()) {
            session()->flash('error', 'Only super admins can manage roles.');
            return;
        }

        $this->validate();

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'can_add' => $this->can_add,
                'can_edit' => $this->can_edit,
                'can_view' => $this->can_view,
                'can_delete' => $this->can_delete,
                'sort_order' => $this->sort_order,
            ]);

            session()->flash('message', 'Role updated successfully.');
        } else {
            Role::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_system' => true,
                'is_active' => $this->is_active,
                'can_add' => $this->can_add,
                'can_edit' => $this->can_edit,
                'can_view' => $this->can_view,
                'can_delete' => $this->can_delete,
                'sort_order' => $this->sort_order,
            ]);

            session()->flash('message', 'Role created successfully.');
        }

        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.roles.form');
    }
}
