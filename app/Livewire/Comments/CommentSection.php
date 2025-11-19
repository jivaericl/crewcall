<?php

namespace App\Livewire\Comments;

use App\Models\Comment;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class CommentSection extends Component
{
    public $commentable;
    public $eventId;
    public $newComment = '';
    public $replyingTo = null;
    public $editingComment = null;
    public $editText = '';
    public $searchUsers = '';
    public $showUserSuggestions = false;
    public $userSuggestions = [];

    public function mount($commentable, $eventId)
    {
        $this->commentable = $commentable;
        $this->eventId = $eventId;
    }

    public function updatedNewComment($value)
    {
        // Check for @mention being typed
        if (preg_match('/@([^\s]*)$/', $value, $matches)) {
            $this->searchUsers = $matches[1];
            $this->loadUserSuggestions();
        } else {
            $this->showUserSuggestions = false;
        }
    }

    public function loadUserSuggestions()
    {
        // Get users assigned to this event (including current user)
        $query = User::whereHas('assignedEvents', function($q) {
                $q->where('event_id', $this->eventId);
            });
        
        // Apply search filter if there's text after @
        if (strlen($this->searchUsers) > 0) {
            $query->where('name', 'like', '%' . $this->searchUsers . '%');
        }
        
        $this->userSuggestions = $query->limit(10)->get(['id', 'name', 'email']);
        $this->showUserSuggestions = $this->userSuggestions->isNotEmpty();
    }

    public function selectUser($userId, $userName)
    {
        // Get first name only for cleaner display
        $firstName = explode(' ', $userName)[0];
        
        // Replace the partial @mention with clean @FirstName format
        $this->newComment = preg_replace('/@[^\s]*$/', '@' . $firstName . ' ', $this->newComment);
        $this->showUserSuggestions = false;
        $this->searchUsers = '';
        
        // Store mapping for later conversion (stored in session temporarily)
        $mentions = session()->get('temp_mentions', []);
        $mentions['@' . $firstName] = ['id' => $userId, 'name' => $userName];
        session()->put('temp_mentions', $mentions);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:5000',
        ]);
        
        // Convert clean @FirstName mentions to @[Full Name:ID] format
        $commentText = $this->newComment;
        $mentions = session()->get('temp_mentions', []);
        
        foreach ($mentions as $mention => $userData) {
            // Replace @FirstName with @[Full Name:ID]
            $commentText = str_replace(
                $mention,
                '@[' . $userData['name'] . ':' . $userData['id'] . ']',
                $commentText
            );
        }

        Comment::create([
            'event_id' => $this->eventId,
            'user_id' => auth()->id(),
            'commentable_type' => get_class($this->commentable),
            'commentable_id' => $this->commentable->id,
            'comment' => $commentText,
            'parent_id' => $this->replyingTo,
        ]);

        $this->newComment = '';
        $this->replyingTo = null;
        $this->resetMentionState();
        
        session()->flash('comment-success', 'Comment added successfully!');
        $this->dispatch('comment-added');
    }

    public function startReply($commentId)
    {
        $this->replyingTo = $commentId;
        $this->newComment = '';
        $this->resetMentionState();
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->newComment = '';
        $this->resetMentionState();
    }

    public function startEdit($commentId, $currentText)
    {
        $this->editingComment = $commentId;
        $this->editText = $currentText;
    }

    public function saveEdit()
    {
        $this->validate([
            'editText' => 'required|string|max:5000',
        ]);

        $comment = Comment::find($this->editingComment);
        
        if ($comment && $comment->user_id === auth()->id()) {
            $comment->update(['comment' => $this->editText]);
            session()->flash('comment-success', 'Comment updated successfully!');
        }

        $this->editingComment = null;
        $this->editText = '';
        $this->dispatch('comment-updated');
    }

    public function cancelEdit()
    {
        $this->editingComment = null;
        $this->editText = '';
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);
        
        if ($comment && ($comment->user_id === auth()->id() || auth()->user()->isSuperAdmin())) {
            $comment->delete();
            session()->flash('comment-success', 'Comment deleted successfully!');
            $this->dispatch('comment-deleted');
        }
    }

    protected function resetMentionState()
    {
        $this->showUserSuggestions = false;
        $this->userSuggestions = [];
        $this->searchUsers = '';
        session()->forget('temp_mentions');
    }

    #[On('comment-added')]
    #[On('comment-updated')]
    #[On('comment-deleted')]
    public function refreshComments()
    {
        // Trigger re-render
    }

    public function render()
    {
        $comments = $this->commentable->comments()
            ->with('user', 'replies.user')
            ->latest()
            ->get();

        return view('livewire.comments.comment-section', [
            'comments' => $comments,
        ]);
    }
}
