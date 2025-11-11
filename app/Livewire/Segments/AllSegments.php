<?php

namespace App\Livewire\Segments;

use App\Models\Event;
use App\Models\Session;
use Livewire\Component;

class AllSegments extends Component
{
    public $eventId;
    public $event;
    public $search = '';

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function render()
    {
        $sessions = Session::where('event_id', $this->eventId)
            ->with([
                'segments' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('segments', function ($sq) {
                            $sq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('start_date')
            ->get();

        return view('livewire.segments.all-segments', [
            'sessions' => $sessions,
        ]);
    }
}
