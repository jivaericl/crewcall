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
                            <x-lineicon alias="{{ $item['icon'] }}" size="w-5 h-5" />
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
                                @if(isset($child['badge']) && $child['badge'] > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $child['badge'] }}</span>
                                @endif
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
                    <x-lineicon alias="{{ $item['icon'] }}" size="w-5 h-5" />
                    <span>{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endif
        @endforeach
    @else
        <div class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
            Select an event to view navigation
        </div>
    @endif
</div>
