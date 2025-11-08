<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Event;
use App\Models\AuditLog;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $contactId;
    public $contact;
    public $event;
    public $auditLogs;

    public function mount($eventId, $contactId)
    {
        $this->eventId = $eventId;
        $this->contactId = $contactId;
        $this->event = Event::findOrFail($eventId);
        $this->contact = Contact::with(['tags', 'sessions', 'contentFiles', 'comments.user', 'creator', 'updater'])
            ->findOrFail($contactId);
        
        // Get audit logs for this contact
        $this->auditLogs = AuditLog::where('auditable_type', Contact::class)
            ->where('auditable_id', $contactId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function delete()
    {
        $this->contact->delete();
        session()->flash('message', 'Contact deleted successfully.');
        return redirect()->route('events.contacts.index', $this->eventId);
    }

    public function toggleActive()
    {
        $this->contact->update(['is_active' => !$this->contact->is_active]);
        $this->contact->refresh();
        session()->flash('message', 'Contact status updated.');
    }

    public function render()
    {
        return view('livewire.contacts.show');
    }
}
