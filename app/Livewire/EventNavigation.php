<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\On;

class EventNavigation extends Component
{
    public $eventId;
    public $event;
    public $currentRoute;

    protected $listeners = ['eventChanged' => 'handleEventChanged'];

    public function mount()
    {
        $this->eventId = session('selected_event_id');
        if ($this->eventId) {
            $this->event = Event::find($this->eventId);
        }
        $this->currentRoute = request()->route()->getName();
    }

    #[On('eventChanged')]
    public function handleEventChanged($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::find($eventId);
    }

    public function render()
    {
        $menuItems = [];

        if ($this->event) {
            $menuItems = [
                [
                    'label' => 'Sessions',
                    'icon' => 'calendar',
                    'route' => 'events.sessions.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Content',
                    'icon' => 'folder',
                    'route' => 'events.content.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'People',
                    'icon' => 'users',
                    'children' => [
                        [
                            'label' => 'Speakers',
                            'route' => 'events.speakers.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Contacts',
                            'route' => 'events.contacts.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Team',
                            'route' => 'events.users',
                            'params' => [$this->eventId],
                        ],
                    ],
                ],
                [
                    'label' => 'Tags',
                    'icon' => 'tag',
                    'route' => 'events.tags.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Audit Log',
                    'icon' => 'clipboard-list',
                    'route' => 'events.audit-logs.index',
                    'params' => [$this->eventId],
                ],
            ];
        }

        return view('livewire.event-navigation', [
            'menuItems' => $menuItems,
        ]);
    }
}
