<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $showDeleteModal = false;
    public $userToDelete = null;

    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->is_super_admin) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($userId)
    {
        $this->userToDelete = User::findOrFail($userId);
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->userToDelete) {
            // Prevent deleting yourself
            if ($this->userToDelete->id === Auth::id()) {
                session()->flash('error', 'You cannot delete your own account.');
                $this->showDeleteModal = false;
                return;
            }

            $this->userToDelete->delete();
            session()->flash('message', 'User deleted successfully.');
            $this->showDeleteModal = false;
            $this->userToDelete = null;
        }
    }

    public function toggleSuperAdmin($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent removing your own super admin status
        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot remove your own super admin status.');
            return;
        }

        $user->update(['is_super_admin' => !$user->is_super_admin]);
        session()->flash('message', 'User super admin status updated.');
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->withCount('assignedEvents')
            ->latest()
            ->paginate(20);

        return view('livewire.admin.users', [
            'users' => $users,
        ]);
    }
}
