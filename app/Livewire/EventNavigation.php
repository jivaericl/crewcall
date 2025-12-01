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
                    'icon' => 'navigation.dashboard',
                    'route' => 'events.dashboard',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Event Info',
                    'icon' => 'navigation.event-info',
                    'route' => 'events.show',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Chat',
                    'icon' => 'navigation.chat',
                    'route' => 'chat.index',
                    'params' => [],
                    'badge' => \App\Models\ChatMessage::whereHas('event.assignedUsers', function ($query) {
                        $query->where('users.id', auth()->id());
                    })
                    ->where('user_id', '!=', auth()->id())
                    ->where('created_at', '>', now()->subDays(7))
                    ->whereNull('read_at')
                    ->count(),
                ],
                [
                    'label' => 'Sessions',
                    'icon' => 'navigation.sessions',
                    'route' => 'events.sessions.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Segments',
                    'icon' => 'navigation.segments',
                    'route' => 'events.all-segments',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Cues',
                    'icon' => 'navigation.cues',
                    'route' => 'events.all-cues',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Content',
                    'icon' => 'navigation.content',
                    'route' => 'events.content.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Event Resources',
                    'icon' => 'navigation.resources',
                    'route' => 'events.resources.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'Calendar',
                    'icon' => 'navigation.calendar',
                    'route' => 'events.calendar.index',
                    'params' => [$this->eventId],
                ],
                [
                    'label' => 'People',
                    'icon' => 'navigation.people',
                    'children' => array_filter([
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
                        [
                            'label' => 'Travel',
                            'route' => 'events.travel.index',
                            'params' => [$this->eventId],
                        ],
                        auth()->user()->isSuperAdmin() ? [
                            'label' => 'System Access',
                            'route' => 'roles.index',
                            'params' => [],
                        ] : null,
                    ]),
                ],
                [
                    'label' => 'Event Settings',
                    'icon' => 'navigation.event-settings',
                    'children' => [
                        [
                            'label' => 'Content Categories',
                            'route' => 'events.content-categories.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Resource Categories',
                            'route' => 'events.content-categories.index',
                            'params' => [$this->eventId, 'resourceFilter' => 'resources'],
                        ],
                        [
                            'label' => 'Tags',
                            'route' => 'events.tags.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Custom Fields',
                            'route' => 'custom-fields.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Cue Types',
                            'route' => 'events.cue-types.index',
                            'params' => [$this->eventId],
                        ],
                        [
                            'label' => 'Team Roles',
                            'route' => 'events.team-roles.index',
                            'params' => [$this->eventId],
                        ],
                    ],
                ],
                [
                    'label' => 'Activity',
                    'icon' => 'navigation.activity',
                    'children' => [
                        [
                            'label' => 'Conversations',
                            'route' => 'activity-feed.index',
                            'params' => [],
                            'badge' => \App\Models\CommentMention::forUser(auth()->id())->unread()->count(),
                        ],
                        [
                            'label' => 'Audit Logs',
                            'route' => 'audit-logs.index',
                            'params' => [],
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
