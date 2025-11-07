<div class="mt-8 border-t border-zinc-200 dark:border-zinc-700 pt-8">
    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
        Comments ({{ $comments->count() }})
    </h3>

    @if (session()->has('comment-success'))
        <flux:alert variant="success" class="mb-4">
            {{ session('comment-success') }}
        </flux:alert>
    @endif

    <!-- Comment Form -->
    <div class="mb-6">
        <form wire:submit="addComment">
            <div class="relative">
                <flux:textarea 
                    wire:model.live="newComment" 
                    :label="$replyingTo ? 'Reply' : 'Add a comment'"
                    placeholder="Type @ to mention someone..."
                    rows="3"
                />

                <!-- User Suggestions Dropdown -->
                @if ($showUserSuggestions && count($userSuggestions) > 0)
                    <div class="absolute z-50 mt-2 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl max-h-48 overflow-y-auto" style="top: 100%;">
                        @foreach ($userSuggestions as $user)
                            <button 
                                type="button"
                                wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                class="w-full px-4 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-700 flex items-center gap-2"
                            >
                                <div class="w-8 h-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif

                @error('newComment') 
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> 
                @enderror
            </div>

            <div class="flex items-center gap-2 mt-3">
                <flux:button type="submit" variant="primary">
                    {{ $replyingTo ? 'Post Reply' : 'Post Comment' }}
                </flux:button>
                
                @if ($replyingTo)
                    <flux:button type="button" wire:click="cancelReply" variant="ghost">
                        Cancel Reply
                    </flux:button>
                @endif
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                <!-- Comment Header -->
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $comment->user->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    @if ($comment->user_id === auth()->id() || auth()->user()->isSuperAdmin())
                        <div class="flex items-center gap-1">
                            @if ($comment->user_id === auth()->id())
                                <flux:button 
                                    wire:click="startEdit({{ $comment->id }}, '{{ addslashes($comment->comment) }}')" 
                                    variant="ghost" 
                                    size="sm"
                                    icon="pencil"
                                >
                                </flux:button>
                            @endif
                            <flux:button 
                                wire:click="deleteComment({{ $comment->id }})" 
                                wire:confirm="Are you sure you want to delete this comment?"
                                variant="ghost" 
                                size="sm"
                                icon="trash"
                            >
                            </flux:button>
                        </div>
                    @endif
                </div>

                <!-- Comment Body -->
                @if ($editingComment === $comment->id)
                    <div class="mt-2">
                        <flux:textarea 
                            wire:model="editText" 
                            rows="3"
                        />
                        <div class="flex items-center gap-2 mt-2">
                            <flux:button wire:click="saveEdit" variant="primary" size="sm">
                                Save
                            </flux:button>
                            <flux:button wire:click="cancelEdit" variant="ghost" size="sm">
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                @else
                    <div class="text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">
                        {!! nl2br($comment->formatted_comment) !!}
                    </div>

                    <div class="mt-2">
                        <flux:button 
                            wire:click="startReply({{ $comment->id }})" 
                            variant="ghost" 
                            size="sm"
                        >
                            Reply
                        </flux:button>
                    </div>
                @endif

                <!-- Replies -->
                @if ($comment->replies->count() > 0)
                    <div class="mt-4 ml-8 space-y-3 border-l-2 border-zinc-200 dark:border-zinc-700 pl-4">
                        @foreach ($comment->replies as $reply)
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-green-500 dark:bg-green-600 flex items-center justify-center text-white text-sm font-semibold">
                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $reply->user->name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $reply->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>

                                    @if ($reply->user_id === auth()->id() || auth()->user()->isSuperAdmin())
                                        <flux:button 
                                            wire:click="deleteComment({{ $reply->id }})" 
                                            wire:confirm="Are you sure you want to delete this reply?"
                                            variant="ghost" 
                                            size="sm"
                                            icon="trash"
                                        >
                                        </flux:button>
                                    @endif
                                </div>

                                <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">
                                    {!! nl2br($reply->formatted_comment) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                No comments yet. Be the first to comment!
            </div>
        @endforelse
    </div>
</div>
