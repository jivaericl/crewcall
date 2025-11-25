@props([
    'formKey',
    'label' => 'Add a comment',
    'submitLabel' => 'Post Comment',
    'showCancel' => false,
])

<form wire:submit="addComment" wire:key="{{ $formKey }}">
    <div class="relative">
        <flux:textarea 
            wire:model.live="newComment" 
            :label="$label"
            placeholder="Type @ to mention someone..."
            rows="3"
        />

        @if ($showUserSuggestions && count($userSuggestions) > 0)
            <div class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl max-h-48 overflow-y-auto" style="top: 100%;">
                @foreach ($userSuggestions as $user)
                    <button 
                        type="button"
                        wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                        class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                    >
                        <div class="w-8 h-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
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
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
            <x-lineicon alias="ui.chat_send" size="w-4 h-4" />
            {{ $submitLabel }}
        </button>
        
        @if ($showCancel)
            <flux:button type="button" wire:click="cancelReply" variant="ghost">
                Cancel Reply
            </flux:button>
        @endif
    </div>
</form>
