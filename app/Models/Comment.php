<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'commentable_type',
        'commentable_id',
        'comment',
        'parent_id',
    ];
    
    protected $appends = ['formatted_comment'];

    protected static function booted()
    {
        static::created(function ($comment) {
            $comment->processMentions();
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    }

    // Scopes
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Helper methods
    public function processMentions()
    {
        // Extract @mentions from comment text in format @[Name:ID]
        preg_match_all('/@\[([^:]+):(\d+)\]/', $this->comment, $matches);
        
        if (!empty($matches[2])) {
            $userIds = array_unique($matches[2]);
            
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                
                if ($user && $user->id !== $this->user_id) {
                    // Create mention record
                    CommentMention::create([
                        'comment_id' => $this->id,
                        'mentioned_user_id' => $user->id,
                    ]);
                    
                    // Create notification
                    Notification::create([
                        'event_id' => $this->event_id,
                        'user_id' => $user->id,
                        'type' => 'mention',
                        'title' => 'You were mentioned',
                        'message' => $this->user->name . ' mentioned you in a comment',
                        'action_url' => $this->getActionUrl(),
                        'data' => [
                            'comment_id' => $this->id,
                            'commentable_type' => $this->commentable_type,
                            'commentable_id' => $this->commentable_id,
                        ],
                    ]);
                }
            }
        }
    }

    public function getActionUrl()
    {
        // Generate URL based on commentable type
        $type = class_basename($this->commentable_type);
        
        $baseUrl = match($type) {
            'Event' => "/events/{$this->commentable_id}",
            'Session' => "/events/{$this->event_id}/sessions",
            'Segment' => "/segments/{$this->commentable_id}",
            'Cue' => "/cues/{$this->commentable_id}",
            'ContentFile' => "/events/{$this->event_id}/content",
            default => "/events/{$this->event_id}",
        };
        
        // Add anchor to scroll to specific comment
        return $baseUrl . '#comment-' . $this->id;
    }

    public function getCommentableNameAttribute()
    {
        return $this->commentable ? $this->commentable->name : 'Unknown';
    }

    public function getCommentableTypeNameAttribute()
    {
        return class_basename($this->commentable_type);
    }
    
    public function getFormattedCommentAttribute()
    {
        // Replace @[Name:ID] with styled mention spans
        $formatted = preg_replace_callback(
            '/@\[([^:]+):(\d+)\]/',
            function ($matches) {
                $name = $matches[1];
                $userId = $matches[2];
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">@' . htmlspecialchars($name) . '</span>';
            },
            $this->comment
        );
        
        return $formatted;
    }
}
