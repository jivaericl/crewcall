<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showActiveOnly = true;

    protected $queryString = ['search', 'showActiveOnly'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingShowActiveOnly()
    {
        $this->resetPage();
    }

    public function toggleActive($roleId)
    {
        if (!auth()->user()->isSuperAdmin()) {
            session()->flash('error', 'Only super admins can toggle role status.');
            return;
        }

        $role = Role::findOrFail($roleId);
        $role->update(['is_active' => !$role->is_active]);

        session()->flash('message', "Role '{$role->name}' has been " . ($role->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function render()
    {
        $query = Role::query()->orderBy('sort_order');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->showActiveOnly) {
            $query->where('is_active', true);
        }

        $roles = $query->paginate(20);

        return view('livewire.roles.index', [
            'roles' => $roles,
        ]);
    }
}
