<?php

namespace App\Livewire\Cues;

use App\Models\Event;
use App\Models\Session;
use Livewire\Component;

class AllCues extends Component
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
                'segments.cues' => function ($query) {
                    $query->orderBy('order');
                },
                'segments' => function ($query) {
                    $query->orderBy('order');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('segments', function ($sq) {
                            $sq->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('segments.cues', function ($cq) {
                            $cq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('start_date')
            ->get();

        return view('livewire.cues.all-cues', [
            'sessions' => $sessions,
        ]);
    }
}
