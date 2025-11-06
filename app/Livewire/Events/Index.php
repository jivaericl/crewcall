<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTag = '';
    public $filterStatus = 'all'; // all, upcoming, ongoing, past
    public $sortBy = 'start_date';
    public $sortDirection = 'desc';
    public $showDeleteModal = false;
    public $eventToDelete = null;
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTag' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'sortBy' => ['except' => 'start_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTag()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterTag = '';
        $this->filterStatus = 'all';
        $this->resetPage();
    }

    public function confirmDelete($eventId)
    {
        $this->eventToDelete = $eventId;
        $this->showDeleteModal = true;
    }

    public function deleteEvent()
    {
        if ($this->eventToDelete) {
            $event = Event::find($this->eventToDelete);
            if ($event) {
                $eventName = $event->name;
                $event->delete();
                session()->flash('message', "Event \"{$eventName}\" deleted successfully.");
            }
        }
        
        $this->showDeleteModal = false;
        $this->eventToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->eventToDelete = null;
    }

    public function duplicateEvent($eventId)
    {
        $event = Event::with('tags')->findOrFail($eventId);
        
        $newEvent = $event->replicate();
        $newEvent->name = $event->name . ' (Copy)';
        $newEvent->save();
        
        // Copy tags
        $newEvent->tags()->sync($event->tags->pluck('id'));
        
        session()->flash('message', "Event \"{$event->name}\" duplicated successfully.");
    }

    public function render()
    {
        $query = Event::with(['tags', 'creator', 'updater']);
        
        // Filter by user access (super admins see all, others see only assigned events)
        if (!auth()->user()->isSuperAdmin()) {
            $query->where(function ($q) {
                $q->whereHas('assignedUsers', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->orWhere('created_by', auth()->id());
            });
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Tag filter
        if ($this->filterTag) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.id', $this->filterTag);
            });
        }

        // Status filter
        $now = Carbon::now();
        switch ($this->filterStatus) {
            case 'upcoming':
                $query->where('start_date', '>', $now);
                break;
            case 'ongoing':
                $query->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
                break;
            case 'past':
                $query->where('end_date', '<', $now);
                break;
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $events = $query->paginate($this->perPage);

        // Get all tags for filter dropdown
        $tags = Tag::orderBy('name')->get();

        // Calculate stats
        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('start_date', '>', $now)->count(),
            'ongoing' => Event::where('start_date', '<=', $now)->where('end_date', '>=', $now)->count(),
            'past' => Event::where('end_date', '<', $now)->count(),
        ];

        return view('livewire.events.index', [
            'events' => $events,
            'tags' => $tags,
            'stats' => $stats,
        ]);
    }
}
