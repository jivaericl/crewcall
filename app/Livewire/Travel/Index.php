<?php

namespace App\Livewire\Travel;

use App\Models\Event;
use App\Models\Travel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $travelId;
    public $userId;
    public $isTraveling = false;
    public $notes;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'isTraveling' => 'boolean',
        'notes' => 'nullable|string',
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->reset(['userId', 'isTraveling', 'notes']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function openEditModal($travelId)
    {
        $this->travelId = $travelId;
        $travel = Travel::findOrFail($travelId);
        $this->userId = $travel->user_id;
        $this->isTraveling = $travel->is_traveling;
        $this->notes = $travel->notes;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function save()
    {
        $this->validate();

        // Check if travel record already exists for this user and event
        $existingTravel = Travel::where('event_id', $this->eventId)
            ->where('user_id', $this->userId)
            ->first();

        if ($existingTravel) {
            session()->flash('error', 'Travel record already exists for this user.');
            return;
        }

        Travel::create([
            'event_id' => $this->eventId,
            'user_id' => $this->userId,
            'is_traveling' => $this->isTraveling,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Travel record created successfully.');
        $this->closeAddModal();
    }

    public function update()
    {
        $this->validate();

        $travel = Travel::findOrFail($this->travelId);
        $travel->update([
            'is_traveling' => $this->isTraveling,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Travel record updated successfully.');
        $this->closeEditModal();
    }

    public function delete($travelId)
    {
        $travel = Travel::findOrFail($travelId);
        $travel->delete();

        session()->flash('message', 'Travel record deleted successfully.');
    }

    public function render()
    {
        // Get all users assigned to this event
        $assignedUsers = DB::table('event_user')
            ->where('event_id', $this->eventId)
            ->join('users', 'event_user.user_id', '=', 'users.id')
            ->join('roles', 'event_user.role_id', '=', 'roles.id')
            ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name')
            ->orderBy('users.name')
            ->get();

        // Get users who don't have travel records yet
        $usersWithoutTravel = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('event_user')
                ->where('event_id', $this->eventId);
        })->whereNotIn('id', function ($query) {
            $query->select('user_id')
                ->from('travels')
                ->where('event_id', $this->eventId);
        })->orderBy('name')->get();

        // Get travel records for this event with search
        $travels = Travel::with(['user', 'flights', 'hotelReservations'])
            ->where('event_id', $this->eventId)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.travel.index', [
            'travels' => $travels,
            'assignedUsers' => $assignedUsers,
            'usersWithoutTravel' => $usersWithoutTravel,
        ]);
    }
}
