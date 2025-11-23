<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarItem;
use App\Models\Event;
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
        $query = CalendarItem::with(['creator', 'users', 'clients', 'speakers', 'tags'])
            ->forEvent($this->eventId)
            ->active();

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        // Apply type filter
        if ($this->typeFilter) {
            $query->ofType($this->typeFilter);
        } else {
            // Apply show/hide toggles
            $visibleTypes = [];
            if ($this->showMilestones) $visibleTypes[] = 'milestone';
            if ($this->showOutOfOffice) $visibleTypes[] = 'out_of_office';
            if ($this->showCalls) $visibleTypes[] = 'call';
            
            if (!empty($visibleTypes)) {
                $query->whereIn('type', $visibleTypes);
            } else {
                // If all are hidden, show none
                $query->whereRaw('1 = 0');
            }
        }

        // Apply date range filter
        if ($this->startDate && $this->endDate) {
            $query->betweenDates($this->startDate, $this->endDate);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $calendarItems = $query->paginate(20);

        // Get counts by type for the toggles
        $milestonesCount = CalendarItem::forEvent($this->eventId)->ofType('milestone')->active()->count();
        $outOfOfficeCount = CalendarItem::forEvent($this->eventId)->ofType('out_of_office')->active()->count();
        $callsCount = CalendarItem::forEvent($this->eventId)->ofType('call')->active()->count();

        return view('livewire.calendar.index', [
            'calendarItems' => $calendarItems,
            'milestonesCount' => $milestonesCount,
            'outOfOfficeCount' => $outOfOfficeCount,
            'callsCount' => $callsCount,
        ]);
    }
}
