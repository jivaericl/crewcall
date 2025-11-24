<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarItem;
use App\Models\Event;
use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;

    // Filters
    public $search = '';
    public $typeFilter = '';
    public $showMilestones = true;
    public $showOutOfOffice = true;
    public $showCalls = true;
    public $showSessions = true;
    public $startDate = '';
    public $endDate = '';

    // Sorting
    public $sortBy = 'start_date';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'sortBy' => ['except' => 'start_date'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function changeSortField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleType($type)
    {
        switch ($type) {
            case 'milestone':
                $this->showMilestones = !$this->showMilestones;
                break;
            case 'out_of_office':
                $this->showOutOfOffice = !$this->showOutOfOffice;
                break;
            case 'call':
                $this->showCalls = !$this->showCalls;
                break;
            case 'session':
                $this->showSessions = !$this->showSessions;
                break;
        }
        $this->resetPage();
    }

    public function delete($id)
    {
        $calendarItem = CalendarItem::findOrFail($id);
        
        // Check if calendar item belongs to this event
        if ($calendarItem->event_id != $this->eventId) {
            abort(403);
        }
        
        $calendarItem->delete();
        
        session()->flash('message', 'Calendar item deleted successfully.');
    }

    public function render()
    {
        // Get calendar items
        $calendarQuery = CalendarItem::with(['creator', 'users', 'speakers', 'tags'])
            ->forEvent($this->eventId)
            ->active();

        // Apply search to calendar items
        if ($this->search) {
            $calendarQuery->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        // Apply type filter to calendar items
        if ($this->typeFilter) {
            $calendarQuery->ofType($this->typeFilter);
        } else {
            // Apply show/hide toggles
            $visibleTypes = [];
            if ($this->showMilestones) $visibleTypes[] = 'milestone';
            if ($this->showOutOfOffice) $visibleTypes[] = 'out_of_office';
            if ($this->showCalls) $visibleTypes[] = 'call';
            
            if (!empty($visibleTypes)) {
                $calendarQuery->whereIn('type', $visibleTypes);
            } else {
                // If all calendar types are hidden, show none
                $calendarQuery->whereRaw('1 = 0');
            }
        }

        // Apply date range filter to calendar items
        if ($this->startDate && $this->endDate) {
            $calendarQuery->betweenDates($this->startDate, $this->endDate);
        }

        // Get sessions
        $sessionsQuery = Session::where('event_id', $this->eventId);
        
        // Apply search to sessions
        if ($this->search) {
            $sessionsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply date range filter to sessions
        if ($this->startDate && $this->endDate) {
            $sessionsQuery->whereBetween('start_date', [$this->startDate, $this->endDate]);
        }

        // Combine calendar items and sessions
        $calendarItems = $calendarQuery->get();
        $sessions = $this->showSessions ? $sessionsQuery->get() : collect();
        
        // Merge and sort
        $allItems = $calendarItems->concat($sessions)->sortBy(function($item) {
            if ($item instanceof CalendarItem) {
                return $item->start_date;
            } else {
                return $item->start_date ?? now();
            }
        });
        
        // Apply sorting direction
        if ($this->sortDirection === 'desc') {
            $allItems = $allItems->reverse();
        }

        // Get counts by type for the toggles
        $milestonesCount = CalendarItem::forEvent($this->eventId)->ofType('milestone')->active()->count();
        $outOfOfficeCount = CalendarItem::forEvent($this->eventId)->ofType('out_of_office')->active()->count();
        $callsCount = CalendarItem::forEvent($this->eventId)->ofType('call')->active()->count();
        $sessionsCount = Session::where('event_id', $this->eventId)->count();

        return view('livewire.calendar.index', [
            'allItems' => $allItems,
            'milestonesCount' => $milestonesCount,
            'outOfOfficeCount' => $outOfOfficeCount,
            'callsCount' => $callsCount,
            'sessionsCount' => $sessionsCount,
        ]);
    }
}
