<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $filterType = '';
    public $filterStatus = 'active';
    public $sortField = 'last_name';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'filterType', 'filterStatus', 'sortField', 'sortDirection'];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteContact($contactId)
    {
        $contact = Contact::findOrFail($contactId);
        
        // Check permission
        if (!auth()->user()->isSuperAdmin() && !$this->event->isAdmin(auth()->user())) {
            session()->flash('error', 'You do not have permission to delete contacts.');
            return;
        }

        $contact->delete();
        session()->flash('message', 'Contact deleted successfully.');
    }

    public function toggleStatus($contactId)
    {
        $contact = Contact::findOrFail($contactId);
        $contact->update(['is_active' => !$contact->is_active]);
        
        session()->flash('message', 'Contact status updated.');
    }

    public function render()
    {
        $query = Contact::where('event_id', $this->eventId);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply type filter
        if ($this->filterType) {
            $query->where('contact_type', $this->filterType);
        }
        
        // Apply status filter
        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $contacts = $query->paginate(20);

        // Get counts for stats
        $stats = [
            'total' => Contact::where('event_id', $this->eventId)->count(),
            'active' => Contact::where('event_id', $this->eventId)->active()->count(),
            'clients' => Contact::where('event_id', $this->eventId)->clients()->count(),
            'producers' => Contact::where('event_id', $this->eventId)->producers()->count(),
        ];

        // Get tags for filter
        $tags = \App\Models\Tag::where('event_id', $this->eventId)
            ->orderBy('name')
            ->get();

        return view('livewire.contacts.index', [
            'contacts' => $contacts,
            'stats' => $stats,
            'tags' => $tags,
        ]);
    }
}
