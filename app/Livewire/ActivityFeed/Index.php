<?php

namespace App\Livewire\ActivityFeed;

use App\Models\CommentMention;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $days = 30;

    public function markAsRead($mentionId)
    {
        $mention = CommentMention::find($mentionId);
        
        if ($mention && $mention->mentioned_user_id === auth()->id()) {
            $mention->markAsRead();
            session()->flash('success', 'Mention marked as read');
        }
    }

    public function markAllAsRead()
    {
        CommentMention::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        session()->flash('success', 'All mentions marked as read');
        $this->dispatch('mentions-updated');
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedDays()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = CommentMention::forUser(auth()->id())
            ->with(['comment' => function($q) {
                $q->with('user', 'commentable', 'event');
            }]);

        // Apply filters
        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->where('is_read', true);
        }

        if ($this->days > 0) {
            $query->recent($this->days);
        }

        $mentions = $query->latest()->paginate(20);

        $unreadCount = CommentMention::forUser(auth()->id())
            ->unread()
            ->count();

        return view('livewire.activity-feed.index', [
            'mentions' => $mentions,
            'unreadCount' => $unreadCount,
        ]);
    }
}
