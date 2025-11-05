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
                    'label' => 'Dashboard',
                    'icon' => 'home',
                    'route' => 'events.dashboard',
                    'params' => [$this->eventId],
                ],
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
                    'label' => 'Event Settings',
                    'icon' => 'cog',
                    'children' => [
                        [
                            'label' => 'Content Categories',
                            'route' => 'events.content-categories.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Tags',
                            'route' => 'events.tags.index',
                            'params' => [$this->eventId],
                        ],
                    ],
                ],
            ];
        }

        return view('livewire.event-navigation', [
            'menuItems' => $menuItems,
        ]);
    }
}
