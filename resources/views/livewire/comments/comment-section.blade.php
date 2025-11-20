<div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        Comments ({{ $comments->count() }})
    </h3>

    @if (session()->has('comment-success'))
        <flux:alert variant="success" class="mb-4">
            {{ session('comment-success') }}
        </flux:alert>
    @endif

    @if (!$replyingTo)
        <!-- Comment Form -->
        <div class="mb-6">
            @include('livewire.comments.partials.comment-form', [
                'formKey' => 'comment-form-root',
                'label' => 'Add a comment',
                'submitLabel' => 'Post Comment',
                'showCancel' => false,
            ])
        </div>
    @endif

    <!-- Comments List -->
    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div id="comment-{{ $comment->id }}" class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 scroll-mt-20">
                <!-- Comment Header -->
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</div>
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
                            <button wire:click="saveEdit" type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition">
                                Save
                            </button>
                            <flux:button wire:click="cancelEdit" variant="ghost" size="sm">
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                @else
                    <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{!! nl2br($comment->formatted_comment) !!}</div>

                    @if ($replyingTo === $comment->id)
                        <div class="mt-2 text-sm text-blue-500 dark:text-blue-300">Replying to this comment…</div>
                    @else
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
                @endif

                <!-- Replies -->
                @if ($comment->replies->count() > 0 || $replyingTo === $comment->id)
                    <div class="mt-4 ml-8 space-y-3 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                        @foreach ($comment->replies as $reply)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-green-500 dark:bg-green-600 flex items-center justify-center text-white text-sm font-semibold">
                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $reply->user->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</div>
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

                                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{!! nl2br($reply->formatted_comment) !!}</div>
                            </div>
                        @endforeach

                        @if ($replyingTo === $comment->id)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                @include('livewire.comments.partials.comment-form', [
                                    'formKey' => 'reply-form-' . $comment->id,
                                    'label' => 'Reply',
                                    'submitLabel' => 'Post Reply',
                                    'showCancel' => true,
                                ])
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                Be the first to comment on this object.
            </div>
        @endforelse
    </div>
</div>
