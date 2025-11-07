<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Session;
use App\Models\Contact;
use App\Models\Comment;
use App\Models\AuditLog;
use Livewire\Component;

class Dashboard extends Component
{
    public $eventId;
    public $event;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::with(['assignedUsers', 'creator'])->findOrFail($eventId);
    }

    public function render()
    {
        // Get statistics
        $stats = [
            'sessions' => Session::where('event_id', $this->eventId)->count(),
            'contacts' => Contact::where('event_id', $this->eventId)->count(),
            'team_members' => $this->event->assignedUsers()->count() + 1, // +1 for creator
            'comments' => Comment::where('event_id', $this->eventId)->count(),
        ];

        // Get upcoming sessions (next 7 days)
        $upcomingSessions = Session::where('event_id', $this->eventId)
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get recent sessions (last 7 days)
        $recentSessions = Session::where('event_id', $this->eventId)
            ->where('start_date', '<', now())
            ->where('start_date', '>=', now()->subDays(7))
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        // Get team members
        $teamMembers = $this->event->assignedUsers()
            ->with('role')
            ->get();

        // Get key contacts
        $keyContacts = Contact::where('event_id', $this->eventId)
            ->where('is_active', true)
            ->whereIn('contact_type', ['client', 'producer'])
            ->orderBy('contact_type')
            ->orderBy('first_name')
            ->take(10)
            ->get();

        // Get recent comments
        $recentComments = Comment::where('event_id', $this->eventId)
            ->with(['user', 'commentable'])
            ->latest()
            ->take(10)
            ->get();

        // Get recent activity
        // Since audit_logs doesn't have event_id, we get logs for the event itself and related models
        $recentActivity = AuditLog::where(function($q) {
                // Logs for the event itself
                $q->where('auditable_type', Event::class)
                  ->where('auditable_id', $this->eventId);
                
                // Or logs for sessions in this event
                $sessionIds = Session::where('event_id', $this->eventId)->pluck('id');
                if ($sessionIds->isNotEmpty()) {
                    $q->orWhere(function($sq) use ($sessionIds) {
                        $sq->where('auditable_type', Session::class)
                           ->whereIn('auditable_id', $sessionIds);
                    });
                }
                
                // Or logs for contacts in this event
                $contactIds = Contact::where('event_id', $this->eventId)->pluck('id');
                if ($contactIds->isNotEmpty()) {
                    $q->orWhere(function($cq) use ($contactIds) {
                        $cq->where('auditable_type', Contact::class)
                           ->whereIn('auditable_id', $contactIds);
                    });
                }
            })
            ->with(['user', 'auditable'])
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.events.dashboard', [
            'stats' => $stats,
            'upcomingSessions' => $upcomingSessions,
            'recentSessions' => $recentSessions,
            'teamMembers' => $teamMembers,
            'keyContacts' => $keyContacts,
            'recentComments' => $recentComments,
            'recentActivity' => $recentActivity,
        ]);
    }
}
