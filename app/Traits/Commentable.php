<?php

namespace App\Traits;

use App\Models\Comment;

trait Commentable
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
                    ->whereNull('parent_id')
                    ->with('user', 'replies.user')
                    ->latest();
    }

    public function allComments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getCommentCountAttribute()
    {
        return $this->allComments()->count();
    }
}
