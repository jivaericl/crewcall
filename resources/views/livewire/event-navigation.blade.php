<div class="space-y-1">
    @if($event)
        @foreach($menuItems as $item)
            @if(isset($item['children']))
                {{-- Menu item with children (expandable) --}}
                <div x-data="{ open: {{ request()->routeIs($item['route'] ?? '') || collect($item['children'])->contains(fn($child) => request()->routeIs($child['route'] ?? '')) ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <div class="flex items-center gap-2">
                            @if($item['icon'] === 'users')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </div>
                        <svg 
                            class="w-4 h-4 transition-transform"
                            :class="{'rotate-90': open}"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="ml-4 mt-1 space-y-1"
                    >
                        @foreach($item['children'] as $child)
                            <a 
                                href="{{ route($child['route'], $child['params']) }}"
                                class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg {{ request()->routeIs($child['route']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) ? 'bg-blue-600 dark:bg-blue-400' : 'bg-gray-400 dark:bg-gray-600' }}"></span>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Simple menu item --}}
                <a 
                    href="{{ route($item['route'], $item['params']) }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs($item['route']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                >
                    @if($item['icon'] === 'calendar')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    @elseif($item['icon'] === 'folder')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    @elseif($item['icon'] === 'tag')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    @elseif($item['icon'] === 'clipboard-list')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    @endif
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    @else
        <div class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
            Select an event to view navigation
        </div>
    @endif
</div>
