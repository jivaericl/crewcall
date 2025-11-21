<?php

namespace App\Livewire\Chat;

use App\Models\EventUser;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class UserProfileModal extends Component
{
    public $isOpen = false;
    public $userId;
    public $user;
    public $eventId;
    public $eventRoleName;
    public $eventName;
    protected $listeners = ['showUserProfile' => 'showUserProfile'];
    
    #[On('showUserProfile')]
    public function showUserProfile($payload = null)
    {
        [$userId, $resolvedEventId] = $this->normalizePayload($payload);

        if (!$userId) {
            $this->resetProfileState();
            return;
        }

        $this->userId = $userId;
        $this->user = User::find($userId);

        if (!$this->user) {
            $this->resetProfileState();
            return;
        }

        $this->eventId = $resolvedEventId;
        $this->hydrateEventContext();
        $this->isOpen = true;
    }
    
    public function close()
    {
        $this->isOpen = false;
        $this->user = null;
        $this->resetProfileState();
    }
    
    public function startDirectMessage()
    {
        if ($this->userId) {
            return redirect()->route('chat.dm', ['userId' => $this->userId]);
        }
    }

    public function render()
    {
        return view('livewire.chat.user-profile-modal');
    }

    protected function normalizePayload($payload): array
    {
        if (is_null($payload)) {
            return [null, null];
        }

        if (is_numeric($payload)) {
            return [(int) $payload, null];
        }

        if (is_string($payload) && ctype_digit($payload)) {
            return [(int) $payload, null];
        }

        if (is_array($payload)) {
            return [
                $payload['userId'] ?? $payload['user_id'] ?? null,
                $payload['eventId'] ?? $payload['event_id'] ?? null,
            ];
        }

        if (is_object($payload)) {
            return [
                $payload->userId ?? $payload->user_id ?? null,
                $payload->eventId ?? $payload->event_id ?? null,
            ];
        }

        return [null, null];
    }

    protected function hydrateEventContext(): void
    {
        $this->eventRoleName = null;
        $this->eventName = null;

        if (!$this->userId) {
            return;
        }

        $candidateEventId = $this->eventId ?? $this->defaultEventId();

        $eventUserQuery = EventUser::with(['role', 'event'])
            ->where('user_id', $this->userId)
            ->orderByDesc('updated_at');

        if ($candidateEventId) {
            $eventUser = (clone $eventUserQuery)
                ->where('event_id', $candidateEventId)
                ->first();

            if ($eventUser) {
                $this->applyEventUser($eventUser);
                return;
            }
        }

        $eventUser = $eventUserQuery->first();

        if ($eventUser) {
            $this->applyEventUser($eventUser);
        }
    }

    protected function applyEventUser(EventUser $eventUser): void
    {
        $this->eventId = $eventUser->event_id;
        $this->eventName = $eventUser->event?->name;

        $roleName = $eventUser->role?->name;

        if ($eventUser->is_admin) {
            $roleName = $roleName
                ? $roleName . ' · Event Admin'
                : 'Event Admin';
        }

        $this->eventRoleName = $roleName;
    }

    protected function defaultEventId(): ?int
    {
        return session('current_event_id')
            ?? request()->route('eventId')
            ?? request()->get('event_id');
    }

    protected function resetProfileState(): void
    {
        $this->eventId = null;
        $this->eventRoleName = null;
        $this->eventName = null;
    }
}
