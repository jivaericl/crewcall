<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Event;
use App\Models\Session;
use App\Models\Cue;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->is_super_admin) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function render()
    {
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_sessions' => Session::count(),
            'total_cues' => Cue::count(),
            'recent_users' => User::latest()->take(10)->get(),
            'recent_events' => Event::latest()->take(10)->get(),
        ];

        return view('livewire.admin.dashboard', $stats);
    }
}
