<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $filterModel = '';
    public $filterUser = '';
    public $filterEvent = '';
    public $search = '';
    public $selectedLog = null;
    public $showDetailsModal = false;

    protected $queryString = ['filterModel', 'filterUser', 'filterEvent', 'search'];

    public function mount($eventId = null)
    {
        $this->eventId = $eventId;
        if ($eventId) {
            $this->event = Event::findOrFail($eventId);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }



    public function updatingFilterModel()
    {
        $this->resetPage();
    }

    public function updatingFilterUser()
    {
        $this->resetPage();
    }

    public function updatingFilterEvent()
    {
        $this->resetPage();
    }

    public function showDetails($logId)
    {
        $this->selectedLog = AuditLog::with('user')->find($logId);
        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedLog = null;
    }

    public function render()
    {
        $query = AuditLog::with(['user', 'auditable']);
        
        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->filterModel) {
            $query->where('auditable_type', $this->filterModel);
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('auditable_type', 'like', '%' . $this->search . '%')
                  ->orWhere('auditable_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get unique model types for filter
        $modelTypes = AuditLog::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn($type) => ['value' => $type, 'label' => class_basename($type)])
            ->toArray();

        // Get unique users for filter
        $users = \App\Models\User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('audit_logs')
                ->whereNotNull('user_id')
                ->distinct();
        })->get(['id', 'name']);

        return view('livewire.audit-logs.index', [
            'auditLogs' => $auditLogs,
            'modelTypes' => $modelTypes,
            'users' => $users,
        ]);
    }
}
