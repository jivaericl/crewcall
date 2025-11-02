<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $showDeleteModal = false;
    public $sessionToDelete = null;

    protected $queryString = ['search'];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($sessionId)
    {
        $this->sessionToDelete = $sessionId;
        $this->showDeleteModal = true;
    }

    public function deleteSession()
    {
        if ($this->sessionToDelete) {
            $session = Session::find($this->sessionToDelete);
            if ($session && $session->event_id == $this->eventId) {
                $sessionName = $session->name;
                $session->delete();
                session()->flash('message', "Session \"{$sessionName}\" deleted successfully.");
            }
        }
        
        $this->showDeleteModal = false;
        $this->sessionToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->sessionToDelete = null;
    }

    public function duplicateSession($sessionId)
    {
        $session = Session::with(['customFieldValues', 'tags'])->findOrFail($sessionId);
        
        if ($session->event_id == $this->eventId) {
            $newSession = $session->replicate();
            $newSession->name = $session->name . ' (Copy)';
            $newSession->save();
            
            // Copy custom field values
            foreach ($session->customFieldValues as $value) {
                $newSession->customFieldValues()->create([
                    'custom_field_id' => $value->custom_field_id,
                    'value' => $value->value,
                ]);
            }
            
            // Copy tags
            $newSession->tags()->sync($session->tags->pluck('id')->toArray());
            
            session()->flash('message', "Session \"{$session->name}\" duplicated successfully.");
        }
    }

    public function render()
    {
        $query = Session::with(['client', 'producer', 'creator', 'updater', 'tags'])
            ->where('event_id', $this->eventId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        $sessions = $query->ordered()->paginate(15);

        return view('livewire.sessions.index', [
            'sessions' => $sessions,
        ]);
    }
}
