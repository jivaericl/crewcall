<div wire:poll.30s="updateUnreadCount">
    @if($unreadCount > 0)
        <span class="ms-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
    @endif
</div>
